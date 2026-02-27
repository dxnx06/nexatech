<?php
require_once 'includes/config.php';
$page_title = 'Servicios';
$db = getDB();

$categorias = $db->query("SELECT * FROM categorias_servicios WHERE activo=1")->fetchAll();
$servicios = $db->query("SELECT s.*, c.nombre as cat_nombre, c.color, c.icono FROM servicios s LEFT JOIN categorias_servicios c ON s.categoria_id=c.id WHERE s.activo=1 ORDER BY s.destacado DESC, s.id ASC")->fetchAll();

include 'includes/header.php';
?>

<div class="page-hero">
  <div class="container">
    <div class="section-tag" style="justify-content:center;color:#6B9FFF;"><i class="fa-solid fa-grid-2"></i> Catálogo de Servicios</div>
    <h1 style="margin-bottom:.75rem;">Nuestros <span style="color:var(--accent)">Servicios</span></h1>
    <p>Soluciones tecnológicas integrales diseñadas para cada etapa del crecimiento de tu empresa.</p>
  </div>
</div>

<section>
  <div class="container">
    <div class="filter-bar">
      <button class="filter-btn active" data-filter="all">Todos</button>
      <?php foreach($categorias as $cat): ?>
        <button class="filter-btn" data-filter="cat-<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['nombre']); ?></button>
      <?php endforeach; ?>
    </div>

    <div class="cards-grid col-3" id="serviciosList">
      <?php foreach($servicios as $s): ?>
      <div class="service-card filterable" data-cat="cat-<?php echo $s['categoria_id']; ?>" style="--card-color:<?php echo $s['color']?:'#0057FF'; ?>">
        <?php if($s['destacado']): ?><span class="destacado-badge">⭐ Destacado</span><?php endif; ?>
        <div class="s-icon" style="background:<?php echo ($s['color']?:'#0057FF')?>22; color:<?php echo $s['color']?:'#0057FF'; ?>">
          <i class="fa-solid <?php echo $s['icono']?:'fa-cog'; ?>"></i>
        </div>
        <div style="margin-bottom:.5rem;">
          <span class="badge-nivel badge-<?php echo $s['nivel']; ?>"><?php echo $s['nivel']; ?></span>
          <span style="font-size:.75rem;color:var(--gray-500);margin-left:.5rem;"><?php echo htmlspecialchars($s['cat_nombre']); ?></span>
        </div>
        <h3><?php echo htmlspecialchars($s['nombre']); ?></h3>
        <p><?php echo htmlspecialchars($s['descripcion']); ?></p>
        <?php if(!empty($s['caracteristicas'])): ?>
          <ul style="margin-bottom:1rem;">
            <?php foreach(explode(',', $s['caracteristicas']) as $f): ?>
              <li style="display:flex;align-items:center;gap:.4rem;font-size:.82rem;color:var(--gray-700);margin-bottom:.25rem;">
                <i class="fa-solid fa-check-circle" style="color:var(--accent);font-size:.75rem;"></i>
                <?php echo htmlspecialchars(trim($f)); ?>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-top:auto;padding-top:.75rem;border-top:1px solid var(--gray-100);">
          <div class="price"><?php echo formatPrice($s['precio']); ?> <span>/ proyecto</span></div>
          <a href="contacto.php?servicio=<?php echo urlencode($s['nombre']); ?>" class="btn btn-primary btn-sm">Cotizar</a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta-section">
  <div class="container">
    <h2>¿No encuentras lo que buscas?</h2>
    <p>Creamos soluciones personalizadas según las necesidades específicas de tu empresa.</p>
    <div class="cta-actions">
      <a href="contacto.php" class="btn btn-white btn-lg"><i class="fa-solid fa-envelope"></i> Contactar Ahora</a>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
