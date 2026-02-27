<?php
require_once 'includes/config.php';
$page_title = 'Inicio';
$db = getDB();

// Servicios destacados
$servicios = $db->query("SELECT s.*, c.nombre as cat_nombre, c.color FROM servicios s LEFT JOIN categorias_servicios c ON s.categoria_id = c.id WHERE s.destacado=1 AND s.activo=1 LIMIT 4")->fetchAll();

// Testimonios
$testimonios = $db->query("SELECT * FROM testimonios WHERE visible=1 ORDER BY calificacion DESC LIMIT 3")->fetchAll();

// Proyectos recientes
$proyectos = $db->query("SELECT p.*, u.nombre as cliente_nombre FROM proyectos p LEFT JOIN usuarios u ON p.cliente_id=u.id ORDER BY p.fecha_creacion DESC LIMIT 3")->fetchAll();

// Stats
$total_proyectos = $db->query("SELECT COUNT(*) FROM proyectos WHERE estado='Completado'")->fetchColumn();
$total_servicios = $db->query("SELECT COUNT(*) FROM servicios WHERE activo=1")->fetchColumn();
$total_clientes = $db->query("SELECT COUNT(*) FROM usuarios WHERE rol='cliente'")->fetchColumn();

include 'includes/header.php';
?>

<?php if(isset($_GET['bienvenido'])): ?>
<div id="welcomeToast" style="position:fixed;top:82px;left:50%;transform:translateX(-50%);z-index:9999;
     background:#D1FAE5;border:1.5px solid #6EE7B7;border-radius:var(--radius-full);
     padding:.8rem 2rem;font-size:.9rem;color:#065F46;font-weight:600;
     display:flex;align-items:center;gap:.6rem;box-shadow:0 8px 30px rgba(0,0,0,.12);
     animation:toastIn .45s cubic-bezier(.34,1.56,.64,1) both;">
  <i class="fa-solid fa-circle-check" style="font-size:1.1rem;"></i>
  ¡Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre'] ?? 'usuario'); ?>! 🎉 Tu cuenta fue creada exitosamente.
  <button onclick="document.getElementById('welcomeToast').remove()"
          style="background:none;border:none;cursor:pointer;color:#065F46;margin-left:.5rem;font-size:1rem;opacity:.6;">✕</button>
</div>
<style>
  @keyframes toastIn { from{opacity:0;transform:translateX(-50%) translateY(-16px)} to{opacity:1;transform:translateX(-50%) translateY(0)} }
</style>
<script>setTimeout(()=>{const t=document.getElementById('welcomeToast');if(t){t.style.transition='opacity .4s,transform .4s';t.style.opacity='0';t.style.transform='translateX(-50%) translateY(-10px)';setTimeout(()=>t.remove(),400);}},5000);</script>
<?php endif; ?>

<!-- HERO -->
<section class="hero">
  <div class="hero-bg"></div>
  <div class="hero-grid"></div>
  <div class="hero-container">
    <div class="hero-content fade-in">
      <div class="hero-badge">
        <span class="badge-dot"></span>
        Tecnología · Innovación · Resultados
      </div>
      <h1>Transformamos tu empresa con <span>tecnología inteligente</span></h1>
      <p>Soluciones digitales a medida que impulsan el crecimiento de tu negocio. Desarrollo web, ciberseguridad, IA y más.</p>
      <div class="hero-actions">
        <a href="servicios.php" class="btn btn-primary btn-lg"><i class="fa-solid fa-rocket"></i> Ver Servicios</a>
        <a href="contacto.php" class="btn btn-outline-white btn-lg"><i class="fa-solid fa-comments"></i> Hablar con un Experto</a>
      </div>
      <div class="hero-stats">
        <div class="hero-stat"><div class="stat-num"><?php echo $total_proyectos; ?><span>+</span></div><p>Proyectos Completados</p></div>
        <div class="hero-stat"><div class="stat-num">98<span>%</span></div><p>Satisfacción de Clientes</p></div>
        <div class="hero-stat"><div class="stat-num">5<span>+</span></div><p>Años de Experiencia</p></div>
      </div>
    </div>
    <div class="hero-visual fade-in delay-2">
      <div class="hero-card">
        <div class="card-icon" style="background:rgba(0,87,255,.15);color:#0057FF"><i class="fa-solid fa-chart-line"></i></div>
        <div class="metric">+247%</div>
        <h4>Crecimiento Promedio</h4>
        <p>En proyectos de transformación digital</p>
      </div>
      <div class="hero-card">
        <div class="card-icon" style="background:rgba(0,201,167,.15);color:#00C9A7"><i class="fa-solid fa-shield-halved"></i></div>
        <h4>Seguridad Garantizada</h4>
        <p>Protección 24/7</p>
      </div>
      <div class="hero-card">
        <div class="card-icon" style="background:rgba(124,58,237,.15);color:#7C3AED"><i class="fa-solid fa-robot"></i></div>
        <h4>IA Integrada</h4>
        <p>Automatización inteligente</p>
      </div>
    </div>
  </div>
</section>

<!-- SERVICIOS DESTACADOS -->
<section style="background:#FAFBFF;">
  <div class="container">
    <div class="section-header">
      <div class="section-tag"><i class="fa-solid fa-star"></i> Servicios Destacados</div>
      <h2 class="section-title">Soluciones que <span class="text-primary">transforman</span> negocios</h2>
      <p class="section-subtitle">Desde desarrollo web hasta inteligencia artificial, ofrecemos un ecosistema completo de servicios tecnológicos.</p>
    </div>
    <div class="cards-grid col-2" style="margin-bottom:2rem;">
      <?php foreach($servicios as $s): ?>
      <div class="service-card" style="--card-color:<?php echo $s['color'] ?: '#0057FF'; ?>">
        <?php if($s['destacado']): ?><span class="destacado-badge">⭐ Destacado</span><?php endif; ?>
        <div class="s-icon" style="background:<?php echo ($s['color']?:'#0057FF')?>22;color:<?php echo $s['color']?:'#0057FF';?>">
          <i class="fa-solid fa-circle-nodes"></i>
        </div>
        <span class="badge-nivel badge-<?php echo $s['nivel']; ?>"><?php echo $s['nivel']; ?></span>
        <h3><?php echo htmlspecialchars($s['nombre']); ?></h3>
        <p><?php echo htmlspecialchars($s['descripcion']); ?></p>
        <div class="price"><?php echo formatPrice($s['precio']); ?> <span>/ proyecto</span></div>
        <a href="servicios.php" class="btn btn-ghost btn-sm mt-1">Ver detalles <i class="fa-solid fa-arrow-right"></i></a>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="text-center">
      <a href="servicios.php" class="btn btn-primary btn-lg">Ver todos los servicios <i class="fa-solid fa-arrow-right"></i></a>
    </div>
  </div>
</section>

<!-- STATS -->
<section class="stats-section">
  <div class="container">
    <div class="stats-grid">
      <div class="stat-item">
        <div class="number"><span data-count="<?php echo $total_proyectos; ?>"><?php echo $total_proyectos; ?></span>+</div>
        <p>Proyectos Completados</p>
      </div>
      <div class="stat-item">
        <div class="number"><span data-count="<?php echo $total_clientes; ?>"><?php echo $total_clientes; ?></span>+</div>
        <p>Clientes Satisfechos</p>
      </div>
      <div class="stat-item">
        <div class="number"><span data-count="<?php echo $total_servicios; ?>"><?php echo $total_servicios; ?></span></div>
        <p>Servicios Especializados</p>
      </div>
      <div class="stat-item">
        <div class="number"><span data-count="98">98</span><span>%</span></div>
        <p>Tasa de Satisfacción</p>
      </div>
    </div>
  </div>
</section>

<!-- PROYECTOS RECIENTES -->
<section>
  <div class="container">
    <div class="section-header">
      <div class="section-tag"><i class="fa-solid fa-folder-open"></i> Proyectos</div>
      <h2 class="section-title">Proyectos en Curso</h2>
      <p class="section-subtitle">Seguimiento en tiempo real de nuestros proyectos activos.</p>
    </div>
    <div class="cards-grid col-3">
      <?php foreach($proyectos as $p): ?>
      <div class="project-card">
        <div class="project-header">
          <h3><?php echo htmlspecialchars($p['nombre']); ?></h3>
          <span class="status status-<?php echo $p['estado']; ?>"><?php echo $p['estado']; ?></span>
        </div>
        <p class="meta"><i class="fa-solid fa-user"></i> <?php echo htmlspecialchars($p['cliente_nombre']); ?> &nbsp;·&nbsp; <span class="status priority-<?php echo $p['prioridad']; ?>"><?php echo $p['prioridad']; ?></span></p>
        <div class="progress-bar"><div class="progress-fill" style="width:<?php echo $p['avance']; ?>%"></div></div>
        <div class="progress-label"><span>Avance</span><span><?php echo $p['avance']; ?>%</span></div>
        <p class="meta mt-1"><i class="fa-regular fa-calendar"></i> Entrega: <?php echo $p['fecha_fin_estimada'] ? date('d/m/Y', strtotime($p['fecha_fin_estimada'])) : 'Por definir'; ?></p>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-2">
      <a href="proyectos.php" class="btn btn-ghost">Ver todos los proyectos <i class="fa-solid fa-arrow-right"></i></a>
    </div>
  </div>
</section>

<!-- TESTIMONIOS -->
<section style="background:#F0F4FF;">
  <div class="container">
    <div class="section-header centered">
      <div class="section-tag"><i class="fa-solid fa-quote-left"></i> Testimonios</div>
      <h2 class="section-title">Lo que dicen nuestros clientes</h2>
    </div>
    <div class="cards-grid col-3">
      <?php foreach($testimonios as $t): ?>
      <div class="testimonial-card">
        <div class="stars">
          <?php for($i=0;$i<$t['calificacion'];$i++) echo '⭐'; ?>
        </div>
        <p>"<?php echo htmlspecialchars($t['mensaje']); ?>"</p>
        <div class="author">
          <div class="author-avatar"><?php echo strtoupper(substr($t['nombre_cliente'],0,2)); ?></div>
          <div class="author-info">
            <strong><?php echo htmlspecialchars($t['nombre_cliente']); ?></strong>
            <span><?php echo htmlspecialchars($t['cargo']); ?> · <?php echo htmlspecialchars($t['empresa']); ?></span>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta-section">
  <div class="container">
    <h2>¿Listo para transformar tu empresa?</h2>
    <p>Agenda una consulta gratuita y descubre cómo podemos impulsar tu negocio con tecnología de vanguardia.</p>
    <div class="cta-actions">
      <a href="contacto.php" class="btn btn-white btn-lg"><i class="fa-solid fa-paper-plane"></i> Solicitar Consulta Gratis</a>
      <a href="servicios.php" class="btn btn-outline-white btn-lg"><i class="fa-solid fa-grid-2"></i> Ver Servicios</a>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
