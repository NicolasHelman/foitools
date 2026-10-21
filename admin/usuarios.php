<?php

declare(strict_types=1);

require_once __DIR__ . '/class/Conexion.php';

$pdo = Conexion::get();
$usuarios = $pdo->query(
    'SELECT id, usuario, nombre, activo, created_at FROM usuarios ORDER BY id ASC'
)->fetchAll();
?>
<div class="container">
    <div class="page-inner">

        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            <div>
                <h3 class="fw-bold mb-3">Usuarios</h3>
                <h6 class="op-7 mb-2"><?= count($usuarios) ?> usuario(s)</h6>
            </div>
            <div class="ms-md-auto py-2 py-md-0">
                <button class="btn btn-success btn-round" onclick="abrirModalNuevoUsuario()">
                    <i class="fas fa-plus me-1"></i> Nuevo Usuario
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div></div>
                <div class="d-flex gap-1">
                    <button onclick="exportarExcelDataTables('usuarios','tablaUsuarios')" class="btn btn-success" title="Exportar Excel">
                        <i class="fas fa-file-excel"></i>
                    </button>
                    <button onclick="exportarPdfDataTables('Usuarios','usuarios','tablaUsuarios')" class="btn btn-danger" title="Exportar PDF">
                        <i class="fas fa-file-pdf"></i>
                    </button>
                    <button onclick="imprimirPdfDataTables('Usuarios','usuarios','tablaUsuarios')" class="btn btn-warning" title="Imprimir">
                        <i class="fas fa-print"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="display nowrap w-100" id="tablaUsuarios">
                        <thead class="bg-gray3">
                            <tr>
                                <th>#</th>
                                <th>Usuario</th>
                                <th>Nombre</th>
                                <th class="text-center">Estado</th>
                                <th>Creado</th>
                                <th style="width:120px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $u): ?>
                            <tr>
                                <td><?= $u['id'] ?></td>
                                <td><strong><?= htmlspecialchars($u['usuario']) ?></strong></td>
                                <td><?= htmlspecialchars($u['nombre']) ?></td>
                                <td class="text-center">
                                    <?php if ($u['activo']): ?>
                                        <span class="badge bg-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($u['created_at']) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning me-1"
                                        onclick="abrirModalEditarUsuario(<?= $u['id'] ?>, '<?= htmlspecialchars($u['usuario'], ENT_QUOTES) ?>', '<?= htmlspecialchars($u['nombre'], ENT_QUOTES) ?>')"
                                        title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm <?= $u['activo'] ? 'btn-secondary' : 'btn-success' ?> me-1"
                                        onclick="toggleUsuario(<?= $u['id'] ?>, <?= $u['activo'] ?>)"
                                        title="<?= $u['activo'] ? 'Desactivar' : 'Activar' ?>">
                                        <i class="fas fa-<?= $u['activo'] ? 'ban' : 'check' ?>"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger"
                                        onclick="eliminarUsuario(<?= $u['id'] ?>, '<?= htmlspecialchars($u['usuario'], ENT_QUOTES) ?>')"
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

<!-- Modal: Nuevo Usuario -->
<div class="modal fade" id="modalNuevoUsuario" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nuevo Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Usuario <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nuevo_usuario" autocomplete="off">
                </div>
                <div class="mb-3">
                    <label class="form-label">Nombre (opcional)</label>
                    <input type="text" class="form-control" id="nuevo_nombre">
                </div>
                <div class="mb-1">
                    <label class="form-label">Contraseña <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="nuevo_pass" autocomplete="new-password">
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePass('nuevo_pass')"><i class="fas fa-eye"></i></button>
                    </div>
                    <small class="text-muted">Mínimo 6 caracteres.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="btnCrearUsuario" onclick="crearUsuario()">
                    <i class="fas fa-plus me-1"></i> Crear
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Editar Usuario -->
<div class="modal fade" id="modalEditarUsuario" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_usuario_id">
                <div class="mb-3">
                    <label class="form-label">Usuario <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="edit_usuario" autocomplete="off">
                </div>
                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="edit_nombre">
                </div>
                <div class="mb-1">
                    <label class="form-label">Nueva contraseña <small class="text-muted">(dejar vacío para no cambiar)</small></label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="edit_pass" autocomplete="new-password">
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePass('edit_pass')"><i class="fas fa-eye"></i></button>
                    </div>
                    <small class="text-muted">Mínimo 6 caracteres si la cambiás.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" id="btnEditarUsuario" onclick="guardarEdicionUsuario()">
                    <i class="fas fa-save me-1"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    $('#tablaUsuarios').DataTable({
        language: { url: 'assets/datatables/es-ES.json' },
        pageLength: 25,
        order: [[0, 'asc']],
        fixedHeader: true,
        scrollX: true,
        dom: 'Blfrtip',
        buttons: [{ extend: 'colvis', text: '<i class="fas fa-bars me-1"></i> Filtrar Columnas', className: 'btn', titleAttr: 'Columns' }],
        columnDefs: [{ orderable: false, targets: [5] }, { searchable: false, targets: [5] }],
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
    });
});

function togglePass(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}

function abrirModalNuevoUsuario() {
    ['nuevo_usuario','nuevo_nombre','nuevo_pass'].forEach(id => document.getElementById(id).value = '');
    new bootstrap.Modal(document.getElementById('modalNuevoUsuario')).show();
    setTimeout(() => document.getElementById('nuevo_usuario').focus(), 300);
}

function crearUsuario() {
    const usuario = document.getElementById('nuevo_usuario').value.trim();
    const nombre  = document.getElementById('nuevo_nombre').value.trim();
    const pass    = document.getElementById('nuevo_pass').value;
    if (!usuario || !pass) { notificar('Usuario y contraseña son obligatorios.', 'warning'); return; }
    const btn = document.getElementById('btnCrearUsuario');
    btn.disabled = true;
    $.post('api.php', { a: 'crear_usuario', usuario, nombre, pass }, r => {
        btn.disabled = false;
        if (r.ok) {
            notificar('Usuario creado.', 'success');
            bootstrap.Modal.getInstance(document.getElementById('modalNuevoUsuario')).hide();
            setTimeout(() => location.reload(), 800);
        } else { notificar(r.msg || 'Error.', 'danger'); }
    }, 'json').fail(() => { btn.disabled = false; notificar('Error de conexión.', 'danger'); });
}

function abrirModalEditarUsuario(id, usuario, nombre) {
    document.getElementById('edit_usuario_id').value = id;
    document.getElementById('edit_usuario').value    = usuario;
    document.getElementById('edit_nombre').value     = nombre;
    document.getElementById('edit_pass').value       = '';
    new bootstrap.Modal(document.getElementById('modalEditarUsuario')).show();
}

function guardarEdicionUsuario() {
    const id      = document.getElementById('edit_usuario_id').value;
    const usuario = document.getElementById('edit_usuario').value.trim();
    const nombre  = document.getElementById('edit_nombre').value.trim();
    const pass    = document.getElementById('edit_pass').value;
    if (!usuario) { notificar('El usuario es obligatorio.', 'warning'); return; }
    const btn = document.getElementById('btnEditarUsuario');
    btn.disabled = true;
    $.post('api.php', { a: 'editar_usuario', id, usuario, nombre, pass }, r => {
        btn.disabled = false;
        if (r.ok) {
            notificar('Usuario actualizado.', 'success');
            bootstrap.Modal.getInstance(document.getElementById('modalEditarUsuario')).hide();
            setTimeout(() => location.reload(), 800);
        } else { notificar(r.msg || 'Error.', 'danger'); }
    }, 'json').fail(() => { btn.disabled = false; notificar('Error de conexión.', 'danger'); });
}

function toggleUsuario(id, activo) {
    const msg = activo ? '¿Desactivar este usuario?' : '¿Activar este usuario?';
    swal({ title: msg, icon: 'warning', buttons: ['Cancelar', 'Sí'], dangerMode: !!activo })
    .then(ok => {
        if (!ok) return;
        $.post('api.php', { a: 'toggle_usuario', id }, r => {
            if (r.ok) { notificar('Estado actualizado.', 'success'); setTimeout(() => location.reload(), 800); }
            else notificar(r.msg || 'Error.', 'danger');
        }, 'json');
    });
}

function eliminarUsuario(id, usuario) {
    swal({ title: `¿Eliminar usuario "${usuario}"?`, icon: 'warning', buttons: ['Cancelar', 'Sí, eliminar'], dangerMode: true })
    .then(ok => {
        if (!ok) return;
        $.post('api.php', { a: 'eliminar_usuario', id }, r => {
            if (r.ok) { notificar('Usuario eliminado.', 'success'); setTimeout(() => location.reload(), 800); }
            else notificar(r.msg || 'Error.', 'danger');
        }, 'json');
    });
}
</script>
