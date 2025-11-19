<?php
require_once __DIR__ .'/../config/init_config.php';
require_once '../models/UsuariosCanales.php';
require_once '../models/UsuariosVideos.php';
require_once '../models/EjecucionTareas.php';
class GeneracionVistas
{
    private $conn;
    private $table = "videos_youtube";
    private $tableToken = "tokens_acceso";
    private $funcionGeneral;
    private $parametros;

    public $id_video, $id_canal_youtube, $titulo_video,  $url_video, $descripcion_video, $tiempo_duracion, $idVideo;
    public $vistas, $likes, $comentarios, $tipoVideo, $esActivo, $cantidad_deuda;
    
    public $tipo_sistema, $ip, $fechaEstadoUsuario, $token_sesion;
    public $foto_usuario;
    private $rutaFotousuario;
    public $id_usuario, $id_usuarioDeudor;
    private $tempRuta;

    private $objCanales;
    private $objVideos;
    private $objTareas;
   
    public function __construct($db)
    {
        $this->conn = $db; 
        $this->funcionGeneral = new FuncionesGenerales();   
        $this->parametros = new Parametros($db);
        $this->objCanales = new UsuariosCanales($db);
        $this->objVideos = new UsuariosVideos($db);
        $this->objTareas = new EjecucionTareas($db);
        $this->tempRuta= "imagenes/fotos_usuarios/";
    }

    // BUSQUEADA DE VIDEOS Y CANALES
    public function obtenerTodosVideos($usuario)
    {

        try{            
          
            $query = "SELECT *, 'video' AS tipo, cy.nombre_canal FROM videos_youtube vy 
            INNER JOIN canales_youtube cy ON vy.id_canal_youtube = cy.id_canal_youtube
            INNER JOIN usuarios_youtube uy ON cy.id_usuario = uy.id_usuario
            WHERE uy.es_activo = 1 AND uy.id_usuario <> ?
            UNION ALL 
            SELECT *, 'short' AS tipo, cy.nombre_canal FROM short_youtube sy 
            INNER JOIN canales_youtube cy ON sy.id_canal_youtube = cy.id_canal_youtube
            INNER JOIN usuarios_youtube uy ON cy.id_usuario = uy.id_usuario
            WHERE uy.es_activo = 1 AND uy.id_usuario <> ?";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([$usuario]);
            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);        

            foreach ($datos as &$fila) {

                if (!empty($datos) && array_key_exists('id_video', $datos[0])) {
                    $idvideo = $fila['id_video'];
                } 
   
                if (!empty($datos) && array_key_exists('id_short_youtube', $datos[0])) {
                    $idvideo = $fila['id_short_youtube'];
                } 

                $rol = "ADMINISTRADOR";
                
                // $fila['foto_usuario'] = $codigoBarrasURL . str_replace(' ', '', $fila['foto_usuario']);

                if(strtoupper($rol) == "ADMINISTRADOR"){
                    
                    $fila['botones'] = '
                        <div class="text-center">
                            <div class="btn-group">
                                <button data-id="' . $idvideo . '" class="btn btn-primary btn-sm btnEditar">
                                    <i class="material-icons"></i> Editar
                                </button>
                                <button data-id="' . $idvideo . '" class="btn btn-danger btn-sm btnEliminar">
                                    <i class="material-icons"></i> Eliminar
                                </button>
                            </div>
                        </div>';
                }else{
                    $fila['botones'] = '<span class="badge bg-secondary">Sin Dato</span>';
                }

                if($fila['tipo']=="Short"){
                    // $fila['tipo']='<span  data-idtipo="short" class="badge bg-primary">Video</span>';
                    $fila['url_video'] = '<a href="https://www.youtube.com/shorts/'.$fila['idvideo'].'" target="_blank" class="btn btn-sm btn-outline-danger">Ver Short</a>';
                }else{
                    // $fila['tipo']='<span data-idtipo="video" class="badge bg-success">Video</span>';
                    $fila['url_video'] = '<a href="https://www.youtube.com/watch?v='.$fila['idvideo'].'" target="_blank" class="btn btn-sm btn-outline-danger">Ver Video</a>';
                }
                
            
            }
            return $datos;

            
        }catch (PDOException $e) {
            http_response_code(500);
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            http_response_code(500);
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
        
        
    }

    public function ObtenerCardVideosYoutube($usuario)
    {

        try{         

            $html = '';
            $tareasPendientes = $this->objTareas->cantidadTareasPendiente($usuario);
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

            $cantidadVideos = $this->ObtenerCantidadVideosPorUsuario($usuario, "video");
            if($cantidadVideos['total_video'] <= 0){
                $html .= '<div class="col-12">
                        <a href="ingresar_video.php" class="text-decoration-none">
                        <div class="card text-center border-danger">
                            <div class="card-body">
                                <h5 class="card-title text-danger">⚠️ Usted No cuenta con Videos disponibles</h5>
                                <p class="card-text">Ingrese Videos en su Cuenta para Generar Vistas.</p>
                            </div>
                        </div>
                        </a>
                    </div>';
                return ["success" => false,  "error" => false, "mensaje" => "No tiene Videos Disponibles", "datos" => $html];
            }
            
            $query = "SELECT *, 'video' AS tipo, cy.nombre_canal FROM videos_youtube vy 
            INNER JOIN canales_youtube cy ON vy.id_canal_youtube = cy.id_canal_youtube
            INNER JOIN usuarios_youtube uy ON cy.id_usuario = uy.id_usuario
            WHERE uy.id_usuario <> ?";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([$usuario]);
            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if ($stmt->rowCount() > 0){
            
                foreach ($datos as &$fila) {

                    if (array_key_exists('idvideo', $fila)) {
                        $idDato = $fila['idvideo'];
                    } 

                    $html .= '<div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card card-custom-v" onclick=window.location.href="vista_video.php?id='.$idDato.'&tipo='.$fila['tipo'].'&usuario='.$fila['id_usuario'].'">
                    <img src="../img/video_card.jpeg" class="card-img-top" alt="'.$fila['titulo_video'].'">
                    <div class="card-body">
                    <h5 class="card-title">'.$fila['titulo_video'].'</h5>
                    <p class="card-text">'.$fila['descripcion_video'].'</p>
                    <p class="mb-1">⏱️ Duración: '.$fila['tiempo_duracion'].'</p>
                    <p class="views">👁️ Vistas: '.$fila['vistas'].'</p>
                    </div>
                </div>
                </div>';

                }
            }else{
                $html .= '<div class="col-12">
                        <a href="#" class="text-decoration-none">
                        <div class="card text-center border-danger">
                            <div class="card-body">
                                <h5 class="card-title text-danger">⚠️ No hay registros disponibles</h5>
                                <p class="card-text">Intenta nuevamente más tarde.</p>
                            </div>
                        </div>
                        </a>
                    </div>';
            }

            return  ["success" => true,  "error" => false, "mensaje" => "Se Generaron las Card",  "datos" => $html];;

            
        }catch (PDOException $e) {
            http_response_code(500);
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            http_response_code(500);
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
        
        
    }

    public function ObtenerCardShortsYoutube($usuario)
    {
        try{            
            $html = '';
            $tareasPendientes = $this->objTareas->cantidadTareasPendiente($usuario);
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

            $cantidadVideos = $this->ObtenerCantidadVideosPorUsuario($usuario, "short");
            if($cantidadVideos['total_video'] <= 0){
                $html .= '<div class="col-12">
                        <a href="ingresar_video.php" class="text-decoration-none">
                        <div class="card text-center border-danger">
                            <div class="card-body">
                                <h5 class="card-title text-danger">⚠️ Usted No cuenta con Shorts disponibles</h5>
                                <p class="card-text">Ingrese Shorts en su Cuenta para Generar Vistas.</p>
                            </div>
                        </div>
                        </a>
                    </div>';
                return ["success" => false,  "error" => false, "mensaje" => "No tiene Shorts Disponibles", "datos" => $html];
            }

            $query = "SELECT *, 'short' AS tipo, cy.nombre_canal FROM short_youtube sy 
            INNER JOIN canales_youtube cy ON sy.id_canal_youtube = cy.id_canal_youtube
            INNER JOIN usuarios_youtube uy ON cy.id_usuario = uy.id_usuario
            WHERE uy.id_usuario <> ?  ORDER BY RAND()";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([$usuario]);
            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);            

            if ($stmt->rowCount() > 0){
                foreach ($datos as &$fila) {

                    // if (array_key_exists('id_video', $fila)) {
                    //     $idDato = $fila['id_video'];
                    // } 

                    if (array_key_exists('idvideo', $fila)) {
                        $idDato = $fila['idvideo'];
                    } 

                    $html .= '<div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card card-custom" onclick=window.location.href=vista_video.php?id="'.$idDato.'&tipo='.$fila['tipo'].'&usuario='.$fila['id_usuario'].'">
                        <img src="../img/video_card.jpeg" class="card-img-top" alt="'.$fila['titulo_video'].'">
                        <div class="card-body">
                        <h5 class="card-title">'.$fila['titulo_video'].'</h5>
                        <p class="card-text">'.$fila['descripcion_video'].'</p>
                        <p class="mb-1">⏱️ Duración: '.$fila['tiempo_duracion'].'</p>
                        <p class="views">👁️ Vistas: '.$fila['vistas'].'</p>
                        </div>
                    </div>
                    </div>';

                }
            }else{
                $html .= '<div class="col-12">
                        <a href="#" class="text-decoration-none">
                        <div class="card text-center border-danger">
                            <div class="card-body">
                                <h5 class="card-title text-danger">⚠️ No hay registros disponibles</h5>
                                <p class="card-text">Intenta nuevamente más tarde.</p>
                            </div>
                        </div>
                        </a>
                    </div>';
            }
            
            return ["success" => true,  "error" => false, "mensaje" => "Se Generaron las Card",  "datos" => $html];

            
        }catch (PDOException $e) {
            http_response_code(500);
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            http_response_code(500);
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
        
        
    }

    public function ObtenerCardCanalesYoutube($usuario)
    {

        try{
            
            $html = '';
            $tareasPendientes = $this->objTareas->cantidadTareasPendiente($usuario);
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
            
            $query = "SELECT *, 'cardCanales' FROM canales_youtube 
            WHERE id_usuario <> ? ORDER BY RAND()";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([$usuario]);
            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);           

            if ($stmt->rowCount() > 0){
            
                foreach ($datos as &$fila) {
                    if (array_key_exists('id_canal_youtube', $fila)) {
                        $idDato = $fila['id_canal_youtube'];
                    }    
                    
                    $url =$this->objCanales->ObtenerImgCanal($fila['idcanal']);
                    $cantidad = $this->ObtenerCantidadVideos($idDato, "todos");
                    $totalVideos = $cantidad['total_video'];
                    if($totalVideos > 0){
                        $html .= '<div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card card-custom-v" onclick="ObtenerVideosPorCanal('.$idDato.')">
                            <img src="'.$url.'" class="card-img-top" alt="Imagen Canal">
                            <div class="card-body">
                            <h5 class="card-title">'.$fila['nombre_canal'].'</h5>
                            <p class="card-text">'. $fila['descripcion_canal'].'</p>
                            <p class="mb-1">🎥 Videos: '.$totalVideos.'</p>                        
                            </div>
                        </div>
                        </div>';
                    }
                
                }
            }else{
                $html .= '<div class="col-12">
                        <a href="#" class="text-decoration-none">
                        <div class="card text-center border-danger">
                            <div class="card-body">
                                <h5 class="card-title text-danger">⚠️ No hay registros disponibles</h5>
                                <p class="card-text">Intenta nuevamente más tarde.</p>
                            </div>
                        </div>
                        </a>
                    </div>';
            }

  
            // <img src="${item.imagen}" class="card-img-top" alt="${item.nombre}">
            // <p class="views">👁️ Vistas: ${item.vistas.toLocaleString()}</p>
           
            return ["success" => true,  "error" => false, "mensaje" => "Se Generaron las Card",  "datos" => $html];

            
        }catch (PDOException $e) {
            http_response_code(500);
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            http_response_code(500);
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
        
        
    }

    public function ObtenerCardVideosPorCanalesYoutube($usuario)
    {

        try{

            $html = '';
            $tareasPendientes = $this->objTareas->cantidadTareasPendiente($usuario);
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

            $query = "SELECT *, 'video' AS tipo, cy.nombre_canal FROM videos_youtube vy 
            INNER JOIN canales_youtube cy ON vy.id_canal_youtube = cy.id_canal_youtube
            INNER JOIN usuarios_youtube uy ON cy.id_usuario = uy.id_usuario
            WHERE uy.es_activo = 1 AND cy.id_canal_youtube = ? 
            UNION ALL 
            SELECT *, 'short' AS tipo, cy.nombre_canal FROM short_youtube sy 
            INNER JOIN canales_youtube cy ON sy.id_canal_youtube = cy.id_canal_youtube
            INNER JOIN usuarios_youtube uy ON cy.id_usuario = uy.id_usuario
            WHERE uy.es_activo = 1 AND cy.id_canal_youtube = ?  ORDER BY RAND()";

            $stmt  = $this->conn->prepare($query);
            $stmt->execute([$usuario, $usuario]);
            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // $datos = array_rand($datos);
           
            if ($stmt->rowCount() > 0){
            
                foreach ($datos as &$fila) {

                    if (array_key_exists('idvideo', $fila)) {
                        $idDato = $fila['idvideo'];
                    } 

                    $html .= '<div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card card-custom-v" onclick=window.location.href="vista_video.php?id='.$idDato.'&tipo='.$fila['tipo'].'&usuario='.$fila['id_usuario'].'">
                            <img src="../img/video_card.jpeg" class="card-img-top" alt="'.$fila['titulo_video'].'">
                            <div class="card-body">
                            <h5 class="card-title">'.$fila['titulo_video'].'</h5>
                            <p class="card-text">'.$fila['descripcion_video'].'</p>
                            <p class="mb-1">⏱️ Duración: '.$fila['tiempo_duracion'].'</p>
                            <p class="views">👁️ Vistas: '.$fila['vistas'].'</p>
                            </div>
                        </div>
                        </div>';
                
                }
            }else{
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
            // <p class="views">👁️ Vistas: ${item.vistas.toLocaleString()}</p>
           
            return ["success" => true,  "error" => false, "mensaje" => "Se Generaron las Card",  "datos" => $html];

            
        }catch (PDOException $e) {
            http_response_code(500);
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            http_response_code(500);
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
        
        
    }

    public function ObtenerCantidadVideos($idCanal, $tipo)
    {

        try{            
            
            if($tipo == "video"){
                $query = "SELECT COUNT(*) AS total_video FROM videos_youtube vy 
                INNER JOIN canales_youtube cy ON vy.id_canal_youtube = cy.id_canal_youtube                
                WHERE cy.id_canal_youtube = ?";
                $stmt  = $this->conn->prepare($query);
                $stmt->execute([$idCanal]);
            }else if($tipo == "short"){
                $query = "SELECT COUNT(*) AS total_video FROM short_youtube sy 
                INNER JOIN canales_youtube cy ON sy.id_canal_youtube = cy.id_canal_youtube               
                WHERE cy.id_canal_youtube = ?";
                $stmt  = $this->conn->prepare($query);
                $stmt->execute([$idCanal]);
            }else if($tipo == "todos"){
                $query = "SELECT COUNT(*) AS total_video FROM (
                    SELECT id_video FROM videos_youtube WHERE id_canal_youtube = ?
                    UNION ALL
                    SELECT id_video  FROM short_youtube WHERE id_canal_youtube = ?
                ) AS combined_videos";
                $stmt  = $this->conn->prepare($query);
                $stmt->execute([$idCanal, $idCanal]);
            }            
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);           
            return $datos;

            
        }catch (PDOException $e) {
            // http_response_code(500);
            // echo $e->getMessage();
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            // http_response_code(500);
            // echo $e->getMessage();
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
        
        
    }

    public function ObtenerCantidadVideosPorUsuario($usuario, $tipo)
    {

        try{            
            
            if($tipo == "video"){
                $query = "SELECT COUNT(*) AS total_video FROM videos_youtube vy 
                INNER JOIN canales_youtube cy ON vy.id_canal_youtube = cy.id_canal_youtube                
                WHERE cy.id_usuario = ?";
                $stmt  = $this->conn->prepare($query);
                $stmt->execute([$usuario]);
            }else if($tipo == "short"){
                $query = "SELECT COUNT(*) AS total_video FROM short_youtube sy 
                INNER JOIN canales_youtube cy ON sy.id_canal_youtube = cy.id_canal_youtube               
                WHERE cy.id_usuario = ?";
                $stmt  = $this->conn->prepare($query);
                $stmt->execute([$usuario]);
            }else if($tipo == "todos"){
                $query = "SELECT COUNT(*) AS total_video FROM (
                    SELECT id_video FROM videos_youtube 
                    INNER JOIN canales_youtube cy ON vy.id_canal_youtube = cy.id_canal_youtube                
                    WHERE cy.id_usuario = ?
                    UNION ALL
                    SELECT id_video  FROM short_youtube
                    INNER JOIN canales_youtube cy ON sy.id_canal_youtube = cy.id_canal_youtube               
                    WHERE cy.id_usuario = ?
                ) AS combined_videos";
                $stmt  = $this->conn->prepare($query);
                $stmt->execute([$usuario, $usuario]);
            }            
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);           
            return $datos;

            
        }catch (PDOException $e) {
            // http_response_code(500);
            // echo $e->getMessage();
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            // http_response_code(500);
            // echo $e->getMessage();
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
        
        
    }

    public function ValidarTipoVistasUsuario($usuario, $tipo){
        try{
            $html='';            
            
            if($tipo == "video"){
                $cantidadVideos = $this->ObtenerCantidadVideosPorUsuario($usuario, "video");
                if($cantidadVideos['total_video'] <= 0){
                    $html .= '<div class="col-12">
                            <a href="ingresar_video.php" class="text-decoration-none" style="cursor: pointer;">
                            <div class="card text-center border-danger">
                                <div class="card-body">
                                    <h5 class="card-title text-danger">⚠️ Usted No cuenta con Videos disponibles</h5>
                                    <p class="card-text">Ingrese Videos en su Cuenta para Generar Vistas.</p>
                                </div>
                            </div>
                            </a>
                        </div>';
                    return ["success" => false,  "error" => false, "mensaje" => "No tiene Videos Disponibles", "datos" => $html];
                }
            }else if($tipo=="short"){
                $cantidadShorts = $this->ObtenerCantidadVideosPorUsuario($usuario, "short");
                if($cantidadShorts['total_video'] <= 0){
                    $html .= '<div class="col-12">
                            <a href="ingresar_video.php" class="text-decoration-none">
                            <div class="card text-center border-danger">
                                <div class="card-body">
                                    <h5 class="card-title text-danger">⚠️ Usted No cuenta con Shorts disponibles</h5>
                                    <p class="card-text">Ingrese Shorts en su Cuenta para Generar Vistas.</p>
                                </div>
                            </div>
                            </a>
                        </div>';
                    return ["success" => false,  "error" => false, "mensaje" => "No tiene Shorts Disponibles", "datos" => $html];
                }
            }else if($tipo!="short" && $tipo != "video"){
                $html .= '<div class="col-12">
                            <a href="ingresar_video.php" class="text-decoration-none">
                            <div class="card text-center border-danger">
                                <div class="card-body">
                                    <h4 class="card-title text-danger">⚠️ El Tipo de Vieo no es Valido</h4>
                                    <p class="card-text">Favor de Verificar el Tipo de Video.</p>
                                </div>
                            </div>
                            </a>
                        </div>';
                    return ["success" => false,  "error" => false, "mensaje" => "No tiene Shorts Disponibles", "datos" => $html];
            }

             return ["success" => true,  "error" => false, "mensaje" => "Iniciar"];

            // if($cantidadVideos['total_video'] <= 0){
            //     return ["success" => false,  "error" => true, "mensaje" => "No tiene Videos Disponibles"];
            // }
            
            


        }catch (PDOException $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } 
    }

    // VERIFICA SI EL VIDEO YA EXISTE
    public function obtenerPorIdVideo(UsuariosVideos $video)
    {
        try{

            if($video->tipoVideo == "" || empty($video->tipoVideo)){
                $this->table = "videos_youtube";
                $query = "SELECT *, 'video' As tipo FROM " . $this->table." WHERE id_video = ?";
                $stmt  = $this->conn->prepare($query);
                $stmt->execute([$video->id_video]);
            }else if($video->tipoVideo == "video"){
                $this->table = "videos_youtube";
                $query = "SELECT *, 'video' As tipo FROM " . $this->table." WHERE id_video = ?";
                $stmt  = $this->conn->prepare($query);
                $stmt->execute([$video->id_video]);
            }else if($video->tipoVideo == "short"){
                $this->table = "short_youtube";
                $query = "SELECT *, 'video' As tipo FROM " . $this->table." WHERE id_video = ?";
                $stmt  = $this->conn->prepare($query);
                $stmt->execute([$video->id_video]);
            }
            
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);           
            return $datos;

        }catch (PDOException $e) {
            http_response_code(500);
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            http_response_code(500);
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
    }

   
    public function obtenerExistenciaVideo(UsuariosVideos $video, $tipo)
    {
        try{

            if($video->tipoVideo == "" || empty($video->tipoVideo)){
                $this->table = "videos_youtube";
            }else if($video->tipoVideo == "video"){
                $this->table = "videos_youtube";
            }else if($video->tipoVideo == "short"){
                $this->table = "short_youtube";
            }

            if($tipo == true){
                $query = "SELECT * FROM " . $this->table." WHERE idvideo = ?";
                $stmt  = $this->conn->prepare($query);
                $stmt->execute([$video->idVideo]);
            }else{
                $query = "SELECT * FROM " . $this->table." WHERE id_video  <> ? AND  idvideo = ?";
                $stmt  = $this->conn->prepare($query);
                $stmt->execute([$video->id_video,$video->idVideo]);
            }
            
            $datos= $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($datos as &$fila){
                $usuario = $fila['user_usuario'];
                $fila['user_usuario'] = $usuario;
            }

            return $datos;

        }catch (PDOException $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }

    }

    public function ObtenerIdVideo(UsuariosVideos $video)
    {
        try{
            if($video->tipoVideo == "" || empty($video->tipoVideo)){
                $this->table = "videos_youtube";
                $query = "SELECT * FROM " . $this->table . " WHERE idvideo= ?";
                $stmt  = $this->conn->prepare($query);
                $stmt->execute([$video->idVideo]);
            }else if($video->tipoVideo == "video"){
                $this->table = "videos_youtube";
                $query = "SELECT * FROM " . $this->table . " WHERE idvideo= ?";
                $stmt  = $this->conn->prepare($query);
                $stmt->execute([$video->idVideo]);
            }else if($video->tipoVideo == "short"){
                $this->table = "short_youtube";
                $query = "SELECT * FROM " . $this->table . " WHERE idvideo= ?";
                $stmt  = $this->conn->prepare($query);
                $stmt->execute([$video->idVideo]);
            }
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }catch (PDOException $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage()) ];
        } catch (Exception $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
    }

    public function busquedaDeVideos($campo, $id)
    {

        try{
            $query = "SELECT * FROM personal WHERE $campo LIKE ?";           

            $stmt = $this->conn->prepare($query);
            $searchTerm = '%' . $id . '%';
            $stmt->execute([$searchTerm]);
            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $html = '';
            foreach ($datos as &$fila) {
                $idPersona = $fila['id_persona'];
                $documento = $fila['documento_personal']; // puede venir null si no tiene estado
                $nombre = $fila['nombre_personal'];
                $apellido = $fila['apellido_personal'];
                $html .= '<div class="row-data">
                <div class="cell" style="display:none;">'.$idPersona.'</div>
                <div class="cell">'.$documento.'</div>
                <div class="cell">'.$nombre.'</div>
                <div class="cell">'.$apellido.'</div>
                <div class="cell button">
                        <button class="btn btn-primary btn-sm" id="seleccionarPersona">Seleccionar</button>
                </div>
                </div>';
            }

            return $html;

        }catch (PDOException $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
        
    }


    public function ObtenerIdUsuario($idUsuario)
    {
        try{
                $query = "SELECT *  FROM usuarios_youtube WHERE id_usuario = ?";
                $stmt  = $this->conn->prepare($query);
                $stmt->execute([$idUsuario]);            
                $datos = $stmt->fetch(PDO::FETCH_ASSOC);           
                return $datos;

        }catch (PDOException $e) {
            http_response_code(500);
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            http_response_code(500);
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
    }

    public function GenerarVistasDeudas(GeneracionVistas $video)
    {

        try {
           
            $this->objVideos->tipoVideo = $video->tipoVideo;
            $this->objVideos->idVideo = $video->idVideo;

            $existenciaVideo = $this->ObtenerIdVideo($this->objVideos);
           
            if(!$existenciaVideo){
                return ["success" => false, "existe" => false];
            }
            
            $usuarioAcreedor = $this->ObtenerIdUsuario($video->id_usuario);
            if ($usuarioAcreedor) {
                if(is_array($usuarioAcreedor) && array_key_exists('error', $usuarioAcreedor)){
                    if(isset($usuarioAcreedor['error']) && $usuarioAcreedor['error'] === true){
                        return ["success" => false, "mensaje" => $usuarioAcreedor['mensaje'], "error" => true];                    
                    }
                }                
            }

            $usuarioDeudor = $this->ObtenerIdUsuario($video->id_usuarioDeudor);
            if ($usuarioDeudor) {
                if(is_array($usuarioDeudor) && array_key_exists('error', $usuarioDeudor)){
                    if(isset($usuarioDeudor['error']) && $usuarioDeudor['error'] === true){
                        return ["success" => false, "mensaje" => $usuarioDeudor['mensaje'], "error" => true];                    
                    }
                }                
            }
           
            // $query = "INSERT INTO deudas_vistas_usuario(usuario_deudor, usuario_acreedor, cantidad_deuda, tipo_video, estado_deuda) 
            // VALUES (?, ?, 1, ?, 'PENDIENTE') 
            // ON DUPLICATE KEY UPDATE cantidad_deuda = cantidad_deuda + 1, estado_deuda = CASE 
            //    WHEN cantidad_deuda = 0 THEN 'pagado' 
            //    ELSE 'pendiente' 
            //  END";
            $query = "INSERT INTO deudas_vistas_usuario (usuario_deudor, usuario_acreedor, cantidad_deuda, tipo_video, estado_deuda)
            VALUES (?, ?, 1, ?, 'pendiente')
            ON DUPLICATE KEY UPDATE 
                cantidad_deuda = cantidad_deuda + VALUES(cantidad_deuda),
            estado_deuda = CASE 
                WHEN cantidad_deuda - VALUES(cantidad_deuda) <= 0 THEN 'pagado'
                ELSE 'pendiente'
                END";
            $stmt  = $this->conn->prepare($query);
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
            echo  $e->getMessage();
            // http_response_code(500);           
            return ["success" => false, "error" => "true", "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
           
        } catch (Exception $e) {
           echo  $e->getCode();
            // http_response_code(500);           
            return ["success" => false, "error" => "true", "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }

    }

    public function RegistrarVistas(GeneracionVistas $video)
    {

        try {

            if($video->tipoVideo == "" || empty($video->tipoVideo)){
                $this->table = "videos_youtube";
            }else if($video->tipoVideo == "video"){
                $this->table = "videos_youtube";
            }else if($video->tipoVideo == "short"){
                $this->table = "short_youtube";
            }
           
            // if(!$this->ValidarLimitesVideos($video)){
            //     return ["success" => false, "mensaje" => "Ha llegado al Limite de Videos Permitidos", "error" => false]; 
            // }
           
            // $video->idVideo = $this->funcionGeneral->getYoutubeId($video->url_video);
            // if($video->idVideo == "" || empty($video->idVideo)){
            //     return ["success" => false, "mensaje" => "La URL del Video no es Valida", "error" => true]; 
            // }
 
            // $resultado = $this->ObtenerIdVideo($video);                 
            // if ($resultado) {
            //     if(is_array($resultado) && array_key_exists('error', $resultado)){
            //         if(isset($resultado['error']) && $resultado['error'] === true){
            //             return ["success" => false, "mensaje" => $resultado['mensaje'], "error" => true];                    
            //         }
            //     }                
            //     return ["success" => false, "duplicado" => true];
            // }
           
            // $usuario->rutaFotousuario = $this->tempRuta . trim($usuario->nombre_usuario).".png";
            $query = "INSERT INTO vistas (id_video, tipo_video, usuario_view, usuario_video) VALUES (?, ?, ?, ?)";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([   
                $video->id_video,        
                $video->tipoVideo,               
                $video->id_usuario,
                $video->id_usuarioDeudor, 
            ]);
            
            if ($stmt->rowCount() > 0) {
                // $this->crearFoto->crearFotos($this->tempRuta, $usuario->foto_usuario, $usuario->nombre_usuario);
                return ["success" => true];
            } else {
                return ["success" => false];
            }

        } catch (PDOException $e) {
           
            // http_response_code(500);           
            return ["success" => false, "error" => "true", "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
           
        } catch (Exception $e) {
           
            // http_response_code(500);           
            return ["success" => false, "error" => "true", "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }

    }

    public function ActualizarVistasDeudas(UsuariosVideos $video)
    {

        try {

            if($video->tipoVideo == "" || empty($video->tipoVideo)){
                $this->table = "videos_youtube";
            }else if($video->tipoVideo == "video"){
                $this->table = "videos_youtube";
            }else if($video->tipoVideo == "short"){
                $this->table = "short_youtube";
            }
           
            $resultado = $this->obtenerPorIdVideo($video);
            if (!$resultado) {
                return ["success" => false, "existe" => false];
            }
            
            $video->idVideo = $this->funcionGeneral->getYoutubeId($video->url_video);
            if($video->idVideo == "" || empty($video->idVideo)){
                return ["success" => false, "mensaje" => "La URL del Video no es Valida", "error" => true]; 
            }

            $resultado = $this->obtenerExistenciaVideo($video, false);                 
            if ($resultado) {
                if(is_array($resultado) && array_key_exists('error', $resultado)){
                    if(isset($resultado['error']) && $resultado['error'] === true){
                        return ["success" => false, "mensaje" => $resultado['mensaje'], "error" => true];                    
                    }
                }                
                return ["success" => false, "duplicado" => true];
            }

            // $usuario->rutaFotousuario = $this->tempRuta . trim($usuario->nombre_usuario).".png";
            $query = "UPDATE " . $this->table . " SET id_canal_youtube = ?, titulo_video = ?, descripcion_video = ?, url_video =?, tiempo_duracion =?, idvideo=? WHERE id_video = ?";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([
                $video->id_canal_youtube,
                strtoupper($video->titulo_video),
                $video->descripcion_video,
                $video->url_video,
                $video->tiempo_duracion,
                $video->idVideo,
                $video->id_video,
            ]);

            // $actualizadoFoto = $this->crearFoto->crearFotos($this->tempRuta, $usuario->foto_usuario, $usuario->nombre_usuario);
            if ($stmt->rowCount() > 0) {
                return ["success" => true, "actualizado" => true];
            } else {
                // if($actualizadoFoto){
                //     return ["success" => true, "actualizado" => true];
                // }else{
                //     return ["success" => true, "actualizado" => false];
                // }
                return ["success" => true, "actualizado" => false];
            }

        } catch (PDOException $e) {
            http_response_code(500);
            // echo $e->getMessage();
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            http_response_code(500);
            // echo $e->getMessage();
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }

    }

    public function ValidarLimitesVideos(UsuariosVideos $usuario){
        $limite = $this->ObtenerLimitesVideos($usuario);
         if($limite){
            if(is_array($limite)){
                if(array_key_exists('error', $limite)){
                    if(isset($limite) && $limite['error'] === true){
                        return false;
                    }
                }
               
                if(array_key_exists('success', $limite)){                    
                    if (isset($limite['success']) && $limite['success'] === true) {                     
                        return true;
                    }
                }
                return false;

            }else{
                false;
            }
        }

        return false;
    }

    public function ObtenerLimitesVideos(UsuariosVideos $usuario){
        try{

            $query = "SELECT COUNT(*) AS total_videos FROM " . $this->table . " WHERE id_canal_youtube = ?";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([$usuario->id_canal_youtube]);
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);

            if($datos){
                $valoresParametro =  $this->parametros->buscarParametros(ParametrosTabla::LIMITE_URL_VIDEO->value);
                if($valoresParametro){
                    if(is_array($valoresParametro) && array_key_exists('error', $valoresParametro)){
                        if(isset($valoresParametro) && $valoresParametro['error'] === true){
                            if(isset($valoresParametro['mensaje'])){
                                return ["success" => false,  "error" => true, "mensaje" => $valoresParametro['mensaje']];
                            }else{
                                return ["success" => false,  "error" => true, "mensaje" => "No se pudo comunicar con el servidor"];
                            }
                        }
                    }
                    if(isset($valoresParametro['valor_parametro'])){
                        $valorLimites = $valoresParametro['valor_parametro'];
                    }else{
                        $valorLimites = LIMITES_URL_VIDEO;
                    }


                }else{
                    $valorLimites = LIMITES_URL_VIDEO;
                }
                if(is_array($datos) && array_key_exists('total_videos', $datos)){
                    if($datos['total_videos'] >= $valorLimites){
                        return ["success" => false,  "error" =>false, "mensaje" => "Ha llegado al Limite de Videos Permitidos"];
                    }else{
                    return ["success" => true, "error" => false, "mensaje" => "Puede Agregar mas Videos"];
                    }
                }else{
                    return ["success" => false,  "error" =>false, "mensaje" => "No se Han encontrado los limites"];
                }  
                
            }else{
                return ["success" => false,  "error" => false, "mensaje" => "No se pudo validar el Limite de Videos"];
            }

        }catch (PDOException $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage()) ];
        } catch (Exception $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
    }

    public function eliminarVideo($id)
    {

        try {
            sleep(1);
            $resultado = $this->obtenerPorIdVideo($id);
            if(is_array($resultado) && array_key_exists('error', $resultado)){
                if(isset($resultado) && $resultado['error'] == true){
                     if(is_array($resultado) && array_key_exists('mensaje', $resultado)){
                        if(isset($resultado['mensaje'])){
                            return ["success" => false,  "error" => true, "mensaje" => $resultado['mensaje']];
                        }else{
                            return ["success" => false,  "error" => true, "mensaje" => "Ocurrio una Excepcion en el Servidor"];
                        }
                     }

                }
            }

            if(!$resultado){
                 return ["success" => false,  "error" => false, "mensaje" => "El dato no Existe para Eliminar"];
            }
            
            $query = "DELETE FROM " . $this->table . " WHERE id_video = ?";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([$id]);

            // Verifica si se eliminó al menos una fila
            if ($stmt->rowCount() > 0) {
                // if($resultado){
                //     $nombreImg =  str_replace(' ', '', $resultado[0]['nombre_canal']);
                //     $rutaImagen = __DIR__ . "/../" .$this->tempRuta.trim($nombreImg).".png";
                // }
                // if (file_exists($rutaImagen)) {
                //     unlink($rutaImagen);
                // }
                return true; // Eliminado con éxito
            } else {
                return false; // No se eliminó (ID no existe)
            }
        }catch (PDOException $e) {
            http_response_code(500);
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            http_response_code(500);
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }

    }

    public function ValidarUsuario($datos) {
        try{
            if($datos){

                if($datos["es_activo"]=== false){
                    return ["success" => false,  "error" => false, "mensaje" => "El Usuario no esta Activo"];
                }
                
                if($datos['estado_usuario'] == EstadoUsuario::SIN_ACCESO->value || empty($datos['estado_usuario'])){                  
                    return ["success" => "false", "error" => false, "mensaje" => "Este Usuario no tiene Acceso al Sistema"];
                }  


                if($datos['estado_usuario'] == EstadoUsuario::SIN_ACCESO->value){
                    return ["success" => false,  "error" => false, "mensaje" => "Este Usuario no tiene Acceso al Sistema"];
                }else if($datos['estado_usuario'] == EstadoUsuario::BLOQUEADO_TEMPORALMENTE->value){
                    return ["success" => false,  "error" => false, "mensaje" => "Este Usuario esta Bloqueado Temporalmente"];
                }else if($datos['estado_usuario'] == EstadoUsuario::BLOQUEADO_X_INTENTOS->value){
                    return ["success" => false,  "error" => false, "mensaje" => "Este Usuario esta Bloqueado Por intentos Fallidos"];
                }
                else if($datos['estado_usuario'] == EstadoUsuario::BLOQUEADO_X_INACTIVIDAD->value){
                    return ["success" => false,  "error" => false, "mensaje" => "Este Usuario esta Bloqueado Por Inactividad"];
                }
                return ["success" => true,  "error" => false, "mensaje" => "Usuario Valido de Acceso"];
            }
            // $stmt = $this->conn->prepare("SELECT 1 FROM historial_login WHERE token_sesion = ? AND id_estado_sesion = 1");
            // $stmt->execute([$token]);
            // return $stmt->fetchColumn() ? true : false;
        }catch (PDOException $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
        
    }


}
