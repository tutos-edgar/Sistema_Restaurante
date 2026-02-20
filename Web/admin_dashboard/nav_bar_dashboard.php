<!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow">
        <div class="container-fluid">

            <button class="btn btn-outline-light me-3" id="toggleSidebar">
                <i class="bi bi-list"></i>
            </button>

            <span class="navbar-brand fw-bold">ADMINISTRACIÓN RESTAURANTE</span>

            <div class="ms-auto d-flex align-items-center">

                <!-- NOTIFICACIONES -->
                <div class="dropdown me-4">
                    <button class="btn btn-dark position-relative" data-bs-toggle="dropdown">
                        <i class="bi bi-bell fs-5"></i>
                        <span class="badge bg-danger position-absolute top-0 start-100 translate-middle" id="contadorNotificaciones">3</span>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end notificaciones">
                        <li class="dropdown-header fw-bold">Notificaciones</li>

                        <li><a class="dropdown-item" href="notificacion.html">
                            🛒 Nuevo pedido recibido
                            <small class="text-muted d-block">Hace 2 min</small>
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item" href="notificacion.html">
                            📦 Pedido enviado
                            <small class="text-muted d-block">Hace 10 min</small>
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item" href="notificacion.html">
                            ⭐ Nueva reseña
                            <small class="text-muted d-block">Hace 1 hora</small>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- USUARIO -->
                <div class="dropdown">
                    <a class="d-flex align-items-center text-white text-decoration-none" data-bs-toggle="dropdown">
                        <img src="https://i.pravatar.cc/40" class="rounded-circle me-2">
                        <div class="text-start">
                            <div class="fw-semibold">Juan Pérez</div>
                            <small class="text-muted"><span class="text-white">Administrador</span></small>
                        </div>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#">Mi perfil</a></li>
                        <!-- <li><a class="dropdown-item" href="#">Configuración</a></li> -->
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-danger" href="#">Cerrar sesión</a></li>
                    </ul>

                    
                </div>

            </div>
        </div>
    </nav>