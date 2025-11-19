<?php 
ob_start();
include_once '../models/FuncionesGenerales.php';
$generales = new FuncionesGenerales();
include_once 'header_form.php'; 
$generales->ObtenerEstilosWeb(0);
?>



<body>

    <!-- Barra superior fija -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">VideoBoost</a>
            <button class="btn btn-outline-light ms-auto" type="button" onclick="window.location.href='login.php'">Login</button>
        </div>
    </nav>

    <div class="container py-5 text-center">
        <h1 class="mb-4 fw-bold titulo-principal"><span style="color:#fff200;">VideoBoost</span></h1>
        <p class="mb-5 fs-5">Aquí puedes colocar tus videos y generar vistas de manera sencilla y rápida.</p>

        <div class="row justify-content-center g-4">
            <div class="col-6 col-md-3">
                <div class="card p-4 icon-card shadow-sm" data-bs-toggle="tooltip" title="Canal" onclick="scrollToSection('canal')">
                    <i class="bi bi-broadcast fs-1 text-danger"></i>
                    <p class="mt-2 mb-0">Canal</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card p-4 icon-card shadow-sm" data-bs-toggle="tooltip" title="Video" onclick="scrollToSection('video')">
                    <i class="bi bi-camera-video fs-1 text-danger"></i>
                    <p class="mt-2 mb-0">Video</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card p-4 icon-card shadow-sm" data-bs-toggle="tooltip" title="Short" onclick="scrollToSection('short')">
                    <i class="bi bi-bookmark-check-fill fs-1 text-danger"></i>
                    <p class="mt-2 mb-0">Short</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card p-4 icon-card shadow-sm" data-bs-toggle="tooltip" title="Vistas" onclick="scrollToSection('vistas')">
                    <i class="bi bi-eye fs-1 text-danger"></i>
                    <p class="mt-2 mb-0">Vistas</p>
                </div>
            </div>
        </div>

        <!-- Secciones de descripción -->
        <div class="container-seccion-inicial">
            <div id="canal" class="section">
                <h2>Canal</h2>
                <p>Gestiona tu canal, crea tu perfil y organiza tus videos en playlists. Mantén tu contenido centralizado y accesible para tus seguidores.</p>
            </div>

            <div id="video" class="section">
                <h2>Video</h2>
                <p>Sube tus enlaces de los videos que deseas generar vistas fácilmente, ajusta la calidad y agrega títulos y descripciones. Tu contenido estará listo para generar vistas rápidamente.</p>
            </div>

            <div id="short" class="section">
                <h2>Short</h2>
                <p>Puedes subir tus clips cortos de tus videos en tu canal de YouTube y generar vistas también en ellos. Los shorts son ideales para captar atención rápidamente y aumentar tu alcance en poco tiempo.</p>
            </div>

            <div id="vistas" class="section">
                <h2>Vistas</h2>
                <p>Observa y genera vistas para tus videos. Monitorea el desempeño y mejora el alcance de tu contenido con estadísticas claras.</p>
            </div>
        </div>

    </div>

    <!-- Footer -->
    <footer>
        &copy; 2025 VideoBoost. Todos los derechos reservados.
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/funcion_index_principal.js">
    </script>

</body>

</html>
