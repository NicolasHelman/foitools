<?php

declare(strict_types=1);

require_once __DIR__ . '/../class/Conexion.php';

$pdo = Conexion::get();

$totalProductos  = (int)$pdo->query('SELECT COUNT(*) FROM productos')->fetchColumn();
$totalCategorias = (int)$pdo->query('SELECT COUNT(*) FROM categorias')->fetchColumn();
$totalImagenes   = (int)$pdo->query('SELECT COUNT(*) FROM img_producto')->fetchColumn();

$porCategoria = $pdo->query(
    'SELECT c.nombre AS categoria, COUNT(p.id) AS total
     FROM categorias c
     LEFT JOIN productos p ON p.categoria_id = c.id
     GROUP BY c.id, c.nombre
     ORDER BY total DESC, c.nombre ASC'
)->fetchAll();


date_default_timezone_set('America/Argentina/Buenos_Aires');

$usuario = $_SESSION['admin_nombre'] ?? $_SESSION['admin_usuario'] ?? 'Admin';
$hora    = (int)date('H');
$saludo  = $hora < 12 ? 'Buenos días' : ($hora < 19 ? 'Buenas tardes' : 'Buenas noches');
?>
<div class="container">
    <div class="page-inner">

        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            <div>
                <h3 class="fw-bold mb-1"><?= $saludo ?>, <?= htmlspecialchars($usuario) ?> 👋</h3>
                <h6 class="op-7 mb-2">Resumen del panel · <?= date('d/m/Y') ?></h6>
            </div>
            <div class="ms-md-auto py-2 py-md-0">
                <a href="../index.php" target="_blank" class="btn btn-secondary btn-round">
                    <i class="fas fa-external-link-alt me-1"></i> Ver Sitio
                </a>
            </div>
        </div>

        <!-- KPIs -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-4">
                <a href="index.php?a=productos" class="text-decoration-none">
                    <div class="card kpi-card text-white text-center p-3 h-100" style="background:linear-gradient(135deg,#1d7af3,#0d5bd1)">
                        <i class="fas fa-box-open kpi-icon"></i>
                        <h6 class="kpi-label">Productos</h6>
                        <h3 class="kpi-value"><?= $totalProductos ?></h3>
                    </div>
                </a>
            </div>
            <div class="col-6 col-lg-4">
                <a href="index.php?a=categorias" class="text-decoration-none">
                    <div class="card kpi-card text-white text-center p-3 h-100" style="background:linear-gradient(135deg,#31ce36,#1fa823)">
                        <i class="fas fa-tags kpi-icon"></i>
                        <h6 class="kpi-label">Categorías</h6>
                        <h3 class="kpi-value"><?= $totalCategorias ?></h3>
                    </div>
                </a>
            </div>
            <div class="col-6 col-lg-4">
                <div class="card kpi-card text-white text-center p-3 h-100" style="background:linear-gradient(135deg,#f3545d,#d42a34)">
                    <i class="fas fa-images kpi-icon"></i>
                    <h6 class="kpi-label">Imágenes</h6>
                    <h3 class="kpi-value"><?= $totalImagenes ?></h3>
                </div>
            </div>
        </div>

        <!-- Accesos rápidos -->
        <div class="row g-3 mb-4">
            <div class="col-12">
                <h6 class="text-muted mb-2" style="font-size:12px;letter-spacing:.05em;text-transform:uppercase;">Accesos rápidos</h6>
            </div>
            <div class="col-6 col-md-3">
                <a href="index.php?a=productos" class="card text-decoration-none h-100 shortcut-card d-flex flex-column align-items-center justify-content-center p-3 gap-2">
                    <i class="fas fa-box-open fa-2x text-success"></i>
                    <div class="small fw-semibold">Productos</div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="index.php?a=categorias" class="card text-decoration-none h-100 shortcut-card d-flex flex-column align-items-center justify-content-center p-3 gap-2">
                    <i class="fas fa-tags fa-2x text-info"></i>
                    <div class="small fw-semibold">Categorías</div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="index.php?a=configuracion" class="card text-decoration-none h-100 shortcut-card d-flex flex-column align-items-center justify-content-center p-3 gap-2">
                    <i class="fas fa-cog fa-2x text-warning"></i>
                    <div class="small fw-semibold">Configuración</div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="index.php?a=auditoria" class="card text-decoration-none h-100 shortcut-card d-flex flex-column align-items-center justify-content-center p-3 gap-2">
                    <i class="fas fa-history fa-2x text-secondary"></i>
                    <div class="small fw-semibold">Auditoría</div>
                </a>
            </div>
        </div>

        <!-- Gráfico por categoría -->
        <div class="row g-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-chart-bar me-2 text-info"></i>Productos por categoría</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="graficoCategorias" height="55"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
.shortcut-card { transition: transform .15s, box-shadow .15s; cursor: pointer; }
.shortcut-card:hover { transform: translateY(-3px); box-shadow: 0 6px 18px rgba(0,0,0,.1); }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
(function () {
    const labels = <?= json_encode(array_column($porCategoria, 'categoria'), JSON_UNESCAPED_UNICODE) ?>;
    const datos  = <?= json_encode(array_column($porCategoria, 'total')) ?>;
    const colores = ['#1d7af3','#f3545d','#fdaf4b','#31ce36','#a855f7','#06b6d4','#f97316','#84cc16'];

    new Chart(document.getElementById('graficoCategorias'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Productos',
                data: datos,
                backgroundColor: labels.map((_, i) => colores[i % colores.length] + 'cc'),
                borderColor:     labels.map((_, i) => colores[i % colores.length]),
                borderWidth: 2,
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: ctx => ` ${ctx.parsed.y} producto${ctx.parsed.y !== 1 ? 's' : ''}` } }
            },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: 'rgba(0,0,0,.05)' } },
                x: { grid: { display: false } }
            }
        }
    });
})();
</script>
