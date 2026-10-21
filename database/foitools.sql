-- ============================================================
--  FOI Tools Agroindustrial — base de datos
--  Versión: 1.0 | PHP 8.2+ | MySQL 5.7+ / MariaDB 10.4+
--  Importar desde phpMyAdmin o con:
--    mysql -u root -p foitools < foitools.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS `foitools`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `foitools`;

-- ─── Categorías ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `categorias` (
    `id`         INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `nombre`     VARCHAR(100)     NOT NULL,
    `created_at` TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_categorias_nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Productos ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `productos` (
    `id`          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `slug`        VARCHAR(150)     NOT NULL,
    `nombre`      VARCHAR(150)     NOT NULL,
    `carpeta`     VARCHAR(150)     NOT NULL COMMENT 'Nombre real del directorio en img/',
    `categoria_id` INT UNSIGNED   NOT NULL,
    `icono`       VARCHAR(100)     NOT NULL DEFAULT 'bi-gear-wide-connected',
    `descripcion` TEXT,
    `destacado`   TINYINT(1)       NOT NULL DEFAULT 0,
    `orden`       INT              NOT NULL DEFAULT 0,
    `created_at`  TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_productos_slug` (`slug`),
    KEY `fk_productos_categoria` (`categoria_id`),
    CONSTRAINT `fk_productos_categoria`
        FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Imágenes de productos ────────────────────────────────────
CREATE TABLE IF NOT EXISTS `img_producto` (
    `id`          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `producto_id` INT UNSIGNED     NOT NULL,
    `url`         VARCHAR(500)     NOT NULL COMMENT 'Ruta relativa: img/Carpeta/archivo.webp',
    `orden`       INT              NOT NULL DEFAULT 0,
    `es_portada`  TINYINT(1)       NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `fk_imagenes_producto` (`producto_id`),
    CONSTRAINT `fk_imagenes_producto`
        FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  DATOS INICIALES (migración desde products.js)
-- ============================================================

INSERT INTO `categorias` (`nombre`) VALUES
    ('Agro'),
    ('Bandas Transportadoras'),
    ('Hidráulica'),
    ('Transmisión');

INSERT INTO `productos` (`slug`, `nombre`, `carpeta`, `categoria_id`, `icono`, `descripcion`, `destacado`, `orden`) VALUES
    ('bandas-planos-inclinados',       'Bandas planos inclinados',       'Bandas planos inclinados',       (SELECT id FROM categorias WHERE nombre = 'Bandas Transportadoras'), 'bi-distribute-horizontal', 'Bandas para planos inclinados y transporte de material.',                                                              0, 3),
    ('cintas-transportadoras',         'Cintas Transportadoras',         'Cintas Transportadoras',         (SELECT id FROM categorias WHERE nombre = 'Bandas Transportadoras'), 'bi-distribute-horizontal', 'Cintas transportadoras para el manejo de granos y materiales en planta y campo.',                                      0, 2),
    ('cintas-y-grampas',               'Cintas y Grampas',               'Cintas y Grampas',               (SELECT id FROM categorias WHERE nombre = 'Bandas Transportadoras'), 'bi-paperclip',             'Cintas con sus grampas y accesorios de unión.',                                                                       0, 4),
    ('correas-agricolas-e-industriales','Correas Agrícolas e Industriales','Correas Agrícolas e Industriales',(SELECT id FROM categorias WHERE nombre = 'Transmisión'),           'bi-arrow-repeat',          'Correas para maquinaria agrícola e industrial, para transmitir potencia de forma confiable.',                          0, 2),
    ('cosecha',                        'Cosecha',                        'Cosecha',                        (SELECT id FROM categorias WHERE nombre = 'Agro'),                   'bi-basket',                'Repuestos e insumos para cosechadoras y labores de cosecha.',                                                         0, 2),
    ('discos-y-pinones-dentados',      'Discos y Piñones Dentados',      'Discos y Piñones Dentados',      (SELECT id FROM categorias WHERE nombre = 'Transmisión'),            'bi-gear-wide-connected',   'Discos dentados, piñones y masas en todos los modelos, simples y dobles. Alta resistencia y precisión.',                1, 1),
    ('mangueras-dunlop',               'Mangueras Dunlop',               'Mangueras Dunlop',               (SELECT id FROM categorias WHERE nombre = 'Hidráulica'),             'bi-droplet-half',          'Línea de mangueras Dunlop para usos agrícolas e industriales.',                                                       0, 2),
    ('mangueras-hidraulicas',          'Mangueras Hidráulicas',          'Mangueras Hidráulicas',          (SELECT id FROM categorias WHERE nombre = 'Hidráulica'),             'bi-droplet-half',          'Mangueras hidráulicas para maquinaria y equipos de trabajo pesado.',                                                  0, 1),
    ('pastura',                        'Pastura',                        'Pastura',                        (SELECT id FROM categorias WHERE nombre = 'Agro'),                   'bi-tree-fill',             'Productos para pasturas, henificación y forraje.',                                                                    0, 3),
    ('poleas',                         'Poleas',                         'Poleas',                         (SELECT id FROM categorias WHERE nombre = 'Transmisión'),            'bi-disc',                  'Poleas para sistemas de transmisión por correa.',                                                                     0, 3),
    ('siembra',                        'Siembra',                        'Siembra',                        (SELECT id FROM categorias WHERE nombre = 'Agro'),                   'bi-flower1',               'Repuestos e insumos para sembradoras.',                                                                               0, 1);

-- Imágenes: Bandas planos inclinados
INSERT INTO `img_producto` (`producto_id`, `url`, `orden`) VALUES
    ((SELECT id FROM productos WHERE slug='bandas-planos-inclinados'), 'img/Bandas%20planos%20inclinados/Sin%20t%C3%ADtuloaawassasasssasssa.webp',          1),
    ((SELECT id FROM productos WHERE slug='bandas-planos-inclinados'), 'img/Bandas%20planos%20inclinados/Sin%20t%C3%ADtuloaawassasasssasssass.webp',         2),
    ((SELECT id FROM productos WHERE slug='bandas-planos-inclinados'), 'img/Bandas%20planos%20inclinados/Sin%20t%C3%ADtuloaawassasasssasssassx.webp',        3),
    ((SELECT id FROM productos WHERE slug='bandas-planos-inclinados'), 'img/Bandas%20planos%20inclinados/Sin%20t%C3%ADtuloaawassasasssasssassxs.webp',       4),
    ((SELECT id FROM productos WHERE slug='bandas-planos-inclinados'), 'img/Bandas%20planos%20inclinados/Sin%20t%C3%ADtuloaawassasasssaszssssssssssss.webp', 5),
    ((SELECT id FROM productos WHERE slug='bandas-planos-inclinados'), 'img/Bandas%20planos%20inclinados/Sin%20t%C3%ADtuloaawassasasssaszsssssssssssss.webp',6),
    ((SELECT id FROM productos WHERE slug='bandas-planos-inclinados'), 'img/Bandas%20planos%20inclinados/Sin%20t%C3%ADtuloaawassasasssaszsssssssssssssa.webp',7),
    ((SELECT id FROM productos WHERE slug='bandas-planos-inclinados'), 'img/Bandas%20planos%20inclinados/Sin%20t%C3%ADtuloaawassasasssaszsssssssssssssaa.webp',8),
    ((SELECT id FROM productos WHERE slug='bandas-planos-inclinados'), 'img/Bandas%20planos%20inclinados/Sin%20t%C3%ADtuloaawassasasssaszsssssssssssssaaa.webp',9);

-- Imágenes: Cintas Transportadoras
INSERT INTO `img_producto` (`producto_id`, `url`, `orden`) VALUES
    ((SELECT id FROM productos WHERE slug='cintas-transportadoras'), 'img/Cintas%20Transportadoras/Sin%20t%C3%ADtuloaawassacxxssss.webp',  1),
    ((SELECT id FROM productos WHERE slug='cintas-transportadoras'), 'img/Cintas%20Transportadoras/Sin%20t%C3%ADtuloaawassacxxsssss.webp', 2),
    ((SELECT id FROM productos WHERE slug='cintas-transportadoras'), 'img/Cintas%20Transportadoras/Sin%20t%C3%ADtuloaawassacxxsssssc.webp',3),
    ((SELECT id FROM productos WHERE slug='cintas-transportadoras'), 'img/Cintas%20Transportadoras/Sin%20t%C3%ADtuloaawassacxxssssscs.webp',4),
    ((SELECT id FROM productos WHERE slug='cintas-transportadoras'), 'img/Cintas%20Transportadoras/Sin%20t%C3%ADtuloaawassacxxssssscss.webp',5);

-- Imágenes: Cintas y Grampas
INSERT INTO `img_producto` (`producto_id`, `url`, `orden`) VALUES
    ((SELECT id FROM productos WHERE slug='cintas-y-grampas'), 'img/Cintas%20y%20Grampas/Sin%20t%C3%ADtuloaawassasasssas.webp',    1),
    ((SELECT id FROM productos WHERE slug='cintas-y-grampas'), 'img/Cintas%20y%20Grampas/Sin%20t%C3%ADtuloaawassasasssass.webp',   2),
    ((SELECT id FROM productos WHERE slug='cintas-y-grampas'), 'img/Cintas%20y%20Grampas/Sin%20t%C3%ADtuloaawassasasssasss.webp',  3),
    ((SELECT id FROM productos WHERE slug='cintas-y-grampas'), 'img/Cintas%20y%20Grampas/Sin%20t%C3%ADtuloaawassasasssassss.webp', 4),
    ((SELECT id FROM productos WHERE slug='cintas-y-grampas'), 'img/Cintas%20y%20Grampas/Sin%20t%C3%ADtuloaawassasasssasssss.webp',5);

-- Imágenes: Correas Agrícolas e Industriales
INSERT INTO `img_producto` (`producto_id`, `url`, `orden`) VALUES
    ((SELECT id FROM productos WHERE slug='correas-agricolas-e-industriales'), 'img/Correas%20Agr%C3%ADcolas%20e%20Industriales/Sin%20t%C3%ADtuloaawassac.webp',               1),
    ((SELECT id FROM productos WHERE slug='correas-agricolas-e-industriales'), 'img/Correas%20Agr%C3%ADcolas%20e%20Industriales/Sin%20t%C3%ADtuloaawassacxx.webp',              2),
    ((SELECT id FROM productos WHERE slug='correas-agricolas-e-industriales'), 'img/Correas%20Agr%C3%ADcolas%20e%20Industriales/Sin%20t%C3%ADtuloaawassacxxs.webp',             3),
    ((SELECT id FROM productos WHERE slug='correas-agricolas-e-industriales'), 'img/Correas%20Agr%C3%ADcolas%20e%20Industriales/Sin%20t%C3%ADtuloaawassacxxss.webp',            4),
    ((SELECT id FROM productos WHERE slug='correas-agricolas-e-industriales'), 'img/Correas%20Agr%C3%ADcolas%20e%20Industriales/Sin%20t%C3%ADtuloaawassacxxsss.webp',           5),
    ((SELECT id FROM productos WHERE slug='correas-agricolas-e-industriales'), 'img/Correas%20Agr%C3%ADcolas%20e%20Industriales/Sin%20t%C3%ADtuloaawassasasssasssassxss.webp',  6),
    ((SELECT id FROM productos WHERE slug='correas-agricolas-e-industriales'), 'img/Correas%20Agr%C3%ADcolas%20e%20Industriales/Sin%20t%C3%ADtuloaawassasasssasssassxsss.webp', 7);

-- Imágenes: Cosecha
INSERT INTO `img_producto` (`producto_id`, `url`, `orden`) VALUES
    ((SELECT id FROM productos WHERE slug='cosecha'), 'img/Cosecha/Sin%20t%C3%ADtuloaawassasa.webp',    1),
    ((SELECT id FROM productos WHERE slug='cosecha'), 'img/Cosecha/Sin%20t%C3%ADtuloaawassasas.webp',   2),
    ((SELECT id FROM productos WHERE slug='cosecha'), 'img/Cosecha/Sin%20t%C3%ADtuloaawassasass.webp',  3),
    ((SELECT id FROM productos WHERE slug='cosecha'), 'img/Cosecha/Sin%20t%C3%ADtuloaawassasasss.webp', 4),
    ((SELECT id FROM productos WHERE slug='cosecha'), 'img/Cosecha/Sin%20t%C3%ADtuloaawassasasssa.webp',5);

-- Imágenes: Discos y Piñones Dentados
INSERT INTO `img_producto` (`producto_id`, `url`, `orden`) VALUES
    ((SELECT id FROM productos WHERE slug='discos-y-pinones-dentados'), 'img/Discos%20y%20Pi%C3%B1ones%20Dentados/Sin%20t%C3%ADtuloaawassasasssasssssassssssss.webp', 1),
    ((SELECT id FROM productos WHERE slug='discos-y-pinones-dentados'), 'img/Discos%20y%20Pi%C3%B1ones%20Dentados/Sin%20t%C3%ADtuloaawassasasssasz.webp',             2);

-- Imágenes: Mangueras Dunlop
INSERT INTO `img_producto` (`producto_id`, `url`, `orden`) VALUES
    ((SELECT id FROM productos WHERE slug='mangueras-dunlop'), 'img/Mangueras%20Dunlop/Sin%20t%C3%ADtuloaaw.webp',    1),
    ((SELECT id FROM productos WHERE slug='mangueras-dunlop'), 'img/Mangueras%20Dunlop/Sin%20t%C3%ADtuloaawa.webp',   2),
    ((SELECT id FROM productos WHERE slug='mangueras-dunlop'), 'img/Mangueras%20Dunlop/Sin%20t%C3%ADtuloaawas.webp',  3),
    ((SELECT id FROM productos WHERE slug='mangueras-dunlop'), 'img/Mangueras%20Dunlop/Sin%20t%C3%ADtuloaawass.webp', 4),
    ((SELECT id FROM productos WHERE slug='mangueras-dunlop'), 'img/Mangueras%20Dunlop/Sin%20t%C3%ADtuloaawassa.webp',5);

-- Imágenes: Mangueras Hidráulicas
INSERT INTO `img_producto` (`producto_id`, `url`, `orden`) VALUES
    ((SELECT id FROM productos WHERE slug='mangueras-hidraulicas'), 'img/Mangueras%20Hidr%C3%A1ulicas/Sin%20t%C3%ADtuloaawassasasssaszs.webp',    1),
    ((SELECT id FROM productos WHERE slug='mangueras-hidraulicas'), 'img/Mangueras%20Hidr%C3%A1ulicas/Sin%20t%C3%ADtuloaawassasasssaszss.webp',   2),
    ((SELECT id FROM productos WHERE slug='mangueras-hidraulicas'), 'img/Mangueras%20Hidr%C3%A1ulicas/Sin%20t%C3%ADtuloaawassasasssaszsss.webp',  3),
    ((SELECT id FROM productos WHERE slug='mangueras-hidraulicas'), 'img/Mangueras%20Hidr%C3%A1ulicas/Sin%20t%C3%ADtuloaawassasasssaszssss.webp', 4),
    ((SELECT id FROM productos WHERE slug='mangueras-hidraulicas'), 'img/Mangueras%20Hidr%C3%A1ulicas/Sin%20t%C3%ADtuloaawassasasssaszsssss.webp',5);

-- Imágenes: Pastura
INSERT INTO `img_producto` (`producto_id`, `url`, `orden`) VALUES
    ((SELECT id FROM productos WHERE slug='pastura'), 'img/Pastura/Sin%20t%C3%ADtuloaawassasasssasssssa.webp',   1),
    ((SELECT id FROM productos WHERE slug='pastura'), 'img/Pastura/Sin%20t%C3%ADtuloaawassasasssasssssas.webp',  2),
    ((SELECT id FROM productos WHERE slug='pastura'), 'img/Pastura/Sin%20t%C3%ADtuloaawassasasssasssssass.webp', 3),
    ((SELECT id FROM productos WHERE slug='pastura'), 'img/Pastura/Sin%20t%C3%ADtuloaawassasasssasssssasss.webp',4),
    ((SELECT id FROM productos WHERE slug='pastura'), 'img/Pastura/Sin%20t%C3%ADtuloaawassasasssasssssassss.webp',5),
    ((SELECT id FROM productos WHERE slug='pastura'), 'img/Pastura/Sin%20t%C3%ADtuloaawassasasssasssssasssss.webp',6),
    ((SELECT id FROM productos WHERE slug='pastura'), 'img/Pastura/Sin%20t%C3%ADtuloaawassasasssasssssassssss.webp',7),
    ((SELECT id FROM productos WHERE slug='pastura'), 'img/Pastura/Sin%20t%C3%ADtuloaawassasasssasssssasssssss.webp',8);

-- Imágenes: Poleas
INSERT INTO `img_producto` (`producto_id`, `url`, `orden`) VALUES
    ((SELECT id FROM productos WHERE slug='poleas'), 'img/Poleas/Sin%20t%C3%ADtuloaawassas.webp', 1);

-- Imágenes: Siembra
INSERT INTO `img_producto` (`producto_id`, `url`, `orden`) VALUES
    ((SELECT id FROM productos WHERE slug='siembra'), 'img/Siembra/Sin%20t%C3%ADtuloaawassasasssaszssssss.webp',   1),
    ((SELECT id FROM productos WHERE slug='siembra'), 'img/Siembra/Sin%20t%C3%ADtuloaawassasasssaszsssssss.webp',  2),
    ((SELECT id FROM productos WHERE slug='siembra'), 'img/Siembra/Sin%20t%C3%ADtuloaawassasasssaszssssssss.webp', 3),
    ((SELECT id FROM productos WHERE slug='siembra'), 'img/Siembra/Sin%20t%C3%ADtuloaawassasasssaszsssssssss.webp',4),
    ((SELECT id FROM productos WHERE slug='siembra'), 'img/Siembra/Sin%20t%C3%ADtuloaawassasasssaszssssssssss.webp',5),
    ((SELECT id FROM productos WHERE slug='siembra'), 'img/Siembra/Sin%20t%C3%ADtuloaawassasasssaszsssssssssss.webp',6);


-- ─── Configuración del sitio ─────────────────────────────────
CREATE TABLE IF NOT EXISTS `configuracion` (
    `clave`      VARCHAR(60)   NOT NULL,
    `valor`      TEXT          NOT NULL DEFAULT '',
    PRIMARY KEY (`clave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `configuracion` (`clave`, `valor`) VALUES
    ('site_name',    'FOI Tools Agroindustrial'),
    ('whatsapp',     '5493465668465'),
    ('phone',        '3465 66-3235'),
    ('phone_link',   '+5493465663235'),
    ('email_to',     'ventas@foitools.com'),
    ('instagram',    ''),
    ('facebook',     ''),
    ('ubicacion',    ''),
    ('analytics_id', ''),
    ('meta_pixel_id',''),
    ('smtp_host',    'smtp.gmail.com'),
    ('smtp_port',    '587'),
    ('smtp_secure',  'tls'),
    ('smtp_user',    ''),
    ('smtp_pass',    ''),
    ('smtp_from',    ''),
    ('smtp_name',    'Web FOI Tools'),
    ('seo_title',    'FOI Tools Agroindustrial | Implementos y repuestos para el agro y la industria'),
    ('seo_desc',     'Distribuidor de productos para la industria y el agro en Firmat, Santa Fe.'),
    ('seo_keywords', 'correas rotoenfardadoras, implementos agrícolas, repuestos agro');

-- ─── Usuarios del panel admin ────────────────────────────────
CREATE TABLE IF NOT EXISTS `usuarios` (
    `id`         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `usuario`    VARCHAR(60)   NOT NULL,
    `pass_hash`  VARCHAR(255)  NOT NULL,
    `nombre`     VARCHAR(100)  NOT NULL DEFAULT '',
    `activo`     TINYINT(1)    NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_usuarios_usuario` (`usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Usuario inicial: admin / admin (cambiar contraseña al ingresar)
INSERT IGNORE INTO `usuarios` (`usuario`, `pass_hash`, `nombre`) VALUES
    ('admin', '$2y$10$b5EzjxWvF66r6Kq5hXgnLOG7B/9MXvzDePPQQ9eFYjeTr.N7USUVa', 'Administrador');

-- Migración: agregar tabla si ya existe la BD sin ella
-- (ejecutar solo si la tabla no existe todavía)

-- ─── Auditoría ────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `auditoria` (
    `id`         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `accion`     VARCHAR(100)  NOT NULL,
    `detalle`    TEXT,
    `ip`         VARCHAR(45),
    `created_at` TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_auditoria_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
