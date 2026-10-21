<?php
session_start();
if (empty($_SESSION['admin_ok'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php require_once "includes/meta.php"; ?>
    <title>FOI Tools | Panel Admin</title>
    <?php require_once "includes/links.php"; ?>
</head>
<body>

<div class="wrapper sidebar_minimize">

    <div class="sidebar">
        <?php require_once "includes/sidebar-logo.php"; ?>
        <?php require_once "includes/sidebar-wrapper.php"; ?>
    </div>

    <div class="main-panel">
        <?php require_once "includes/main-header.php"; ?>

        <?php
        $paginas = ['productos', 'categorias', 'configuracion', 'auditoria', 'usuarios'];
        $a = $_REQUEST['a'] ?? '';
        if ($a !== '' && in_array($a, $paginas, true)) {
            require_once $a . '.php';
        } else {
            require_once "includes/home.php";
        }
        ?>

        <?php require_once "includes/footer.php"; ?>
    </div>

</div>

<?php require_once "includes/scripts.php"; ?>
<script src="funciones/funciones.js"></script>

</body>
</html>
