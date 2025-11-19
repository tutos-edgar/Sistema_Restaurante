<?php
require_once __DIR__ .'/../config/init_config.php';

class UsuariosCanales
{
    private $conn;
    private $table = "canales_youtube";
    private $tableToken = "tokens_acceso";
    private $funcionGeneral;
    private $parametros;

    public $id_usuario, $id_canal_youtube, $nombre_canal,  $url_canal, $descripcion_canal, $idCanal, $suscriptores;
    public $tipoCanal, $esActivo;
    public $foto_cana;
    private $rutaFotoCanal;

    public $tipo_sistema, $ip, $fechaEstadoUsuario, $token_sesion;

    private $crearFoto;
    private $tempRuta;

    private $keyApi = KEY_API_YOTUBE;

    public function __construct($db)
    {
        $this->conn = $db;
        $this->funcionGeneral = new FuncionesGenerales();
        $this->parametros = new Parametros($db);
        $this->tempRuta= "imagenes/fotos_usuarios/";
        $this->crearFoto = new FuncionesGenerales();
    }

    public function obtenerTodos($usuario)
    {

        try{
           
            $query = "SELECT * FROM " . $this->table." WHERE id_usuario = ?";
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
                $idusuario = $fila['id_canal_youtube'];
                $rol = "ADMINISTRADOR";
                // $fila['foto_usuario'] = $codigoBarrasURL . str_replace(' ', '', $fila['foto_usuario']);

                if(strtoupper($rol) == "ADMINISTRADOR"){
                    $fila['botones'] = '
                        <div class="text-center">
                            <div class="btn-group">
                                <button data-id="' . $idusuario . '" class="btn btn-primary btn-sm btnEditar">
                                    <i class="material-icons"></i> Editar
                                </button>
                                <button data-id="' . $idusuario . '" class="btn btn-danger btn-sm btnEliminar">
                                    <i class="material-icons"></i> Eliminar
                                </button>
                            </div>
                        </div>
                    ';
                    $fila['suscriptores_count'] = '<span class="badge bg-success">'.$fila['suscriptores_count'].'</span>';
                    $fila['url_canal'] = '<a href="https://www.youtube.com/channel/'.$fila['idcanal'].'" target="_blank" class="btn btn-sm btn-outline-danger">Ver Canal</a>';
                }//else{
                //     $fila['botones'] = '<span class="badge bg-secondary">Sin Rol</span>';
                // }

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

    public function obtenerPorIdCanal($id)
    {
        try{
            sleep(1);
            $query = "SELECT * FROM " . $this->table." WHERE id_canal_youtube = ?";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([$id]);
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

    public function obtenerPorIdChanel($id)
    {
        try{
            $query = "SELECT * FROM " . $this->table." WHERE idcanal = ?";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([$id]);
            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $datos;
        }catch (PDOException $e) {
            http_response_code(500);
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            http_response_code(500);
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
    }

    public function obtenerPorIdUsuario($id)
    {
        try{

            $query = "SELECT * FROM " . $this->table." WHERE id_usuario = ?";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([$id]);
            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $datos;
        }catch (PDOException $e) {
            http_response_code(500);
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            http_response_code(500);
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
    }

    public function validarExistenciaCanal(UsuariosCanales $canal, $tipo)
    {
        try{
            if($tipo == true){
                $query = "SELECT * FROM " . $this->table." WHERE idcanal = ? ";
                $stmt  = $this->conn->prepare($query);
                $stmt->execute([$canal->idCanal]);
            }else{
                $query = "SELECT * FROM " . $this->table." WHERE id_canal_youtube <> ? AND idcanal = ? ";
                $stmt  = $this->conn->prepare($query);
                $stmt->execute([$canal->id_canal_youtube, $canal->idCanal]);
            }
            
            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $datos;
        }catch (PDOException $e) {
            http_response_code(500);
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            http_response_code(500);
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
    }

    public function obtenerPorIdPersona($id)
    {
        try{
             // $query = "SELECT * FROM " . $this->table . " WHERE id_usuario= ? OR id_persona =?";
            $query = "SELECT u.*, CONCAT(p.nombre_personal, ' ', p.apellido_personal) AS nombres, p.documento_personal FROM " . $this->table;
            $query .= " u INNER JOIN personal p ON u.id_persona = p.id_persona WHERE u.id_persona= ?";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([$id]);
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


    function obtenerChannelIdSearch($url) {
        $keyApi = KEY_API_YOTUBE;

        // Caso 1: URL con /channel/ID → se puede extraer directo
        if (preg_match('/youtube\.com\/channel\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return $matches[1];
        }
    
        // Caso 2: URL con /user/USERNAME
        if (preg_match('/youtube\.com\/user\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            $username = $matches[1];
            $apiUrl = "https://www.googleapis.com/youtube/v3/channels?part=id&forUsername=$username&key=$keyApi";

            $response = @file_get_contents($apiUrl);
            if ($response === false) return null;

            $data = json_decode($response, true);
            if (!empty($data['items'][0]['id'])) {
                return $data['items'][0]['id'];
            }
        }

        // Caso 3: URL con @handle (nuevo formato)
        if (preg_match('/youtube\.com\/@([a-zA-Z0-9_-]+)/', $url, $matches)) {
            $handle = $matches[1];

            // Buscar el canal con search usando el handle SIN @
            $apiUrl = "https://www.googleapis.com/youtube/v3/search?part=snippet&type=channel&q=$handle&key=$keyApi";
            // $urlValida = str_replace(' ', '', $apiUrl);
            $urlValida = preg_replace('/\s+/', '', $apiUrl);
            print($apiUrl. " - ");
            $response = @file_get_contents($urlValida);
            if ($response === false) return null;

            $data = json_decode($response, true);

            if (!empty($data['items'])) {
                foreach ($data['items'] as $item) {
                    // Validar que el título del canal contenga el handle (para evitar falsos positivos)
                    if (stripos($item['snippet']['channelTitle'], str_replace('-', ' ', $handle)) !== false) {
                        return $item['id']['channelId'];
                    }
                }
            }
           
        }

        return null; // No se pudo obtener el channelId
    }


    public function ObtenerIdVideo($id)
    {
        try{
            $query = "SELECT * FROM " . $this->table . " WHERE idvideo= ?";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([$id]);
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

    public function crearCanales(UsuariosCanales $canal)
    {

        try {
            sleep(1);
            if(!$this->ValidarLimitesCanales($canal)){
                return ["success" => false, "mensaje" => "Ha llegado al Limite de Canales Permitidos", "error" => false];
            }

            $resultado = $this->obtenerChannelId($canal->url_canal);            
            if ($resultado) {
                if(is_array($resultado) && array_key_exists('error', $resultado)){
                    if(isset($resultado['error']) && $resultado['error'] === true){
                        return ["success" => false, "mensaje" => $resultado['mensaje'], "error" => true];
                    }
                }
                $canal->idCanal = $resultado;                
            }else{
                return ["success" => false, "mensaje" => "Este no es un Canal Valido", "error" => false];
            }

            $resultado = $this->obtenerPorIdChanel($canal->idCanal);
            if($resultado){
                if(is_array($resultado) && array_key_exists('error', $resultado)){
                    if(isset($resultado['error']) && $resultado['error'] === true){
                        return ["success" => false, "mensaje" => $resultado['mensaje'], "error" => true];
                    }
                }
                return ["success" => false, "duplicado" => true];
            }

            $resultado = $this->obtenerEstadisticasCanal($canal->idCanal);
            if($resultado){
                if(is_array($resultado) && array_key_exists('suscriptores', $resultado)){
                    $canal->suscriptores = $resultado['suscriptores'];
                }
            }
           
            // $usuario->rutaFotousuario = $this->tempRuta . trim($usuario->nombre_usuario).".png";
            $query = "INSERT INTO " . $this->table . " (id_usuario, nombre_canal, url_canal, idcanal, descripcion_canal, suscriptores_count) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([
                $canal->id_usuario,
                strtoupper($canal->nombre_canal),
                $canal->url_canal,
                $canal->idCanal,
                $canal->descripcion_canal,
                $canal->suscriptores
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
            // http_response_code(500);
            // echo $e->getMessage();
            return ["success" => false, "error" => "true", "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }

    }
    

    //EXTRAER DATOS DE YOUTUBE
    function obtenerChannelId($url) {
        $keyApi = KEY_API_YOTUBE;
        // Caso 1: URL con /channel/ → se puede extraer directo
        if (preg_match('/youtube\.com\/channel\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return $matches[1];
        }

        // Caso 2: URL con /user/USERNAME
        if (preg_match('/youtube\.com\/user\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            $username = $matches[1];
            $apiUrl = "https://www.googleapis.com/youtube/v3/channels?part=id&forUsername=$username&key=$keyApi";

            $response = @file_get_contents($apiUrl);
            $data = json_decode($response, true);

            if (isset($data['items'][0]['id'])) {
                return $data['items'][0]['id'];
            }
        }

        // Caso 3: URL con @handle (nuevo formato)
        if (preg_match('/youtube\.com\/@([a-zA-Z0-9_-]+)/', $url, $matches)) {
            $handle = $matches[1];
            $apiUrl = "https://www.googleapis.com/youtube/v3/search?part=snippet&type=channel&q=@$handle&key=$keyApi";

            $response = @file_get_contents($apiUrl);
            $data = json_decode($response, true);

            if (isset($data['items'][0]['snippet']['channelId'])) {
                return $data['items'][0]['snippet']['channelId'];
            }
        }

        return null; // No se pudo obtener el channelId
    }

    function ObtenerEstadisticasCanal($channelId) {
        $keyApi = KEY_API_YOTUBE;
        $url = "https://www.googleapis.com/youtube/v3/channels?part=statistics&id=$channelId&key=$keyApi";

        $response = @file_get_contents($url);
        $data = json_decode($response, true);

        if (isset($data['items'][0]['statistics'])) {
            return [
                "suscriptores" => $data['items'][0]['statistics']['subscriberCount'],
                "videos"       => $data['items'][0]['statistics']['videoCount'],
                "vistas"       => $data['items'][0]['statistics']['viewCount']
            ];
        }
        return null;
    }

    function ObtenerImgCanal($idCanal){
        $keyApi = KEY_API_YOTUBE; // 👉 reemplázala por tu API KEY
        $channelId  = "UC_x5XG1OV2P6uZZ5FSM9Ttw"; // ID del canal

        $url = "https://www.googleapis.com/youtube/v3/channels?part=snippet&id={$idCanal}&key={$keyApi}";

        $response = @file_get_contents($url);
        $data = json_decode($response, true);

        if (!empty($data['items'])) {
            $snippet = $data['items'][0]['snippet'];
            $title   = $snippet['title'];
            $thumb   = $snippet['thumbnails']['high']['url']; // default | medium | high
            return $thumb;
            // echo "<h3>{$title}</h3>";
            // echo "<img src='{$thumb}' alt='Imagen del canal'>";
        } else {
            return "";
        }
    }

    function ObtenerVideosDeUnCanal($idCanal){
        $keyApi = KEY_API_YOTUBE;
        $searchUrl = "https://www.googleapis.com/youtube/v3/search?key={$keyApi}&channelId={$idCanal}&part=snippet,id&order=date&maxResults=50";
        $searchResponse = @file_get_contents($searchUrl);
        $searchResults = json_decode($searchResponse, true);
        if (!isset($searchResults['items'])) return [];

        return $searchResults['items'];
        
    }

    function ObtenerDetallesVideo($videoId){
        $keyApi = KEY_API_YOTUBE;
        $videosUrl = "https://www.googleapis.com/youtube/v3/videos?key={$keyApi}&id={$videoId}&part=snippet,contentDetails,statistics";
        $videosResponse = @file_get_contents($videosUrl);
        $videos = json_decode($videosResponse, true);

        if (!isset($videos['items'])) return [];

        return $videos['items'];

    }

    // function ObtenerDetallesVideoArray($videoId){
    //     $keyApi = KEY_API_YOTUBE;
        
    //     foreach($videoId as $video){

    //     }
    //     $videosUrl = "https://www.googleapis.com/youtube/v3/videos?key={$keyApi}&id={$videoId}&part=snippet,contentDetails,statistics";
    //     $videosResponse = file_get_contents($videosUrl);
    //     $videos = json_decode($videosResponse, true);

    //     if (!isset($videos['items'])) return [];

    //     return $videos['items'];

    // }



    public function actualizarCanal(UsuariosCanales $canal)
    {

        try {

            sleep(1);
            $resultado = $this->obtenerPorIdCanal($canal->id_canal_youtube);           
            if ($resultado) {               
                if(is_array($resultado) && array_key_exists('error', $resultado)){
                    if(isset($resultado['error']) && $resultado['error'] === true){
                         return ["success" => false, "mensaje" => $resultado['mensaje'], "error" => true];
                    }
                }                    
            }else{
                return ["success" => false, "existe" => false, "error" => false];
            }
            
            if(!isset($canal->suscriptores) || empty($canal->suscriptores)){
                if(is_array($resultado) && array_key_exists('suscriptores_count', $resultado)){                
                    $canal->suscriptores = $resultado['suscriptores_count'];
                }
            }
            
            $resultado = $this->obtenerChannelId($canal->url_canal);            
            if ($resultado) {
                if(is_array($resultado) && array_key_exists('error', $resultado)){
                    if(isset($resultado['error']) && $resultado['error'] === true){
                        return ["success" => false, "mensaje" => $resultado['mensaje'], "error" => true];
                    }
                }
                $canal->idCanal = $resultado;                
            }else{
                return ["success" => false, "mensaje" => "Este no es un Canal Valido", "error" => false];
            }
            
            $resultado = $this->obtenerEstadisticasCanal($canal->idCanal);
            if($resultado){
                if(is_array($resultado) && array_key_exists('suscriptores', $resultado)){
                    $canal->suscriptores = $resultado['suscriptores'];
                }
            }

            $resultado = $this->validarExistenciaCanal($canal, false);
            if ($resultado) {
                if(is_array($resultado) && array_key_exists('error', $resultado)){
                    if(isset($resultado['error']) && $resultado['error'] === true){
                        if(is_array($resultado) && array_key_exists('mensaje', $resultado)){
                            if(isset($resultado['mensaje'])){
                                return ["success" => false, "mensaje" => $resultado['mensaje'], "error" => true];
                            }else{
                                return ["success" => false, "mensaje" => "No fue posible realizar la operacion", "error" => true];
                            }
                        }
                        return ["success" => false, "mensaje" => "No fue posible realizar la operacion", "error" => true];
                    }
                } 
                return ["success" => false, "duplicado" => true];
            }

            // $canal->rutaFotousuario = $this->tempRuta . trim($canal->nombre_usuario).".png";
            $query = "UPDATE " . $this->table . " SET nombre_canal = ?, url_canal = ?, idcanal = ?, descripcion_canal = ?, suscriptores_count=? WHERE id_canal_youtube = ?";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([
                strtoupper($canal->nombre_canal),
                $canal->url_canal,
                $canal->idCanal,
                $canal->descripcion_canal,
                $canal->suscriptores,
                $canal->id_canal_youtube,
            ]);

            // $actualizadoFoto = $this->crearFoto->crearFotos($this->tempRuta, $canal->foto_usuario, $canal->nombre_usuario);
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
            // http_response_code(500);
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            // http_response_code(500);
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }

    }

    public function ValidarLimitesCanales(UsuariosCanales $canal){
        $limite = $this->ObtenerLimitesCanales($canal);

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

    public function ObtenerLimitesCanales(UsuariosCanales $canal){
        try{
            $query = "SELECT COUNT(*) AS total_canales FROM " . $this->table . " WHERE id_usuario = ?";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([$canal->id_usuario]);
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);
            if($datos){
                $valoresParametro =  $this->parametros->buscarParametros(ParametrosTabla::LIMITE_URL_CANAL->value);
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
                        $valorLimites = LIMITES_URL_CANAL;
                    }


                }else{
                    $valorLimites = LIMITES_URL_CANAL;
                }

                if($datos['total_canales'] >= $valorLimites){
                    return ["success" => false,  "error" =>false, "mensaje" => "Ha llegado al Limite de Videos Permitidos"];
                }else{
                   return ["success" => true, "error" => false, "mensaje" => "Puede Agregar mas Videos"];
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

    public function eliminarCanal($id)
    {

        try {
            sleep(1);
            $resultado = $this->obtenerPorIdCanal($id);
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
            
            $query = "DELETE FROM " . $this->table . " WHERE id_canal_youtube = ?";
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
