<?php

declare(strict_types=1);

require_once __DIR__ . '/class/Conexion.php';
require_once __DIR__ . '/class/Config.php';

$cfg = Config::all();
$seoTitle    = $cfg['seo_title']    ?? '';
$seoDesc     = $cfg['seo_desc']     ?? '';
$seoKeywords = $cfg['seo_keywords'] ?? '';
?>
<div class="container">
    <div class="page-inner">

        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            <div>
                <h3 class="fw-bold mb-3">Configuración</h3>
                <h6 class="op-7 mb-2">Ajustes generales del sitio</h6>
            </div>
            <div class="ms-md-auto py-2 py-md-0">
                <button class="btn btn-success btn-round" onclick="guardarConfig()">
                    <i class="fas fa-save me-1"></i> Guardar Cambios
                </button>
            </div>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-4" id="configTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-contacto" data-bs-toggle="tab" data-bs-target="#panel-contacto" type="button">
                    <i class="fas fa-phone me-1"></i> Contacto
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-seo" data-bs-toggle="tab" data-bs-target="#panel-seo" type="button">
                    <i class="fas fa-search me-1"></i> SEO
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-smtp" data-bs-toggle="tab" data-bs-target="#panel-smtp" type="button">
                    <i class="fas fa-envelope me-1"></i> SMTP
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-analytics" data-bs-toggle="tab" data-bs-target="#panel-analytics" type="button">
                    <i class="fas fa-chart-bar me-1"></i> Analítica
                </button>
            </li>
        </ul>

        <div class="tab-content">

            <!-- ── Tab: Contacto ─────────────────────────────────── -->
            <div class="tab-pane fade show active" id="panel-contacto" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-phone me-2 text-success"></i>Datos de Contacto y Empresa</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nombre del Sitio</label>
                                <input type="text" class="form-control" id="cfg_site_name" value="<?= htmlspecialchars($cfg['site_name']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email de contacto</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" id="cfg_email_to" value="<?= htmlspecialchars($cfg['email_to']) ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">WhatsApp <small class="text-muted">(solo números, con código de país)</small></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fab fa-whatsapp"></i></span>
                                    <input type="text" class="form-control" id="cfg_whatsapp" value="<?= htmlspecialchars($cfg['whatsapp']) ?>" placeholder="5493465668465">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Teléfono</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    <input type="text" class="form-control" id="cfg_phone" value="<?= htmlspecialchars($cfg['phone']) ?>" placeholder="3465 66-3235">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Instagram <small class="text-muted">(URL completa)</small></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fab fa-instagram"></i></span>
                                    <input type="url" class="form-control" id="cfg_instagram" value="<?= htmlspecialchars($cfg['instagram'] ?? '') ?>" placeholder="https://instagram.com/foitools">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Facebook <small class="text-muted">(URL completa)</small></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fab fa-facebook"></i></span>
                                    <input type="url" class="form-control" id="cfg_facebook" value="<?= htmlspecialchars($cfg['facebook'] ?? '') ?>" placeholder="https://facebook.com/foitools">
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Ubicación <small class="text-muted">(dirección para mostrar en el sitio)</small></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                    <input type="text" class="form-control" id="cfg_ubicacion" value="<?= htmlspecialchars($cfg['ubicacion'] ?? '') ?>" placeholder="Av. Ejemplo 1234, Ciudad, Provincia">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── Tab: SEO ──────────────────────────────────────── -->
            <div class="tab-pane fade" id="panel-seo" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-search me-2 text-info"></i>SEO / Buscadores</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Título de la página</label>
                                <input type="text" class="form-control" id="cfg_seo_title" value="<?= htmlspecialchars($seoTitle) ?>">
                                <small class="text-muted">Aparece en la pestaña del navegador y en Google. Recomendado: hasta 60 caracteres.</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label">
                                    Meta description
                                    <span class="ms-2 badge" id="descBadge">0 car.</span>
                                </label>
                                <textarea class="form-control" id="cfg_seo_desc" rows="3"><?= htmlspecialchars($seoDesc) ?></textarea>
                                <small class="text-muted">Recomendado: entre 120 y 160 caracteres. Aparece debajo del título en los resultados de Google.</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Palabras clave (meta keywords)</label>
                                <textarea class="form-control" id="cfg_seo_keywords" rows="3"><?= htmlspecialchars($seoKeywords) ?></textarea>
                                <small class="text-muted">Separadas por coma. Ej: correas rotoenfardadoras, discos dentados, implementos agrícolas…</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- ── Tab: SMTP ─────────────────────────────────────── -->
            <div class="tab-pane fade" id="panel-smtp" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-envelope me-2 text-warning"></i>Correo SMTP (formulario de contacto)</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Servidor SMTP (host)</label>
                                <input type="text" class="form-control" id="cfg_smtp_host" value="<?= htmlspecialchars($cfg['smtp_host'] ?? '') ?>" placeholder="smtp.gmail.com">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Puerto</label>
                                <input type="number" class="form-control" id="cfg_smtp_port" value="<?= (int)($cfg['smtp_port'] ?? 587) ?>" placeholder="587">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Seguridad</label>
                                <select class="form-select" id="cfg_smtp_secure">
                                    <option value="tls" <?= ($cfg['smtp_secure'] ?? '') === 'tls' ? 'selected' : '' ?>>TLS (recomendado)</option>
                                    <option value="ssl" <?= ($cfg['smtp_secure'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                                    <option value=""    <?= ($cfg['smtp_secure'] ?? '') === ''    ? 'selected' : '' ?>>Sin seguridad</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Usuario SMTP (tu email)</label>
                                <input type="email" class="form-control" id="cfg_smtp_user" value="<?= htmlspecialchars($cfg['smtp_user'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contraseña / App Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="cfg_smtp_pass" value="<?= htmlspecialchars($cfg['smtp_pass'] ?? '') ?>">
                                    <button class="btn btn-outline-secondary" type="button" onclick="toggleSmtpPass()"><i class="fas fa-eye" id="smtpEyeIcon"></i></button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email remitente (from)</label>
                                <input type="email" class="form-control" id="cfg_smtp_from" value="<?= htmlspecialchars($cfg['smtp_from'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nombre remitente</label>
                                <input type="text" class="form-control" id="cfg_smtp_name" value="<?= htmlspecialchars($cfg['smtp_name'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── Tab: Analítica ────────────────────────────────── -->
            <div class="tab-pane fade" id="panel-analytics" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-chart-bar me-2 text-primary"></i>Analítica y Píxeles</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Google Analytics 4 (ID)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fab fa-google"></i></span>
                                    <input type="text" class="form-control" id="cfg_analytics_id" value="<?= htmlspecialchars($cfg['analytics_id'] ?? '') ?>" placeholder="G-XXXXXXXXXX">
                                </div>
                                <small class="text-muted">Dejar vacío para desactivar.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Meta Pixel ID (Facebook/Instagram)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fab fa-facebook"></i></span>
                                    <input type="text" class="form-control" id="cfg_meta_pixel_id" value="<?= htmlspecialchars($cfg['meta_pixel_id'] ?? '') ?>" placeholder="XXXXXXXXXXXXXXXXXX">
                                </div>
                                <small class="text-muted">Dejar vacío para desactivar.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- /tab-content -->

    </div>
</div>

<script>
// Contador de caracteres para meta description
const descTA    = document.getElementById('cfg_seo_desc');
const descBadge = document.getElementById('descBadge');
function actualizarContador() {
    const n = descTA.value.length;
    descBadge.textContent = n + ' car.';
    descBadge.className = 'ms-2 badge ' + (n < 120 ? 'bg-warning' : (n > 160 ? 'bg-danger' : 'bg-success'));
}
descTA.addEventListener('input', actualizarContador);
actualizarContador();

function toggleSmtpPass() {
    const input = document.getElementById('cfg_smtp_pass');
    const icon  = document.getElementById('smtpEyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
    }
}

function guardarConfig() {
    const btn = document.querySelector('[onclick="guardarConfig()"]');
    btn.disabled = true;
    $.post('api.php', {
        a:              'guardar_config',
        site_name:      $('#cfg_site_name').val(),
        whatsapp:       $('#cfg_whatsapp').val(),
        phone:          $('#cfg_phone').val(),
        email_to:       $('#cfg_email_to').val(),
        instagram:      $('#cfg_instagram').val(),
        facebook:       $('#cfg_facebook').val(),
        ubicacion:      $('#cfg_ubicacion').val(),
        // SEO
        seo_title:      $('#cfg_seo_title').val(),
        seo_desc:       $('#cfg_seo_desc').val(),
        seo_keywords:   $('#cfg_seo_keywords').val(),
        // SMTP
        smtp_host:      $('#cfg_smtp_host').val(),
        smtp_port:      $('#cfg_smtp_port').val(),
        smtp_secure:    $('#cfg_smtp_secure').val(),
        smtp_user:      $('#cfg_smtp_user').val(),
        smtp_pass:      $('#cfg_smtp_pass').val(),
        smtp_from:      $('#cfg_smtp_from').val(),
        smtp_name:      $('#cfg_smtp_name').val(),
        // Analytics
        analytics_id:   $('#cfg_analytics_id').val(),
        meta_pixel_id:  $('#cfg_meta_pixel_id').val(),
    }, r => {
        btn.disabled = false;
        if (r.ok) {
            notificar('Configuración guardada correctamente.', 'success');
        } else {
            notificar(r.msg || 'Error al guardar.', 'danger');
        }
    }, 'json').fail(() => { btn.disabled = false; notificar('Error de conexión.', 'danger'); });
}
</script>
