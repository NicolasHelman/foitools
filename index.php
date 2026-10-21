<?php
require __DIR__ . '/admin/class/Conexion.php';
require __DIR__ . '/admin/class/Config.php';

$cfg      = Config::all();
$scheme   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$base     = $scheme . '://' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/';
$title    = $cfg['seo_title']    ?: 'FOI Tools Agroindustrial | Implementos y repuestos para el agro y la industria';
$desc     = $cfg['seo_desc']     ?: 'Distribuidor de productos para la industria y el agro en Firmat, Santa Fe.';
$keywords = $cfg['seo_keywords'] ?: 'correas rotoenfardadoras, implementos agrícolas, repuestos agro';
?>
<!doctype html>
<html lang="es" data-bs-theme="light">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $title ?></title>
  <meta name="description" content="<?= $desc ?>">
  <meta name="keywords" content="<?= $keywords ?>"><?php // keywords ayudan a motores de búsqueda secundarios y a la comprensión del contenido ?>
  <meta name="theme-color" content="#ffffff">
  <link rel="canonical" href="<?= $base ?>">
  <link rel="icon" href="img/icons/favicon.ico" sizes="any">
  <link rel="icon" type="image/png" sizes="32x32" href="img/icons/favicon-32.png">
  <link rel="icon" type="image/png" sizes="192x192" href="img/icons/favicon-192.png">
  <link rel="apple-touch-icon" href="img/icons/apple-touch-icon.png">

  <!-- Vista previa al compartir (WhatsApp, Facebook, Instagram, etc.) -->
  <meta property="og:type" content="website">
  <meta property="og:locale" content="es_AR">
  <meta property="og:site_name" content="<?= htmlspecialchars($cfg['site_name']) ?>">
  <meta property="og:title" content="<?= $title ?>">
  <meta property="og:description" content="<?= $desc ?>">
  <meta property="og:url" content="<?= $base ?>">
  <meta property="og:image" content="<?= $base ?>img/icons/og-image.jpg">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= $title ?>">
  <meta name="twitter:description" content="<?= $desc ?>">
  <meta name="twitter:image" content="<?= $base ?>img/icons/og-image.jpg">

  <!-- Datos estructurados para Google -->
  <script type="application/ld+json"><?= json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'LocalBusiness',
    'name' => $cfg['site_name'],
    'description' => $desc,
    'url' => $base,
    'logo' => $base . 'img/logo-foitools.png',
    'image' => $base . 'img/icons/og-image.jpg',
    'telephone' => $cfg['phone_link'],
    'email' => $cfg['email_to'],
    'address' => ['@type' => 'PostalAddress', 'addressLocality' => 'Firmat', 'addressRegion' => 'Santa Fe', 'addressCountry' => 'AR'],
    'areaServed' => 'AR',
    'sameAs' => array_filter([$cfg['instagram'] ?? '', $cfg['facebook'] ?? '']),
  ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>

<?php if (!empty($cfg['analytics_id'])): ?>
  <script async src="https://www.googletagmanager.com/gtag/js?id=<?= htmlspecialchars($cfg['analytics_id']) ?>"></script>
  <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments)}gtag('js',new Date());gtag('config','<?= htmlspecialchars($cfg['analytics_id']) ?>');</script>
<?php endif; ?>
<?php if (!empty($cfg['meta_pixel_id'])): ?>
  <script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init','<?= htmlspecialchars($cfg['meta_pixel_id']) ?>');fbq('track','PageView');</script>
<?php endif; ?>

  <script>document.documentElement.classList.add('js');</script>
  <link rel="stylesheet" href="vendor/bootstrap/bootstrap.min.css">
  <link rel="stylesheet" href="vendor/bootstrap-icons/bootstrap-icons.min.css">
  <link rel="stylesheet" href="vendor/aos/aos.css">
  <link rel="stylesheet" href="css/style.css?v=<?= filemtime(__DIR__ . '/css/style.css') ?>">
  <link rel="stylesheet" href="css/extra.css?v=<?= filemtime(__DIR__ . '/css/extra.css') ?>">
</head>
<body data-wa="<?= htmlspecialchars($cfg['whatsapp']) ?>">

<div class="preloader" id="preloader" aria-hidden="true">
  <div class="preloader-in">
    <img src="img/logo-foitools.png" alt="">
    <div class="preloader-bar"><span></span></div>
  </div>
</div>

<nav class="navbar navbar-expand-lg fixed-top foi-nav" id="nav">
  <div class="container">
    <a class="navbar-brand" href="#inicio"><img src="img/logo-foitools.png" alt="FOI Tools Agroindustrial" height="52"></a>
    <button class="btn btn-outline-dark rounded-circle position-relative ms-auto me-2 d-lg-none cart-quick" type="button" data-bs-toggle="offcanvas" data-bs-target="#cart" aria-label="Mi consulta"><i class="bi bi-clipboard-check"></i><span class="cart-badge cart-count" hidden>0</span></button>
        <button class="navbar-toggler border-0 px-1" type="button" data-bs-toggle="collapse" data-bs-target="#menu" aria-controls="menu" aria-expanded="false" aria-label="Abrir menú">
      <i class="bi bi-list fs-1"></i>
    </button>
    <div class="collapse navbar-collapse" id="menu">
      <ul class="navbar-nav mx-auto gap-lg-2">
        <li class="nav-item"><a class="nav-link" href="#catalogo">Catálogo</a></li>
        <li class="nav-item" id="promoLink" hidden><a class="nav-link" href="#promos">Promociones</a></li>
        <li class="nav-item"><a class="nav-link" href="#nosotros">Nosotros</a></li>
        <li class="nav-item"><a class="nav-link" href="#contacto">Contacto</a></li>
      </ul>
      <div class="nav-actions">
        <button class="btn btn-outline-dark rounded-pill px-3 position-relative" type="button" data-bs-toggle="offcanvas" data-bs-target="#cart" aria-label="Mi consulta"><i class="bi bi-clipboard-check me-xl-2"></i><span class="d-none d-xl-inline">Mi consulta</span><span class="cart-badge cart-count" hidden>0</span></button>
        <a class="btn btn-success rounded-pill px-4" data-wa target="_blank" rel="noopener"><i class="bi bi-whatsapp me-2"></i>WhatsApp</a>
      </div>
    </div>
  </div>
</nav>

<main>
  <!-- HERO -->
  <section class="hero<?= file_exists(__DIR__.'/img/hero.jpg') ? ' hero--photo' : '' ?>" id="inicio">
    <div class="hero-bg" aria-hidden="true">
      <i class="bi bi-gear-wide-connected g1"></i>
      <i class="bi bi-gear-fill g2"></i>
    </div>
    <div class="container position-relative">
      <div class="row">
        <div class="col-lg-7">
          <p class="eyebrow" data-hero style="--d:0.10s">Firmat, Santa Fe · Envíos a todo el país</p>
          <h1 data-hero style="--d:0.18s">Tu agro y tu industria, con la <em>fuerza</em> de nuestras piezas.</h1>
          <p class="lead text-secondary my-4" data-hero style="--d:0.26s">Distribuidor de productos para la industria y el agro. Discos dentados, piñones, correas, mangueras, cintas transportadoras y repuestos de alta resistencia y precisión.</p>
          <div class="d-flex flex-wrap gap-3" data-hero style="--d:0.34s">
            <a href="#catalogo" class="btn btn-danger btn-lg rounded-pill px-4">Ver catálogo <i class="bi bi-arrow-right ms-2"></i></a>
            <a class="btn btn-outline-dark btn-lg rounded-pill px-4" data-wa target="_blank" rel="noopener"><i class="bi bi-whatsapp me-2"></i>Consultanos</a>
          </div>
        </div>
        <div class="col-lg-5 d-none d-lg-block" data-hero style="--d:0.30s">
          <div class="collage" id="collage" aria-hidden="true"></div>
        </div>
      </div>
      <div class="row g-3 strip" data-hero style="--d:0.40s">
        <div class="col-6 col-lg-3"><div class="strip-item"><i class="bi bi-truck"></i><div><strong>Envíos a todo el país</strong><span>Entrega a domicilio</span></div></div></div>
        <div class="col-6 col-lg-3"><div class="strip-item"><i class="bi bi-shield-check"></i><div><strong>Alta resistencia</strong><span>Piezas de precisión</span></div></div></div>
        <div class="col-6 col-lg-3"><div class="strip-item"><i class="bi bi-grid-3x3-gap"></i><div><strong id="statCats">+10 líneas</strong><span>De productos</span></div></div></div>
        <div class="col-6 col-lg-3"><div class="strip-item"><i class="bi bi-geo-alt"></i><div><strong>Firmat, Santa Fe</strong><span>Tu mejor opción</span></div></div></div>
      </div>
    </div>
  </section>

  <!-- CATALOGO -->
  <section class="sec" id="catalogo">
    <div class="container">
      <div class="sec-head" data-aos="fade-up">
        <p class="eyebrow">Catálogo</p>
        <h2>Todo lo que tu maquinaria necesita</h2>
        <p class="text-secondary">Elegí una línea para ver los productos. Si no encontrás lo que buscás, consultanos: trabajamos con todos los modelos.</p>
      </div>
      <div class="row g-3 align-items-center mb-4" data-aos="fade-up">
        <div class="col-lg-8"><div class="d-flex flex-wrap gap-2" id="chips"></div></div>
        <div class="col-lg-4"><div class="search"><i class="bi bi-search"></i><input type="search" id="search" class="form-control" placeholder="Buscar: correas, mangueras, discos..." aria-label="Buscar productos"></div></div>
      </div>
      <p class="text-secondary d-none" id="noRes">No encontramos resultados. <a class="link-danger" data-wa="Hola! Estoy buscando un producto que no encuentro en la web." target="_blank" rel="noopener" href="#">Consultanos por WhatsApp</a>.</p>
      <div class="row g-4" id="catGrid"></div>
    </div>
  </section>


  <!-- PROMOS (se muestra solo si hay imágenes cargadas en js/promos.js) -->
  <section class="sec" id="promos" hidden>
    <div class="container">
      <div class="sec-head" data-aos="fade-up">
        <p class="eyebrow">Promociones</p>
        <h2>Ofertas vigentes</h2>
      </div>
      <div class="row g-4" id="promoGrid"></div>
    </div>
  </section>

  <!-- CONTADORES / ENVIOS -->
  <section class="stats" id="stats">
    <div class="container">
      <div class="row g-4 text-center justify-content-center">
        <div class="col-4"><div class="stat"><b id="cntCats">0</b><span>Líneas de productos</span></div></div>
        <div class="col-4"><div class="stat"><b id="cntImgs">0</b><span>Productos en el catálogo</span></div></div>
        <div class="col-4"><div class="stat"><b id="cntClients">0</b><span>Clientes</span></div></div>
      </div>
    </div>
  </section>

  <!-- COMO COMPRAR -->
  <section class="sec" id="como">
    <div class="container">
      <div class="sec-head" data-aos="fade-up">
        <p class="eyebrow">Cómo comprar</p>
        <h2>Simple y rápido</h2>
      </div>
      <div class="row g-4 steps">
        <div class="col-md-6 col-lg-3" data-aos="fade-up"><div class="step"><span>01</span><h3>Elegí</h3><p>Navegá el catálogo y sumá lo que necesitás a "Mi consulta".</p></div></div>
        <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="100"><div class="step"><span>02</span><h3>Consultá</h3><p>Enviá tu lista por WhatsApp con un solo clic.</p></div></div>
        <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="200"><div class="step"><span>03</span><h3>Recibí presupuesto</h3><p>Te confirmamos disponibilidad, medidas y precio.</p></div></div>
        <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="300"><div class="step"><span>04</span><h3>Te lo enviamos</h3><p>Envíos a todo el país o entrega a domicilio.</p></div></div>
      </div>
    </div>
  </section>

  <!-- NOSOTROS -->
  <section class="sec" id="nosotros">
    <div class="container">
      <div class="sec-head" data-aos="fade-up">
        <p class="eyebrow">Nosotros</p>
        <h2>Somos tu mejor opción</h2>
        <p class="text-secondary">En FOI Tools Agroindustrial somos distribuidores de productos para la industria y el agro desde Firmat, Santa Fe. Llegamos a todo el país con piezas y repuestos pensados para trabajar duro.</p>
      </div>
      <div class="row g-4">
        <div class="col-md-4" data-aos="fade-up"><article class="pillar"><i class="bi bi-shield-check"></i><h3>Calidad que resiste</h3><p>Productos de alta resistencia y precisión para el trabajo diario en el campo y la planta.</p></article></div>
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="100"><article class="pillar"><i class="bi bi-truck"></i><h3>Envíos a todo el país</h3><p>Te lo llevamos a donde estés, con entrega a domicilio.</p></article></div>
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="200"><article class="pillar"><i class="bi bi-chat-dots"></i><h3>Atención directa</h3><p>Escribinos por WhatsApp y te asesoramos para encontrar la pieza justa.</p></article></div>
      </div>
    </div>
  </section>

  <!-- CONTACTO -->
  <section class="sec sec-alt" id="contacto">
    <div class="container">
      <div class="row g-5">
        <div class="col-lg-6" data-aos="fade-right">
          <p class="eyebrow">Contacto</p>
          <h2>¿Qué pieza necesitás?</h2>
          <p class="text-secondary">Contanos el modelo o escribinos tu consulta y te respondemos a la brevedad.</p>
          <ul class="list-unstyled contact-list">
            <li><i class="bi bi-telephone"></i><div><span>Teléfono</span><a href="tel:<?= htmlspecialchars($cfg['phone_link']) ?>"><?= htmlspecialchars($cfg['phone']) ?></a></div></li>
            <li><i class="bi bi-envelope"></i><div><span>Email</span><a href="mailto:<?= htmlspecialchars($cfg['email_to']) ?>"><?= htmlspecialchars($cfg['email_to']) ?></a></div></li>
            <li><i class="bi bi-geo-alt"></i><div><span>Ubicación</span><b><?= htmlspecialchars($cfg['ubicacion'] ?: 'Firmat, Santa Fe, Argentina') ?></b></div></li>
            <?php if (!empty($cfg['instagram'])): ?><li><i class="bi bi-instagram"></i><div><span>Instagram</span><a href="<?= htmlspecialchars($cfg['instagram']) ?>" target="_blank" rel="noopener"><?= htmlspecialchars($cfg['instagram']) ?></a></div></li><?php endif; ?>
            <?php if (!empty($cfg['facebook'])): ?><li><i class="bi bi-facebook"></i><div><span>Facebook</span><a href="<?= htmlspecialchars($cfg['facebook']) ?>" target="_blank" rel="noopener"><?= htmlspecialchars($cfg['facebook']) ?></a></div></li><?php endif; ?>
          </ul>
        </div>
        <div class="col-lg-6" data-aos="fade-left">
          <form class="contact-form" id="contactForm" novalidate>
            <h3 class="h4 mb-4">Envianos tu consulta</h3>
            <div class="row g-3">
              <div class="col-sm-6"><label class="form-label" for="cName">Nombre</label><input class="form-control" id="cName" name="name" required maxlength="100" autocomplete="name"></div>
              <div class="col-sm-6"><label class="form-label" for="cPhone">Teléfono <small class="text-secondary">(opcional)</small></label><input class="form-control" id="cPhone" name="phone" type="tel" maxlength="40" autocomplete="tel"></div>
              <div class="col-12"><label class="form-label" for="cEmail">Email</label><input class="form-control" id="cEmail" name="email" type="email" required autocomplete="email"></div>
              <div class="col-12"><label class="form-label" for="cMsg">Consulta</label><textarea class="form-control" id="cMsg" name="message" rows="4" required minlength="5" maxlength="3000"></textarea></div>
              <input type="text" name="website" class="d-none" tabindex="-1" autocomplete="off" aria-hidden="true">
              <div class="col-12"><p class="small text-secondary mb-0">Al enviar aceptás que usemos tus datos solo para responder tu consulta. Ver <a href="privacidad.php" class="link-danger">política de privacidad</a>.</p></div>
              <div class="col-12"><div id="formMsg" role="status" class="small"></div></div>
              <div class="col-12"><button class="btn btn-danger btn-lg rounded-pill px-5 w-100" type="submit" id="formBtn">Enviar consulta</button></div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>

</main>

<footer class="foot">
  <div class="foot-cta">
    <div class="container d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
      <div>
        <h2 class="mb-1">¿Necesitás una pieza?</h2>
        <p class="mb-0 foot-muted">Escribinos y te respondemos a la brevedad.</p>
      </div>
      <a class="btn btn-success rounded-pill px-4 align-self-md-center" data-wa target="_blank" rel="noopener"><i class="bi bi-whatsapp me-2"></i>Consultanos por WhatsApp</a>
    </div>
  </div>
  <div class="container foot-main">
    <div class="row g-4 g-lg-5">
      <div class="col-lg-4">
        <img class="foot-logo" src="img/logo.png" alt="FOI Tools Agroindustrial">
        <p class="foot-muted mt-3">Distribuidor de productos para la industria y el agro. Tu agro y tu industria, con la fuerza de nuestras piezas.</p>
        <div class="foot-social">
          <?php if (!empty($cfg['instagram'])): ?><a href="<?= htmlspecialchars($cfg['instagram']) ?>" target="_blank" rel="noopener" aria-label="Instagram"><i class="bi bi-instagram"></i></a><?php endif; ?>
          <?php if (!empty($cfg['facebook'])): ?><a href="<?= htmlspecialchars($cfg['facebook']) ?>" target="_blank" rel="noopener" aria-label="Facebook"><i class="bi bi-facebook"></i></a><?php endif; ?>
          <a data-wa target="_blank" rel="noopener" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
        </div>
      </div>
      <div class="col-6 col-lg-2">
        <h3 class="foot-title">Navegación</h3>
        <ul class="foot-list foot-icons">
          <li><i class="bi bi-house"></i><a href="#inicio">Inicio</a></li>
          <li><i class="bi bi-grid"></i><a href="#catalogo">Catálogo</a></li>
          <li><i class="bi bi-bag-check"></i><a href="#como">Cómo comprar</a></li>
          <li><i class="bi bi-people"></i><a href="#nosotros">Nosotros</a></li>
          <li><i class="bi bi-chat-dots"></i><a href="#contacto">Contacto</a></li>
        </ul>
      </div>
      <div class="col-6 col-lg-3">
        <h3 class="foot-title">Productos</h3>
        <ul class="foot-list foot-icons" id="footCats"></ul>
      </div>
      <div class="col-lg-3">
        <h3 class="foot-title">Contacto</h3>
        <ul class="foot-list foot-contact">
          <li><i class="bi bi-telephone"></i><a href="tel:<?= htmlspecialchars($cfg['phone_link']) ?>"><?= htmlspecialchars($cfg['phone']) ?></a></li>
          <li><i class="bi bi-envelope"></i><a href="mailto:<?= htmlspecialchars($cfg['email_to']) ?>"><?= htmlspecialchars($cfg['email_to']) ?></a></li>
          <li><i class="bi bi-geo-alt"></i><span>Firmat, Santa Fe, Argentina</span></li>
          <li><i class="bi bi-truck"></i><span>Envíos a todo el país</span></li>
        </ul>
      </div>
    </div>
  </div>
  <div class="foot-bottom">
    <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
      <span>© <span id="year"></span> FOI Tools Agroindustrial. Todos los derechos reservados.</span>
      <a href="privacidad.php">Política de privacidad</a>
    </div>
  </div>
</footer>

<!-- Volver arriba -->
<button class="to-top" id="toTop" type="button" aria-label="Volver arriba"><i class="bi bi-arrow-up"></i></button>

<!-- Botón flotante de WhatsApp -->
<a class="wa-float" data-wa="Hola! Vi su página web y quiero hacer una consulta." target="_blank" rel="noopener" aria-label="Escribinos por WhatsApp">
  <i class="bi bi-whatsapp"></i><span class="wa-tip">¿Te ayudamos?</span>
</a>

<!-- Mi consulta -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="cart" aria-labelledby="cartTitle">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title h4 mb-0" id="cartTitle">Mi consulta</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
  </div>
  <div class="offcanvas-body d-flex flex-column">
    <p class="text-secondary small" id="cartEmpty">Todavía no sumaste productos. Usá el botón <b>+ Consulta</b> en el catálogo.</p>
    <ul class="list-unstyled cart-list" id="cartList"></ul>
    <div class="mt-auto pt-3">
      <label class="form-label small" for="cartNote">Detalle (modelo, medidas, cantidad)</label>
      <textarea class="form-control mb-3" id="cartNote" rows="3"></textarea>
      <a class="btn btn-success rounded-pill w-100 mb-2" id="cartSend" target="_blank" rel="noopener"><i class="bi bi-whatsapp me-2"></i>Enviar consulta por WhatsApp</a>
      <button class="btn btn-link text-secondary w-100 btn-sm" id="cartClear" type="button">Vaciar</button>
    </div>
  </div>
</div>

<!-- Modal galería -->
<div class="modal fade" id="galleryModal" tabindex="-1" aria-labelledby="mTitle" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-fullscreen-lg-down">
    <div class="modal-content foi-modal">
      <button type="button" class="btn-close modal-x" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      <div class="row g-0">
        <div class="col-lg-8">
          <div class="modal-view">
            <button class="nav-btn prev" id="mPrev" aria-label="Anterior"><i class="bi bi-chevron-left"></i></button>
            <img id="mImg" alt="">
            <button class="nav-btn next" id="mNext" aria-label="Siguiente"><i class="bi bi-chevron-right"></i></button>
            <span class="counter" id="mCount"></span>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="modal-side">
            <p class="eyebrow" id="mTag"></p>
            <h3 id="mTitle" class="h3"></h3>
            <p class="text-secondary" id="mDesc"></p>
            <div class="thumbs" id="mThumbs"></div>
            <a class="btn btn-success rounded-pill w-100 mt-auto" id="mWa" target="_blank" rel="noopener"><i class="bi bi-whatsapp me-2"></i>Consultar este producto</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="vendor/bootstrap/bootstrap.bundle.min.js"></script>
<script src="vendor/aos/aos.js"></script>
<script src="js/products.js?v=<?= filemtime(__DIR__ . '/js/products.js') ?>"></script>
<script src="js/promos.js?v=<?= filemtime(__DIR__ . '/js/promos.js') ?>"></script>
<script src="js/main.js?v=<?= filemtime(__DIR__ . '/js/main.js') ?>"></script>
</body>
</html>
