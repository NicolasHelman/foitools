<?php

declare(strict_types=1);

/**
 * Regenera js/products.js a partir de la base de datos.
 * Se llama después de cada operación de escritura en el admin.
 */
final class CatalogoSync
{
    public static function regenerar(): void
    {
        $pdo = Conexion::get();

        $productos = $pdo->query(
            'SELECT p.slug, p.nombre, p.carpeta, p.icono, p.descripcion, p.destacado,
                    c.nombre AS tag
             FROM productos p
             JOIN categorias c ON c.id = p.categoria_id
             ORDER BY p.orden ASC, p.id ASC'
        )->fetchAll();

        $imagenesPorProducto = [];
        $portadaPorProducto  = [];
        $imgs = $pdo->query(
            'SELECT producto_id, url, es_portada FROM img_producto ORDER BY orden ASC, id ASC'
        )->fetchAll();

        foreach ($imgs as $img) {
            $pid = (int)$img['producto_id'];
            $imagenesPorProducto[$pid][] = $img['url'];
            if ($img['es_portada']) {
                $portadaPorProducto[$pid][] = $img['url'];
            }
        }

        $idPorSlug = $pdo->query('SELECT slug, id FROM productos')->fetchAll(PDO::FETCH_KEY_PAIR);

        $catalog = [];
        foreach ($productos as $p) {
            $id   = (int)($idPorSlug[$p['slug']] ?? 0);
            $entry = [
                'id'     => $p['slug'],
                'name'   => $p['nombre'],
                'icon'   => $p['icono'],
                'tag'    => $p['tag'],
                'desc'   => $p['descripcion'] ?? '',
                'images'  => $imagenesPorProducto[$id] ?? [],
                'portada' => $portadaPorProducto[$id] ?? [],
            ];
            if ((bool)$p['destacado']) {
                $entry['featured'] = true;
            }
            $catalog[] = $entry;
        }

        $js = 'window.CATALOG = ' . json_encode(
            $catalog,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        ) . ";\n";

        file_put_contents(__DIR__ . '/../../js/products.js', $js);
    }
}
