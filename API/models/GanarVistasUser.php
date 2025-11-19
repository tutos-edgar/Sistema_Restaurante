<?php
require_once __DIR__ .'/../config/init_config.php';
require_once '../models/UsuariosCanales.php';
require_once '../models/EjecucionTareas.php';
class GanarVistasUser
{
    private $conn;
    private $table = "videos_youtube";
    private $tableToken = "tokens_acceso";
    private $funcionGeneral;
    private $parametros;

    public $id_video, $id_canal_youtube, $titulo_video, $url_video, $descripcion_video, $tiempo_duracion, $idVideo;
    public $vistas, $likes, $comentarios, $tipoVideo, $esActivo, $cantidad_deuda;

    public $tipo_sistema, $ip, $fechaEstadoUsuario, $token_sesion;
    public $foto_usuario;
    private $rutaFotousuario;
    public $id_usuario, $id_usuarioDeudor, $id_usuarioAcreedor;
    private $tempRuta;

    private $objCanales;
    private $objVideos;
    private $objTareas;

    private $keyApi = KEY_API_YOTUBE;

    private $canalesYoutube = [
        "https://www.youtube.com/@tutos-edgar",
        "https://www.youtube.com/@todo-entretenimiento"
    ];

    private $canalesYoutubers = [
        "nombre-edgar" => "UCBAmJt-xVZN8ZYoN9fqtbQQ",
        "todo-entretenimiento" => "UCGteLjfrLAXLrHRsmzeuK1w",
        "archivosdemiedooficial" => "UCln8syh2HODQKdfl9fFAU3g"
    ];

    
    public function __construct($db)
    {
        $this->conn = $db;
        $this->funcionGeneral = new FuncionesGenerales();
        $this->parametros = new Parametros($db);
        $this->objCanales = new UsuariosCanales($db);
        $this->objTareas = new EjecucionTareas($db);
        $this->tempRuta = "imagenes/fotos_usuarios/";
    }

    public function ObtenerVideosDeMisCanalesYoutube()
    {
        $keyApi = KEY_API_YOTUBE;
        $videosData = [];

        if ($this->canalesYoutube && is_array($this->canalesYoutube)) {
            
            foreach ($this->canalesYoutube as $canal) {
                $idCanal = $this->objCanales->obtenerChannelId($canal);                
                $videosDeCanal = $this->objCanales->ObtenerVideosDeUnCanal($idCanal);
               
                $videoIds = [];
                if ($videosDeCanal) {
                    foreach ($videosDeCanal as $video) {
                        if (isset($video['id']['videoId'])) {
                            $videoIds[] = $video['id']['videoId'];
                        }
                    }
                }
                
                if (count($videoIds) == 0)
                    return [];

                shuffle($videoIds);
                $videoIdsStr = implode(",", $videoIds);               

                $detallesVideos = $this->objCanales->ObtenerDetallesVideo($videoIdsStr);
                
                if ($detallesVideos) {
                    foreach ($detallesVideos as $detalle) {
                        $videosData[] = [
                            'idvideo' => $detalle['id'],
                            'titulo' => $detalle['snippet']['title'],
                            'descripcion' => $detalle['snippet']['description'],
                            'imagen_thumb' => $detalle['snippet']['thumbnails']['high']['url'],
                            'duracion' => $detalle['contentDetails']['duration'], // formato ISO 8601
                            'vistas' => $detalle['statistics']['viewCount']
                        ];
                    }
                   
                }

            }
        }
      
        return $videosData;
    }

    function convertirDuracion($isoDuration)
    {
        $interval = new DateInterval($isoDuration);
        $horas = $interval->h;
        $minutos = $interval->i;
        $segundos = $interval->s;
        if ($interval->d > 0) {
            $horas += $interval->d * 24;
        }
        return sprintf("%02d:%02d:%02d", $horas, $minutos, $segundos);
    }

    // Convertir hh:mm:ss a segundos
    function tiempoEnSegundos($duracion)
    {
        $partes = explode(":", $duracion);
        return ($partes[0] * 3600) + ($partes[1] * 60) + $partes[2];
    }

    function tiempoEnSegundosString($duration) {
        $interval = new DateInterval($duration);
        $segundos = ($interval->h * 3600) + ($interval->i * 60) + $interval->s;
        return $segundos;
    }


    // Calcular vistas personalizadas según duración
    function vistasPorDuracion($isoDuration)
    {
        $duracion = $this->convertirDuracion($isoDuration); // ej: "00:05:30"
        $segundos = $this->tiempoEnSegundos($duracion);     // ej: 330

        if ($segundos >= 60 && $segundos < 100) {
            return 1; // 1 a 2 min
        } elseif ($segundos >= 120 && $segundos < 300) {
            return 4; // 2 a 5 min
        } elseif ($segundos >= 300 && $segundos < 480) {
            return 6; // 5 a 8 min
        } elseif ($segundos >= 480 && $segundos < 600) {
            return 8; // más de 8 min
        } elseif ($segundos >= 600) {
            return 13; // más de 8 min
        } else {
            return 1; // menos de 1 min
        }
    }

    function tipoVideoPorDuracion($duracion)
    {
        $segundos = $this->tiempoEnSegundos($duracion);

        if ($segundos <= 100)
            return "Short";
        elseif ($segundos <= 600)
            return "Video"; // 1 a 10 minutos
        elseif ($segundos <= 1800)
            return "Video"; // 10 a 30 minutos
        else
            return "Video"; // más de 30 minutos
    }

    public function ObtenerCardVideosVistas(GanarVistasUser $usuario)
    {

        try {
            $html = '';
            $tareasPendientes = $this->objTareas->cantidadTareasPendiente($usuario->id_usuario);
            if($tareasPendientes){
                if(array_key_exists("error", $tareasPendientes)){
                    if(isset($tareasPendientes['error']) && $tareasPendientes['error'] == true){
                        return ["success" => false,  "error" => true, "mensaje" => "Ocurrio un Error la Cargar los Datos",  "datos" => $html];
                    }
                }
            }

            $cantidadTareas = 0;
            if(array_key_exists("datos", $tareasPendientes)){
                if(isset($tareasPendientes['datos'])){
                   $cantidadTareas = $tareasPendientes['datos'];
                }
            }

            if($cantidadTareas  > 0){
                $html .= '<div class="col-12">
                        <a href="menu_ejecutar_tareas.php" class="text-decoration-none">
                        <div class="card text-center border-danger">
                            <div class="card-body">
                                <h5 class="card-title text-danger">⚠️ Tienes Tareas'.$cantidadTareas.' Pendientes</h5>
                                <p class="card-text">Realiza tus Tareas pendientes para Continuar.</p>
                            </div>
                        </div>
                        </a>
                    </div>';
                return ["success" => true,  "error" => false, "mensaje" => "Se Generaron las Card",  "datos" => $html];
            }

            $videosGenerales = $this->ObtenerVideosDeMisCanalesYoutube();
            
            if (!$videosGenerales) {
                $html .= '<div class="col-12">
                        <div class="card text-center border-danger">
                            <div class="card-body">
                                <h5 class="card-title text-danger">⚠️ No hay registros disponibles</h5>
                                <p class="card-text">Intenta nuevamente más tarde.</p>
                            </div>
                        </div>
                    </div>';
                return ["success" => false, "error" => false, "mensaje" => "No se encontraron datos", "datos" => $html];

            }

            if (is_array($videosGenerales)) {

                foreach ($videosGenerales as &$fila) {

                    if (array_key_exists('idvideo', $fila)) {
                        $idDato = $fila['idvideo'];
                    }

                    $duracion = $this->convertirDuracion($fila['duracion']);                    
                    $vistasPersonalizadas = $this->vistasPorDuracion($fila['duracion']);
                    $tipoVideo = $this->tipoVideoPorDuracion($duracion);
                    $descripcionCorta = substr($fila['descripcion'], 0, 50) . '...';
                    $html .= '<div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card card-custom-v" onclick=window.location.href="vista_video_ganar.php?id=' . $idDato . '&tipo=' . strtolower($tipoVideo) . '&vistas=' . $vistasPersonalizadas . '">
                            <img src="../img/video_card.jpeg" class="card-img-top" alt="' . $fila['titulo'] . '">
                            <div class="card-body">
                            <h5 class="card-title">' . $fila['titulo'] . '</h5>
                            <p class="card-text">' . $descripcionCorta. '</p>
                            <p class="mb-1">⏱️ Duración: ' . $duracion . '</p>
                            <p class="views">👁️ Generas : ' . $vistasPersonalizadas . ' Vistas</p>
                            <p class="views">🎥 Tipo : ' . strtoupper($tipoVideo) . '</p>
                            </div>
                        </div>
                        </div>';

                }
            } else {
                $html .= '<div class="col-12">
                        <div class="card text-center border-danger">
                            <div class="card-body">
                                <h5 class="card-title text-danger">⚠️ No hay registros disponibles</h5>
                                <p class="card-text">Intenta nuevamente más tarde.</p>
                            </div>
                        </div>
                    </div>';
            }

            // <img src="${item.imagen}" class="card-img-top" alt="${item.nombre}">

            return ["success" => true, "error" => false, "mensaje" => "Se Generaron las Card", "datos" => $html];


        } catch (PDOException $e) {
            http_response_code(500);
            return ["success" => false, "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            http_response_code(500);
            return ["success" => false, "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }

    }


    public function AsignacionVistasGeneradas(GanarVistasUser $usuario)
    {

        try {
            $html = '';
            $keyApi = KEY_API_YOTUBE;
            $videosData = [];
            $videoCanalValido = false;

            foreach ($this->canalesYoutubers as $handle => $channelId) {
                if($this->perteneceAlCanal($usuario->idVideo, $channelId)){
                    $videoCanalValido = true;
                    break;
                } 
            }

            if($videoCanalValido == false){
                $html .= '<div class="col-12">
                        <div class="card text-center border-danger">
                            <div class="card-body">
                                <h5 class="card-title text-danger">⚠️ El video no es valido</h5>
                                <p class="card-text">Intenta nuevamente más tarde.</p>
                            </div>
                        </div>
                    </div>';
                return ["success" => false, "error" => false, "mensaje" => "El Video no es valido", "datos" => $html];
            }

            $datosVideo = $this->obtenerDatosDetalleVideo($usuario->idVideo);
            $duracion = $this->convertirDuracion($datosVideo['duracion']);
            $vistasPersonalizadas = $this->vistasPorDuracion($datosVideo['duracion']);           
            $usuario->vistas = $vistasPersonalizadas;
            $tipoVideo = strtolower($this->tipoVideoPorDuracion($duracion));
  
            // for($i = 0; $i < $usuario->vistas; $i++){
                $registros =  $this->RegistrarTarea($usuario);               
            // }
            // return $registros;
            if($registros == true){
                return ["success" => true, "error" => false, "mensaje" => "Vistas Generadas Exitosamente", "datos" => $html];
            }
            exit;

            $detallesVideos = $this->objCanales->ObtenerDetallesVideo($usuario->idVideo);           
            if ($detallesVideos) {
                    foreach ($detallesVideos as $detalle) {
                        $videosData[] = [
                            'idvideo' => $detalle['id'],
                            'titulo' => $detalle['snippet']['title'],
                            'descripcion' => $detalle['snippet']['description'],
                            'imagen_thumb' => $detalle['snippet']['thumbnails']['high']['url'],
                            'duracion' => $detalle['contentDetails']['duration'], // formato ISO 8601
                            'vistas' => $detalle['statistics']['viewCount']
                        ];
                    }                 
            }    

            $videosGenerales = $this->ObtenerVideosDeMisCanalesYoutube();
            if (!$videosGenerales) {
                $html .= '<div class="col-12">
                        <div class="card text-center border-danger">
                            <div class="card-body">
                                <h5 class="card-title text-danger">⚠️ No hay registros disponibles</h5>
                                <p class="card-text">Intenta nuevamente más tarde.</p>
                            </div>
                        </div>
                    </div>';
                return ["success" => false, "error" => false, "mensaje" => "No se encontraron datos", "datos" => $html];

            }

            if (is_array($videosGenerales)) {

                foreach ($videosGenerales as &$fila) {

                    if (array_key_exists('idvideo', $fila)) {
                        $idDato = $fila['idvideo'];
                    }
                   
                    if ($usuario->idVideo == $idDato) {
                        // Asignar las vistas generadas                    
                        $duracion = $this->convertirDuracion($fila['duracion']);                        
                        $vistasPersonalizadas = $this->vistasPorDuracion($fila['duracion']);                        
                        $usuario->vistas = $vistasPersonalizadas;
                        $tipoVideo = strtolower($this->tipoVideoPorDuracion($duracion));                        
                        $usuario->tipoVideo = strtolower($tipoVideo);
                        
                        $registros =  $this->RegistrarTarea($usuario);
                        
                    }

                    $html .= '<div class="col-12">
                        <div class="card text-center border-danger">
                            <div class="card-body">
                                <h5 class="card-title text-danger">⚠️ Este Video no es Valido </h5>
                                <p class="card-text">Intenta nuevamente más tarde.</p>
                            </div>
                        </div>
                    </div>';

                }
            } else {
                $html .= '<div class="col-12">
                        <div class="card text-center border-danger">
                            <div class="card-body">
                                <h5 class="card-title text-danger">⚠️ No hay registros disponibles</h5>
                                <p class="card-text">Intenta nuevamente más tarde.</p>
                            </div>
                        </div>
                    </div>';
            }

            // <img src="${item.imagen}" class="card-img-top" alt="${item.nombre}">

            return ["success" => true, "error" => false, "mensaje" => "Se Generaron las Card", "datos" => $html];


        } catch (PDOException $e) {
            http_response_code(500);
            return ["success" => false, "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            http_response_code(500);
            return ["success" => false, "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }


    }

    public function RegistrarTarea(GanarVistasUser $video)
    {

        try {
            
            $resultadoRegistro = false;

            if ($video->vistas > 0) {
                
                for ($i = 0; $i < $video->vistas; $i++) {                    
                    $usuarioAcreedor = $this->ObtenerIdUsuario($video->id_usuario);
                    if ($usuarioAcreedor) {
                        if (is_array($usuarioAcreedor) && array_key_exists('error', $usuarioAcreedor)) {
                            if (isset($usuarioAcreedor['error']) && $usuarioAcreedor['error'] === true) {
                                return ["success" => false, "mensaje" => $usuarioAcreedor['mensaje'], "error" => true];
                            }
                        }
                    }
                    
                    $query = "SELECT *  FROM usuarios_youtube WHERE id_usuario <> ?";
                    $stmt = $this->conn->prepare($query);
                    $stmt->execute([$video->id_usuario]);
                    $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    if (count($datos) > 0) {
                        $aleatorio = array_rand($datos);
                        $video->id_usuarioDeudor = $datos[$aleatorio]['id_usuario'];
                        $video->id_usuarioAcreedor = $video->id_usuario;
                    } else {
                        return ["success" => false, "mensaje" => "No hay usuarios disponibles para asignar la vista", "error" => true];
                    }

                    $resultado = $this->RegistrarUnaVista($video);
                    if(is_array($resultado) && array_key_exists('error', $resultado)){
                        if(isset($resultado['error']) && $resultado['error'] === true){
                            if(array_key_exists('mensaje', $resultado)){
                                return ["success" => false, "mensaje" => $resultado['mensaje'], "error" => true];
                            } else {
                                return ["success" => false, "mensaje" => "No se pudo realizar el Registro", "error" => true];
                            }
                        }
                    }

                    if(array_key_exists('success', $resultado) && $resultado['success'] === false){
                        return ["success" => false, "mensaje" => "No se pudo realizar el Registro", "error" => true];
                    }
                    $resultadoRegistro = true;
                }
            }

            if($resultadoRegistro== true){
                return ["success" => true];
            }else{
                return ["success" => false];
            }

            if ($stmt->rowCount() > 0) {
                return ["success" => true];
            } else {
                return ["success" => false];
            }

        } catch (PDOException $e) {
            echo $e->getMessage();
            // http_response_code(500);           
            return ["success" => false, "error" => "true", "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];

        } catch (Exception $e) {
            echo $e->getCode();
            // http_response_code(500);           
            return ["success" => false, "error" => "true", "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }

    }

    function perteneceAlCanal($videoId, $canalId) {
        $keyApi = KEY_API_YOTUBE;
        $url = "https://www.googleapis.com/youtube/v3/videos?part=snippet&id=" 
            . urlencode($videoId) . "&key=" . $keyApi;

        $response = @file_get_contents($url);
        if ($response === FALSE) return false;

        $data = json_decode($response, true);
        if (empty($data['items'])) return false;

        $videoChannelId = $data['items'][0]['snippet']['channelId'];

        return ($videoChannelId === $canalId);
    }


    function obtenerDatosDetalleVideo($videoId) {
        // Ejemplo de uso
        $keyApi = KEY_API_YOTUBE;

        // URL de la API
        $url = "https://www.googleapis.com/youtube/v3/videos?part=snippet,statistics,contentDetails&id=" 
            . urlencode($videoId) . "&key=" . $keyApi;

        // Obtener los datos
        $response = @file_get_contents($url);

        if ($response === FALSE) {
            return false; // Error al obtener los datos
        }

        // Decodificar JSON
        $data = json_decode($response, true);

        if (empty($data['items'])) {
            return false; // Video no encontrado
        }

        $video = $data['items'][0];

        // Crear array con los valores deseados
        $resultado = [
            'titulo' => $video['snippet']['title'],
            'descripcion' => $video['snippet']['description'],
            'vistas' => (int)$video['statistics']['viewCount'],
            'likes' => isset($video['statistics']['likeCount']) ? (int)$video['statistics']['likeCount'] : 0,
            'fecha_publicacion' => $video['snippet']['publishedAt'],
            'duracion' => $video['contentDetails']['duration'],
            'canal' => $video['snippet']['channelTitle']
        ];

        return $resultado;
    }


    
    public function RegistrarUnaVista(GanarVistasUser $video)
    {

        try {
            $query = "INSERT INTO deudas_vistas_usuario (usuario_deudor, usuario_acreedor, cantidad_deuda, tipo_video, estado_deuda)
            VALUES (?, ?, 1, ?, 'pendiente')
            ON DUPLICATE KEY UPDATE 
                cantidad_deuda = cantidad_deuda + VALUES(cantidad_deuda),
            estado_deuda = CASE 
                WHEN cantidad_deuda - VALUES(cantidad_deuda) <= 0 THEN 'pagado'
                ELSE 'pendiente'
                END";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                $video->id_usuarioDeudor,
                $video->id_usuario,
                $video->tipoVideo
            ]);

            if ($stmt->rowCount() > 0) {
                return ["success" => true];
            } else {
                return ["success" => false];
            }

        } catch (PDOException $e) {
            echo $e->getMessage();
            // http_response_code(500);           
            return ["success" => false, "error" => "true", "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];

        } catch (Exception $e) {
            echo $e->getCode();
            // http_response_code(500);           
            return ["success" => false, "error" => "true", "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
    }


    public function AutoAsignacionGuardado(GanarVistasUser $usuario)
    {

        try {
            $html = '';
            $keyApi = KEY_API_YOTUBE;
            $videosData = [];
            $videoCanalValido = false;

            foreach ($this->canalesYoutubers as $handle => $channelId) {
                if($this->perteneceAlCanal($usuario->idVideo, $channelId)){
                    $videoCanalValido = true;
                    break;
                } 
            }

            if($videoCanalValido == false){
                $html .= '<div class="col-12">
                        <div class="card text-center border-danger">
                            <div class="card-body">
                                <h5 class="card-title text-danger">⚠️ El video no es valido</h5>
                                <p class="card-text">Intenta nuevamente más tarde.</p>
                            </div>
                        </div>
                    </div>';
                return ["success" => false, "error" => false, "mensaje" => "El Video no es valido", "datos" => $html];
            }

            $datosVideo = $this->obtenerDatosDetalleVideo($usuario->idVideo);
            $duracion = $this->convertirDuracion($datosVideo['duracion']);
            $vistasPersonalizadas = $this->vistasPorDuracion($datosVideo['duracion']);           
            $usuario->vistas = $vistasPersonalizadas;
            $tipoVideo = strtolower($this->tipoVideoPorDuracion($duracion));
  
            // for($i = 0; $i < $usuario->vistas; $i++){
                $registros =  $this->RegistrarTarea($usuario);               
            // }
            // return $registros;
            if($registros == true){
                return ["success" => true, "error" => false, "mensaje" => "Vistas Generadas Exitosamente", "datos" => $html];
            }
            exit;

            $detallesVideos = $this->objCanales->ObtenerDetallesVideo($usuario->idVideo);           
            if ($detallesVideos) {
                    foreach ($detallesVideos as $detalle) {
                        $videosData[] = [
                            'idvideo' => $detalle['id'],
                            'titulo' => $detalle['snippet']['title'],
                            'descripcion' => $detalle['snippet']['description'],
                            'imagen_thumb' => $detalle['snippet']['thumbnails']['high']['url'],
                            'duracion' => $detalle['contentDetails']['duration'], // formato ISO 8601
                            'vistas' => $detalle['statistics']['viewCount']
                        ];
                    }                 
            }    

            $videosGenerales = $this->ObtenerVideosDeMisCanalesYoutube();
            if (!$videosGenerales) {
                $html .= '<div class="col-12">
                        <div class="card text-center border-danger">
                            <div class="card-body">
                                <h5 class="card-title text-danger">⚠️ No hay registros disponibles</h5>
                                <p class="card-text">Intenta nuevamente más tarde.</p>
                            </div>
                        </div>
                    </div>';
                return ["success" => false, "error" => false, "mensaje" => "No se encontraron datos", "datos" => $html];

            }

            if (is_array($videosGenerales)) {

                foreach ($videosGenerales as &$fila) {

                    if (array_key_exists('idvideo', $fila)) {
                        $idDato = $fila['idvideo'];
                    }
                   
                    if ($usuario->idVideo == $idDato) {
                        // Asignar las vistas generadas                    
                        $duracion = $this->convertirDuracion($fila['duracion']);                        
                        $vistasPersonalizadas = $this->vistasPorDuracion($fila['duracion']);                        
                        $usuario->vistas = $vistasPersonalizadas;
                        $tipoVideo = strtolower($this->tipoVideoPorDuracion($duracion));                        
                        $usuario->tipoVideo = strtolower($tipoVideo);
                        
                        $registros =  $this->RegistrarTarea($usuario);
                        
                    }

                    $html .= '<div class="col-12">
                        <div class="card text-center border-danger">
                            <div class="card-body">
                                <h5 class="card-title text-danger">⚠️ Este Video no es Valido </h5>
                                <p class="card-text">Intenta nuevamente más tarde.</p>
                            </div>
                        </div>
                    </div>';

                }
            } else {
                $html .= '<div class="col-12">
                        <div class="card text-center border-danger">
                            <div class="card-body">
                                <h5 class="card-title text-danger">⚠️ No hay registros disponibles</h5>
                                <p class="card-text">Intenta nuevamente más tarde.</p>
                            </div>
                        </div>
                    </div>';
            }

            // <img src="${item.imagen}" class="card-img-top" alt="${item.nombre}">

            return ["success" => true, "error" => false, "mensaje" => "Se Generaron las Card", "datos" => $html];


        } catch (PDOException $e) {
            http_response_code(500);
            return ["success" => false, "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            http_response_code(500);
            return ["success" => false, "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }


    }








    // BUSQUEADA DE VIDEOS Y CANALES
   



    public function ObtenerIdVideo(EjecucionTareas $video)
    {
        try {
            if ($video->tipoVideo == "" || empty($video->tipoVideo)) {
                $this->table = "videos_youtube";
                $query = "SELECT * FROM " . $this->table . " WHERE idvideo= ?";
                $stmt = $this->conn->prepare($query);
                $stmt->execute([$video->idVideo]);
            } else if ($video->tipoVideo == "video") {
                $this->table = "videos_youtube";
                $query = "SELECT * FROM " . $this->table . " WHERE idvideo= ?";
                $stmt = $this->conn->prepare($query);
                $stmt->execute([$video->idVideo]);
            } else if ($video->tipoVideo == "short") {
                $this->table = "short_youtube";
                $query = "SELECT * FROM " . $this->table . " WHERE idvideo= ?";
                $stmt = $this->conn->prepare($query);
                $stmt->execute([$video->idVideo]);
            }

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["success" => false, "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            return ["success" => false, "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
    }


    public function ObtenerIdUsuario($idUsuario)
    {
        try {
            $query = "SELECT *  FROM usuarios_youtube WHERE id_usuario = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$idUsuario]);
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);
            return $datos;

        } catch (PDOException $e) {
            http_response_code(500);
            return ["success" => false, "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            http_response_code(500);
            return ["success" => false, "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
    }







    public function ObtenerCantidadVideos($idCanal, $tipo)
    {

        try {

            if ($tipo == "video") {
                $query = "SELECT COUNT(*) AS total_video FROM videos_youtube vy 
                INNER JOIN canales_youtube cy ON vy.id_canal_youtube = cy.id_canal_youtube                
                WHERE cy.id_canal_youtube = ?";
                $stmt = $this->conn->prepare($query);
                $stmt->execute([$idCanal]);
            } else if ($tipo == "short") {
                $query = "SELECT COUNT(*) AS total_video FROM short_youtube sy 
                INNER JOIN canales_youtube cy ON sy.id_canal_youtube = cy.id_canal_youtube               
                WHERE sy.id_canal_youtube = ?";
                $stmt = $this->conn->prepare($query);
                $stmt->execute([$idCanal]);
            } else if ($tipo == "todos") {
                $query = "SELECT COUNT(*) AS total_video FROM (
                    SELECT id_video FROM videos_youtube WHERE id_canal_youtube = ?
                    UNION ALL
                    SELECT id_video  FROM short_youtube WHERE id_canal_youtube = ?
                ) AS combined_videos";
                $stmt = $this->conn->prepare($query);
                $stmt->execute([$idCanal, $idCanal]);
            }
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);
            return $datos;


        } catch (PDOException $e) {
            // http_response_code(500);
            // echo $e->getMessage();
            return ["success" => false, "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            // http_response_code(500);
            // echo $e->getMessage();
            return ["success" => false, "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }


    }

    public function ObtenerCantidadVideosPorUsuario($usuario, $tipo)
    {

        try {

            if ($tipo == "video") {
                $query = "SELECT COUNT(*) AS total_video FROM videos_youtube vy 
                INNER JOIN canales_youtube cy ON vy.id_canal_youtube = cy.id_canal_youtube                
                WHERE cy.id_usuario = ?";
                $stmt = $this->conn->prepare($query);
                $stmt->execute([$usuario]);
            } else if ($tipo == "short") {
                $query = "SELECT COUNT(*) AS total_video FROM short_youtube sy 
                INNER JOIN canales_youtube cy ON sy.id_canal_youtube = cy.id_canal_youtube               
                WHERE sy.id_usuario = ?";
                $stmt = $this->conn->prepare($query);
                $stmt->execute([$usuario]);
            } else if ($tipo == "todos") {
                $query = "SELECT COUNT(*) AS total_video FROM (
                    SELECT id_video FROM videos_youtube 
                    INNER JOIN canales_youtube cy ON vy.id_canal_youtube = cy.id_canal_youtube                
                    WHERE cy.id_usuario = ?
                    UNION ALL
                    SELECT id_video  FROM short_youtube
                    INNER JOIN canales_youtube cy ON sy.id_canal_youtube = cy.id_canal_youtube               
                    WHERE sy.id_usuario = ?
                ) AS combined_videos";
                $stmt = $this->conn->prepare($query);
                $stmt->execute([$usuario, $usuario]);
            }
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);
            return $datos;


        } catch (PDOException $e) {
            // http_response_code(500);
            // echo $e->getMessage();
            return ["success" => false, "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            // http_response_code(500);
            // echo $e->getMessage();
            return ["success" => false, "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }


    }

    public function ValidarTipoVistasUsuario(GanarVistasUser $usuario)
    {
        try {
            $cantidadVideos = $this->ObtenerCantidadVideosPorUsuario($usuario, "video");
            if ($cantidadVideos['total_video'] <= 0) {
                return ["success" => false, "error" => true, "mensaje" => "No tiene Videos Disponibles"];
            }

            $cantidadShorts = $this->ObtenerCantidadVideosPorUsuario($usuario, "short");
            if ($cantidadShorts['total_video'] <= 0) {
                return ["success" => false, "error" => true, "mensaje" => "No tiene Shorts Disponibles"];
            }


        } catch (PDOException $e) {
            return ["success" => false, "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            return ["success" => false, "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
    }

    // VERIFICA SI EL VIDEO YA EXISTE
    public function obtenerPorIdVideo(EjecucionTareas $video)
    {
        try {

            if ($video->tipoVideo == "" || empty($video->tipoVideo)) {
                $this->table = "videos_youtube";
                $query = "SELECT *, 'video' As tipo FROM " . $this->table . " WHERE id_video = ?";
                $stmt = $this->conn->prepare($query);
                $stmt->execute([$video->id_video]);
            } else if ($video->tipoVideo == "video") {
                $this->table = "videos_youtube";
                $query = "SELECT *, 'video' As tipo FROM " . $this->table . " WHERE id_video = ?";
                $stmt = $this->conn->prepare($query);
                $stmt->execute([$video->id_video]);
            } else if ($video->tipoVideo == "short") {
                $this->table = "short_youtube";
                $query = "SELECT *, 'video' As tipo FROM " . $this->table . " WHERE id_video = ?";
                $stmt = $this->conn->prepare($query);
                $stmt->execute([$video->id_video]);
            }

            $datos = $stmt->fetch(PDO::FETCH_ASSOC);
            return $datos;

        } catch (PDOException $e) {
            http_response_code(500);
            return ["success" => false, "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            http_response_code(500);
            return ["success" => false, "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
    }



}