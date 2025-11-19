<?php 
ob_start();
include_once '../middleware/cerrarsesiones.php';
include_once '../config/config.php';
include_once '../models/FuncionesGenerales.php';
$generales = new FuncionesGenerales();
include 'header_form.php'; 
$generales->ObtenerEstilosWeb(1);
$generales->ObtenerEstilosWeb(3);
echo '<script>var apiKey ="'.TOKENWEB.'";</script>'
?>


<body>
"
    <div class="login-card">
        <div class="text-center mb-4">
            <i class="bi bi-person-circle display-3 text-primary"></i>
            <h3 class="mt-2">Bienvenido</h3>
            <p class="text-muted">Inicia sesión en tu cuenta</p>
        </div>

        <form id="fromEnvio">
            <div class="mb-3">
                <label for="alias" class="form-label">Correo electrónico</label>
                <input type="text" class="form-control" id="alias" placeholder="user o e-mail" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" placeholder="********" required>
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
            <i class="bi bi-eye"></i>
          </button>
                </div>
            </div>

            <div class="d-flex justify-content-between mb-3">
                <!-- <div>
                    <input type="checkbox" id="remember">
                    <label for="remember" class="ms-1">Recordarme</label>
                </div> -->
                <!-- <a href="recuperar_pass.html" class="text-decoration-none">¿Olvidaste tu contraseña?</a> -->
            </div>

            <button type="submit" class="btn btn-custom w-100 text-white">Ingresar</button>
        </form>

        <hr class="my-4">

        <!-- <button class="btn btn-outline-danger google-btn w-100 mb-2">
            <i class="bi bi-google me-2"></i> Iniciar con Google
        </button> -->

        <p class="text-center mt-3">
            ¿No tienes cuenta? <a href="registro.php" class="text-decoration-none">Regístrate</a>
        </p>
    </div>

    
    <?php include 'script_generales.php'; $generales->ObtenerScriptWeb(1);?>
    

</body>

</html>