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
             color: #fff;
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

    <div class="container-recuperacion py-5">

        <!-- =================== OPCIONES =================== -->
        <div id="opciones">
            <h2 class="text-center mb-4">🍽️ Recuperar Contraseña</h2>
            <p class="text-center text-muted mb-5"><span class="seleccionar">Selecciona un método</span></p>

            <div class="row g-4">

                <div class="col-md-3">
                    <div class="card-option p-4 text-center" onclick="mostrar('token')">
                        <i class="bi bi-key fs-1"></i>
                        <h6 class="mt-3">Por Token</h6>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card-option p-4 text-center" onclick="mostrar('correo')">
                        <i class="bi bi-envelope fs-1"></i>
                        <h6 class="mt-3">Por Correo</h6>
                    </div>
                </div>

                <!-- <div class="col-md-3">
                    <div class="card-option p-4 text-center" onclick="mostrar('telefono')">
                        <i class="bi bi-phone fs-1"></i>
                        <h6 class="mt-3">Por Teléfono</h6>
                    </div>
                </div> -->

                <div class="col-md-3">
                    <div class="card-option p-4 text-center" onclick="mostrar('pregunta')">
                        <i class="bi bi-shield-lock fs-1"></i>
                        <h6 class="mt-3">Pregunta Secreta</h6>
                    </div>
                </div>

            </div>
        </div>

        <!-- =================== TOKEN =================== -->
        <div id="token" class="hidden">
            <div class="form-box">
                <h4 class="mb-3">🔑 Recuperar por Token</h4>
                <div class="mb-3 position-relative">
                    <i class="bi bi-shield-lock-fill input-icon"></i>
                    <input class="form-control mb-3" placeholder="Token recibido">
                </div>
                
                <div class="mb-3 position-relative">
                    <i class="bi bi-lock-fill input-icon"></i>
                    <input type="password" class="form-control mb-3" placeholder="Nueva contraseña">
                </div>
                
                <button class="btn btn-main w-100">Restablecer</button>
                <div class="text-center mt-3"><a class="link-red" href="#" onclick="volver()">← Volver</a></div>
            </div>
        </div>

        <!-- =================== CORREO =================== -->
        <div id="correo" class="hidden">
            <div class="form-box">
                <h4 class="mb-3">📧 Recuperar por Correo</h4>
                <div class="mb-3 position-relative">
                    <i class="bi bi-envelope-at-fill input-icon"></i>
                    <input type="email" class="form-control mb-3" placeholder="Correo registrado">
                </div>                
                <button class="btn btn-main w-100">Enviar correo</button>
                <div class="text-center mt-3"><a class="link-red" href="#" onclick="volver()">← Volver</a></div>
            </div>
        </div>

        <!-- =================== TELÉFONO =================== -->
        <div id="telefono" class="hidden">
            <div class="form-box">
                <h4 class="mb-3">📱 Recuperar por Teléfono</h4>
                <div class="mb-3 position-relative">
                    <i class="bi bi-telephone-fill input-icon"></i>
                    <input type="tel" class="form-control mb-3" placeholder="Número telefónico">
                </div> 
                <div class="mb-3 position-relative">
                    <i class="bi bi-tablet-landscape-fill input-icon"></i>
                    <input class="form-control mb-3" placeholder="Código SMS">
                </div>                
                <button class="btn btn-main w-100">Validar</button>
                <div class="text-center mt-3"><a class="link-red" href="#" onclick="volver()">← Volver</a></div>
            </div>
        </div>

        <!-- =================== PREGUNTA =================== -->
        <div id="pregunta" class="hidden">
            <div class="form-box">
                <h4 class="mb-3">🛡️ Pregunta Secreta</h4>
                <div class="mb-3 position-relative">
                    <i class="bi bi-person-lines-fill input-icon"></i>
                    <input class="form-control mb-3" placeholder="Usuario">
                </div>
                
                <select class="form-select mb-3">
                    <option>¿Nombre de tu primer restaurante?</option>
                    <option>¿Platillo favorito?</option>
                    <option>¿Ciudad de nacimiento?</option>
                </select>
                <div class="mb-3 position-relative">
                    <i class="bi bi-chat-dots-fill input-icon"></i>
                    <input class="form-control mb-3" placeholder="Respuesta">
                </div>                
                <button class="btn btn-main w-100">Validar</button>
                <div class="text-center mt-3"><a class="link-red" href="#" onclick="volver()">← Volver</a></div>
            </div>
        </div>

    </div>

    <script>
        function mostrar(id) {
            document.getElementById("opciones").style.display = "none";
            document.querySelectorAll("#token,#correo,#telefono,#pregunta")
                .forEach(e => e.style.display = "none");
            document.getElementById(id).style.display = "block";
        }

        function volver() {
            document.getElementById("opciones").style.display = "block";
            document.querySelectorAll("#token,#correo,#telefono,#pregunta")
                .forEach(e => e.style.display = "none");
        }
    </script>


    
    <?php include 'script_generales.php'; $generales->ObtenerScriptWeb(1);?>
    

</body>

</html>