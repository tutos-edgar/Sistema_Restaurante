<?php
// Asegúrate de que no haya espacios, líneas en blanco o HTML antes de esto
// header("Location: ../index.php");
// exit; // Siempre usar exit después de header para detener la ejecución
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Iconos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <link href="../css/estyle_dashboard.css" rel="stylesheet">
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="text-center py-4" id="logoSection">
            <!-- <i class="bi bi-speedometer2 fs-2"></i> -->
            <img src="https://upload.wikimedia.org/wikipedia/commons/e/e1/Logo_of_YouTube_%282015-2017%29.svg" alt="Logo View Youtube" class="img-fluid" style="width:80px; height:auto;">
            <h5 class="mt-2" id="title_dash">View Youtube</h5>
        </div>

        <!-- <button id="toggleLogo" class="btn btn-primary mt-3">Ocultar/Mostrar Logo</button>

        <script>
            const logoSection = document.getElementById('logoSection');
            const toggleBtn = document.getElementById('toggleLogo');

            toggleBtn.addEventListener('click', () => {
                // Alternar visibilidad usando clase d-none de Bootstrap
                logoSection.classList.toggle('d-none');
            });
        </script> -->

        <a href="#" class="active"><i class="bi bi-house-door"></i> <span>Inicio</span></a>
        <a href="#"><i class="bi bi-people"></i> <span>Usuarios</span></a>
        <a href="#"><i class="bi bi-graph-up"></i> <span>Reportes</span></a>
        <a href="#"><i class="bi bi-calendar-check"></i> <span>Citas</span></a>
        <a href="#"><i class="bi bi-gear"></i> <span>Configuración</span></a>
        <a href="#"><i class="bi bi-box-arrow-right"></i> <span>Cerrar sesión</span></a>

        <ul class="submenu">
            <li><a href="#">Agregar Usuario</a></li>
            <li><a href="#">Lista de Usuarios</a></li>
            <li>
                <a href="#" class="has-submenu">Más Opciones <i class="bi bi-chevron-down float-end"></i></a>
                <ul class="submenu">
                    <li><a href="#">Opciones 1</a></li>
                    <li><a href="#">Opciones 2</a></li>
                </ul>
            </li>
        </ul>

    </div>

    <!-- Contenido -->
    <div class="content" id="content">
        <!-- Header -->
        <div class="header">
            <button class="btn btn-outline-dark" id="toggleBtn"><i class="bi bi-list"></i></button>
            <div>
                <i class="bi bi-bell me-3 fs-5"></i>
                <i class="bi bi-person-circle fs-5"></i>
            </div>
        </div>

        <!-- Dashboard Content -->
        <div class="container-fluid mt-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="card card-custom text-center p-3">
                        <i class="bi bi-people fs-1 text-primary"></i>
                        <h5 class="mt-2">Usuarios</h5>
                        <p class="fs-4 fw-bold">120</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-custom text-center p-3">
                        <i class="bi bi-graph-up-arrow fs-1 text-success"></i>
                        <h5 class="mt-2">Ventas</h5>
                        <p class="fs-4 fw-bold">$3,200</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-custom text-center p-3">
                        <i class="bi bi-calendar-check fs-1 text-warning"></i>
                        <h5 class="mt-2">Citas</h5>
                        <p class="fs-4 fw-bold">45</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-custom text-center p-3">
                        <i class="bi bi-chat-left-text fs-1 text-info"></i>
                        <h5 class="mt-2">Mensajes</h5>
                        <p class="fs-4 fw-bold">18</p>
                    </div>
                </div>
            </div>

            <!-- Tabla -->
            <div class="card card-custom mt-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Usuarios recientes</h5>
                </div>
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Fecha Registro</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Ana Pérez</td>
                                <td>ana@example.com</td>
                                <td>2025-08-20</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Carlos López</td>
                                <td>carlos@example.com</td>
                                <td>2025-08-22</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>María Gómez</td>
                                <td>maria@example.com</td>
                                <td>2025-08-25</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sidebar = document.getElementById("sidebar");
        const content = document.getElementById("content");
        const toggleBtn = document.getElementById("toggleBtn");
        const titleDash = document.getElementById('title_dash');
        const logoSection = document.getElementById('logoSection');

        toggleBtn.addEventListener("click", () => {
            sidebar.classList.toggle("collapsed");
            content.classList.toggle("collapsed-sidebar");
            titleDash.classList.toggle('d-none');
        });

        // Seleccionamos todos los enlaces del sidebar
        const sidebarLinks = document.querySelectorAll('.sidebar a');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', function() {
                // Primero quitamos 'active' de todos
                sidebarLinks.forEach(l => l.classList.remove('active'));

                // Luego agregamos 'active' al que se hizo clic
                this.classList.add('active');
            });
        });
    </script>
</body>

</html>