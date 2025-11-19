<?php
header("Content-Type: application/json");
$data = json_decode(file_get_contents("php://input"), true);

require_once '../models/UsuariosVideos.php';
require_once '../controller/UsuariosVideosController.php';
require_once '../config/database.php';
include_once '../middleware/obtenerSesion.php';
include_once '../middleware/auth.php';

if (!isset($data['metodo'])) {
    echo json_encode(["success" => "false", "mensaje" => "Faltan datos del metodo", "dato" => []]);
    exit;
}

try{

    $metodo = $data['metodo'];
    if($metodo != "listar"){
        if(!isset($data['datoRecibido'])){
            echo json_encode(["success" => "false", "mensaje" => "Faltan datos", "datos" => []]);
            exit;
        }
        $datosRecibidos = $data['datoRecibido'];
    }

    $database = new Database();
    $db = $database->connect();
    
    // Obtén la ruta de la URL
    $uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
    $objModelo = new UsuariosVideos($db); // $db es la conexión PDO
    $objController = new UsuariosVideosController($db);

    $objModelo->id_usuario = (isset($data['id_usuario'])) ?  $data['id_usuario'] : '';

  
        $objModelo->id_canal_youtube = (isset($datosRecibidos['id_canal_youtube'])) ?  $datosRecibidos['id_canal_youtube'] : '';
        $objModelo->id_usuario = (isset($datosRecibidos['id_usuario'])) ?  $datosRecibidos['id_usuario'] : '';
        $objModelo->id_video = (isset($datosRecibidos['id_video'])) ?  $datosRecibidos['id_video'] : '';
        $objModelo->titulo_video = (isset($datosRecibidos['titulo_video'])) ?  $datosRecibidos['titulo_video'] : '';
        $objModelo->url_video = (isset($datosRecibidos['url_video'])) ?  $datosRecibidos['url_video'] : '';
        $objModelo->idVideo = (isset($datosRecibidos['idVideo'])) ?  $datosRecibidos['idVideo'] : '';
        $objModelo->descripcion_video = (isset($datosRecibidos['descripcion_video'])) ?  $datosRecibidos['descripcion_video'] : '';
        $objModelo->tiempo_duracion = (isset($datosRecibidos['tiempo_duracion'])) ?  $datosRecibidos['tiempo_duracion'] : '';
        $objModelo->tipoVideo = (isset($datosRecibidos['tipo_video'])) ?  $datosRecibidos['tipo_video'] : '';
        $objModelo->esActivo = (isset($datosRecibidos['esActivo'])) ?  $datosRecibidos['esActivo'] : '';
        $apiKey = (isset($datosRecibidos['apiKey'])) ?  $datosRecibidos['apiKey'] : '';
    
    // if($metodo != "listar"){
    //     $validaWeb = AUTH::ValidarPaginas($apiKey);
    // }

    //CONVERTIR DATOS HTML SCRIPT A TEXTO
    $objModelo->id_video = htmlspecialchars($objModelo->id_video);
    $objModelo->id_canal_youtube = htmlspecialchars($objModelo->id_canal_youtube);
    $objModelo->titulo_video = htmlspecialchars($objModelo->titulo_video);
    $objModelo->descripcion_video = htmlspecialchars($objModelo->descripcion_video);
    $objModelo->url_video = htmlspecialchars($objModelo->url_video);
    $objModelo->idVideo = htmlspecialchars($objModelo->idVideo);
    $objModelo->tipoVideo = htmlspecialchars($objModelo->tipoVideo);
    $objModelo->esActivo = htmlspecialchars($objModelo->esActivo);
    $objModelo->tiempo_duracion = htmlspecialchars($objModelo->tiempo_duracion);

    if(strtolower($metodo) == "registrar" || strtolower($metodo) == "actualizar"){
        // //VALIDACIONES DE DATOS OBLIGATORIOS
        if(empty($objModelo->titulo_video)){
            echo json_encode(["success" => "false", "mensaje" => "Favor Ingresar una Nombre del Video", "error" => "Datos Faltantes", "datos" => []]);
            exit;        
        }else if(empty($objModelo->url_video)){
            echo json_encode(["success" => "false", "mensaje" => "Favor de Ingresar un enlace del Video", "error" => "Datos Faltantes", "datos" => []]);
            exit;
        }else if(empty($objModelo->tiempo_duracion)){
            echo json_encode(["success" => "false", "mensaje" => "Favor de Ingresar un Tiempo del Video", "error" => "Datos Faltantes", "datos" => []]);
            exit;
        }else if(empty($objModelo->id_usuario)){
            $objModelo->id_usuario = $IdUser;
        }else if(empty($objModelo->tipoVideo) || $objModelo->tipoVideo=="0"){
            echo json_encode(["success" => "false", "mensaje" => "Favor de Seleccionar un Tipo del Video", "error" => "Datos Faltantes", "datos" => []]);
            exit;
        }else if(strtolower($objModelo->tipoVideo) != "video" || strtolower($objModelo->tipoVideo) !="short"){
            echo json_encode(["success" => "false", "mensaje" => "Favor de Seleccionar un Tipo del Video Valido", "error" => "Datos Faltantes", "datos" => []]);
            exit;
        }

         // Validar formato correcto con regex (HH:MM)
        if (!preg_match("/^(?:2[0-3]|[01][0-9]):[0-5][0-9]:[0-5][0-9]$/", $objModelo->tiempo_duracion)) {
            echo json_encode(["success" => "false", "mensaje" => "Formato de Tiempo no Valido", "error" => "Datos Faltantes", "datos" => []]);
            exit;
        } elseif ($objModelo->tiempo_duracion === "00:00:00") {
            echo json_encode(["success" => "false", "mensaje" => "La Hora no Puede ser : ".$objModelo->tiempo_duracion, "error" => "Datos Faltantes", "datos" => []]);
            exit;
        }

        $regex = "/^[^\"'\/\\\\\!\=\{\}\>\<\|\°\;\(\\^)]+$/u";
        if (!preg_match($regex, $objModelo->titulo_video)) {
            echo json_encode(["success" => "false", "mensaje" => "El Nombre del Video Contiene Caracteres no Válidos", "error" => "Datos Faltantes", "datos" => []]);
            exit;
        }else if (!preg_match($regex, $objModelo->tiempo_duracion)) {
            echo json_encode(["success" => "false", "mensaje" => "El Tiempo del Video Contiene Caracteres no Válidos", "error" => "Datos Faltantes", "datos" => []]);
            exit;
        }else if (!preg_match($regex, $objModelo->tipoVideo)) {
            echo json_encode(["success" => "false", "mensaje" => "El Tipo del Video Contiene Caracteres no Válidos", "error" => "Datos Faltantes", "datos" => []]);
            exit;
        }
    }

    if(empty($objModelo->id_usuario)){
        $objModelo->id_usuario = $IdUser;
    }

        switch (strtolower($metodo)) { 
            case 'registrar':
                // Aquí creas tu objeto Usuario, lo llenas y lo guardas
                if (!isset($objModelo->titulo_video) || !isset($objModelo->url_video) || !isset($objModelo->tiempo_duracion)) {
                    echo json_encode(["success" => "false", "mensaje" => "Favor de Ingresar datos Necesario para el Registro",  "error" => "Faltan datos", "datos" => []]);
                    exit;
                }  
                $objController->insertarVideo($objModelo);
                break;

            case 'actualizar':
                // lógica para editar 
                if (!isset($objModelo->id_canal_youtube) || !isset($objModelo->id_video)) {
                    echo json_encode(["error" => "Faltan datos"]);
                    exit;
                }
                
                $objController->actualizarVideo($objModelo);
                break;

            case 'eliminar':
                if (!$objModelo->id_video) {
                    echo json_encode(["error" => "Faltan datos "]);
                    exit;
                }
                $objController->eliminarVideo($objModelo->id_video);
                break;

            case 'listar':
                if (!isset($objModelo->id_usuario)) {
                    echo json_encode(["error" => "Faltan datos"]);
                    exit;
                }
               
                $objController->obtenerTodosPorId($objModelo->id_usuario);
                break;
        
            case 'obtenerid':
                if (!$objModelo->id_video) {
                    echo json_encode(["error" => "Faltan datos"]);
                    exit;
                }
                $objModelo->tipoVideo = strtolower($objModelo->tipoVideo);
                $objController->obtenerPorIdVideo($objModelo);
                break;  
            // case 'obteneridprecios':
            //     if (!$objModelo->id_producto) {
            //         echo json_encode(["error" => "Faltan datos"]);
            //         exit;
            //     }
            
            //     $objController->obtenerPorIdPrecios($objModelo->id_producto);
            //     break;
            // case 'crearprecios':            
            //     $objController->crearPreciosProducto($objModelo);
            //     break;       
            // case 'buscardatospersonallike':
            //     if (!$busqueda) {
            //         echo json_encode(["error" => "Faltan datos "]);
            //         exit;
            //     }
            
            //     $objController->buscarDatosGeneralesLike($busqueda);
            //     break;
            default:
                echo json_encode(["error" => "Método no válido"]);
                break;
        }

    

}catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error de base de datos", "detalle" => $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}


        // else if (!DateTime::createFromFormat('Y-m-d', $objModelo->fecha_compra) !== false) {
        //     echo json_encode(["success" => "false", "mensaje" => "Formato de Fecha no Valida", "error" => "Datos Faltantes"]);
        //     exit;
        // }else if (DateTime::createFromFormat('Y-m-d', $objModelo->fecha_compra) !== false) {
        //     $fechaActual = date('Y-m-d');
        //     if (strtotime($objModelo->fecha_compra) > strtotime($fechaActual)) {
        //         echo json_encode(["success" => "false", "mensaje" => "La Fecha es Mayor a la Actual", "error" => "Datos Faltantes"]);
        //         exit;
        //     } 

        // if (!is_array($objModelo->detalle_compra)) {
        //     echo json_encode(["success" => "false", "mensaje" => "El Dato del Detalle no es una Lista", "error" => "Datos Faltantes"]);
        //     exit;
            
        // }else if (empty($objModelo->detalle_compra)) {
        //     echo json_encode(["success" => "false", "mensaje" => "La Lista de Productos esta Vacía", "error" => "Datos Faltantes"]);
        //     exit;
        // } elseif (is_object($objModelo->detalle_compra)) {
        //     if (empty((array)$objModelo->detalle_compra)) {
        //         echo json_encode(["success" => "false", "mensaje" => "El Objeto de lista esta Vacía", "error" => "Datos Faltantes"]);
        //         exit;
        //     }
        // } else {
        //     if (count($objModelo->detalle_compra) == 0) {
        //         echo json_encode(["success" => "false", "mensaje" => "La Lista de Productos no tiene Productos", "error" => "Datos Faltantes"]);
        //         exit;
        //     }
        // }

        



?>