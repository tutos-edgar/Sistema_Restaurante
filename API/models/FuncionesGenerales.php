<?php
require_once __DIR__ .'/../config/init_config.php';
require_once __DIR__ .'/../config/database.php';
class FuncionesGenerales {

    function limpiarEntrada( $input ) {
        $input = trim($input); // Quita espacios
        $input = strip_tags($input);  // Elimina HTML y JS
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');  // Escapa caracteres
        $input =  filter_input(INPUT_POST, '$input', FILTER_SANITIZE_STRING);
        return $input;
    }


    function obtenerToke(){
        return bin2hex(random_bytes(32));
    }

    public function obtenerIpPublica()
    {
        $ip = "";
        $cabeceras_posibles = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR', // puede contener múltiples IPs
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR',
        ];

        foreach ($cabeceras_posibles as $key) {
            if (! empty($_SERVER[$key])) {
                $ips = explode(',', $_SERVER[$key]); // por si vienen múltiples IPs
                foreach ($ips as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                        return $ip;
                    }
                }
            }
        }

        return $ip;
    }

    function obtenerIPUsuario() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        }

        return $_SERVER['REMOTE_ADDR'];
    }

    function obtenerIPPublicaUsuario() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // Puede venir una lista, tomar la primera
            return trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]);
        }
        return $_SERVER['REMOTE_ADDR'];
    }

    function obtenerLocalizacionPorIP($ip) {
        $url = "http://ip-api.com/json/$ip?fields=status,message,country,regionName,city,lat,lon";
        $respuesta = @file_get_contents($url);
        $data = json_decode($respuesta, true);

        if ($data['status'] === 'success') {
            return [
                'pais' => $data['country'],
                'region' => $data['regionName'],
                'ciudad' => $data['city'],
                'latitud' => $data['lat'],
                'longitud' => $data['lon']
            ];
        } else {
            return ['error' => $data['message']];
        }
    }

    function obtenerLatitudLongitudPorIP($ip) {
        $url = "http://ip-api.com/json/$ip?fields=status,message,lat,lon";
        $respuesta = file_get_contents($url);
        $data = json_decode($respuesta, true);

        if ($data['status'] === 'success') {
            return [
                'latitud' => $data['lat'],
                'longitud' => $data['lon']
            ];
        } else {
            return ['error' => $data['message']];
        }
    }

    function obtenerInformacionIP($ip) {
        // Quitar el parámetro fields para obtener toda la información
        $url = "http://ip-api.com/json/$ip";
        $respuesta = file_get_contents($url);
        $data = json_decode($respuesta, true);

        if ($data['status'] === 'success') {
            // Retornar todos los datos disponibles (excepto el status)
            unset($data['status']);
            return $data;
        } else {
            return ['error' => $data['message']];
        }
    }

    public function BuscarDatos($tabla, $campo, $id)
    {

        try{

            $conexion = new Database();
            $db = $conexion->connect();
            $query = "SELECT * FROM ".$tabla." WHERE $campo = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$id]);
            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $datos;

        }catch (PDOException $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
        
    }

    // OBTENER DATOS DE LA TABLA
    // LLENAR DATOS DEL SELECT

    function llenarEstadoPersonalDiv(){
        $conexion = new Database();
        $db = $conexion->connect();
        $query = "SELECT * FROM estado_personal";
        $stmt = $db->prepare($query);
        $stmt->execute();

        $salida = "";

        if ($stmt->rowCount() > 0) {
            echo '<div class="col-lg-6">
                    <div class="form-group">
                        <label for="estadoPersonal" class="col-form-label">Estado Personal</label>
                        <select name="estadoPersonal" id="estadoPersonal" class="form-control">
                        <option value="0" selected disabled>Seleccione un dato</option>';
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '<option value="' . $row['id_estado_personal'] . '">' . $row['descripcion'] . '</option>';
            }
            echo '    </select>
                    </div>
                  </div>';

        } else {
            echo '<div class="col-lg-6">
                    <div class="form-group">
                        <label for="estadoPersonal" class="col-form-label">Estado Personal</label>
                        <select name="estadoPersonal" id="estadoPersonal" class="form-control">
                            <option value="">NO HAY ESTADOS DISPONIBLES</option>
                        </select>
                    </div>
                  </div>';
        }
    }


    function llenarEstadoPersonal($nombreID){
        try{
            $conexion = new Database();
            $db = $conexion->connect();
            $query = "SELECT * FROM estado_personal";
            $stmt = $db->prepare($query);
            $stmt->execute();

            $salida = "";

            if ($stmt->rowCount() > 0) {
                echo '<label for="estadoPersonal" class="col-form-label">Estado Personal</label>
                            <select name="'.$nombreID.'" id="'.$nombreID.'" class="form-control">
                            <option value="0" selected disabled>Seleccione un dato</option>';
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<option value="' . $row['id_estado_personal'] . '">' . $row['descripcion'] . '</option>';
                }
                echo '    </select>
                    ';

            } else {
                echo '
                            <label for="estadoPersonal" class="col-form-label">Estado Personal</label>
                            <select name="'.$nombreID.'" id="'.$nombreID.'" class="form-control">
                                <option value="">NO HAY ESTADOS DISPONIBLES</option>
                            </select>
                    ';
            }
        }catch(Exception $e){
            echo '
                            <label for="estadoPersonal" class="col-form-label">Estado Personal</label>
                            <select name="'.$nombreID.'" id="'.$nombreID.'" class="form-control">
                                <option value="">ESTADOS PERSONAL NO ENCONTRADOS</option>
                            </select>
                    ';
        }
    }

    function llenarRoles($nombreID){

        try{
            $conexion = new Database();
            $db = $conexion->connect();
            $query = "SELECT * FROM rol_usuarios";
            $stmt = $db->prepare($query);
            $stmt->execute();

            $salida = "";

            if ($stmt->rowCount() > 0) {
                echo '<label for="'.$nombreID.'" class="col-form-label">Roles</label>
                            <select name="'.$nombreID.'" id="'.$nombreID.'" class="form-control">
                            <option value="0" selected disabled>Seleccione un dato</option>';
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<option value="' . $row['id_rol'] . '">' . $row['nombre_rol'] . '</option>';
                }
                echo '    </select>
                    ';

            } else {
                echo '
                            <label for="'.$nombreID.'" class="col-form-label">Roles</label>
                            <select name="'.$nombreID.'" id="'.$nombreID.'" class="form-control">
                                <option value="">NO HAY ROLES DISPONIBLES</option>
                            </select>
                    ';
            }

        }catch(Exception $e){
            echo '
                            <label for="estadoPersonal" class="col-form-label">Roles</label>
                            <select name="'.$nombreID.'" id="'.$nombreID.'" class="form-control">
                                <option value="">ROLES NO ENCONTRADOS</option>
                            </select>
                    ';
        }
        
    }


    function llenarSeccionesProducto($nombreID){

        try{
            $conexion = new Database();
            $db = $conexion->connect();
            $query = "SELECT * FROM secciones_producto";
            $stmt = $db->prepare($query);
            $stmt->execute();

            $salida = "";

            if ($stmt->rowCount() > 0) {
                echo '<label for="'.$nombreID.'" class="col-form-label">Secciones Producto</label>
                            <select name="'.$nombreID.'" id="'.$nombreID.'" class="form-control">
                            <option value="0" selected disabled>Seleccione un dato</option>';
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<option value="' . $row['id_seccion_producto'] . '">' . $row['nombre_seccion'] . '</option>';
                }
                echo '    </select>
                    ';

            } else {
                echo '
                            <label for="'.$nombreID.'" class="col-form-label">Secciones Producto</label>
                            <select name="'.$nombreID.'" id="'.$nombreID.'" class="form-control">
                                <option value="">NO HAY SECCIONES DISPONIBLES</option>
                            </select>
                    ';
            }

        }catch(Exception $e){
            echo '
                            <label for="estadoPersonal" class="col-form-label">Secciones Producto</label>
                            <select name="'.$nombreID.'" id="'.$nombreID.'" class="form-control">
                                <option value="">SECCION PRODUCTO NO ENCONTRADOS</option>
                            </select>
                    ';
        }
        
    }

    function llenarProveedor($nombreID){
        try{
            $conexion = new Database();
            $db = $conexion->connect();
            $query = "SELECT * FROM proveedores";
            $stmt = $db->prepare($query);
            $stmt->execute();

            $salida = "";

            if ($stmt->rowCount() > 0) {
                echo '<label for="'.$nombreID.'" class="col-form-label">Proveedores</label>
                            <select name="'.$nombreID.'" id="'.$nombreID.'" class="form-control">
                            <option value="0" selected disabled>Seleccione un dato</option>';
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<option value="' . $row['id_proveedor'] . '">' . $row['nombre_proveedor'] . '</option>';
                }
                echo '    </select>
                    ';

            } else {
                echo '
                            <label for="'.$nombreID.'" class="col-form-label">Proveedores</label>
                            <select name="'.$nombreID.'" id="'.$nombreID.'" class="form-control">
                                <option value="">NO HAY PROVEEDORES DISPONIBLES</option>
                            </select>
                    ';
            }
            
        }catch(Exception $e){
            echo '
                            <label for="estadoPersonal" class="col-form-label">Proveedores</label>
                            <select name="'.$nombreID.'" id="'.$nombreID.'" class="form-control">
                                <option value="">PROVEEDORES NO ENCONTRADOS</option>
                            </select>
                    ';
        }
        
    }



    function llenarCanales($nombreID, $idUsuario){

        try{
           
            $conexion = new Database();
            $db = $conexion->connect();
            $query = "SELECT * FROM canales_youtube WHERE id_usuario =?";
            $stmt = $db->prepare($query);
            $stmt->execute([$idUsuario]);

            if ($stmt->rowCount() > 0) {
                echo '<label for="'.$nombreID.'" class="col-form-label">Canales</label>
                            <select name="'.$nombreID.'" id="'.$nombreID.'" class="form-control">
                            <option value="0" selected disabled>Seleccione un dato</option>';
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<option value="' . $row['id_canal_youtube'] . '">' . $row['nombre_canal'] . '</option>';
                }
                echo '    </select>
                    ';

            } else {
                echo '
                            <label for="'.$nombreID.'" class="col-form-label">Canales</label>
                            <select name="'.$nombreID.'" id="'.$nombreID.'" class="form-control">
                                <option value="">NO HAY CANALES DISPONIBLES</option>
                            </select>
                    ';
            }

        }catch(Exception $e){
            echo '
                            <label for="estadoPersonal" class="col-form-label">Canales</label>
                            <select name="'.$nombreID.'" id="'.$nombreID.'" class="form-control">
                                <option value="">CANALES NO ENCONTRADOS</option>
                            </select>
                    ';
        }
        
    }

    function validarCodigoDeError($errorCode, $mensaje){

        $errorMessage = "No se pudo Comunicar con el Servidor";

        // Detectar tipos de error
        if ($errorCode == 1049) {
            $errorMessage = "La base de datos especificada no existe.";
        } elseif ($errorCode == 2002) {
            $errorMessage = "No se pudo comunicar con el servidor de base de datos.";
        } elseif ($errorCode == 1045) {
            $errorMessage = "Usuario o contraseña de base de datos incorrectos.";
        }elseif ($errorCode == 2502) {
            $errorMessage = "La Tabla no Existe.";
        }elseif ($errorCode == "42S02") {
            $errorMessage = "La Tabla no Existe.";
        }elseif ($errorCode == "42000") {
            $errorMessage = "Error en la Consulta";
        }elseif ($errorCode == "1064") {
            $errorMessage = "Violación en la consulta";
        }else if ($errorCode == 23000) {
        // Extraer la columna duplicada desde el mensaje
            preg_match("/for key '(.+)'/", $mensaje, $matches);
            $columna = $matches[1] ?? 'UNIQUE';
            $errorMessage = "No se pudo registrar: el valor ya existe en la columna '$columna'";            
        }elseif ($errorCode == "HY093") {
            $errorMessage = "Los Parametros en la Tabla son Invalidos";
        }elseif ($errorCode == "42S22") {
            $errorMessage = "La Columna de la Tabla no Coincide";
        }

        
        // echo json_encode([
        //     "success" => false,
        //     "mensaje" => $errorMessage
        // ]);
        return $errorMessage;

    }


    function crearFotos($rutaCarpeta, $foto, $nombreFoto){
       
        $rutaFoto = __DIR__ . "/../";

        if(str_starts_with($foto, 'data:image')) {
            // nueva foto, guardamos archivo
            // $nombreArchivo = "imagenes/fotos_personal/" . uniqid() . ".png";
            list($tipo, $data) = explode(',', $foto);
            // file_put_contents(__DIR__ . '/../' . $nombreArchivo, base64_decode($data));
            // $fotoFinal = $nombreArchivo;
        
            $base64 = preg_replace('#^data:image/\w+;base64,#i', '', $foto);

            // $base64 = str_replace('data:image/png;base64,', '', $foto);
            $base64 = str_replace(' ', '+', $base64);
            $decoded = base64_decode($base64, true);
            $imageData = base64_decode($base64);

            if ($decoded === false) {
                echo json_encode([
                    "success" => "false",
                    "mensaje" => "La Foto del Personal enviado está corrupto o malformado.",
                    "error" => "Base64 inválido"
                ]);
                exit;
            }

            // Carpeta
            $rutaCarpeta = $rutaFoto. $rutaCarpeta;
            $rutaCarpetaDB = $rutaFoto. $rutaCarpeta;
            if (!file_exists($rutaCarpeta)) {
                mkdir($rutaCarpeta, 0775, true);
            }

            // Nombre personalizado
            $nombreArchivo = preg_replace('/[^a-zA-Z0-9]/', '', $nombreFoto) . '.png';
            $rutaCompleta = $rutaCarpeta . $nombreArchivo;

            if (file_exists($rutaCompleta)) {
                unlink($rutaCompleta);
            }

            // Guardar imagen
            file_put_contents($rutaCompleta, $imageData);

            // Guardar la ruta relativa en BD
            $rutaEnDB = $rutaCarpetaDB . $nombreArchivo;
            $nombreFoto = $rutaEnDB;
            return true;
        } 
        return false;

    }

    function EliminarFotos($rutaCarpeta, $nombreFoto){
       
        $rutaFoto = __DIR__ . "/../";

        $rutaImagen = __DIR__ . "/../" . $rutaCarpeta.$nombreFoto.".png";
            // Validar si el archivo existe antes de borrarlo
        if (file_exists($rutaImagen)) {
            unlink($rutaImagen);
        }

    }

    function getYoutubeId($url) {

        if (strpos($url, "youtube.com") === false && strpos($url, "youtu.be") === false) {
            return "";
        }
       
        // Separar por "/"
        $partes = explode("/", $url);
        $ultimaParte = end($partes);

        // Validar si contiene watch?v=
        if (strpos($ultimaParte, "watch?v=") !== false) {
            $ultimaParte = str_replace("watch?v=", "", $ultimaParte);
        }

      
        if (strpos($ultimaParte, "watch?v=") !== false) {
            $ultimaParte = str_replace("watch?v=", "", $ultimaParte);
        }

        if (strpos($url, "?si=") !== false) {
            // Cortamos la URL en la parte de "?si="
            $ultimaParte = explode("?si=", $ultimaParte)[0];
        }

        if (strpos($url, "&") !== false) {
            // Cortamos la URL en la parte de "?si="
            $ultimaParte = explode("&", $ultimaParte)[0];
        }

        return $ultimaParte;
    }

    function setYoutubeId($url) {
        // Separar por "/"
        $partes = explode("/", $url);
        $ultimaParte = end($partes);

        // Validar si contiene watch?v=
        if (strpos($ultimaParte, "watch?v=") !== false) {
            $ultimaParte = str_replace("watch?v=", "", $ultimaParte);
        }

      
        if (strpos($ultimaParte, "watch?v=") !== false) {
            $ultimaParte = str_replace("watch?v=", "", $ultimaParte);
        }

        if (strpos($url, "?si=") !== false) {
            // Cortamos la URL en la parte de "?si="
            $ultimaParte = explode("?si=", $ultimaParte)[0];
        }

        if (strpos($url, "&") !== false) {
            // Cortamos la URL en la parte de "?si="
            $ultimaParte = explode("&", $ultimaParte)[0];
        }

        return $ultimaParte;
    }

    function obtenerYoutubeVideoId($url) {
        // Validar si contiene youtube.com o youtu.be
        if (strpos($url, "youtube.com") !== false || strpos($url, "youtu.be") !== false) {
            
            // Caso 1: URL tipo https://www.youtube.com/watch?v=VIDEO_ID
            if (preg_match('/v=([a-zA-Z0-9_-]{11})/', $url, $matches)) {
                return $matches[1];
            }
            
            // Caso 2: URL corta tipo https://youtu.be/VIDEO_ID
            if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]{11})/', $url, $matches)) {
                return $matches[1];
            }
        }
        
        return null; // No es un link válido de YouTube
    }

    function obtenerCantidadVideos($canalId) {
       
        // API Key de Google Cloud (reemplaza con la tuya)
        $apiKey = "TU_API_KEY";

        // ID del canal (ejemplo: UC_x5XG1OV2P6uZZ5FSM9Ttw es el de Google Developers)
        $channelId = "UC_x5XG1OV2P6uZZ5FSM9Ttw";

        // Endpoint de la API
        $url = "https://www.googleapis.com/youtube/v3/channels?part=statistics&id=$channelId&key=$apiKey";

        // Obtener datos de la API
        $response = file_get_contents($url);
        $data = json_decode($response, true);

        // Extraer cantidad de videos
        if (isset($data['items'][0]['statistics']['videoCount'])) {
            $cantidadVideos = $data['items'][0]['statistics']['videoCount'];
            echo "📺 El canal tiene $cantidadVideos videos.";
        } else {
            echo "❌ No se pudo obtener la cantidad de videos.";
        }

    }

    function obtenerCantidadVideosDePlayList($playlistId) {
        $apiKey = "TU_API_KEY";
        $playlistId = "PLBCF2DAC6FFB574DE"; // Ejemplo

        $url = "https://www.googleapis.com/youtube/v3/playlists?part=contentDetails&id=$playlistId&key=$apiKey";

        $response = file_get_contents($url);
        $data = json_decode($response, true);

        if (isset($data['items'][0]['contentDetails']['itemCount'])) {
            $cantidad = $data['items'][0]['contentDetails']['itemCount'];
            echo "📂 La playlist tiene $cantidad videos.";
        } else {
            echo "❌ No se pudo obtener la cantidad de videos.";
        }
    }

    function obtenerCantidadPlaylists($channelId) {
        $apiKey = "TU_API_KEY";
        $url = "https://www.googleapis.com/youtube/v3/playlists?part=id&channelId=$channelId&maxResults=50&key=$apiKey";

        $response = file_get_contents($url);
        $data = json_decode($response, true);

        if (isset($data['items'])) {
            return count($data['items']); // Cantidad de playlists visibles
        }
        return 0;

        // Nota: La API devuelve máximo 50 playlists por request, si el canal tiene más, hay que paginar (nextPageToken).
    }

    function ObtenerScriptWeb($url) {
        $datoScript = "";

        switch ($url){
            case 0: 
                $datoScript = "<script src='ajax/registroUsuario.js'></script>";
                break;
            case 1:
                $datoScript = "<script src='ajax/validarLogin.js'></script>";
                break;
            case 2:
                $datoScript = "<script src='ajax/registroUsuario.js'></script>";
                break;
            case 3:
                $datoScript = "<script src='ajax/user_generar_vistas.js'></script>";
                break;
            case 4:
                $datoScript = "<script src='ajax/user_canales.js'></script>";
                break;
            case 5:
                $datoScript = "<script src='ajax/user_videos.js'></script>";
                break;
            case 6:
                $datoScript = "<script src='ajax/user_perfil.js'></script>";
                break;
            case 7:
                $datoScript = "<script src='ajax/user_visualizaciones.js'></script>";
                break;
            case 8:
                $datoScript = "<script src='ajax/ejecutar_tareas.js'></script>";
                break;
            case 9:
                $datoScript = "<script src='ajax/ganar_vistas.js'></script>";
                break;
            case 10:
                $datoScript = "<script src='ajax/configuracion_user.js'></script>";
                break;
            case 11:
                $datoScript = "<script src='ajax/principal.js'></script>";
                break;
        }

        echo $datoScript;
    }

    function ObtenerEstilosWeb($opcion) {
        $datoEstilo = "";

        switch ($opcion){
            case 0: 
                $datoEstilo = "<link href='css/estilo_inicial.css' rel='stylesheet' />";
                break;
            case 1:
                $datoEstilo = "<link href='css/estilo.css' rel='stylesheet' />";
                break;
            case 2:
                $datoEstilo = "<link href='css/estyle_dashboard.css' rel='stylesheet' />";
                break;
            case 3:
                $datoEstilo = "<style> /* Modal full-page */
                    #loadingModal {
                        display: none; /* inicialmente oculto */
                        position: fixed;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        background: rgba(0,0,0,0.5); /* semitransparente */
                        z-index: 9999; /* encima de todo */
                        justify-content: center;
                        align-items: center;
                    }

                    /* Spinner circular */
                    .spinner {
                        border: 8px solid #f3f3f3; /* gris claro */
                        border-top: 8px solid #3498db; /* azul */
                        border-radius: 50%;
                        width: 60px;
                        height: 60px;
                        animation: spin 1s linear infinite;
                    }

                    /* Animación */
                    @keyframes spin {
                        0% { transform: rotate(0deg); }
                        100% { transform: rotate(360deg); }
                    } </style>";
                break;
        }

        echo $datoEstilo;
    }

    function TerminosPoliticas(){
        $datosTermino = "";
        $datosTermino = '<p>Estos son los términos y condiciones del sitio.</p>';
        $datosTermino .= '<p>1. Está de acuerdo en visualizar videos de otros para obtener vistas</p>';
        $datosTermino .= '<p>2. Está de acuerdo que otros puedan ver sus videos</p>';
        $datosTermino .= '<p>3. Solo se visualzaran sus videos si usted visualiza el de los demás</p>';
        echo $datosTermino;
    }


    function OptenerValorParametro($id){
        try{
            $conexion = new Database();
            $db = $conexion->connect();
            $query = "SELECT * FROM parametros WHERE id_parametro = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$id]);
            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $datos;
            
        }catch (PDOException $e) {
            
            return ["success" => false, "error" => "true", "mensaje" => $this->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            
            return ["success" => false, "error" => "true", "mensaje" => $this->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
        
    }
    


}