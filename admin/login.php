<?php
session_start();
if (!empty($_SESSION['admin_ok'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php require_once "includes/meta.php"; ?>
    <title>FOI Tools | Admin Login</title>
    <?php require_once "includes/links.php"; ?>
</head>
<body>
    <?php require_once "includes/login.php"; ?>
    <?php require_once "includes/scripts.php"; ?>
    <script src="funciones/funciones.js"></script>
</body>
</html>
