<?php

declare(strict_types=1);

session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/class/Conexion.php';
require_once __DIR__ . '/class/Config.php';
require_once __DIR__ . '/class/CatalogoSync.php';

$accion = $_POST['a'] ?? $_GET['a'] ?? '';

// ─── Respuesta helper ─────────────────────────────────────────────────────────
function ok(mixed ...$extra): never
{
    echo json_encode(['ok' => true, ...$extra]);
    exit;
}

function error(string $msg, int $status = 200): never
{
    http_response_code($status);
    echo json_encode(['ok' => false, 'msg' => $msg]);
    exit;
}

function auditar(string $accion, string $detalle = ''): void
{
    try {
        $pdo = Conexion::get();
        $ip  = $_SERVER['REMOTE_ADDR'] ?? null;
        $pdo->prepare('INSERT INTO auditoria (accion, detalle, ip) VALUES (?, ?, ?)')
            ->execute([$accion, $detalle ?: null, $ip]);
    } catch (Throwable) {}
}

function slugify(string $s): string
{
    $s = mb_strtolower($s, 'UTF-8');
    $s = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s) ?: $s;
    $s = preg_replace('/[^a-z0-9]+/', '-', $s) ?? $s;
    return trim($s, '-');
}

// ─── Login ────────────────────────────────────────────────────────────────────
if ($accion === 'login') {
    $usuario = trim($_POST['usuario'] ?? '');
    $pass    = $_POST['pass'] ?? '';

    $pdo  = Conexion::get();
    $stmt = $pdo->prepare('SELECT id, pass_hash, nombre FROM usuarios WHERE usuario = ? AND activo = 1');
    $stmt->execute([$usuario]);
    $u = $stmt->fetch();

    if ($u && password_verify($pass, $u['pass_hash'])) {
        $_SESSION['admin_ok']      = true;
        $_SESSION['admin_usuario'] = $usuario;
        $_SESSION['admin_nombre']  = $u['nombre'];
        $_SESSION['admin_id']      = (int)$u['id'];
        auditar('Login', "Usuario: $usuario");
        ok();
    }
    error('Usuario o contraseña incorrectos.');
}

// ─── Guard: requiere sesión ───────────────────────────────────────────────────
if (empty($_SESSION['admin_ok'])) {
    error('No autorizado.', 401);
}

$pdo = Conexion::get();

// ════════════════════════════════════════════════════════════════════════════
//  PRODUCTOS
// ════════════════════════════════════════════════════════════════════════════

if ($accion === 'guardar_producto') {
    $idOriginal  = trim($_POST['id_original'] ?? '');   // slug original si es edición
    $nombre      = trim($_POST['nombre'] ?? '');
    $categoriaId = (int)($_POST['categoria_id'] ?? 0);
    $icono       = trim($_POST['icono'] ?? 'bi-gear-wide-connected');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $destacado   = !empty($_POST['destacado']) ? 1 : 0;

    if ($nombre === '' || $categoriaId === 0) {
        error('Nombre y categoría son obligatorios.');
    }

    $slug = slugify($nombre);

    if ($idOriginal === '') {
        // Verificar slug único
        $existe = $pdo->prepare('SELECT id FROM productos WHERE slug = ?');
        $existe->execute([$slug]);
        if ($existe->fetchColumn()) {
            error('Ya existe un producto con ese nombre.');
        }

        $stmt = $pdo->prepare(
            'INSERT INTO productos (slug, nombre, carpeta, categoria_id, icono, descripcion, destacado)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$slug, $nombre, $nombre, $categoriaId, $icono, $descripcion, $destacado]);
        $nuevo = true;
    } else {
        $stmt = $pdo->prepare(
            'UPDATE productos
             SET slug = ?, nombre = ?, categoria_id = ?, icono = ?, descripcion = ?, destacado = ?
             WHERE slug = ?'
        );
        $stmt->execute([$slug, $nombre, $categoriaId, $icono, $descripcion, $destacado, $idOriginal]);
        $nuevo = false;
    }

    CatalogoSync::regenerar();
    auditar($nuevo ? 'Producto creado' : 'Producto editado', "Slug: $slug");
    ok(esNuevo: $nuevo, slug: $slug);
}

if ($accion === 'eliminar_producto') {
    $slug = trim($_POST['slug'] ?? '');
    $stmt = $pdo->prepare('DELETE FROM productos WHERE slug = ?');
    $stmt->execute([$slug]);
    CatalogoSync::regenerar();
    auditar('Producto eliminado', "Slug: $slug");
    ok();
}

// ════════════════════════════════════════════════════════════════════════════
//  IMÁGENES
// ════════════════════════════════════════════════════════════════════════════

if ($accion === 'subir_imagen') {
    $slug = trim($_POST['product_slug'] ?? '');

    $prod = $pdo->prepare('SELECT id, carpeta FROM productos WHERE slug = ?');
    $prod->execute([$slug]);
    $producto = $prod->fetch();

    if ($producto === false) {
        error('Producto no encontrado.');
    }

    if (empty($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
        error('Error al recibir el archivo.');
    }

    $ext       = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
    $permitidos = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    if (!in_array($ext, $permitidos, true)) {
        error('Formato no permitido. Usar: jpg, png, webp, gif.');
    }

    $imgBase     = realpath(__DIR__ . '/../img');
    $dirProducto = $imgBase . DIRECTORY_SEPARATOR . $producto['carpeta'];

    if (!is_dir($dirProducto)) {
        mkdir($dirProducto, 0755, true);
    }

    $nombreArchivo = uniqid('img_') . '.' . $ext;
    $destino       = $dirProducto . DIRECTORY_SEPARATOR . $nombreArchivo;

    if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $destino)) {
        error('No se pudo guardar la imagen.');
    }

    $urlRelativa = 'img/' . rawurlencode($producto['carpeta']) . '/' . rawurlencode($nombreArchivo);

    $orden = (int)$pdo->prepare('SELECT COALESCE(MAX(orden),0)+1 FROM img_producto WHERE producto_id = ?')
                       ->execute([(int)$producto['id']]) ? 1 : 1;
    // recalcular orden correctamente
    $maxOrden = $pdo->prepare('SELECT COALESCE(MAX(orden),0)+1 FROM img_producto WHERE producto_id = ?');
    $maxOrden->execute([(int)$producto['id']]);
    $orden = (int)$maxOrden->fetchColumn();

    $ins = $pdo->prepare('INSERT INTO img_producto (producto_id, url, orden) VALUES (?, ?, ?)');
    $ins->execute([(int)$producto['id'], $urlRelativa, $orden]);

    CatalogoSync::regenerar();
    auditar('Imagen subida', "Producto: $slug | Archivo: $nombreArchivo");
    ok(url: $urlRelativa, nombre: $nombreArchivo);
}

if ($accion === 'eliminar_imagen') {
    $imgId = (int)($_POST['img_id'] ?? 0);

    $fila = $pdo->prepare('SELECT pi.url, p.carpeta FROM img_producto pi JOIN productos p ON p.id = pi.producto_id WHERE pi.id = ?');
    $fila->execute([$imgId]);
    $img = $fila->fetch();

    if ($img === false) {
        error('Imagen no encontrada.');
    }

    $pdo->prepare('DELETE FROM img_producto WHERE id = ?')->execute([$imgId]);
    auditar('Imagen eliminada', "ID imagen: $imgId");

    // Borrar archivo físico
    $imgBase = realpath(__DIR__ . '/../img');
    $relativa = urldecode(ltrim($img['url'], '/'));
    $absoluta = realpath(__DIR__ . '/../' . $relativa);

    if ($absoluta !== false && $imgBase !== false && str_starts_with($absoluta, $imgBase) && is_file($absoluta)) {
        unlink($absoluta);
    }

    CatalogoSync::regenerar();
    ok();
}

if ($accion === 'get_imagenes') {
    $slug = trim($_POST['product_slug'] ?? $_GET['product_slug'] ?? '');

    $prod = $pdo->prepare('SELECT id FROM productos WHERE slug = ?');
    $prod->execute([$slug]);
    $productoId = $prod->fetchColumn();

    if ($productoId === false) {
        error('Producto no encontrado.');
    }

    $imgs = $pdo->prepare('SELECT id, url, es_portada FROM img_producto WHERE producto_id = ? ORDER BY orden ASC, id ASC');
    $imgs->execute([(int)$productoId]);
    $totalPortadas = (int)$pdo->query('SELECT COUNT(*) FROM img_producto WHERE es_portada = 1')->fetchColumn();
    ok(imagenes: $imgs->fetchAll(), total_portadas: $totalPortadas);
}

if ($accion === 'toggle_portada') {
    $imgId = (int)($_POST['img_id'] ?? 0);

    $fila = $pdo->prepare('SELECT producto_id, es_portada FROM img_producto WHERE id = ?');
    $fila->execute([$imgId]);
    $img = $fila->fetch();
    if (!$img) error('Imagen no encontrada.');

    // Contar portadas globales (máx 3 en total)
    if (!$img['es_portada']) {
        $cnt = $pdo->query('SELECT COUNT(*) FROM img_producto WHERE es_portada = 1');
        if ((int)$cnt->fetchColumn() >= 3) {
            error('Máximo 3 imágenes de portada en total.');
        }
    }

    $nuevo = $img['es_portada'] ? 0 : 1;
    $pdo->prepare('UPDATE img_producto SET es_portada = ? WHERE id = ?')->execute([$nuevo, $imgId]);

    CatalogoSync::regenerar();
    ok(es_portada: $nuevo);
}

if ($accion === 'reordenar_imagenes') {
    // Recibe JSON: [{"id":1,"orden":1},{"id":2,"orden":2},...]
    $items = json_decode($_POST['items'] ?? '[]', true);
    if (!is_array($items)) {
        error('Datos inválidos.');
    }
    $stmt = $pdo->prepare('UPDATE img_producto SET orden = ? WHERE id = ?');
    foreach ($items as $item) {
        $stmt->execute([(int)$item['orden'], (int)$item['id']]);
    }
    CatalogoSync::regenerar();
    ok();
}

// ════════════════════════════════════════════════════════════════════════════
//  CATEGORÍAS
// ════════════════════════════════════════════════════════════════════════════

if ($accion === 'crear_categoria') {
    $nombre = trim($_POST['nombre'] ?? '');
    if ($nombre === '') {
        error('El nombre no puede estar vacío.');
    }
    $stmt = $pdo->prepare('INSERT INTO categorias (nombre) VALUES (?)');
    try {
        $stmt->execute([$nombre]);
    } catch (PDOException $e) {
        error('Esa categoría ya existe.');
    }
    auditar('Categoría creada', "Nombre: $nombre");
    ok(id: (int)$pdo->lastInsertId(), nombre: $nombre);
}

if ($accion === 'renombrar_categoria') {
    $id     = (int)($_POST['id'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');
    if ($nombre === '') {
        error('El nombre no puede estar vacío.');
    }
    $stmt = $pdo->prepare('UPDATE categorias SET nombre = ? WHERE id = ?');
    try {
        $stmt->execute([$nombre, $id]);
    } catch (PDOException $e) {
        error('Ya existe una categoría con ese nombre.');
    }
    CatalogoSync::regenerar();
    auditar('Categoría renombrada', "ID: $id | Nuevo nombre: $nombre");
    ok();
}

if ($accion === 'eliminar_categoria') {
    $id = (int)($_POST['id'] ?? 0);

    $enUso = $pdo->prepare('SELECT COUNT(*) FROM productos WHERE categoria_id = ?');
    $enUso->execute([$id]);
    $count = (int)$enUso->fetchColumn();

    if ($count > 0) {
        error("No se puede eliminar: hay {$count} producto(s) con esta categoría.");
    }

    $pdo->prepare('DELETE FROM categorias WHERE id = ?')->execute([$id]);
    auditar('Categoría eliminada', "ID: $id");
    ok();
}

// ════════════════════════════════════════════════════════════════════════════
//  CONFIGURACIÓN
// ════════════════════════════════════════════════════════════════════════════

if ($accion === 'guardar_config') {
    $phone     = trim($_POST['phone'] ?? '');
    $smtpPass  = $_POST['smtp_pass'] ?? '';

    $pares = [
        'site_name'    => trim($_POST['site_name']    ?? ''),
        'whatsapp'     => preg_replace('/\D/', '', $_POST['whatsapp'] ?? '') ?? '',
        'phone'        => $phone,
        'phone_link'   => '+' . (preg_replace('/\D/', '', $phone) ?? ''),
        'email_to'     => trim($_POST['email_to']     ?? ''),
        'instagram'    => trim($_POST['instagram']    ?? ''),
        'facebook'     => trim($_POST['facebook']     ?? ''),
        'ubicacion'    => trim($_POST['ubicacion']    ?? ''),
        'analytics_id' => trim($_POST['analytics_id'] ?? ''),
        'meta_pixel_id'=> trim($_POST['meta_pixel_id']?? ''),
        'seo_title'    => trim($_POST['seo_title']    ?? ''),
        'seo_desc'     => trim($_POST['seo_desc']     ?? ''),
        'seo_keywords' => trim($_POST['seo_keywords'] ?? ''),
        'smtp_host'    => trim($_POST['smtp_host']    ?? ''),
        'smtp_port'    => trim($_POST['smtp_port']    ?? '587'),
        'smtp_secure'  => trim($_POST['smtp_secure']  ?? 'tls'),
        'smtp_user'    => trim($_POST['smtp_user']    ?? ''),
        'smtp_from'    => trim($_POST['smtp_from']    ?? ''),
        'smtp_name'    => trim($_POST['smtp_name']    ?? ''),
    ];

    if ($smtpPass !== '') {
        $pares['smtp_pass'] = $smtpPass;
    }

    Config::setMany($pares);
    auditar('Configuración guardada');
    ok();
}

if ($accion === 'limpiar_auditoria') {
    $pdo->exec('DELETE FROM auditoria');
    ok();
}

// ════════════════════════════════════════════════════════════════════════════
//  USUARIOS
// ════════════════════════════════════════════════════════════════════════════

if ($accion === 'crear_usuario') {
    $usuario = trim($_POST['usuario'] ?? '');
    $nombre  = trim($_POST['nombre']  ?? '');
    $pass    = $_POST['pass'] ?? '';

    if ($usuario === '' || $pass === '') error('Usuario y contraseña son obligatorios.');
    if (strlen($pass) < 6) error('La contraseña debe tener al menos 6 caracteres.');

    $hash = password_hash($pass, PASSWORD_DEFAULT);
    try {
        $pdo->prepare('INSERT INTO usuarios (usuario, pass_hash, nombre) VALUES (?, ?, ?)')
            ->execute([$usuario, $hash, $nombre]);
    } catch (PDOException) {
        error('Ya existe un usuario con ese nombre.');
    }
    auditar('Usuario creado', "Usuario: $usuario");
    ok(id: (int)$pdo->lastInsertId());
}

if ($accion === 'editar_usuario') {
    $id      = (int)($_POST['id'] ?? 0);
    $usuario = trim($_POST['usuario'] ?? '');
    $nombre  = trim($_POST['nombre']  ?? '');
    $pass    = $_POST['pass'] ?? '';

    if ($usuario === '') error('El nombre de usuario es obligatorio.');

    // No permitir editar el propio usuario si es el único activo
    try {
        $pdo->prepare('UPDATE usuarios SET usuario = ?, nombre = ? WHERE id = ?')
            ->execute([$usuario, $nombre, $id]);
    } catch (PDOException) {
        error('Ya existe un usuario con ese nombre.');
    }

    if ($pass !== '') {
        if (strlen($pass) < 6) error('La contraseña debe tener al menos 6 caracteres.');
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $pdo->prepare('UPDATE usuarios SET pass_hash = ? WHERE id = ?')->execute([$hash, $id]);
    }
    auditar('Usuario editado', "ID: $id | Usuario: $usuario");
    ok();
}

if ($accion === 'toggle_usuario') {
    $id = (int)($_POST['id'] ?? 0);

    // No desactivar si es el único activo
    $activos = (int)$pdo->query('SELECT COUNT(*) FROM usuarios WHERE activo = 1')->fetchColumn();
    $actual  = $pdo->prepare('SELECT activo FROM usuarios WHERE id = ?');
    $actual->execute([$id]);
    $u = $actual->fetch();
    if (!$u) error('Usuario no encontrado.');

    if ($u['activo'] && $activos <= 1) error('No podés desactivar el único usuario activo.');

    $nuevo = $u['activo'] ? 0 : 1;
    $pdo->prepare('UPDATE usuarios SET activo = ? WHERE id = ?')->execute([$nuevo, $id]);
    auditar('Usuario ' . ($nuevo ? 'activado' : 'desactivado'), "ID: $id");
    ok(activo: $nuevo);
}

if ($accion === 'eliminar_usuario') {
    $id = (int)($_POST['id'] ?? 0);

    if ((int)($_SESSION['admin_id'] ?? 0) === $id) error('No podés eliminar tu propio usuario.');

    $activos = (int)$pdo->query('SELECT COUNT(*) FROM usuarios WHERE activo = 1')->fetchColumn();
    $actual  = $pdo->prepare('SELECT activo FROM usuarios WHERE id = ?');
    $actual->execute([$id]);
    $u = $actual->fetch();
    if (!$u) error('Usuario no encontrado.');
    if ($u['activo'] && $activos <= 1) error('No podés eliminar el único usuario activo.');

    $pdo->prepare('DELETE FROM usuarios WHERE id = ?')->execute([$id]);
    auditar('Usuario eliminado', "ID: $id");
    ok();
}

error('Acción desconocida.');
