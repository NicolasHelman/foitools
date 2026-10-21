<?php

declare(strict_types=1);

require_once __DIR__ . '/class/Conexion.php';
require_once __DIR__ . '/class/CatalogoSync.php';

$pdo = Conexion::get();

$categorias = $pdo->query(
    'SELECT c.id, c.nombre,
            COUNT(p.id) AS total_productos
     FROM categorias c
     LEFT JOIN productos p ON p.categoria_id = c.id
     GROUP BY c.id, c.nombre
     ORDER BY c.nombre ASC'
)->fetchAll();
?>
<div class="container">
    <div class="page-inner">

        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            <div>
                <h3 class="fw-bold mb-3">Categorías</h3>
                <h6 class="op-7 mb-2"><?= count($categorias) ?> categoría(s)</h6>
            </div>
            <div class="ms-md-auto py-2 py-md-0">
                <button class="btn btn-success btn-round" onclick="abrirModalNuevaCategoria()">
                    <i class="fas fa-plus me-1"></i> Nueva Categoría
                </button>
            </div>
        </div>

        <!-- Card de filtros colapsable -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center toggle-filtros"
                style="cursor:pointer;" data-bs-toggle="collapse" data-bs-target="#colapsoFiltrosCategorias"
                aria-expanded="false" aria-controls="colapsoFiltrosCategorias">
                <h5 class="mb-0">Filtros</h5>
                <i class="fas fa-chevron-down icono-toggle-filtros"></i>
            </div>
            <div class="collapse" id="colapsoFiltrosCategorias">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6 col-sm-12">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="filtro_cat_nombre" placeholder=" ">
                                <label for="filtro_cat_nombre">Nombre</label>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="form-floating">
                                <select class="form-control form-select" id="filtro_cat_productos">
                                    <option value="">Todas</option>
                                    <option value="con">Con productos</option>
                                    <option value="sin">Sin productos</option>
                                </select>
                                <label for="filtro_cat_productos">Productos asignados</label>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center align-items-center mt-4">
                        <button class="btn btn-primary me-1" onclick="buscarCategorias()" id="btnBuscarCat">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                        <button class="btn btn-danger" onclick="limpiarFiltrosCat()">
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
                    <button onclick="exportarExcelDataTables('listado_categorias','tablaCategorias')" class="btn btn-success" title="Exportar Excel">
                        <i class="fas fa-file-excel"></i>
                    </button>
                    <button onclick="exportarPdfDataTables('Categorías','listado_categorias','tablaCategorias')" class="btn btn-danger" title="Exportar PDF">
                        <i class="fas fa-file-pdf"></i>
                    </button>
                    <button onclick="imprimirPdfDataTables('Categorías','listado_categorias','tablaCategorias')" class="btn btn-warning" title="Imprimir">
                        <i class="fas fa-print"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="display nowrap w-100" id="tablaCategorias">
                        <thead class="bg-gray3">
                            <tr>
                                <th>Nombre</th>
                                <th class="text-center" style="width:120px;">Productos</th>
                                <th style="width:120px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categorias as $cat): ?>
                            <tr data-nombre="<?= htmlspecialchars(mb_strtolower($cat['nombre'])) ?>"
                                data-total="<?= (int)$cat['total_productos'] ?>">
                                <td><?= htmlspecialchars($cat['nombre']) ?></td>
                                <td class="text-center"><?= (int)$cat['total_productos'] ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning me-1"
                                        onclick="abrirModalRenombrar(<?= $cat['id'] ?>, '<?= htmlspecialchars($cat['nombre'], ENT_QUOTES) ?>')"
                                        title="Renombrar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger"
                                        <?php if ((int)$cat['total_productos'] > 0): ?>
                                            disabled title="Reasigná los productos antes de eliminar"
                                        <?php else: ?>
                                            onclick="eliminarCategoria(<?= $cat['id'] ?>, '<?= htmlspecialchars($cat['nombre'], ENT_QUOTES) ?>')"
                                        <?php endif; ?>
                                        title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal: Nueva Categoría -->
<div class="modal fade" id="modalNuevaCategoria" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nueva Categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nueva_cat_nombre">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="btnCrearCat" onclick="crearCategoria()">
                    <i class="fas fa-plus me-1"></i> Crear
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Renombrar Categoría -->
<div class="modal fade" id="modalRenombrar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Renombrar Categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="rename_cat_id">
                <div class="mb-3">
                    <label class="form-label">Nombre actual</label>
                    <input type="text" class="form-control" id="rename_cat_actual" disabled>
                </div>
                <div class="mb-1">
                    <label class="form-label">Nuevo nombre <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="rename_cat_nuevo">
                </div>
                <small class="text-muted">Se actualizará en todos los productos de esta categoría.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" id="btnRenombrar" onclick="confirmarRenombrar()">
                    <i class="fas fa-save me-1"></i> Renombrar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function buscarCategorias() {
    $('#btnBuscarCat').html('<i class="fas fa-spinner fa-spin me-1"></i> Espere...');
    $('#tablaCategorias').DataTable().draw();
    $('#btnBuscarCat').html('<i class="fas fa-search"></i> Buscar');
}

function limpiarFiltrosCat() {
    $('#filtro_cat_nombre').val('');
    $('#filtro_cat_productos').val('');
    $('#tablaCategorias').DataTable().draw();
}

$(document).ready(function () {

    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        if (settings.nTable.id !== 'tablaCategorias') return true;

        const nombre    = $('#filtro_cat_nombre').val().trim().toLowerCase();
        const productos = $('#filtro_cat_productos').val();

        const nTr = settings.aoData[dataIndex].nTr;
        if (!nTr) return true;
        const row = $(nTr);

        if (nombre && !(row.data('nombre') || '').includes(nombre)) return false;
        if (productos === 'con' && row.data('total') < 1) return false;
        if (productos === 'sin' && row.data('total') > 0) return false;

        return true;
    });

    $('#tablaCategorias').DataTable({
        language: { url: 'assets/datatables/es-ES.json' },
        pageLength: 25,
        order: [],
        fixedHeader: true,
        scrollX: true,
        dom: 'Blfrtip',
        buttons: [
            {
                extend: 'colvis',
                text: '<i class="fas fa-bars me-1"></i> Filtrar Columnas',
                className: 'btn',
                titleAttr: 'Columns'
            }
        ],
        columnDefs: [
            { orderable: false, targets: [2] },
            { searchable: false, targets: [2] }
        ],
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
    });
});

function abrirModalNuevaCategoria() {
    document.getElementById('nueva_cat_nombre').value = '';
    new bootstrap.Modal(document.getElementById('modalNuevaCategoria')).show();
    setTimeout(() => document.getElementById('nueva_cat_nombre').focus(), 300);
}

function crearCategoria() {
    const nombre = document.getElementById('nueva_cat_nombre').value.trim();
    if (!nombre) { notificar('El nombre no puede estar vacío.', 'warning'); return; }
    const btn = document.getElementById('btnCrearCat');
    btn.disabled = true;
    $.post('api.php', { a: 'crear_categoria', nombre }, r => {
        btn.disabled = false;
        if (r.ok) {
            notificar('Categoría creada.', 'success');
            bootstrap.Modal.getInstance(document.getElementById('modalNuevaCategoria')).hide();
            setTimeout(() => location.reload(), 800);
        } else { notificar(r.msg || 'Error.', 'danger'); }
    }, 'json').fail(() => { btn.disabled = false; notificar('Error de conexión.', 'danger'); });
}

function abrirModalRenombrar(id, nombre) {
    document.getElementById('rename_cat_id').value     = id;
    document.getElementById('rename_cat_actual').value = nombre;
    document.getElementById('rename_cat_nuevo').value  = nombre;
    new bootstrap.Modal(document.getElementById('modalRenombrar')).show();
    setTimeout(() => { document.getElementById('rename_cat_nuevo').select(); }, 300);
}

function confirmarRenombrar() {
    const id     = document.getElementById('rename_cat_id').value;
    const nombre = document.getElementById('rename_cat_nuevo').value.trim();
    if (!nombre) { notificar('El nombre no puede estar vacío.', 'warning'); return; }
    const btn = document.getElementById('btnRenombrar');
    btn.disabled = true;
    $.post('api.php', { a: 'renombrar_categoria', id, nombre }, r => {
        btn.disabled = false;
        if (r.ok) {
            notificar('Categoría renombrada.', 'success');
            bootstrap.Modal.getInstance(document.getElementById('modalRenombrar')).hide();
            setTimeout(() => location.reload(), 800);
        } else { notificar(r.msg || 'Error.', 'danger'); }
    }, 'json').fail(() => { btn.disabled = false; notificar('Error de conexión.', 'danger'); });
}

function eliminarCategoria(id, nombre) {
    swal({ title: `¿Eliminar "${nombre}"?`, icon: 'warning', buttons: ['Cancelar', 'Sí, eliminar'], dangerMode: true })
    .then(ok => {
        if (!ok) return;
        $.post('api.php', { a: 'eliminar_categoria', id }, r => {
            if (r.ok) { notificar('Categoría eliminada.', 'success'); setTimeout(() => location.reload(), 800); }
            else notificar(r.msg || 'Error.', 'danger');
        }, 'json');
    });
}

document.getElementById('nueva_cat_nombre').addEventListener('keypress', e => { if (e.key === 'Enter') crearCategoria(); });
document.getElementById('rename_cat_nuevo').addEventListener('keypress', e => { if (e.key === 'Enter') confirmarRenombrar(); });
</script>
