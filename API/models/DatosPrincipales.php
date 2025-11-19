<?php
require_once __DIR__ .'/../config/init_config.php';
require_once __DIR__ .'/../models/EjecucionTareas.php';

class DatosPrincipales
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
    public $id_usuario, $id_usuarioDeudor, $id_usuarioAcreedor;
    private $tempRuta;

    private $objCanales;
    private $objTareas;
    private $objVideos;
   
    public function __construct($db)
    {
        $this->conn = $db; 
        $this->funcionGeneral = new FuncionesGenerales();   
        $this->parametros = new Parametros($db);
        $this->objTareas = new EjecucionTareas($db);
        $this->tempRuta= "imagenes/fotos_usuarios/";
    }

    // BUSQUEADA DE VIDEOS Y CANALES
    public function ObtenerCardPrincipales(DatosPrincipales $usuario)
    {

        try{            
            $html = '';
            // Deudas que debo
            $tareasPendientes = $this->cantidadTareasPendiente($usuario->id_usuario);
            
            if($tareasPendientes){
                if(is_array($tareasPendientes) && array_key_exists('error', $tareasPendientes)){                      
                    if(isset($tareasPendientes['error']) && $tareasPendientes['error'] === true){
                        if(is_array($tareasPendientes) && array_key_exists('mensaje', $tareasPendientes)){
                            echo json_encode(["success" => false, "mensaje" => $tareasPendientes['mensaje'], "error" => true, "datos" => []]);
                            exit();
                        }else{
                            echo json_encode(["success" => false, "mensaje" => "Ocurrio Una Exepcion en el Registro", "error" => true, "datos" => []]);
                            exit();
                        }
                    }                        
                }
                $cantidadTareas = $tareasPendientes['datos'];

                $html.= '<div class="col-12 col-md-3">                
                        <a href="menu_ejecutar_tareas.php" class="card card-custom-c text-center p-3 d-block">
                            <i class="bi bi-shield-lock fs-1 text-danger"></i>
                            <h5 class="mt-2">TAREAS PENDIENTES</h5>
                            <p class="fs-4 fw-bold">'.(isset($tareasPendientes['datos']) ? $tareasPendientes['datos'] : '0').'</p>
                        </a>
                    </div>';

                // $html .= '<div class="col-md-3">
                //         <div class="card card-custom text-center p-3">
                //             <i class="bi bi-hourglass fs-1 text-danger"></i>
                //             <h5 class="mt-2">TAREAS</h5>
                //             <p class="fs-4 fw-bold">'.$tareasPendientes['datos'].'</p>
                //         </div>
                //     </div>';
                
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

    public function ObtenerTareasPendientes($usuario)
    {
        try{

            $query = "SELECT d.*, u.alias_usuario
                    FROM deudas_vistas_usuario d 
                    JOIN usuarios_youtube u ON d.usuario_acreedor=u.id_usuario
                    WHERE d.usuario_deudor = ? AND d.estado_deuda='pendiente'";

            $stmt  = $this->conn->prepare($query);
            $stmt->execute([$usuario]);
            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);          
            return  $datos;
            
        }catch (PDOException $e) {
            http_response_code(500);
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            http_response_code(500);
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
        
        
    }

    public function ObtenerCantidadTareasPendientes($usuario)
    {

        try{
            $html = '';
            $cantidadVideos = $this->ObtenerCantidadVideosPorUsuario($usuario, "video");
            if($cantidadVideos['total_video'] <= 0){
                $html .= '<div class="col-12">
                        <div class="card text-center border-danger">
                            <div class="card-body">
                                <h5 class="card-title text-danger">⚠️ Usted No cuenta con Videos disponibles</h5>
                                <p class="card-text">Ingrese Videos para Generar Vistas.</p>
                            </div>
                        </div>
                    </div>';
                return ["success" => false,  "error" => true, "mensaje" => "No tiene Shorts Disponibles", "datos" => $html];
            }
            
            $query = "SELECT *, 'video' AS tipo, cy.nombre_canal FROM videos_youtube vy 
            INNER JOIN canales_youtube cy ON vy.id_canal_youtube = cy.id_canal_youtube
            INNER JOIN usuarios_youtube uy ON cy.id_usuario = uy.id_usuario
            WHERE uy.id_usuario <> ?";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([$usuario]);
            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $html = '';
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

            return  ["success" => true,  "error" => false, "mensaje" => "Se Generaron las Card",  "datos" => $html];;

            
        }catch (PDOException $e) {
            http_response_code(500);
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            http_response_code(500);
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
        
        
    }

    public function ObtenerCardVideosPorUsuario(EjecucionTareas $usuario)
    {

        try{

             $query = "SELECT *, 'video' AS tipo FROM videos_youtube vy 
                INNER JOIN canales_youtube cy ON cy.id_canal_youtube = vy.id_canal_youtube 
                WHERE cy.id_usuario = ?
                UNION ALL
                SELECT *, 'short' AS tipo  FROM short_youtube sy 
                INNER JOIN canales_youtube cy ON cy.id_canal_youtube = sy.id_canal_youtube 
                WHERE cy.id_usuario = ?";

            $stmt  = $this->conn->prepare($query);
            $stmt->execute([$usuario->id_usuarioAcreedor, $usuario->id_usuarioAcreedor]);
            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $html = '';

            if ($stmt->rowCount() > 0){
            
                foreach ($datos as &$fila) {

                    if (array_key_exists('idvideo', $fila)) {
                        $idDato = $fila['idvideo'];
                    }                    
                   
                        $html .= '<div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card card-custom-v" onclick=window.location.href="vista_video_tareas.php?id='.$idDato.'&tipo='.$fila['tipo'].'&usuario='.$fila['id_usuario'].'">
                            <img src="../img/video_card.jpeg" class="card-img-top" alt="'.$fila['titulo_video'].'">
                            <div class="card-body">
                            <h5 class="card-title">'.$fila['titulo_video'].'</h5>
                            <p class="card-text">'.$fila['descripcion_video'].'</p>
                            <p class="mb-1">⏱️ Duración: '.$fila['tiempo_duracion'].'</p>
                            <p class="views">🎥 Tipo : '.strtoupper($fila['tipo']).'</p>
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

    public function RegistrarTarea(EjecucionTareas $video)
    {

        try {
           
            // $this->objVideos->tipoVideo = $video->tipoVideo;
            // $this->objVideos->idVideo = $video->idVideo;
            // $existenciaVideo = $this->ObtenerIdVideo($this->objVideos);
           
            $existenciaVideo = $this->ObtenerIdVideo($video);
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
            // VALUES (?, ?, 1, ?, 'pendiente') 
            // ON DUPLICATE KEY UPDATE cantidad_deuda = cantidad_deuda - 1, estado_deuda = CASE 
            //    WHEN cantidad_deuda = 0 THEN 'pagado' 
            //    ELSE 'pendiente' 
            //  END";
            $query = "INSERT INTO deudas_vistas_usuario (usuario_deudor, usuario_acreedor, cantidad_deuda, tipo_video, estado_deuda)
            VALUES (?, ?, 1, ?, 'pendiente')
            ON DUPLICATE KEY UPDATE 
                cantidad_deuda = cantidad_deuda - VALUES(cantidad_deuda),
            estado_deuda = CASE 
                WHEN cantidad_deuda - VALUES(cantidad_deuda) <= 0 THEN 'pagado'
                ELSE 'pendiente'
                END";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([   
                $video->id_usuario,
                $video->id_usuarioAcreedor,              
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

    public function ObtenerIdVideo(EjecucionTareas $video)
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

    public function cantidadTareasPendiente($idusuario){
         
        try{
            
            $query = "SELECT sum(d.cantidad_deuda) as cantidad
                    FROM deudas_vistas_usuario d 
                    JOIN usuarios_youtube u ON d.usuario_acreedor=u.id_usuario
                    WHERE d.usuario_deudor = ? AND d.estado_deuda='pendiente'";

            $stmt  = $this->conn->prepare($query);
            $stmt->execute([$idusuario]);
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);
            return ["success" => true,  "error" => false, "mensaje" => "Se encontraron Tareas Pendientes ", "datos" => $datos['cantidad']];
        }catch (PDOException $e) {
            http_response_code(500);
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            http_response_code(500);
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } 
    }









    public function ActualizacionDeTareas($usuario)
    {

        try{            
            $html = '';

            $cantidadVideos = $this->ObtenerCantidadVideosPorUsuario($usuario, "short");
            if($cantidadVideos['total_video'] <= 0){
                $html .= '<div class="col-12">
                        <div class="card text-center border-danger">
                            <div class="card-body">
                                <h5 class="card-title text-danger">⚠️ Usted No cuenta con Shorts disponibles</h5>
                                <p class="card-text">Ingrese Shorts para Generar Vistas.</p>
                            </div>
                        </div>
                    </div>';
                return ["success" => false,  "error" => true, "mensaje" => "No tiene Shorts Disponibles", "datos" => $html];
            }

            $query = "SELECT *, 'short' AS tipo, cy.nombre_canal FROM short_youtube sy 
            INNER JOIN canales_youtube cy ON sy.id_canal_youtube = cy.id_canal_youtube
            INNER JOIN usuarios_youtube uy ON cy.id_usuario = uy.id_usuario
            WHERE uy.id_usuario <> ?";
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
                        <div class="card text-center border-danger">
                            <div class="card-body">
                                <h5 class="card-title text-danger">⚠️ No hay registros disponibles</h5>
                                <p class="card-text">Intenta nuevamente más tarde.</p>
                            </div>
                        </div>
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


    public function ObtenerCardVideosPorCanalesYoutube($usuario)
    {

        try{

            $query = "SELECT *, 'video' AS tipo, cy.nombre_canal FROM videos_youtube vy 
            INNER JOIN canales_youtube cy ON vy.id_canal_youtube = cy.id_canal_youtube
            INNER JOIN usuarios_youtube uy ON cy.id_usuario = uy.id_usuario
            WHERE uy.es_activo = 1 AND cy.id_canal_youtube =  ?
            UNION ALL 
            SELECT *, 'short' AS tipo, cy.nombre_canal FROM short_youtube sy 
            INNER JOIN canales_youtube cy ON sy.id_canal_youtube = cy.id_canal_youtube
            INNER JOIN usuarios_youtube uy ON cy.id_usuario = uy.id_usuario
            WHERE uy.es_activo = 1 AND cy.id_canal_youtube = ?";

            $stmt  = $this->conn->prepare($query);
            $stmt->execute([$usuario, $usuario]);
            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $html = '';

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
                WHERE sy.id_canal_youtube = ?";
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
                WHERE sy.id_usuario = ?";
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
                    WHERE sy.id_usuario = ?
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

    public function ValidarTipoVistasUsuario($usuario){
        try{
            $cantidadVideos = $this->ObtenerCantidadVideosPorUsuario($usuario, "video");
            if($cantidadVideos['total_video'] <= 0){
                return ["success" => false,  "error" => true, "mensaje" => "No tiene Videos Disponibles"];
            }
            
            $cantidadShorts = $this->ObtenerCantidadVideosPorUsuario($usuario, "short");
            if($cantidadShorts['total_video'] <= 0){
                return ["success" => false,  "error" => true, "mensaje" => "No tiene Shorts Disponibles"];
            }


        }catch (PDOException $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } 
    }

    // VERIFICA SI EL VIDEO YA EXISTE
    public function obtenerPorIdVideo(EjecucionTareas $video)
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

   


}