<?php

declare(strict_types=1);

require_once __DIR__ . '/class/Conexion.php';
require_once __DIR__ . '/class/CatalogoSync.php';

$pdo = Conexion::get();

$productos = $pdo->query(
    'SELECT p.id, p.slug, p.nombre, p.icono, p.descripcion, p.destacado,
            c.id AS categoria_id, c.nombre AS categoria,
            (SELECT COUNT(*) FROM img_producto WHERE producto_id = p.id) AS total_imagenes,
            (SELECT url FROM img_producto WHERE producto_id = p.id ORDER BY orden ASC, id ASC LIMIT 1) AS imagen_portada
     FROM productos p
     JOIN categorias c ON c.id = p.categoria_id
     ORDER BY p.orden ASC, p.id ASC'
)->fetchAll();

$categorias = $pdo->query('SELECT id, nombre FROM categorias ORDER BY nombre ASC')->fetchAll();
?>
<div class="container">
    <div class="page-inner">

        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            <div>
                <h3 class="fw-bold mb-3">Productos</h3>
                <h6 class="op-7 mb-2"><?= count($productos) ?> en total</h6>
            </div>
            <div class="ms-md-auto py-2 py-md-0">
                <button class="btn btn-success btn-round" onclick="abrirModalProducto()">
                    <i class="fas fa-plus me-1"></i> Nuevo Producto
                </button>
            </div>
        </div>

        <!-- Card de filtros colapsable -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center toggle-filtros"
                style="cursor:pointer;" data-bs-toggle="collapse" data-bs-target="#colapsoFiltrosProductos"
                aria-expanded="false" aria-controls="colapsoFiltrosProductos">
                <h5 class="mb-0">Filtros</h5>
                <i class="fas fa-chevron-down icono-toggle-filtros"></i>
            </div>
            <div class="collapse" id="colapsoFiltrosProductos">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4 col-sm-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="filtro_nombre" placeholder=" ">
                                <label for="filtro_nombre">Nombre</label>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="form-floating">
                                <select class="form-control form-select" id="filtro_categoria">
                                    <option value="">Todas</option>
                                    <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= htmlspecialchars($cat['nombre']) ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="filtro_categoria">Categoría</label>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="form-floating">
                                <select class="form-control form-select" id="filtro_destacado">
                                    <option value="">Todos</option>
                                    <option value="1">Sí</option>
                                    <option value="0">No</option>
                                </select>
                                <label for="filtro_destacado">Destacado</label>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center align-items-center mt-4">
                        <button class="btn btn-primary me-1" onclick="buscarProductos()" id="btnBuscar">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                        <button class="btn btn-danger" onclick="limpiarFiltros()" id="btnLimpiar">
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
                    <button onclick="exportarExcelDataTables('listado_productos','tablaProductos')" class="btn btn-success" title="Exportar Excel">
                        <i class="fas fa-file-excel"></i>
                    </button>
                    <button onclick="exportarPdfDataTables('Productos','listado_productos','tablaProductos')" class="btn btn-danger" title="Exportar PDF">
                        <i class="fas fa-file-pdf"></i>
                    </button>
                    <button onclick="imprimirPdfDataTables('Productos','listado_productos','tablaProductos')" class="btn btn-warning" title="Imprimir">
                        <i class="fas fa-print"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="display nowrap w-100" id="tablaProductos">
                        <thead class="bg-gray3">
                            <tr>
                                <th style="width:50px"></th>
                                <th>Nombre</th>
                                <th>Categoría</th>
                                <th>Descripción</th>
                                <th style="width:70px" class="text-center">Imágenes</th>
                                <th style="width:140px">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productos as $p): ?>
                            <tr data-slug="<?= htmlspecialchars($p['slug']) ?>"
                                data-nombre="<?= htmlspecialchars(mb_strtolower($p['nombre'])) ?>"
                                data-categoria="<?= htmlspecialchars($p['categoria']) ?>"
                                data-destacado="<?= $p['destacado'] ? '1' : '0' ?>">
                                <td class="text-center">
                                    <?php if (!empty($p['imagen_portada'])): ?>
                                    <img src="../<?= htmlspecialchars($p['imagen_portada']) ?>" style="width:40px;height:40px;object-fit:cover;border-radius:4px;" alt="">
                                    <?php else: ?>
                                    <span class="text-muted"><i class="fas fa-image"></i></span>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?= htmlspecialchars($p['nombre']) ?></strong></td>
                                <td><?= htmlspecialchars($p['categoria']) ?></td>
                                <td class="text-muted small"><?= htmlspecialchars(mb_substr($p['descripcion'] ?? '', 0, 80)) ?><?= mb_strlen($p['descripcion'] ?? '') > 80 ? '…' : '' ?></td>
                                <td class="text-center"><?= $p['total_imagenes'] ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning me-1" onclick="abrirModalProducto('<?= htmlspecialchars($p['slug'], ENT_QUOTES) ?>')" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-info me-1" onclick="abrirModalImagenes('<?= htmlspecialchars($p['slug'], ENT_QUOTES) ?>','<?= htmlspecialchars($p['nombre'], ENT_QUOTES) ?>')" title="Imágenes">
                                        <i class="fas fa-images"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="eliminarProducto('<?= htmlspecialchars($p['slug'], ENT_QUOTES) ?>','<?= htmlspecialchars($p['nombre'], ENT_QUOTES) ?>')" title="Eliminar">
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

<!-- Modal: Crear / Editar Producto -->
<div class="modal fade" id="modalProducto" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalProductoTitulo">Nuevo Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="prod_slug_original">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="prod_nombre">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Categoría <span class="text-danger">*</span></label>
                        <select class="form-select form-control" id="prod_categoria_id">
                            <option value="">— Seleccioná —</option>
                            <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" id="prod_descripcion" rows="3" placeholder="Breve descripción del producto..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="btnGuardarProducto" onclick="guardarProducto()">
                    <i class="fas fa-save me-1"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Gestión de Imágenes -->
<div class="modal fade" id="modalImagenes" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Imágenes — <span id="modalImagenesNombre"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="img_product_slug">

                <div class="mb-4 p-3 border rounded bg-light">
                    <h6 class="mb-2"><i class="fas fa-upload me-1"></i>Subir imágenes</h6>
                    <div class="d-flex gap-2 align-items-center flex-wrap">
                        <input type="file" id="inputImagen" class="form-control" style="max-width:320px;" accept="image/*" multiple>
                        <button class="btn btn-success" onclick="subirImagenes()">
                            <i class="fas fa-cloud-upload-alt me-1"></i> Subir
                        </button>
                    </div>
                    <small class="text-muted d-block mt-1">Formatos: jpg, png, webp, gif. Selección múltiple permitida.</small>
                </div>

                <div id="galeriaImagenes" class="row g-3"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
const CATALOG_DATA = <?= json_encode(array_column($productos, null, 'slug'), JSON_UNESCAPED_UNICODE) ?>;

function buscarProductos() {
    $('#btnBuscar').html('<i class="fas fa-spinner fa-spin me-1"></i> Espere...');
    $('#tablaProductos').DataTable().draw();
    $('#btnBuscar').html('<i class="fas fa-search"></i> Buscar');
}

function limpiarFiltros() {
    $('#filtro_nombre').val('');
    $('#filtro_categoria').val('');
    $('#filtro_destacado').val('');
    $('#tablaProductos').DataTable().draw();
}

$(document).ready(function () {

    // Filtro personalizado: usa atributos data-* del <tr>
    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        if (settings.nTable.id !== 'tablaProductos') return true;

        const nombre    = $('#filtro_nombre').val().trim().toLowerCase();
        const categoria = $('#filtro_categoria').val();
        const destacado = $('#filtro_destacado').val();

        const nTr = settings.aoData[dataIndex].nTr;
        if (!nTr) return true;
        const row = $(nTr);

        if (nombre && !(row.data('nombre') || '').includes(nombre)) return false;
        if (categoria && row.data('categoria') !== categoria) return false;
        if (destacado !== '' && String(row.data('destacado')) !== destacado) return false;

        return true;
    });

    $('#tablaProductos').DataTable({
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
            { orderable: false, targets: [0, 5] },
            { searchable: false, targets: [0, 4, 5] }
        ],
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
    });
});

function abrirModalProducto(slug = '') {
    const modal = new bootstrap.Modal(document.getElementById('modalProducto'));
    if (slug && CATALOG_DATA[slug]) {
        const p = CATALOG_DATA[slug];
        document.getElementById('modalProductoTitulo').textContent = 'Editar Producto';
        document.getElementById('prod_slug_original').value   = slug;
        document.getElementById('prod_nombre').value          = p.nombre ?? '';
        document.getElementById('prod_categoria_id').value    = p.categoria_id ?? '';
        document.getElementById('prod_descripcion').value     = p.descripcion ?? '';
    } else {
        document.getElementById('modalProductoTitulo').textContent = 'Nuevo Producto';
        document.getElementById('prod_slug_original').value   = '';
        document.getElementById('prod_nombre').value          = '';
        document.getElementById('prod_categoria_id').value    = '';
        document.getElementById('prod_descripcion').value     = '';
    }
    modal.show();
}

function guardarProducto() {
    const nombre      = document.getElementById('prod_nombre').value.trim();
    const categoriaId = document.getElementById('prod_categoria_id').value;
    if (!nombre || !categoriaId) { notificar('Nombre y categoría son obligatorios.', 'warning'); return; }

    const btn = document.getElementById('btnGuardarProducto');
    btn.disabled = true;

    $.post('api.php', {
        a:            'guardar_producto',
        id_original:  document.getElementById('prod_slug_original').value,
        nombre,
        categoria_id: categoriaId,
        descripcion:  document.getElementById('prod_descripcion').value.trim(),
    }, r => {
        btn.disabled = false;
        if (r.ok) {
            notificar(r.esNuevo ? 'Producto creado.' : 'Producto actualizado.', 'success');
            bootstrap.Modal.getInstance(document.getElementById('modalProducto')).hide();
            setTimeout(() => location.reload(), 800);
        } else {
            notificar(r.msg || 'Error al guardar.', 'danger');
        }
    }, 'json').fail(() => { btn.disabled = false; notificar('Error de conexión.', 'danger'); });
}

function eliminarProducto(slug, nombre) {
    swal({ title: `¿Eliminar "${nombre}"?`, text: 'Esta acción no se puede deshacer.', icon: 'warning', buttons: ['Cancelar', 'Sí, eliminar'], dangerMode: true })
    .then(ok => {
        if (!ok) return;
        $.post('api.php', { a: 'eliminar_producto', slug }, r => {
            if (r.ok) {
                notificar('Producto eliminado.', 'success');
                document.querySelector(`#tablaProductos tr[data-slug="${slug}"]`)?.remove();
            } else { notificar(r.msg || 'Error.', 'danger'); }
        }, 'json');
    });
}

function abrirModalImagenes(slug, nombre) {
    document.getElementById('img_product_slug').value = slug;
    document.getElementById('modalImagenesNombre').textContent = nombre;
    document.getElementById('inputImagen').value = '';
    cargarGaleria(slug);
    new bootstrap.Modal(document.getElementById('modalImagenes')).show();
}

function cargarGaleria(slug) {
    const galeria = document.getElementById('galeriaImagenes');
    galeria.innerHTML = '<div class="col-12 text-center text-muted py-3"><i class="fas fa-spinner fa-spin me-2"></i>Cargando...</div>';
    $.post('api.php', { a: 'get_imagenes', product_slug: slug }, r => {
        if (!r.ok) { galeria.innerHTML = '<div class="col-12 text-muted">Error al cargar.</div>'; return; }
        if (!r.imagenes.length) {
            galeria.innerHTML = '<div class="col-12 text-muted text-center py-3"><i class="fas fa-image fa-2x mb-2 d-block"></i>Sin imágenes todavía.</div>';
            return;
        }
        const totalPortadas = r.total_portadas ?? r.imagenes.filter(i => i.es_portada).length;
        galeria.innerHTML = r.imagenes.map(img => {
            const esPortada = !!img.es_portada;
            const maxAlcanzado = !esPortada && totalPortadas >= 3;
            return `<div class="col-6 col-md-3 col-lg-2" id="img-card-${img.id}">
                <div class="card h-100 shadow-sm${esPortada ? ' border-warning border-2' : ''}">
                    <img src="../${img.url}" class="card-img-top" style="height:130px;object-fit:cover;" alt="">
                    <div class="card-footer p-1 d-flex flex-column gap-1">
                        ${maxAlcanzado
                            ? `<button class="btn btn-sm btn-outline-secondary w-100" disabled title="Máximo 3 portadas"><i class="fas fa-star me-1"></i>Portada</button>`
                            : `<button class="btn btn-sm w-100 ${esPortada ? 'btn-warning' : 'btn-outline-warning'}" onclick="togglePortada(${img.id})">
                                <i class="fas fa-star me-1"></i>${esPortada ? 'En portada' : 'Portada'}
                               </button>`
                        }
                        <button class="btn btn-sm btn-danger w-100" onclick="eliminarImagen(${img.id},'img-card-${img.id}')">
                            <i class="fas fa-trash me-1"></i>Eliminar
                        </button>
                    </div>
                </div>
            </div>`;
        }).join('');
    }, 'json');
}

function subirImagenes() {
    const slug  = document.getElementById('img_product_slug').value;
    const files = document.getElementById('inputImagen').files;
    if (!files.length) { notificar('Seleccioná al menos una imagen.', 'warning'); return; }

    let pendientes = files.length, exitosos = 0;

    Array.from(files).forEach(file => {
        const fd = new FormData();
        fd.append('a', 'subir_imagen');
        fd.append('product_slug', slug);
        fd.append('imagen', file);
        $.ajax({ url: 'api.php', type: 'POST', data: fd, processData: false, contentType: false, dataType: 'json',
            success: r => { if (r.ok) exitosos++; else notificar(r.msg || 'Error en ' + file.name, 'danger'); },
            error:   () => notificar('Error al subir ' + file.name, 'danger'),
            complete: () => { if (--pendientes === 0) { if (exitosos) notificar(`${exitosos} imagen(es) subida(s).`, 'success'); document.getElementById('inputImagen').value = ''; cargarGaleria(slug); } }
        });
    });
}

function togglePortada(imgId) {
    $.post('api.php', { a: 'toggle_portada', img_id: imgId }, r => {
        if (r.ok) {
            const txt = r.es_portada ? 'Marcada como portada.' : 'Quitada de portada.';
            notificar(txt, 'success');
            cargarGaleria(document.getElementById('img_product_slug').value);
        } else notificar(r.msg || 'Error.', 'danger');
    }, 'json');
}

function eliminarImagen(imgId, cardId) {
    swal({ title: '¿Eliminar imagen?', text: 'Se borrará el archivo del servidor.', icon: 'warning', buttons: ['Cancelar', 'Sí, eliminar'], dangerMode: true })
    .then(ok => {
        if (!ok) return;
        $.post('api.php', { a: 'eliminar_imagen', img_id: imgId }, r => {
            if (r.ok) { document.getElementById(cardId)?.remove(); notificar('Imagen eliminada.', 'success'); }
            else notificar(r.msg || 'Error.', 'danger');
        }, 'json');
    });
}
</script>
