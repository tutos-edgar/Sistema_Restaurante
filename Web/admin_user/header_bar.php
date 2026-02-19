<!-- Header -->
 <div class="header">
    <button class="btn btn-outline-dark custom-btn" id="toggleBtn"><i class="bi bi-list"></i></button>
    <h1>Bienvenido <?php //echo $aliasUsuario; ?> </h1>
    <div class="icons">
        <!-- <a href="menu_notificaciones_user.php" title="Notificaciones">
            <i class="bi bi-bell-fill fs-5"></i>
        </a> -->
        <div class="position-relative dropdown">
            <div class="notif-bell" id="notifBell" data-bs-toggle="dropdown" aria-expanded="false" style="cursor:pointer;">
                <i class="bi bi-bell fs-5"></i>
                <span class="badge rounded-pill bg-danger notif-badge">0</span>
            </div>
            <!-- Ventanita de notificaciones -->
            <!-- <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="notifBell" style="width:300px; max-height:300px; overflow-y:auto;">
                <li><a class="dropdown-item" href="#">🔔 Nueva cotización recibida</a></li>
                <li><a class="dropdown-item" href="#">📩 Tienes un nuevo mensaje</a></li>
                <li><a class="dropdown-item" href="#">✅ Tu perfil fue actualizado</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-center text-primary" href="#">Ver todas las notificaciones</a></li>
            </ul> -->
        </div>
        
        <a href="menu_perfil_user.php" title="Perfil">
            <i class="bi bi-person-circle fs-5"></i>
        </a>
    </div>
</div>
       
 


