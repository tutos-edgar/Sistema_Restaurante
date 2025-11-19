<?php 
include_once '../../config/config.php';
include_once '../../middleware/validaSesion.php';

$current = basename($_SERVER['PHP_SELF']);

$idVideo = "";
$tipoVideo = "";
$usuario="";

if(isset($_GET['id'])){
    $idVideo = $_GET['id'];
}

if(isset($_GET['tipo'])){
    $tipoVideo = $_GET['tipo'];
}

if(isset($_GET['usuario'])){
    $usuario = $_GET['usuario'];
}
echo '<script>var idVideo = "'.$idVideo.'"; var tipoVideo = "'.$tipoVideo.'"; var usuario = "'.$usuario.'";</script>';
echo '<script>var apiKey ="'.TOKENWEB.'";</script>'

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ejecucion de Tareas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script>var tooglepass = false;</script>
  <style>
    .modal-content {
      border-radius: 15px;
    }
    .modal-header {
      border-bottom: none;
    }
    .btn-close {
      font-size: 1.5rem;
    }
  </style>
</head>
<body class="bg-light">
    <div class="container" id="contenido"></div>
  <!-- Modal -->
  <div class="modal fade" id="videoModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Completar Tarea</h5>
          <!-- <button type="button" class="btn-close" id="closeBtn" data-bs-dismiss="modal" aria-label="Cerrar"></button> -->
        </div>
        <div class="modal-body">
          <div class="ratio ratio-16x9">
            <div id="player"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://www.youtube.com/iframe_api"></script>
  <script src="../js/funciones_generales.js"></script>
  <script>
    let player;
    // let closeBtn = document.getElementById("closeBtn");
    let myModal = new bootstrap.Modal(document.getElementById("videoModal"), {backdrop: 'static', keyboard: false});
    let autoCloseTimeout;
    urlPetiones = urlApi + "datosEjecucionTareas.php";
    // Abrir modal al cargar la página


    window.onload = () => {
          // cargaInicial();
        myModal.show();
      };

 
  
    // Cargar API de YouTube
    function onYouTubeIframeAPIReady() {
      player = new YT.Player('player', {
        videoId:idVideo,
        events: {
          'onStateChange': onPlayerStateChange,
          'onReady': onPlayerReady
        },
        playerVars: {
          'autoplay': 0,
          'controls': 0,
          'rel': 0,
          'modestbranding': 1
        }
      });
    }

    // Detectar cambios en el video
    function onPlayerStateChange(event) {
      if (event.data === YT.PlayerState.PLAYING) {
        // closeBtn.style.display = "none";
      }

      if (event.data === YT.PlayerState.PAUSED) {
        player.playVideo();
      }
      
      if (event.data === YT.PlayerState.ENDED) {
        // closeBtn.style.display = "block";
        autoCloseTimeout = setTimeout(() => {
          // myModal.hide();
          // window.location.href = "menu_generar_vistas.php";
          registrarVistas();
        }, 3000);
      }
    }

    // Detectar cuando el usuario hace click sobre el video
    function onPlayerReady(event){
      const iframe = $("#player iframe");

      iframe.on("click", function(){
        // Ocultar controles
        player.getIframe().setAttribute("controls", 0);

      });
    }

    // Si el usuario cierra manualmente, limpiar timeout
    // closeBtn.addEventListener("click", async () => {      
    //   registrarVistas();
    // });

    function cargaInicial(){
      metodoProceso = "validarVideoDeVista";
        let jsonData = {
            metodo: metodoProceso,
            tipo_video: tipoVideo,
            idVideo: idVideo,
            usuarioB: usuario,
            apiKey: apiKey
        };

        $.ajax({
            url: urlPetiones,
            type: 'POST',
            data: JSON.stringify(jsonData),
            contentType: "application/json",
            beforeSend: function() {
                showLoading();
            },
            success: function(res) {
                hideLoading();
                let datos = typeof res === 'string' ? JSON.parse(res) : res;
                if (datos.success === "true") {
                    myModal.show();                    
                } else {                   
                    $("#contenido").html('');
                    datosRecibidos = res.datos.datos || "No es Posible cargar los datos";
                    $("#contenido").html(datosRecibidos);
                }
            },
            error: function(xhr, status, error) {
                hideLoading();
                console.error("Error en la solicitud AJAX:", {
                    status: status,
                    error: error,
                    responseText: xhr.responseText
                });
                Swal.fire({
                    icon: 'error',
                    title: 'ERROR al crear la petición',
                    showConfirmButton: false,
                    timer: tiempoEsperaMensaje
                });
            }
        });

    }


    function registrarVistas(){
      metodoProceso = "registrar";
        let jsonData = {
            metodo: metodoProceso,
            tipo_video: tipoVideo,
            idVideo: idVideo,
            usuarioB: usuario,
            apiKey: apiKey
        };

        $.ajax({
            url: urlPetiones,
            type: 'POST',
            data: JSON.stringify(jsonData),
            contentType: "application/json",
            beforeSend: function() {
                showLoading();
            },
            success: function(res) {
                hideLoading();
                let datos = typeof res === 'string' ? JSON.parse(res) : res;
                if (datos.success === "true") {
                  clearTimeout(autoCloseTimeout);
                  window.location.href = "menu_ejecutar_tareas.php";                    
                } else {                   
                    $("#contenido").html('');
                    datosRecibidos = res.datos.datos || "No es Posible cargar los datos";
                    $("#contenido").html(datosRecibidos);
                }
            },
            error: function(xhr, status, error) {
                hideLoading();
                console.error("Error en la solicitud AJAX:", {
                    status: status,
                    error: error,
                    responseText: xhr.responseText
                });
                Swal.fire({
                    icon: 'error',
                    title: 'ERROR al crear la petición',
                    showConfirmButton: false,
                    timer: tiempoEsperaMensaje
                });
            }
        });

    }

   
  </script>
</body>
</html>
