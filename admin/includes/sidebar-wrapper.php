<div class="sidebar-wrapper scrollbar scrollbar-inner">
    <div class="sidebar-content">

        <div class="sidebar-search-wrapper" onclick="expandirSidebarYBuscar()">
            <div class="sidebar-search-group">
                <i class="fas fa-search sidebar-search-icon"></i>
                <input type="text" id="sidebar-search-input" class="sidebar-search-input" placeholder="Buscar en el menú" oninput="filtrarSidebar(this.value)" autocomplete="off">
            </div>
        </div>

        <ul class="nav nav-primary">

            <li class="nav-item">
                <a href="index.php">
                    <i class="fas fa-home"></i>
                    <p>Inicio</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="index.php?a=productos">
                    <i class="fas fa-box-open"></i>
                    <p>Productos</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="index.php?a=categorias">
                    <i class="fas fa-tags"></i>
                    <p>Categorías</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="index.php?a=usuarios">
                    <i class="fas fa-users"></i>
                    <p>Usuarios</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="index.php?a=configuracion">
                    <i class="fas fa-cog"></i>
                    <p>Configuración</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="index.php?a=auditoria">
                    <i class="fas fa-history"></i>
                    <p>Auditoría</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="../index.php" target="_blank">
                    <i class="fas fa-external-link-alt"></i>
                    <p>Ver Sitio</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="logout.php">
                    <i class="fas fa-power-off"></i>
                    <p>Salir</p>
                </a>
            </li>

        </ul>
    </div>
</div>
