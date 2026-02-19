<?php 
ob_start();
include_once __DIR__ . '/../API/config/config.php';
include_once ROOT_PATH. '/../API/middleware/cerrarsesiones.php';
include_once ROOT_PATH. '/../API/models/FuncionesGenerales.php';
$generales = new FuncionesGenerales();
include 'header.php'; 
$generales->ObtenerEstilosWeb(0);
// $generales->ObtenerEstilosWeb(3);
echo '<script>var apiKey ="'.TOKENWEB.'";</script>'

?>


<style>
    body {
            background: url('https://images.unsplash.com/photo-1528605248644-14dd04022da1') no-repeat center center/cover;
            backdrop-filter: blur(3px);
            font-family: 'Poppins', sans-serif;
        }
        
        .bg-overlay {
            background: rgba(0, 0, 0, 0.6);
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
        }
</style>

<body>

    <div class="bg-overlay"></div>

    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="login-container shadow-lg">

            <div class="text-center mb-4">
                <i class="bi bi-person-circle display-3 text-danger"></i>
                <h1 class="login-title mt-2">Bienvenido</h3>
                    <p class="text-danger">Inicia sesión en tu cuenta</p>
            </div>

            <!-- <h2 class="login-title">Restaurante</h2> -->

            <form id="loginForm" action="admin_dashboard/index.php" >
                <!-- Usuario -->
                <div class="mb-3 position-relative">
                    <i class="bi bi-person-fill input-icon"></i>
                    <input type="text" class="form-control" id="usuario" placeholder="Usuario" required>
                </div>

                <!-- Contraseña -->
                <div class="mb-3 position-relative">
                    <!-- <div class="input-group"> -->
                    <i class="bi bi-lock-fill input-icon"></i>
                    <input type="password" class="form-control" id="password" placeholder="Contraseña" required>
                    <!-- <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                        <i class="bi bi-eye"></i>
                        </button> -->
                    <!-- </div> -->
                </div>

                <!-- Botón -->
                <div class="d-grid">
                    <button type="submit" class="btn btn-danger btn-login fw-bold">Ingresar</button>
                </div>

                <!-- Enlaces -->
                <div class="text-center mt-3">
                    <!-- <p class="text-center mt-3">
                        ¿No tienes cuenta? <a href="registro.php" class="text-decoration-none">Regístrate</a>
                    </p> -->
                    <a href="opciones_recuperacion_pass.php">¿Olvidaste tu contraseña?</a>
                </div>

            </form>

        </div>
    </div>


    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JavaScript Propio -->
    <script>
        // document.getElementById("loginForm").addEventListener("submit", function(e) {
        //     e.preventDefault();

        //     let usuario = document.getElementById("usuario").value.trim();
        //     let password = document.getElementById("password").value.trim();

        //     if (usuario === "" || password === "") {
        //         alert("Debe completar todos los campos");
        //         return;
        //     }

        //     // Simulación
        //     if (usuario === "admin" && password === "1234") {
        //         alert("Inicio de sesión exitoso ✔️");
        //         window.location.href = "index.html"; // Redirige al Home o Panel
        //     } else {
        //         alert("Usuario o contraseña incorrectos ❌");
        //     }
        // });
    </script>

    <!-- <?php include 'script_generales.php'; $generales->ObtenerScriptWeb(1);?> -->
    

</body>

</html>