<div class="main-header">
    <div class="main-header-logo">
        <div class="logo-header">
            <a href="index.php" class="logo">
                <img src="../img/logo-foitools.png" alt="FOI Tools" class="navbar-brand" height="28">
            </a>
            <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                    <i class="fas fa-caret-left icon-sidebar-colapsar"></i>
                    <i class="fas fa-caret-right icon-sidebar-expandir"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                    <i class="fas fa-bars icon-menu-abrir"></i>
                    <i class="fas fa-times icon-menu-cerrar"></i>
                </button>
            </div>
            <button class="topbar-toggler more">
                <i class="gg-more-vertical-alt"></i>
            </button>
        </div>
    </div>

    <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
        <div class="container-fluid">
            <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">

                <li class="nav-item topbar-icon dropdown hidden-caret">
                    <a onclick="fullscreen()" class="nav-link" href="#" role="button">
                        <i class="fas fa-expand" id="expand-icon"></i>
                        <i class="fas fa-compress" id="compress-icon" style="display:none;"></i>
                    </a>
                </li>

                <li class="nav-item topbar-user dropdown hidden-caret">
                    <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                        <div class="avatar-sm">
                            <img src="assets/img/user.png" alt="Admin" class="avatar-img rounded-circle">
                        </div>
                        <span class="profile-username">
                            <span class="fw-bold"><?php echo htmlspecialchars($_SESSION['admin_usuario']); ?></span>
                            <span class="op-7"><i class="fas fa-caret-down fa-sm"></i></span>
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-user animated fadeIn">
                        <div class="dropdown-user-scroll scrollbar-outer">
                            <li>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="../index.php" target="_blank">
                                    <i class="fas fa-external-link-alt me-1"></i> Ver Sitio
                                </a>
                                <a class="dropdown-item" href="logout.php">
                                    <i class="fas fa-power-off me-1"></i> Salir
                                </a>
                            </li>
                        </div>
                    </ul>
                </li>

            </ul>
        </div>
    </nav>
</div>
