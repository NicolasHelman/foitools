<?php
header('Content-Type: text/plain; charset=utf-8');
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$base = $scheme . '://' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/';
?>
User-agent: *
Allow: /
Disallow: /lib/
Disallow: /config.php
Disallow: /contact.php

Sitemap: <?= $base ?>sitemap.xml
