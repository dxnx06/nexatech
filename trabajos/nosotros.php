<?php
require_once 'includes/config.php';
$page_title = 'Nosotros';
$db = getDB();

$empleados = $db->query("SELECT e.*, u.nombre, u.apellido, u.email FROM empleados e LEFT JOIN usuarios u ON e.usuario_id=u.id")->fetchAll();
$colores_avatar = ['#0057FF','#00C9A7','#7C3AED','#FF3860','#F59E0B'];

include 'includes/header.php';
?>

<div class="page-hero">
  <div class="container">
    <div class="section-tag" style="justify-content:center;color:#6B9FFF;"><i class="fa-solid fa-users"></i> Nuestra Empresa</div>
    <h1>Quiénes <span style="color:var(--accent)">Somos</span></h1>
    <p>Un equipo apasionado por la tecnología, comprometido con el éxito de cada cliente.</p>
  </div>
</div>

<section>
  <div class="container">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:5rem;align-items:center;margin-bottom:5rem;">
      <div>
        <div class="section-tag"><i class="fa-solid fa-building"></i> Sobre NexaTech</div>
        <h2 class="section-title">Más de 5 años construyendo el futuro digital</h2>
        <p style="color:var(--gray-500);line-height:1.8;margin-bottom:1.5rem;">Fundada en 2020, NexaTech Solutions nació con una misión clara: democratizar el acceso a tecnología de calidad para empresas de todos los tamaños. Combinamos experiencia técnica de alto nivel con un profundo entendimiento del negocio de nuestros clientes.</p>
        <p style="color:var(--gray-500);line-height:1.8;margin-bottom:2rem;">Nuestro equipo multidisciplinario de más de 20 profesionales trabaja bajo metodologías ágiles, garantizando entregas puntuales, código limpio y resultados que superan las expectativas.</p>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
          <?php
          $values = [
            ['fa-bullseye','Misión','Impulsar la transformación digital con soluciones innovadoras y accesibles.'],
            ['fa-eye','Visión','Ser la empresa de tecnología más confiable de Latinoamérica para 2030.'],
            ['fa-gem','Calidad','Código limpio, entregas puntuales y soporte post-venta excepcional.'],
            ['fa-handshake','Confianza','Transparencia y honestidad en cada etapa del proyecto.'],
          ];
          foreach($values as $v):
          ?>
          <div style="background:var(--primary-light);border-radius:var(--radius);padding:1.25rem;">
            <div style="color:var(--primary);font-size:1.3rem;margin-bottom:.5rem;"><i class="fa-solid <?php echo $v[0]; ?>"></i></div>
            <strong style="font-family:var(--font-display);display:block;margin-bottom:.25rem;"><?php echo $v[1]; ?></strong>
            <p style="font-size:.82rem;color:var(--gray-600);"><?php echo $v[2]; ?></p>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <div style="background:var(--dark);border-radius:var(--radius);padding:3rem;color:white;position:relative;overflow:hidden;">
        <div style="position:absolute;inset:0;background:radial-gradient(ellipse at 80% 20%,rgba(0,87,255,.2),transparent 60%),radial-gradient(ellipse at 20% 80%,rgba(0,201,167,.1),transparent 60%);"></div>
        <div style="position:relative;">
          <h3 style="font-family:var(--font-display);font-size:1.5rem;margin-bottom:2rem;">NexaTech en Números</h3>
          <?php
          $nums = [['<?php echo $total_proyectos ?? 4; ?>+','Proyectos Completados'],['100%','Código bajo control de versiones'],['24/7','Soporte y monitoreo'],['6','Áreas de especialidad']];
          $nums = [['4+','Proyectos Completados'],['100%','Código versionado'],['24/7','Soporte activo'],['6','Áreas de especialidad']];
          foreach($nums as $n):
          ?>
          <div style="display:flex;justify-content:space-between;align-items:center;padding:1rem 0;border-bottom:1px solid rgba(255,255,255,.08);">
            <span style="color:rgba(255,255,255,.6);font-size:.9rem;"><?php echo $n[1]; ?></span>
            <span style="font-family:var(--font-display);font-size:1.4rem;font-weight:800;color:var(--accent);"><?php echo $n[0]; ?></span>
          </div>
          <?php endforeach; ?>
          <a href="contacto.php" class="btn btn-primary btn-lg" style="margin-top:2rem;width:100%;justify-content:center;">
            <i class="fa-solid fa-paper-plane"></i> Trabajemos Juntos
          </a>
        </div>
      </div>
    </div>

    <!-- Equipo -->
    <div class="section-header centered">
      <div class="section-tag"><i class="fa-solid fa-people-group"></i> El Equipo</div>
      <h2 class="section-title">Conoce a nuestros expertos</h2>
      <p class="section-subtitle">Profesionales apasionados con años de experiencia en tecnología y negocios digitales.</p>
    </div>
    <div class="cards-grid col-3">
      <?php foreach($empleados as $k=>$e): ?>
      <div class="team-card">
        <div class="team-avatar" style="background:linear-gradient(135deg,<?php echo $colores_avatar[$k%count($colores_avatar)]; ?>,<?php echo $colores_avatar[($k+1)%count($colores_avatar)]; ?>)">
          <?php echo strtoupper(substr($e['nombre'],0,1).substr($e['apellido'],0,1)); ?>
        </div>
        <div class="team-info">
          <h3><?php echo htmlspecialchars($e['nombre'].' '.$e['apellido']); ?></h3>
          <p class="cargo"><?php echo htmlspecialchars($e['cargo']); ?></p>
          <p class="depto"><i class="fa-solid fa-building"></i> <?php echo htmlspecialchars($e['departamento']); ?></p>
          <?php if($e['fecha_ingreso']): ?><p style="font-size:.78rem;color:var(--gray-400);">Desde <?php echo date('Y', strtotime($e['fecha_ingreso'])); ?></p><?php endif; ?>
          <div class="team-social" style="margin-top:.75rem;">
            <a href="#"><i class="fa-brands fa-linkedin"></i></a>
            <a href="mailto:<?php echo $e['email']; ?>"><i class="fa-solid fa-envelope"></i></a>
            <a href="#"><i class="fa-brands fa-github"></i></a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="cta-section">
  <div class="container">
    <h2>¿Quieres ser parte de NexaTech?</h2>
    <p>Siempre buscamos talento apasionado por la tecnología. Únete a nuestro equipo.</p>
    <div class="cta-actions">
      <a href="contacto.php" class="btn btn-white btn-lg"><i class="fa-solid fa-briefcase"></i> Ver Vacantes</a>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
