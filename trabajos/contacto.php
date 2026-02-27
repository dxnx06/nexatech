<?php
require_once 'includes/config.php';
$page_title = 'Contacto';
$db = getDB();

$mensaje = '';
$tipo_mensaje = '';
$servicio_pre = isset($_GET['servicio']) ? clean($_GET['servicio']) : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = clean($_POST['nombre'] ?? '');
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $empresa = clean($_POST['empresa'] ?? '');
    $telefono = clean($_POST['telefono'] ?? '');
    $servicio = clean($_POST['servicio'] ?? '');
    $msg = clean($_POST['mensaje'] ?? '');

    if ($nombre && $email && $msg) {
        $stmt = $db->prepare("INSERT INTO contactos (nombre, email, empresa, telefono, servicio_interes, mensaje) VALUES (?,?,?,?,?,?)");
        $stmt->execute([$nombre, $email, $empresa, $telefono, $servicio, $msg]);
        $mensaje = '✅ ¡Mensaje enviado con éxito! Te contactaremos en menos de 24 horas.';
        $tipo_mensaje = 'success';
    } else {
        $mensaje = '⚠️ Por favor completa todos los campos requeridos.';
        $tipo_mensaje = 'error';
    }
}

$servicios = $db->query("SELECT nombre FROM servicios WHERE activo=1 ORDER BY nombre")->fetchAll();

include 'includes/header.php';
?>

<div class="page-hero">
  <div class="container">
    <div class="section-tag" style="justify-content:center;color:#6B9FFF;"><i class="fa-solid fa-paper-plane"></i> Contacto</div>
    <h1>Hablemos de tu <span style="color:var(--accent)">Proyecto</span></h1>
    <p>Agenda una consulta gratuita. Respuesta garantizada en menos de 24 horas.</p>
  </div>
</div>

<section>
  <div class="container">
    <div class="contact-grid">
      <div class="contact-info">
        <div class="section-tag"><i class="fa-solid fa-location-dot"></i> Dónde Estamos</div>
        <h2 class="section-title">Estamos listos para ayudarte</h2>
        <p style="color:var(--gray-500);margin-bottom:2.5rem;line-height:1.7;">Nuestro equipo de especialistas está disponible para analizar tu caso y proponer la mejor solución tecnológica para tu empresa.</p>

        <div class="contact-item">
          <div class="contact-icon"><i class="fa-solid fa-map-marker-alt"></i></div>
          <div><strong>Oficina Principal</strong><p>Av. Tecnología 2050, Torre Nexus Piso 12<br>Ciudad de México, CDMX 06600</p></div>
        </div>
        <div class="contact-item">
          <div class="contact-icon"><i class="fa-solid fa-phone"></i></div>
          <div><strong>Teléfono</strong><p>+52 (55) 5100-2050<br>+52 (55) 5100-2051</p></div>
        </div>
        <div class="contact-item">
          <div class="contact-icon"><i class="fa-solid fa-envelope"></i></div>
          <div><strong>Email</strong><p>contacto@nexatech.com<br>soporte@nexatech.com</p></div>
        </div>
        <div class="contact-item">
          <div class="contact-icon"><i class="fa-solid fa-clock"></i></div>
          <div><strong>Horario de Atención</strong><p>Lunes a Viernes: 9:00 – 18:00<br>Sábados: 10:00 – 14:00</p></div>
        </div>

        <div style="background:var(--primary-light);border-radius:var(--radius);padding:1.5rem;margin-top:2rem;">
          <h4 style="font-family:var(--font-display);color:var(--primary);margin-bottom:.75rem;"><i class="fa-solid fa-star"></i> Primera Consulta Gratuita</h4>
          <p style="font-size:.9rem;color:var(--gray-700);line-height:1.6;">Agenda una videollamada sin costo con nuestros especialistas. Analizamos tu proyecto y te proponemos la mejor solución.</p>
        </div>
      </div>

      <div class="contact-form">
        <h3><i class="fa-solid fa-message text-primary"></i> Envíanos un Mensaje</h3>
        <?php if($mensaje): ?>
          <div class="alert alert-<?php echo $tipo_mensaje; ?>"><?php echo $mensaje; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
          <div class="form-row">
            <div class="form-group">
              <label>Nombre Completo *</label>
              <input type="text" name="nombre" placeholder="Carlos Mendoza" required>
            </div>
            <div class="form-group">
              <label>Correo Electrónico *</label>
              <input type="email" name="email" placeholder="carlos@empresa.com" required>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Empresa</label>
              <input type="text" name="empresa" placeholder="Mi Empresa S.A.">
            </div>
            <div class="form-group">
              <label>Teléfono</label>
              <input type="tel" name="telefono" placeholder="+52 555 123 4567">
            </div>
          </div>
          <div class="form-group">
            <label>Servicio de Interés</label>
            <select name="servicio">
              <option value="">-- Selecciona un servicio --</option>
              <?php foreach($servicios as $sv): ?>
                <option value="<?php echo htmlspecialchars($sv['nombre']); ?>" <?php echo $servicio_pre==$sv['nombre']?'selected':''; ?>>
                  <?php echo htmlspecialchars($sv['nombre']); ?>
                </option>
              <?php endforeach; ?>
              <option value="Otro">Otro / No estoy seguro</option>
            </select>
          </div>
          <div class="form-group">
            <label>Mensaje *</label>
            <textarea name="mensaje" placeholder="Cuéntanos sobre tu proyecto, necesidades y cualquier detalle relevante..." required></textarea>
          </div>
          <button type="submit" class="btn btn-primary btn-lg" style="width:100%;justify-content:center;">
            <i class="fa-solid fa-paper-plane"></i> Enviar Mensaje
          </button>
          <p style="font-size:.78rem;color:var(--gray-400);text-align:center;margin-top:.75rem;">🔒 Tu información está protegida. No enviamos spam.</p>
        </form>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
