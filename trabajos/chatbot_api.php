<?php
// ============================================================
// NEXATECH — Chatbot API Endpoint
// chatbot_api.php
// ============================================================

require_once 'includes/config.php';
require_once 'includes/chatbot_engine.php';

header('Content-Type: application/json; charset=utf-8');

// Solo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
    exit;
}

$input  = json_decode(file_get_contents('php://input'), true) ?? [];
$action = $input['action'] ?? 'message';

// ── Helper: obtener IP ────────────────────────────────────────
function getIP(): string {
    foreach (['HTTP_CF_CONNECTING_IP','HTTP_X_FORWARDED_FOR','HTTP_X_REAL_IP','REMOTE_ADDR'] as $h) {
        if (!empty($_SERVER[$h])) {
            $ip = trim(explode(',', $_SERVER[$h])[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) return $ip;
        }
    }
    return '0.0.0.0';
}

// ── Helper: obtener o crear sesión de chat ───────────────────
function getOrCreateSession(PDO $db, string $token): int {
    $q = $db->prepare("SELECT id FROM chat_sesiones WHERE session_token = ? LIMIT 1");
    $q->execute([$token]);
    $row = $q->fetch();
    if ($row) {
        // Actualizar timestamp
        $db->prepare("UPDATE chat_sesiones SET fecha_ultimo = NOW() WHERE id = ?")
           ->execute([$row['id']]);
        return (int)$row['id'];
    }
    // Crear nueva sesión
    $ip        = getIP();
    $ua        = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500);
    $pagina    = substr($_SERVER['HTTP_REFERER'] ?? '/', 0, 255);
    $usuario_id = $_SESSION['usuario_id'] ?? null;

    $db->prepare("INSERT INTO chat_sesiones (session_token, usuario_id, ip, user_agent, pagina_origen) VALUES (?,?,?,?,?)")
       ->execute([$token, $usuario_id, $ip, $ua, $pagina]);
    return (int)$db->lastInsertId();
}

// ── Helper: guardar mensaje ───────────────────────────────────
function saveMessage(PDO $db, int $sesion_id, string $rol, string $mensaje, array $meta = []): void {
    $db->prepare("INSERT INTO chat_mensajes (sesion_id, rol, mensaje, metadata) VALUES (?,?,?,?)")
       ->execute([$sesion_id, $rol, $mensaje, $meta ? json_encode($meta) : null]);
    $db->prepare("UPDATE chat_sesiones SET total_mensajes = total_mensajes + 1 WHERE id = ?")
       ->execute([$sesion_id]);
}

$db   = getDB();
$bot  = new NexaChatbot();

// ════════════════════════════════════════════════════════════
// ACCIÓN: iniciar sesión
// ════════════════════════════════════════════════════════════
if ($action === 'start') {
    $token = bin2hex(random_bytes(24)); // token único 48 chars
    $sesion_id = getOrCreateSession($db, $token);

    // Mensaje de bienvenida
    $welcome = "¡Hola! 👋 Soy **Nexa**, el asistente virtual de NexaTech Solutions.\n\nEstoy aquí para ayudarte con información sobre nuestros servicios, precios y cómo podemos impulsar tu negocio con tecnología.\n\n¿En qué puedo ayudarte hoy?";
    saveMessage($db, $sesion_id, 'bot', $welcome, ['intent' => 'welcome']);

    echo json_encode([
        'ok'           => true,
        'token'        => $token,
        'message'      => NexaChatbot::markdownToHtml($welcome),
        'quick_replies'=> ['¿Qué servicios ofrecen?', 'Precios', 'Quiero cotizar', 'Contacto'],
    ]);
    exit;
}

// ════════════════════════════════════════════════════════════
// ACCIÓN: enviar mensaje
// ════════════════════════════════════════════════════════════
if ($action === 'message') {
    $token   = trim($input['token']   ?? '');
    $mensaje = trim($input['message'] ?? '');

    if (!$token || !$mensaje) {
        echo json_encode(['ok' => false, 'error' => 'Datos incompletos']);
        exit;
    }
    if (mb_strlen($mensaje) > 500) {
        $mensaje = mb_substr($mensaje, 0, 500);
    }

    $sesion_id = getOrCreateSession($db, $token);

    // Contexto de la sesión (nombre si lo capturamos antes)
    $sesion = $db->prepare("SELECT nombre_visita, email_visita FROM chat_sesiones WHERE id = ?");
    $sesion->execute([$sesion_id]);
    $ctx = $sesion->fetch() ?: [];

    // Guardar mensaje del usuario
    saveMessage($db, $sesion_id, 'user', $mensaje);

    // Pequeña pausa simulada (realismo) — en producción se haría async
    // usleep(600000); // 600ms — descomenta si quieres

    // Generar respuesta
    $context  = ['nombre' => $ctx['nombre_visita'] ?? null];
    $response = $bot->respond($mensaje, $context);

    // Guardar respuesta del bot
    saveMessage($db, $sesion_id, 'bot', $response['text'], [
        'intent' => $response['intent'],
    ]);

    // Si el usuario menciona su email en el mensaje, guardarlo en la sesión
    if (preg_match('/[\w.+-]+@[\w-]+\.[a-z]{2,}/i', $mensaje, $m)) {
        $db->prepare("UPDATE chat_sesiones SET email_visita = ? WHERE id = ?")
           ->execute([$m[0], $sesion_id]);
    }

    echo json_encode([
        'ok'           => true,
        'message'      => NexaChatbot::markdownToHtml($response['text']),
        'intent'       => $response['intent'],
        'quick_replies'=> $response['quick_replies'],
    ]);
    exit;
}

// Acción desconocida
http_response_code(400);
echo json_encode(['ok' => false, 'error' => 'Acción no reconocida']);
