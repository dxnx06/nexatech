<?php
require_once 'includes/config.php';
$page_title = 'Proyectos';
$db = getDB();

$proyectos = $db->query("
  SELECT p.*, u.nombre as cliente_nombre, s.nombre as servicio_nombre, c.color
  FROM proyectos p 
  LEFT JOIN usuarios u ON p.cliente_id=u.id 
  LEFT JOIN servicios s ON p.servicio_id=s.id
  LEFT JOIN categorias_servicios c ON s.categoria_id=c.id
  ORDER BY p.fecha_creacion DESC
")->fetchAll();

include 'includes/header.php';
?>

<div class="page-hero">
  <div class="container">
    <div class="section-tag" style="justify-content:center;color:#6B9FFF;"><i class="fa-solid fa-folder-open"></i> Portfolio</div>
    <h1>Proyectos <span style="color:var(--accent)">Realizados</span></h1>
    <p>Seguimiento de todos los proyectos en desarrollo y completados por NexaTech Solutions.</p>
  </div>
</div>

<section>
  <div class="container">
    <!-- Resumen -->
    <div class="kpi-grid" style="margin-bottom:3rem;">
      <?php
      $estados = ['En Progreso','Completado','Pendiente','Revisión'];
      $colores = [['#DBEAFE','#1D4ED8'],['#D1FAE5','#065F46'],['#FEF3C7','#92400E'],['#EDE9FE','#5B21B6']];
      $iconos = ['fa-spinner','fa-check-circle','fa-clock','fa-eye'];
      foreach($estados as $k=>$e):
        $cnt = $db->prepare("SELECT COUNT(*) FROM proyectos WHERE estado=?");
        $cnt->execute([$e]);
        $n = $cnt->fetchColumn();
      ?>
      <div class="kpi-card">
        <div class="kpi-icon" style="background:<?php echo $colores[$k][0]; ?>;color:<?php echo $colores[$k][1]; ?>">
          <i class="fa-solid <?php echo $iconos[$k]; ?>"></i>
        </div>
        <div class="kpi-info">
          <div class="kpi-num" data-count="<?php echo $n; ?>"><?php echo $n; ?></div>
          <div class="kpi-label"><?php echo $e; ?></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="cards-grid col-2">
      <?php foreach($proyectos as $p): ?>
      <div class="project-card" style="border-left:4px solid <?php echo $p['color']?:'#0057FF'; ?>">
        <div class="project-header">
          <div>
            <h3><?php echo htmlspecialchars($p['nombre']); ?></h3>
            <p class="meta" style="margin-top:.25rem;"><i class="fa-solid fa-user text-primary"></i> <?php echo htmlspecialchars($p['cliente_nombre']??'N/A'); ?></p>
          </div>
          <div style="text-align:right;">
            <span class="status status-<?php echo $p['estado']; ?>"><?php echo $p['estado']; ?></span><br>
            <span class="status priority-<?php echo $p['prioridad']; ?>" style="margin-top:.25rem;display:inline-block;"><?php echo $p['prioridad']; ?></span>
          </div>
        </div>
        <?php if($p['descripcion']): ?>
          <p style="font-size:.85rem;color:var(--gray-500);margin-bottom:1rem;"><?php echo htmlspecialchars($p['descripcion']); ?></p>
        <?php endif; ?>
        <div style="display:flex;justify-content:space-between;font-size:.82rem;color:var(--gray-500);margin-bottom:.75rem;">
          <span><i class="fa-solid fa-calendar-plus"></i> Inicio: <?php echo $p['fecha_inicio'] ? date('d/m/Y', strtotime($p['fecha_inicio'])) : '—'; ?></span>
          <span><i class="fa-solid fa-calendar-check"></i> Entrega: <?php echo $p['fecha_fin_estimada'] ? date('d/m/Y', strtotime($p['fecha_fin_estimada'])) : '—'; ?></span>
          <span><i class="fa-solid fa-dollar-sign"></i> <?php echo formatPrice($p['presupuesto']); ?></span>
        </div>
        <div class="progress-bar"><div class="progress-fill" style="width:<?php echo $p['avance']; ?>%"></div></div>
        <div class="progress-label"><span><?php echo htmlspecialchars($p['servicio_nombre']??''); ?></span><span><strong><?php echo $p['avance']; ?>%</strong> completado</span></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="cta-section">
  <div class="container">
    <h2>¿Tienes un proyecto en mente?</h2>
    <p>Cuéntanos tu idea y la hacemos realidad con la mejor tecnología disponible.</p>
    <div class="cta-actions">
      <a href="contacto.php" class="btn btn-white btn-lg"><i class="fa-solid fa-rocket"></i> Iniciar Proyecto</a>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
