<?php
require_once 'includes/config.php';

if (isLoggedIn()) {
    header('Location: ' . SITE_URL . '/admin/');
    exit;
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = clean($_POST['nombre']   ?? '');
    $apellido = clean($_POST['apellido'] ?? '');
    $email    = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $empresa  = clean($_POST['empresa']  ?? '');
    $pass     = $_POST['password']       ?? '';
    $pass2    = $_POST['password2']      ?? '';

    // Validaciones
    if (!$nombre || !$apellido || !$email || !$pass) {
        $error = 'Por favor completa todos los campos obligatorios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'El formato del correo electrónico no es válido.';
    } elseif (strlen($pass) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres.';
    } elseif ($pass !== $pass2) {
        $error = 'Las contraseñas no coinciden. Verifica e inténtalo de nuevo.';
    } else {
        $db = getDB();
        // Verificar si el email ya existe
        $check = $db->prepare("SELECT id FROM usuarios WHERE email = ? LIMIT 1");
        $check->execute([$email]);
        if ($check->fetch()) {
            $error = 'Este correo electrónico ya está registrado. ¿Quieres <a href="login.php" style="color:var(--primary);font-weight:600;">iniciar sesión</a>?';
        } else {
            $hash = hashPassword($pass);
            $stmt = $db->prepare("INSERT INTO usuarios (nombre, apellido, email, password, rol, activo) VALUES (?, ?, ?, ?, 'cliente', 1)");
            $stmt->execute([$nombre, $apellido, $email, $hash]);
            $new_id = $db->lastInsertId();

            // Auto-login tras registro
            $_SESSION['usuario_id'] = $new_id;
            $_SESSION['nombre']     = $nombre;
            $_SESSION['email']      = $email;
            $_SESSION['rol']        = 'cliente';

            header('Location: ' . SITE_URL . '/index.php?bienvenido=1');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Crear Cuenta | NexaTech Solutions</title>
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="<?php echo SITE_URL; ?>/css/style.css">
  <style>
    /* Barra de fuerza de contraseña */
    .pass-strength { margin-top: .4rem; height: 4px; border-radius: 99px; background: var(--gray-200); overflow: hidden; transition: .3s; }
    .pass-strength-fill { height: 100%; border-radius: 99px; width: 0%; transition: width .4s ease, background .3s; }
    .pass-strength-label { font-size: .73rem; margin-top: .3rem; font-weight: 600; }

    /* Toggle password */
    .input-eye { position: relative; }
    .input-eye input { padding-right: 2.75rem; }
    .eye-btn { position: absolute; right: .85rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--gray-400); font-size: .95rem; transition: color .2s; }
    .eye-btn:hover { color: var(--primary); }

    /* Divisor */
    .divider { display: flex; align-items: center; gap: .75rem; margin: 1.5rem 0; color: var(--gray-400); font-size: .8rem; }
    .divider::before, .divider::after { content:''; flex:1; height:1px; background: var(--gray-200); }

    /* Registro izquierda — pasos */
    .steps-list { margin-top: 2.5rem; display: flex; flex-direction: column; gap: 1.25rem; }
    .step-item { display: flex; align-items: flex-start; gap: .9rem; }
    .step-num { width: 30px; height: 30px; border-radius: 50%; background: rgba(0,87,255,.18); color: #6B9FFF; display: grid; place-items: center; font-family: var(--font-display); font-size: .8rem; font-weight: 800; flex-shrink: 0; margin-top: .05rem; }
    .step-item p { font-size: .85rem; color: rgba(255,255,255,.55); line-height: 1.5; }
    .step-item strong { display: block; color: rgba(255,255,255,.85); font-size: .88rem; margin-bottom: .1rem; }

    /* Checkboxes inline */
    .terms-check { display: flex; align-items: flex-start; gap: .6rem; cursor: pointer; font-size: .85rem; color: var(--gray-600); line-height: 1.5; }
    .terms-check input { width: auto; margin-top: .2rem; flex-shrink: 0; accent-color: var(--primary); }
    .terms-check a { color: var(--primary); font-weight: 600; }

    /* Animación de entrada */
    .slide-up { animation: slideUp .5s ease both; }
    @keyframes slideUp { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
  </style>
</head>
<body class="login-page">

<div class="login-wrapper">

  <!-- ═══ PANEL IZQUIERDO ═══ -->
  <div class="login-left">
    <div class="login-left-bg"></div>
    <div class="login-left-content">
      <a href="index.php" class="nav-logo" style="margin-bottom:3rem; text-decoration:none;">
        <div class="logo-icon"><i class="fa-solid fa-hexagon-nodes"></i></div>
        <span class="logo-text" style="color:white;">Nexa<strong style="color:var(--primary);">Tech</strong></span>
      </a>

      <h2 style="font-family:var(--font-display);font-size:2.2rem;font-weight:800;color:white;line-height:1.2;margin-bottom:.75rem;">
        Únete a NexaTech<br>
        <span style="color:var(--accent);">en 3 pasos</span>
      </h2>
      <p style="color:rgba(255,255,255,.55);line-height:1.7;font-size:.95rem;">
        Crea tu cuenta gratuita y accede a cotizaciones, seguimiento de proyectos y soporte personalizado.
      </p>

      <div class="steps-list">
        <div class="step-item">
          <div class="step-num">1</div>
          <div>
            <strong>Completa tus datos</strong>
            <p>Solo necesitamos tu nombre, correo y una contraseña segura.</p>
          </div>
        </div>
        <div class="step-item">
          <div class="step-num">2</div>
          <div>
            <strong>Acceso inmediato</strong>
            <p>Tu cuenta se activa al instante, sin esperas ni verificaciones.</p>
          </div>
        </div>
        <div class="step-item">
          <div class="step-num">3</div>
          <div>
            <strong>Solicita tu servicio</strong>
            <p>Cotiza proyectos, haz seguimiento y chatea con nuestro equipo.</p>
          </div>
        </div>
      </div>

      <!-- Testimonial mini -->
      <div style="margin-top:3rem;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:var(--radius);padding:1.25rem;">
        <div style="color:var(--warning);font-size:.8rem;margin-bottom:.5rem;">★★★★★</div>
        <p style="color:rgba(255,255,255,.6);font-size:.85rem;font-style:italic;line-height:1.6;margin-bottom:.75rem;">
          "El portal de clientes es increíble. Puedo ver el avance de mi proyecto en tiempo real."
        </p>
        <div style="display:flex;align-items:center;gap:.6rem;">
          <div style="width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--accent));display:grid;place-items:center;color:white;font-size:.75rem;font-weight:700;">RF</div>
          <span style="color:rgba(255,255,255,.5);font-size:.78rem;">Roberto Flores · Innovatek S.A.</span>
        </div>
      </div>
    </div>
  </div>

  <!-- ═══ PANEL DERECHO — FORMULARIO ═══ -->
  <div class="login-right">
    <div class="login-form-wrapper slide-up">

      <a href="index.php" class="login-logo">
        <div class="logo-icon"><i class="fa-solid fa-hexagon-nodes"></i></div>
        <span class="logo-text">Nexa<strong>Tech</strong></span>
      </a>

      <h1 style="font-size:1.75rem;">Crear Cuenta Gratuita</h1>
      <p style="color:var(--gray-500);margin-bottom:1.75rem;">Completa el formulario · Acceso inmediato</p>

      <?php if ($error): ?>
        <div class="alert alert-error"><i class="fa-solid fa-triangle-exclamation"></i> <?php echo $error; ?></div>
      <?php endif; ?>

      <form method="POST" action="" id="registerForm" novalidate>

        <!-- Nombre y Apellido -->
        <div class="form-row">
          <div class="form-group">
            <label><i class="fa-solid fa-user" style="color:var(--primary);"></i> Nombre *</label>
            <input type="text" name="nombre"
                   value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>"
                   placeholder="Carlos" required autocomplete="given-name">
          </div>
          <div class="form-group">
            <label><i class="fa-solid fa-user" style="color:var(--primary);"></i> Apellido *</label>
            <input type="text" name="apellido"
                   value="<?php echo htmlspecialchars($_POST['apellido'] ?? ''); ?>"
                   placeholder="Mendoza" required autocomplete="family-name">
          </div>
        </div>

        <!-- Email -->
        <div class="form-group">
          <label><i class="fa-solid fa-envelope" style="color:var(--primary);"></i> Correo Electrónico *</label>
          <input type="email" name="email"
                 value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                 placeholder="carlos@empresa.com" required autocomplete="email">
        </div>

        <!-- Empresa (opcional) -->
        <div class="form-group">
          <label><i class="fa-solid fa-building" style="color:var(--gray-400);"></i> Empresa <span style="color:var(--gray-400);font-weight:400;">(opcional)</span></label>
          <input type="text" name="empresa"
                 value="<?php echo htmlspecialchars($_POST['empresa'] ?? ''); ?>"
                 placeholder="Mi Empresa S.A." autocomplete="organization">
        </div>

        <!-- Contraseña -->
        <div class="form-group">
          <label><i class="fa-solid fa-lock" style="color:var(--primary);"></i> Contraseña *</label>
          <div class="input-eye">
            <input type="password" name="password" id="pass1"
                   placeholder="Mínimo 6 caracteres" required autocomplete="new-password"
                   oninput="checkStrength(this.value)">
            <button type="button" class="eye-btn" onclick="toggleEye('pass1',this)">
              <i class="fa-solid fa-eye"></i>
            </button>
          </div>
          <div class="pass-strength" id="strengthBar">
            <div class="pass-strength-fill" id="strengthFill"></div>
          </div>
          <div class="pass-strength-label" id="strengthLabel" style="color:var(--gray-400);">Ingresa una contraseña</div>
        </div>

        <!-- Confirmar contraseña -->
        <div class="form-group">
          <label><i class="fa-solid fa-lock-open" style="color:var(--primary);"></i> Confirmar Contraseña *</label>
          <div class="input-eye">
            <input type="password" name="password2" id="pass2"
                   placeholder="Repite tu contraseña" required autocomplete="new-password">
            <button type="button" class="eye-btn" onclick="toggleEye('pass2',this)">
              <i class="fa-solid fa-eye"></i>
            </button>
          </div>
          <div id="matchMsg" style="font-size:.73rem;margin-top:.3rem;min-height:1rem;"></div>
        </div>

        <!-- Términos -->
        <div class="form-group">
          <label class="terms-check">
            <input type="checkbox" name="terminos" id="terminos" required>
            Acepto los <a href="#">Términos de Servicio</a> y la <a href="#">Política de Privacidad</a> de NexaTech Solutions.
          </label>
        </div>

        <button type="submit" class="btn btn-primary btn-lg"
                style="width:100%;justify-content:center;margin-top:.25rem;font-size:1rem;">
          <i class="fa-solid fa-user-plus"></i> Crear Mi Cuenta
        </button>
      </form>

      <div class="divider">o</div>

      <!-- Link a login -->
      <div style="text-align:center;">
        <p style="color:var(--gray-500);font-size:.9rem;margin-bottom:.75rem;">¿Ya tienes una cuenta?</p>
        <a href="login.php" class="btn btn-ghost btn-lg" style="width:100%;justify-content:center;">
          <i class="fa-solid fa-right-to-bracket"></i> Iniciar Sesión
        </a>
      </div>

      <div style="text-align:center;margin-top:1.5rem;">
        <a href="index.php" style="color:var(--gray-400);font-size:.82rem;">
          <i class="fa-solid fa-arrow-left"></i> Volver al sitio web
        </a>
      </div>

    </div>
  </div>

</div><!-- /login-wrapper -->

<script>
// ── Toggle visibilidad contraseña
function toggleEye(inputId, btn) {
  const input = document.getElementById(inputId);
  const icon  = btn.querySelector('i');
  if (input.type === 'password') {
    input.type = 'text';
    icon.className = 'fa-solid fa-eye-slash';
  } else {
    input.type = 'password';
    icon.className = 'fa-solid fa-eye';
  }
}

// ── Fuerza de contraseña
function checkStrength(val) {
  const fill  = document.getElementById('strengthFill');
  const label = document.getElementById('strengthLabel');
  let score = 0;
  if (val.length >= 6)  score++;
  if (val.length >= 10) score++;
  if (/[A-Z]/.test(val)) score++;
  if (/[0-9]/.test(val)) score++;
  if (/[^A-Za-z0-9]/.test(val)) score++;

  const levels = [
    { w:'0%',   color:'#E5E7EB', text:'Ingresa una contraseña',  tc:'var(--gray-400)' },
    { w:'20%',  color:'#EF4444', text:'Muy débil',               tc:'#EF4444' },
    { w:'40%',  color:'#F97316', text:'Débil',                   tc:'#F97316' },
    { w:'60%',  color:'#EAB308', text:'Aceptable',               tc:'#CA8A04' },
    { w:'80%',  color:'#22C55E', text:'Fuerte',                  tc:'#16A34A' },
    { w:'100%', color:'#10B981', text:'Muy fuerte ✓',            tc:'#059669' },
  ];
  const lvl = val.length === 0 ? levels[0] : levels[Math.min(score, 5)];
  fill.style.width      = lvl.w;
  fill.style.background = lvl.color;
  label.textContent     = lvl.text;
  label.style.color     = lvl.tc;
}

// ── Coincidencia de contraseñas en tiempo real
document.getElementById('pass2').addEventListener('input', function() {
  const p1  = document.getElementById('pass1').value;
  const msg = document.getElementById('matchMsg');
  if (!this.value) { msg.textContent = ''; return; }
  if (this.value === p1) {
    msg.innerHTML = '<span style="color:#16A34A;font-weight:600;"><i class="fa-solid fa-check"></i> Las contraseñas coinciden</span>';
  } else {
    msg.innerHTML = '<span style="color:#EF4444;font-weight:600;"><i class="fa-solid fa-xmark"></i> Las contraseñas no coinciden</span>';
  }
});

// ── Validar términos antes de enviar
document.getElementById('registerForm').addEventListener('submit', function(e) {
  if (!document.getElementById('terminos').checked) {
    e.preventDefault();
    alert('Debes aceptar los Términos de Servicio para continuar.');
  }
});
</script>

</body>
</html>
