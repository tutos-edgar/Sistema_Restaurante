<?php
require_once __DIR__ .'/../config/init_config.php';

class EjecucionTareas
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
    private $objVideos;
   
    public function __construct($db)
    {
        $this->conn = $db; 
        $this->funcionGeneral = new FuncionesGenerales();   
        $this->parametros = new Parametros($db);
        // $this->objCanales = new UsuariosCanales($db);
        $this->tempRuta= "imagenes/fotos_usuarios/";
    }

    // BUSQUEADA DE VIDEOS Y CANALES
    public function ObtenerCardTareasPendientes(EjecucionTareas $usuario)
    {

        try{            
            $html = '';
            // Deudas que debo
            $tareasPendientes = $this->ObtenerTareasPendientes($usuario->id_usuario);
            if(!$tareasPendientes){
                $html .= '<div class="col-12">
                        <div class="card text-center border-danger">
                            <div class="card-body">
                                <h5 class="card-title text-danger">⚠️ Usted No Tiene Tareas Pendientes</h5>
                                <p class="card-text">Intenta Revisar Más Tarde.</p>
                            </div>
                        </div>
                    </div>';
                return ["success" => false,  "error" => true, "mensaje" => "No tiene Tareas Disponibles", "datos" => $html];
            }else{
                if(is_array($tareasPendientes) && array_key_exists('error', $tareasPendientes)){
                    if(isset( $tareasPendientes['error']) && $tareasPendientes['error'] === true){
                        if(isset( $tareasPendientes['mensaje'])){
                            return ["success" => "false", "mensaje" => $tareasPendientes['mensaje'], "datos" => []];
                        }
                        return ["success" => "false", "mensaje" => "Ocurrio un Error al Validar las Tareas", "datos" => []];
                    }               
                }
            }
            
            foreach ($tareasPendientes as &$fila) {

                    if (array_key_exists('usuario_acreedor', $fila)) {
                        $idUsuarioAcreedor = $fila['usuario_acreedor'];
                    } 

                    // $url =$this->objCanales->ObtenerImgCanal($fila['idcanal']);
                    // $cantidad = $this->ObtenerCantidadVideos($idDato, "todos");
                    // $totalVideos = $cantidad['total_video'];
                    $cantidadTareas = $fila['cantidad_deuda'];
                    // if($totalVideos > 0){
                        $html .= '<div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card card-custom-v" onclick="ObtenerVideosPorUsuario('.$idUsuarioAcreedor.')">
                            <img src="../img/tarea_pendiente.png" class="card-img-top" alt="Tarea Pendiente">
                            <div class="card-body">
                            <h5 class="card-title">'.$fila['alias_usuario'].'</h5>
                            <p class="card-text">Tareas Pendientes</p>
                            <p class="mb-1">👁️ Vistas Pendientes: '.$cantidadTareas.'</p>                        
                            </div>
                        </div>
                        </div>';
                    // }
                
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
                    WHERE d.usuario_deudor = ? AND d.cantidad_deuda > 0  AND d.estado_deuda='pendiente'";

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

            // $query = "INSERT INTO deudas_vistas_usuario (usuario_deudor, usuario_acreedor, cantidad_deuda, tipo_video, estado_deuda)
            // VALUES (?, ?, 1, ?, 'pendiente')
            // ON DUPLICATE KEY UPDATE 
            //     cantidad_deuda = cantidad_deuda - VALUES(cantidad_deuda),
            // estado_deuda = CASE 
            //     WHEN cantidad_deuda - VALUES(cantidad_deuda) <= 0 THEN 'pagado'
            //     ELSE 'pendiente'
            //     END";
            // $query = "INSERT INTO deudas_vistas_usuario (usuario_deudor, usuario_acreedor, cantidad_deuda, tipo_video, estado_deuda)
            // VALUES (?, ?, 1, ?, 'pendiente')
            // ON DUPLICATE KEY UPDATE 
            //     cantidad_deuda = GREATEST(cantidad_deuda - VALUES(cantidad_deuda), 0),
            //     estado_deuda = CASE 
            //         WHEN GREATEST(cantidad_deuda - VALUES(cantidad_deuda), 0) = 0 THEN 'pagado'
            //         ELSE 'pendiente'
            //     END;";

            // $query = "INSERT INTO deudas_vistas_usuario (usuario_deudor, usuario_acreedor, cantidad_deuda, tipo_video, estado_deuda)
            // VALUES (?, ?, 1, ?, 'pendiente')
            // ON DUPLICATE KEY UPDATE 
            //     cantidad_deuda = GREATEST(cantidad_deuda - VALUES(cantidad_deuda), 0),
            //     estado_deuda = CASE 
            //         WHEN GREATEST(cantidad_deuda - VALUES(cantidad_deuda), 0) <= 0 THEN 'pagado'
            //         ELSE estado_deuda
            //     END";

            $query = "INSERT INTO deudas_vistas_usuario (usuario_deudor, usuario_acreedor, cantidad_deuda, tipo_video, estado_deuda)
            VALUES (?, ?, 1, ?, 'pendiente')
            ON DUPLICATE KEY UPDATE 
                cantidad_deuda = @nueva := GREATEST(cantidad_deuda - VALUES(cantidad_deuda), 0),
                estado_deuda = IF(@nueva = 0, 'pagado', 'pendiente');";

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

    public function obtenerExistenciaVideo(EjecucionTareas $video, $tipo)
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

    

    public function RegistrarVistas(EjecucionTareas $video)
    {

        try {

            if($video->tipoVideo == "" || empty($video->tipoVideo)){
                $this->table = "videos_youtube";
            }else if($video->tipoVideo == "video"){
                $this->table = "videos_youtube";
            }else if($video->tipoVideo == "short"){
                $this->table = "short_youtube";
            }
           
            if(!$this->ValidarLimitesVideos($video)){
                return ["success" => false, "mensaje" => "Ha llegado al Limite de Videos Permitidos", "error" => false]; 
            }
           
            $video->idVideo = $this->funcionGeneral->getYoutubeId($video->url_video);
            if($video->idVideo == "" || empty($video->idVideo)){
                return ["success" => false, "mensaje" => "La URL del Video no es Valida", "error" => true]; 
            }
 
            $resultado = $this->ObtenerIdVideo($video);                 
            if ($resultado) {
                if(is_array($resultado) && array_key_exists('error', $resultado)){
                    if(isset($resultado['error']) && $resultado['error'] === true){
                        return ["success" => false, "mensaje" => $resultado['mensaje'], "error" => true];                    
                    }
                }                
                return ["success" => false, "duplicado" => true];
            }
           
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

    public function ActualizarVistasDeudas(EjecucionTareas $video)
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

    public function ValidarLimitesVideos(EjecucionTareas $usuario){
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

    public function ObtenerLimitesVideos(EjecucionTareas $usuario){
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