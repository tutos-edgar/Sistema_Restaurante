<div class="sidebar" id="sidebar">
    <div class="text-center py-4" id="logoSection">
        <img src="https://upload.wikimedia.org/wikipedia/commons/e/e1/Logo_of_YouTube_%282015-2017%29.svg"
             alt="Logo View Youtube"
             class="img-fluid"
             style="width:80px; height:auto;">
        <h5 class="mt-2" id="title_dash">View Youtube</h5>
    </div>

    <a class="nav-link <?= ($current == 'index.php') ? 'active' : '' ?>" href="index.php">
        <i class="bi bi-house-door"></i> <span>Inicio</span>
    </a>

    <a class="nav-link <?= ($current == 'menu_generar_vistas.php') ? 'active' : '' ?>" href="menu_generar_vistas.php">
        <i class="bi bi-play-btn"></i> <span>Generar Vistas</span>
    </a>

    <a class="nav-link <?= ($current == 'menu_ganar_vistas.php') ? 'active' : '' ?>" href="menu_ganar_vistas.php">
        <i class="fas fa-eye"></i> <span>Ganar Vistas</span>
    </a>

    <a class="nav-link <?= in_array($current, $paginasCanal) ? 'active' : '' ?>" href="menu_canal.php">
        <i class="bi bi-broadcast"></i> <span>Canales</span>
    </a>

    <a class="nav-link <?= in_array($current, $paginasVideo) ? 'active' : '' ?>" href="menu_video.php">
        <i class="bi bi-camera-reels"></i> <span>Videos</span>
    </a>

     <a class="nav-link <?= ($current == 'menu_ejecutar_tareas.php') ? 'active' : '' ?>" href="menu_ejecutar_tareas.php">
        <i class="bi bi-hourglass-split"></i> <span>Tareas Pendientes</span>
    </a>

    <!-- <a class="nav-link <?= in_array($current, $paginasNotificaiones) ? 'active' : '' ?>" href="menu_notificaciones_user.php">
        <i class="bi bi bi-bell"></i> <span>Notificaciones</span>
    </a>   -->

    <a class="nav-link <?= in_array($current, $paginasConfirguracion) ? 'active' : '' ?>" href="menu_configuracion_user.php">
        <i class="bi bi-gear"></i> <span>Configuración</span>
    </a>

    <a class="nav-link" href="../../middleware/logout.php" id="logoutLink">
        <i class="bi bi-box-arrow-right"></i> <span>Cerrar sesión</span>
    </a>
</div>


<!-- <div class="sidebar" id="sidebar">
    <div class="text-center py-4" id="logoSection">
        <img src="https://upload.wikimedia.org/wikipedia/commons/e/e1/Logo_of_YouTube_%282015-2017%29.svg"
             alt="Logo View Youtube"
             class="img-fluid"
             style="width:80px; height:auto;">
        <h5 class="mt-2" id="title_dash">View Youtube</h5>
    </div>

    <a class="nav-link <?= ($current == 'index.php') ? 'active' : '' ?>" href="index.php">
        <i class="bi bi-house-door"></i> <span>Inicio</span>
    </a>

    <a class="nav-link <?= ($current == 'generar.php') ? 'active' : '' ?>" href="#">
        <i class="bi bi-play-btn"></i> <span>Generar Vistas</span>
    </a>

    <a class="nav-link <?= ($current == 'menu_canal.php') ? 'active' : '' ?>" href="menu_canal.php">
        <i class="bi bi-broadcast"></i> <span>Canales</span>
    </a>

    <a class="nav-link <?= ($current == 'menu_video.php') ? 'active' : '' ?>" href="menu_video.php">
        <i class="bi bi-camera-reels"></i> <span>Videos</span>
    </a>

    <a class="nav-link <?= ($current == 'notificaciones.php') ? 'active' : '' ?>" href="notificaciones.php">
        <i class="bi bi-calendar-check"></i> <span>Notificaciones</span>
    </a>

    <a class="nav-link <?= ($current == 'reportes.php') ? 'active' : '' ?>" href="reportes.php">
        <i class="bi bi-graph-up"></i> <span>Reportes</span>
    </a>

    <a class="nav-link <?= ($current == 'configuracion.php') ? 'active' : '' ?>" href="configuracion.php">
        <i class="bi bi-gear"></i> <span>Configuración</span>
    </a>

    <a class="nav-link" href="logout.php">
        <i class="bi bi-box-arrow-right"></i> <span>Cerrar sesión</span>
    </a>
</div> -->



<!-- Sidebar -->
    <!-- <div class="sidebar" id="sidebar">
        <div class="text-center py-4" id="logoSection"> -->
            <!-- <i class="bi bi-speedometer2 fs-2"></i> -->
            <!-- <img src="https://upload.wikimedia.org/wikipedia/commons/e/e1/Logo_of_YouTube_%282015-2017%29.svg" alt="Logo View Youtube" class="img-fluid" style="width:80px; height:auto;">
            <h5 class="mt-2" id="title_dash">View Youtube</h5>
        </div> -->

        <!-- <a class="nav-link" href="#" class="active"><i class="bi bi-house-door"></i> <span>Inicio</span></a>
        <a class="nav-link" href="#"><i class="bi bi-play-btn"></i> <span>Generar Vistas</span></a>
        <a class="nav-link" href="menu_canal.php"><i class="bi bi-broadcast"></i> <span>Canales</span></a>
        <a class="nav-link" href="menu_video.php"><i class="bi bi-camera-reels"></i> <span>Videos</span></a>
        <a class="nav-link" href="#"><i class="bi bi-calendar-check"></i> <span>Notificaciones</span></a>
        <a class="nav-link" href="#"><i class="bi bi-graph-up"></i> <span>Reportes</span></a>
        <a class="nav-link" href="#"><i class="bi bi-gear"></i> <span>Configuración</span></a>
        <a class="nav-link" href="#"><i class="bi bi-box-arrow-right"></i> <span>Cerrar sesión</span></a> -->

        <!-- <ul class="submenu">
            <li><a href="#">Agregar Usuario</a></li>
            <li><a href="#">Lista de Usuarios</a></li>
            <li>
                <a href="#" class="has-submenu">Más Opciones <i class="bi bi-chevron-down float-end"></i></a>
                <ul class="submenu">
                    <li><a href="#">Opciones 1</a></li>
                    <li><a href="#">Opciones 2</a></li>
                </ul>
            </li>
        </ul> -->

    </div>