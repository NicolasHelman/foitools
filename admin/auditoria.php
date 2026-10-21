<?php
declare(strict_types=1);
require_once __DIR__ . '/class/Conexion.php';

$pdo = Conexion::get();
$registros = $pdo->query(
    'SELECT id, accion, detalle, ip, created_at FROM auditoria ORDER BY created_at DESC'
)->fetchAll();
?>
<div class="container">
    <div class="page-inner">

        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            <div>
                <h3 class="fw-bold mb-3">Auditoría</h3>
                <h6 class="op-7 mb-2"><?= count($registros) ?> registro(s)</h6>
            </div>
        </div>

        <!-- Card de filtros colapsable -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center toggle-filtros"
                style="cursor:pointer;" data-bs-toggle="collapse" data-bs-target="#colapsoFiltrosAuditoria"
                aria-expanded="false" aria-controls="colapsoFiltrosAuditoria">
                <h5 class="mb-0">Filtros</h5>
                <i class="fas fa-chevron-down icono-toggle-filtros"></i>
            </div>
            <div class="collapse" id="colapsoFiltrosAuditoria">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3 col-sm-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="filtro_aud_accion" placeholder=" ">
                                <label for="filtro_aud_accion">Acción</label>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="filtro_aud_detalle" placeholder=" ">
                                <label for="filtro_aud_detalle">Detalle</label>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="filtro_aud_desde">
                                <label for="filtro_aud_desde">Desde</label>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="filtro_aud_hasta">
                                <label for="filtro_aud_hasta">Hasta</label>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center align-items-center mt-4">
                        <button class="btn btn-primary me-1" onclick="buscarAuditoria()" id="btnBuscarAud">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                        <button class="btn btn-danger" onclick="limpiarFiltrosAud()">
                            <i class="fas fa-sync-alt"></i> Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card de la tabla -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div></div>
                <div class="d-flex gap-1">
                    <button onclick="exportarExcelDataTables('auditoria','tablaAuditoria')" class="btn btn-success" title="Exportar Excel">
                        <i class="fas fa-file-excel"></i>
                    </button>
                    <button onclick="exportarPdfDataTables('Auditoría','auditoria','tablaAuditoria')" class="btn btn-danger" title="Exportar PDF">
                        <i class="fas fa-file-pdf"></i>
                    </button>
                    <button onclick="imprimirPdfDataTables('Auditoría','auditoria','tablaAuditoria')" class="btn btn-warning" title="Imprimir">
                        <i class="fas fa-print"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="display nowrap w-100" id="tablaAuditoria">
                        <thead class="bg-gray3">
                            <tr>
                                <th>#</th>
                                <th>Acción</th>
                                <th>Detalle</th>
                                <th>IP</th>
                                <th>Fecha y hora</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($registros as $r): ?>
                            <tr data-accion="<?= htmlspecialchars(mb_strtolower($r['accion'])) ?>"
                                data-detalle="<?= htmlspecialchars(mb_strtolower($r['detalle'] ?? '')) ?>"
                                data-fecha="<?= substr($r['created_at'], 0, 10) ?>">
                                <td><?= $r['id'] ?></td>
                                <td><?= htmlspecialchars($r['accion']) ?></td>
                                <td class="text-muted small"><?= htmlspecialchars($r['detalle'] ?? '') ?></td>
                                <td class="text-muted small"><?= htmlspecialchars($r['ip'] ?? '') ?></td>
                                <td><?= htmlspecialchars($r['created_at']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
function buscarAuditoria() {
    $('#btnBuscarAud').html('<i class="fas fa-spinner fa-spin me-1"></i> Espere...');
    $('#tablaAuditoria').DataTable().draw();
    $('#btnBuscarAud').html('<i class="fas fa-search"></i> Buscar');
}

function limpiarFiltrosAud() {
    $('#filtro_aud_accion, #filtro_aud_detalle, #filtro_aud_desde, #filtro_aud_hasta').val('');
    $('#tablaAuditoria').DataTable().draw();
}


$(document).ready(function () {

    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        if (settings.nTable.id !== 'tablaAuditoria') return true;

        const accion  = $('#filtro_aud_accion').val().trim().toLowerCase();
        const detalle = $('#filtro_aud_detalle').val().trim().toLowerCase();
        const desde   = $('#filtro_aud_desde').val();
        const hasta   = $('#filtro_aud_hasta').val();

        const nTr = settings.aoData[dataIndex].nTr;
        if (!nTr) return true;
        const row = $(nTr);

        if (accion  && !(row.data('accion')  || '').includes(accion))  return false;
        if (detalle && !(row.data('detalle') || '').includes(detalle)) return false;
        if (desde   && row.data('fecha') < desde) return false;
        if (hasta   && row.data('fecha') > hasta) return false;

        return true;
    });

    $('#tablaAuditoria').DataTable({
        language: { url: 'assets/datatables/es-ES.json' },
        pageLength: 25,
        order: [[0, 'desc']],
        fixedHeader: true,
        scrollX: true,
        dom: 'Blfrtip',
        buttons: [{ extend: 'colvis', text: '<i class="fas fa-bars me-1"></i> Filtrar Columnas', className: 'btn', titleAttr: 'Columns' }],
        columnDefs: [{ searchable: false, targets: [0, 3] }],
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
    });
});
</script>
