<?php 
ob_start();
include_once __DIR__.'/../API/middleware/cerrarsesiones.php';
include_once __DIR__.'/../API/config/init_config.php';

$generales = new FuncionesGenerales();
include 'header_form.php'; 
$generales->ObtenerEstilosWeb(1);
$generales->ObtenerEstilosWeb(3);
echo '<script>var apiKey ="'.TOKENWEB.'";</script>'
?>

<body>
    <div class="register-card">
        <div class="text-center mb-4">
            <i class="bi bi-person-plus-fill display-3 text-primary"></i>
            <h3 class="mt-2">Crear cuenta</h3>
            <p class="text-muted">Rellena los campos para registrarte</p>
        </div>

        <form id="fromEnvio">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="nombre" placeholder="Juan" required />
                </div>
                <div class="col-md-6">
                    <label for="apellido" class="form-label">Apellido</label>
                    <input type="text" class="form-control" id="apellido" placeholder="Pérez" required />
                </div>
                <div class="col-12">
                    <label for="email" class="form-label">Correo electrónico</label>
                    <input type="email" class="form-control" id="email" placeholder="ejemplo@gmail.com" required />
                </div>

                <div class="col-md-8">
                    <label for="apellido" class="form-label">Usuario</label>
                    <input type="text" class="form-control" id="alias" placeholder="Alias" required />
                </div>

                <div class="col-12">
                    <label for="password" class="form-label">Contraseña</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" placeholder="********" required />
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="col-12">
                    <label for="confirmPassword" class="form-label">Confirmar contraseña</label>
                    <input type="password" class="form-control" id="confirmPassword" placeholder="********" required />
                </div>

                <!-- <div class="col-md-10">
                    <label for="apellido" class="form-label">Nombre Canal</label>
                    <input type="text" class="form-control" id="apellido" placeholder="Alias" required />
                </div>

                <div class="col-md-12">
                    <label for="apellido" class="form-label">Descripción Canal</label>
                    <textarea name="" id="" class="form-control"></textarea>
                    <input type="textarea" class="form-control" id="apellido" placeholder="Alias" required />
                </div> -->
            </div>

            <div class="form-check mt-3">
                <input class="form-check-input" type="checkbox" id="terminos" required />
                <label class="form-check-label" for="terminos" id="labelTerminos">Acepto los<a data-bs-toggle="modal" data-bs-target="#termsModal" href="#" class="text-decoration-none">Términos y Condiciones</a>
                </label>
            </div>

            <button type="submit" class="btn btn-custom w-100 text-white mt-3">
                Registrarse
            </button>
        </form>

        <hr class="my-4" />

        <!-- <button class="btn btn-outline-danger google-btn w-100 mb-2">
            <i class="bi bi-google me-2"></i> Registrarse con Google
        </button> -->

        <p class="text-center mt-3">
            ¿Ya tienes cuenta?
            <a href="login.php" class="text-decoration-none">Inicia sesión</a>
        </p>
    </div>

    <!-- Modal Términos y Condiciones -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="termsModalLabel">Términos y Condiciones</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <?php 
                        $generales->TerminosPoliticas();
                    ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-custom" id="btnCerrarModal" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>


    <?php 
        include 'script_generales.php';    
        $generales->ObtenerScriptWeb(2);
    ?>

</body>

</html>