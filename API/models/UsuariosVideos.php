<?php
require_once __DIR__ .'/../config/init_config.php';

class UsuariosVideos
{
    private $conn;
    private $table = "videos_youtube";
    private $tableToken = "tokens_acceso";
    private $funcionGeneral;
    private $parametros;

    public $id_video, $id_canal_youtube, $titulo_video,  $url_video, $descripcion_video, $tiempo_duracion, $idVideo;
    public $vistas, $likes, $comentarios, $tipoVideo, $esActivo;
    
    public $tipo_sistema, $ip, $fechaEstadoUsuario, $token_sesion;
    public $foto_usuario;
    private $rutaFotousuario;
    public $id_usuario;
    private $tempRuta;
   
    public function __construct($db)
    {
        $this->conn = $db; 
        $this->funcionGeneral = new FuncionesGenerales();   
        $this->parametros = new Parametros($db);
        $this->tempRuta= "imagenes/fotos_usuarios/";
    }

    public function obtenerTodos($usuario)
    {

        try{
            
            // $query = "SELECT * FROM " . $this->table;
            $query = "SELECT *, 'Video' AS tipo, cy.nombre_canal FROM videos_youtube vy 
            INNER JOIN canales_youtube cy ON vy.id_canal_youtube = cy.id_canal_youtube
            INNER JOIN usuarios_youtube uy ON cy.id_usuario = uy.id_usuario
            WHERE uy.es_activo = 1 AND uy.id_usuario=?
            UNION ALL 
            SELECT *, 'Short' AS tipo, cy.nombre_canal FROM short_youtube sy 
            INNER JOIN canales_youtube cy ON sy.id_canal_youtube = cy.id_canal_youtube
            INNER JOIN usuarios_youtube uy ON cy.id_usuario = uy.id_usuario
            WHERE uy.es_activo = 1 AND uy.id_usuario=?";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([$usuario]);
            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
            $protocolo = trim($protocolo);
            $host = trim($_SERVER['HTTP_HOST']);
            $carpetaBase = trim(dirname(dirname($_SERVER['REQUEST_URI'])));
            $codigoBarrasURL = $protocolo . $host . $carpetaBase . '/';
            $codigoBarrasURL = trim($codigoBarrasURL);

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

    public function obtenerTodosPorId($usuario)
    {

        try{
           
            $query = "SELECT *, 'video' AS tipo, cy.nombre_canal FROM videos_youtube vy 
            INNER JOIN canales_youtube cy ON vy.id_canal_youtube = cy.id_canal_youtube
            INNER JOIN usuarios_youtube uy ON cy.id_usuario = uy.id_usuario
            WHERE uy.es_activo = 1 AND uy.id_usuario=?
            UNION ALL 
            SELECT *, 'short' AS tipo, cy.nombre_canal FROM short_youtube sy 
            INNER JOIN canales_youtube cy ON sy.id_canal_youtube = cy.id_canal_youtube
            INNER JOIN usuarios_youtube uy ON cy.id_usuario = uy.id_usuario
            WHERE uy.es_activo = 1 AND uy.id_usuario=?";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([$usuario, $usuario]);
            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($datos as &$fila) {

                if (array_key_exists('id_video', $fila)) {
                    $idvideo = $fila['id_video'];
                } 
   
                if (array_key_exists('id_video', $fila)) {
                    $idvideo = $fila['id_video'];
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
                    $fila['tipo']='<span  data-idtipo="short" class="badge bg-primary">Video</span>';
                    $fila['url_video'] = '<a href="https://www.youtube.com/shorts/'.$fila['idvideo'].'" target="_blank" class="btn btn-sm btn-outline-danger">Ver Short</a>';
                }else{
                    $fila['tipo']='<span data-idtipo="video" class="badge bg-success">Video</span>';
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
                $stmt->execute([$video->id_video]);
            }else if($video->tipoVideo == "video"){
                $this->table = "videos_youtube";
                $query = "SELECT * FROM " . $this->table . " WHERE idvideo= ?";
                $stmt  = $this->conn->prepare($query);
                $stmt->execute([$video->id_video]);
            }else if($video->tipoVideo == "short"){
                $this->table = "short_youtube";
                $query = "SELECT * FROM " . $this->table . " WHERE idvideo= ?";
                $stmt  = $this->conn->prepare($query);
                $stmt->execute([$video->id_video]);
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
            // $query = "SELECT u.*, p.documento_personal, p.nombre_personal, p.apellido_personal, 
            //          CONCAT(p.nombre_personal, ' ', p.apellido_personal) AS nombres 
            //   FROM " . $this->table . " u 
            //   LEFT JOIN personal p ON u.id_persona = p.id_persona 
            //   WHERE  $campo LIKE ?";

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

    public function crearVideos(UsuariosVideos $video)
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
            $query = "INSERT INTO " . $this->table . " (id_canal_youtube, titulo_video, descripcion_video, url_video, tiempo_duracion, idvideo) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([   
                $video->id_canal_youtube,             
                strtoupper($video->titulo_video),                
                $video->descripcion_video,               
                $video->url_video,
                $video->tiempo_duracion, 
                $video->idVideo
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

    public function actualizarVideos(UsuariosVideos $video)
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


    public function ObtenrImagenVideo($channelId)
    {
        try{
            
            $apiKey     = KEY_API_YOTUBE; // 👉 reemplázala por tu API KEY
            $maxResults = 5;
            $url = "https://www.googleapis.com/youtube/v3/search?part=snippet&channelId={$channelId}&maxResults={$maxResults}&order=date&type=video&key={$apiKey}";

            $response = @file_get_contents($url);
            $data = json_decode($response, true);

            if (!empty($data['items'])) {
                $snippet = $data['items'][0]['snippet'];
                $title   = $snippet['title'];
                $thumb   = $snippet['thumbnails']['high']['url']; // default | medium | high

                return $thumb;
            } else {
                return "";
            }          

        }catch (PDOException $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage()) ];
        } catch (Exception $e) {
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


    public function esSesionValida($token) {
        try{
            $stmt = $this->conn->prepare("SELECT 1 FROM historial_login WHERE token_sesion = ? AND id_estado_sesion = 1");
            $stmt->execute([$token]);
            return $stmt->fetchColumn() ? true : false;
        }catch (PDOException $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
        
    }


    public function CrearHistorialLogin($id_usuario, $estadoLogin) {
        $fechaActual = date('Y-m-d H:i:s');
        $this->table = "historial_login";
        $id_estado_sesion = 0;
        $query = "UPDATE " . $this->table . " SET estado_usuario = ?, fecha_fin_sesion = ? WHERE id_usuario= ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id_estado_sesion, $fechaActual,  $id_usuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function CrearTokenUsuario(UsuariosYoutube $usuario)
    {

        try {

           do {
                // Generar token aleatorio
                $token = bin2hex(random_bytes(32)); // 32 caracteres hexadecimales
                $query = "SELECT COUNT(*) as total FROM tokens_acceso WHERE token_generado = ? AND estado_token = 'A'";
                $stmt = $this->conn->prepare($query);
                // Pasar los parámetros como array
                $stmt->execute([$token]);
                // Obtener el resultado
                $existe = $stmt->fetchColumn() > 0;

            } while ($existe);
           
            $usuario->token_sesion = $token;
            $tiempoSesion = TIEMPOEXPIRASESIONLOGIN; // en minutos
            $fechaVencimiento = date("Y-m-d H:i:s", strtotime("+$tiempoSesion minutes"));
            
            $query = "INSERT INTO " . $this->tableToken . " (id_usuario, token_generado, tiempo_duracion, fecha_vence) VALUES (?, ?, ?, ?)";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([                
                $usuario->id_usuario,
                $usuario->token_sesion,                             
                $tiempoSesion,
                $fechaVencimiento,
            ]);
            
            if ($stmt->rowCount() > 0) {
                // $this->crearFoto->crearFotos($this->tempRuta, $usuario->foto_usuario, $usuario->nombre_usuario);
                return ["success" => true];
            } else {
                return ["success" => false];
            }

        } catch (PDOException $e) {
            // http_response_code(500);  
            // echo $e->getMessage();         
            return ["success" => false, "error" => "true", "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
           
        } catch (Exception $e) {
            // echo $e->getMessage();  
            // http_response_code(500);           
            return ["success" => false, "error" => "true", "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }

    }

}
