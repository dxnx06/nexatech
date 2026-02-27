<?php
require_once 'includes/config.php';

if (isLoggedIn()) {
    header('Location: ' . SITE_URL . '/admin/');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $pass = $_POST['password'] ?? '';

    if ($email && $pass) {
        $stmt = $db = getDB();
        $q = $db->prepare("SELECT * FROM usuarios WHERE email=? AND activo=1 LIMIT 1");
        $q->execute([$email]);
        $user = $q->fetch();

        if ($user && verifyPassword($pass, $user['password'])) {
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['nombre'] = $user['nombre'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['rol'] = $user['rol'];

            // Actualizar último acceso
            $db->prepare("UPDATE usuarios SET ultimo_acceso=NOW() WHERE id=?")->execute([$user['id']]);

            header('Location: ' . SITE_URL . '/admin/');
            exit;
        } else {
            $error = 'Credenciales incorrectas. Verifica tu email y contraseña.';
        }
    } else {
        $error = 'Por favor completa todos los campos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar Sesión | NexaTech Solutions</title>
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="<?php echo SITE_URL; ?>/css/style.css">
</head>
<body class="login-page">

<div class="login-wrapper">
  <!-- IZQUIERDA -->
  <div class="login-left">
    <div class="login-left-bg"></div>
    <div class="login-left-content">
      <div class="nav-logo" style="margin-bottom:3rem;">
        <div class="logo-icon"><i class="fa-solid fa-hexagon-nodes"></i></div>
        <span class="logo-text" style="color:white;">Nexa<strong>Tech</strong></span>
      </div>
      <h2>Bienvenido al Portal de Gestión</h2>
      <p>Accede a tu dashboard personalizado con proyectos, servicios, reportes y mucho más.</p>
      <div class="login-features">
        <div class="login-feature"><i class="fa-solid fa-gauge-high"></i> Dashboard con KPIs en tiempo real</div>
        <div class="login-feature"><i class="fa-solid fa-folder-open"></i> Gestión completa de proyectos</div>
        <div class="login-feature"><i class="fa-solid fa-users"></i> Administración de clientes y equipo</div>
        <div class="login-feature"><i class="fa-solid fa-shield-halved"></i> Acceso seguro y encriptado</div>
        <div class="login-feature"><i class="fa-solid fa-chart-line"></i> Reportes y análisis avanzados</div>
      </div>
      <div style="margin-top:3rem;padding-top:2rem;border-top:1px solid rgba(255,255,255,.08);">
        <p style="color:rgba(255,255,255,.4);font-size:.83rem;margin-bottom:1rem;">¿Aún no tienes cuenta?</p>
        <a href="registro.php"
           style="display:inline-flex;align-items:center;gap:.6rem;background:rgba(0,87,255,.15);
                  border:1.5px solid rgba(0,87,255,.35);color:#6B9FFF;border-radius:99px;
                  padding:.6rem 1.3rem;font-size:.88rem;font-weight:600;transition:all .25s;text-decoration:none;"
           onmouseover="this.style.background='rgba(0,87,255,.28)';this.style.color='white';"
           onmouseout="this.style.background='rgba(0,87,255,.15)';this.style.color='#6B9FFF';">
          <i class="fa-solid fa-user-plus"></i> Crear cuenta gratuita →
        </a>
      </div>
    </div>
  </div>

  <!-- DERECHA -->
  <div class="login-right">
    <div class="login-form-wrapper">
      <a href="index.php" class="login-logo">
        <div class="logo-icon"><i class="fa-solid fa-hexagon-nodes"></i></div>
        <span class="logo-text">Nexa<strong>Tech</strong></span>
      </a>
      <h1>Iniciar Sesión</h1>
      <p>Ingresa tus credenciales para acceder al sistema</p>

      <div class="demo-creds">
        <strong>🔑 Credenciales de Demostración</strong>
        Admin: admin@nexatech.com / password
      </div>

      <?php if($error): ?>
        <div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?></div>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="form-group">
          <label><i class="fa-solid fa-envelope"></i> Correo Electrónico</label>
          <input type="email" name="email" placeholder="tu@empresa.com" value="admin@nexatech.com" required>
        </div>
        <div class="form-group">
          <label><i class="fa-solid fa-lock"></i> Contraseña</label>
          <input type="password" name="password" placeholder="••••••••" value="password" required>
        </div>
        <button type="submit" class="btn btn-primary btn-lg" style="width:100%;justify-content:center;margin-top:.5rem;">
          <i class="fa-solid fa-right-to-bracket"></i> Ingresar al Sistema
        </button>
      </form>

      <!-- Divisor -->
      <div style="display:flex;align-items:center;gap:.75rem;margin:1.75rem 0;color:var(--gray-400);font-size:.8rem;">
        <span style="flex:1;height:1px;background:var(--gray-200);display:block;"></span>
        ¿Nuevo en NexaTech?
        <span style="flex:1;height:1px;background:var(--gray-200);display:block;"></span>
      </div>

      <!-- Botón crear cuenta -->
      <a href="registro.php" class="btn btn-ghost btn-lg"
         style="width:100%;justify-content:center;border-color:var(--primary);color:var(--primary);font-weight:600;">
        <i class="fa-solid fa-user-plus"></i> Crear Cuenta Gratis
      </a>

      <div style="text-align:center;margin-top:1.5rem;">
        <a href="index.php" style="color:var(--gray-400);font-size:.82rem;">
          <i class="fa-solid fa-arrow-left"></i> Volver al sitio web
        </a>
      </div>
    </div>
  </div>
</div>

<script src="<?php echo SITE_URL; ?>/js/main.js"></script>
</body>
</html>
