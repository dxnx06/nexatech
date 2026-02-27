<?php
// aceptar_cookies.php — Endpoint AJAX
// Guarda el consentimiento de cookies en la BD

require_once 'includes/config.php';

header('Content-Type: application/json');

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'msg' => 'Método no permitido']);
    exit;
}

// Leer datos del body JSON o POST normal
$input = json_decode(file_get_contents('php://input'), true);
$tipo  = isset($input['tipo'])  ? $input['tipo']  : ($_POST['tipo']  ?? 'todas');
$acepto = isset($input['acepto']) ? (int)$input['acepto'] : 1;

// Validar tipo
if (!in_array($tipo, ['todas', 'necesarias'])) $tipo = 'todas';

// Obtener IP real (considera proxies)
function getClientIP() {
    $headers = [
        'HTTP_CF_CONNECTING_IP',   // Cloudflare
        'HTTP_X_FORWARDED_FOR',    // Proxy / load balancer
        'HTTP_X_REAL_IP',          // Nginx proxy
        'HTTP_CLIENT_IP',
        'REMOTE_ADDR',             // Directo (siempre disponible)
    ];
    foreach ($headers as $h) {
        if (!empty($_SERVER[$h])) {
            // X-Forwarded-For puede tener lista de IPs, tomar la primera
            $ip = trim(explode(',', $_SERVER[$h])[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) return $ip;
        }
    }
    return '0.0.0.0';
}

$ip         = getClientIP();
$user_agent = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500);
$pagina     = substr($_SERVER['HTTP_REFERER'] ?? '/index.php', 0, 255);
$usuario_id = $_SESSION['usuario_id'] ?? null;

try {
    $db = getDB();

    // Evitar duplicados: si ya existe un registro reciente (< 1 año) para esta IP, actualizar
    $existe = $db->prepare("SELECT id FROM cookies_consentimiento WHERE ip = ? AND fecha > DATE_SUB(NOW(), INTERVAL 1 YEAR) ORDER BY fecha DESC LIMIT 1");
    $existe->execute([$ip]);
    $row = $existe->fetch();

    if ($row) {
        // Actualizar registro existente
        $db->prepare("UPDATE cookies_consentimiento SET acepto=?, tipo=?, usuario_id=?, fecha=NOW() WHERE id=?")
           ->execute([$acepto, $tipo, $usuario_id, $row['id']]);
        $msg = 'Consentimiento actualizado';
    } else {
        // Insertar nuevo registro
        $db->prepare("INSERT INTO cookies_consentimiento (usuario_id, ip, user_agent, pagina, acepto, tipo) VALUES (?,?,?,?,?,?)")
           ->execute([$usuario_id, $ip, $user_agent, $pagina, $acepto, $tipo]);
        $msg = 'Consentimiento registrado';
    }

    echo json_encode([
        'ok'   => true,
        'msg'  => $msg,
        'ip'   => $ip,
        'tipo' => $tipo,
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'msg' => 'Error interno: ' . $e->getMessage()]);
}
?>
