<?php
require __DIR__ . '/admin/class/Conexion.php';
require __DIR__ . '/admin/class/Config.php';
$cfg = Config::all();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Política de privacidad | FOI Tools Agroindustrial</title>
  <meta name="robots" content="noindex">
  <link rel="icon" href="img/icons/favicon.ico" sizes="any">
  <link rel="stylesheet" href="vendor/bootstrap/bootstrap.min.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/extra.css">
</head>
<body>
<nav class="navbar foi-nav scrolled"><div class="container">
  <a class="navbar-brand" href="index.php"><img src="img/logo-foitools.png" alt="FOI Tools Agroindustrial"></a>
  <a class="btn btn-outline-dark rounded-pill" href="index.php">Volver al sitio</a>
</div></nav>
<main class="container" style="padding:130px 12px 70px;max-width:820px">
  <p class="eyebrow">Legal</p>
  <h1 class="mb-4" style="font-size:clamp(2rem,5vw,3rem)">Política de privacidad</h1>
  <p class="text-secondary">Última actualización: <?= date('d/m/Y') ?></p>
  <h2 class="h4 mt-4">Qué datos recopilamos</h2>
  <p>Cuando usás el formulario de contacto te pedimos tu nombre, email, teléfono (opcional) y el texto de tu consulta. Si nos escribís por WhatsApp, la conversación se rige por las políticas de esa plataforma.</p>
  <h2 class="h4 mt-4">Para qué los usamos</h2>
  <p>Únicamente para responder tu consulta y brindarte información sobre nuestros productos. No vendemos ni cedemos tus datos a terceros.</p>
  <h2 class="h4 mt-4">Servicios de terceros</h2>
  <p>El sitio puede utilizar herramientas de estadísticas (por ejemplo, Google Analytics) y de publicidad (por ejemplo, el píxel de Meta) que emplean cookies para medir visitas. La lista de productos que sumás a "Mi consulta" se guarda solo en tu navegador.</p>
  <h2 class="h4 mt-4">Tus derechos</h2>
  <p>Podés pedir acceso, corrección o eliminación de tus datos escribiendo a <a class="link-danger" href="mailto:<?= htmlspecialchars($cfg['email_to']) ?>"><?= htmlspecialchars($cfg['email_to']) ?></a>.</p>
  <p class="small text-secondary mt-4">Texto orientativo. Conviene que lo revise quien lleve la parte legal del negocio.</p>
</main>
</body>
</html>
