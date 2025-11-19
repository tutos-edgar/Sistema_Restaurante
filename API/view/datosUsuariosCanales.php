<?php
header("Content-Type: application/json");
$data = json_decode(file_get_contents("php://input"), true);

require_once '../models/UsuariosCanales.php';
require_once '../controller/UsuariosCanalesController.php';
require_once '../config/database.php';
include_once '../middleware/obtenerSesion.php';
include_once '../middleware/auth.php';

if (!isset($data['metodo'])) {
    echo json_encode(["success" => "false", "mensaje" => "Faltan datos del metodo", "dato" => []]);
    exit;
}

try{

    $metodo = $data['metodo'];
    if($metodo != "listar" && $metodo != "listarprecios"){
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

    $objModelo = new UsuariosCanales($db); // $db es la conexión PDO
    $objController = new UsuariosCanalesController($db);
    
    $objModelo->id_canal_youtube = (isset($datosRecibidos['id_canal_youtube'])) ?  $datosRecibidos['id_canal_youtube'] : '';
    $objModelo->id_usuario = (isset($datosRecibidos['id_usuario'])) ?  $datosRecibidos['id_usuario'] : '';
    $objModelo->nombre_canal = (isset($datosRecibidos['nombre_canal'])) ?  $datosRecibidos['nombre_canal'] : '';
    $objModelo->url_canal = (isset($datosRecibidos['url_canal'])) ?  $datosRecibidos['url_canal'] : '';
    $objModelo->idCanal = (isset($datosRecibidos['idCanal'])) ?  $datosRecibidos['idCanal'] : '';
    $objModelo->descripcion_canal = (isset($datosRecibidos['descripcion'])) ?  $datosRecibidos['descripcion'] : '';
    $objModelo->suscriptores = (isset($datosRecibidos['suscriptores'])) ?  $datosRecibidos['suscriptores'] : '';
    $objModelo->esActivo = (isset($datosRecibidos['esActivo'])) ?  $datosRecibidos['esActivo'] : '';
    $apiKey = (isset($datosRecibidos['apiKey'])) ?  $datosRecibidos['apiKey'] : '';
    if($metodo != "listar"){
        $validaWeb = AUTH::ValidarPaginas($apiKey);
    }

    //CONVERTIR DATOS HTML SCRIPT A TEXTO
    $objModelo->id_usuario = htmlspecialchars($objModelo->id_usuario);
    $objModelo->id_canal_youtube = htmlspecialchars($objModelo->id_canal_youtube);
    $objModelo->nombre_canal = htmlspecialchars($objModelo->nombre_canal);
    $objModelo->descripcion_canal = htmlspecialchars($objModelo->descripcion_canal);
    $objModelo->url_canal = htmlspecialchars($objModelo->url_canal);
    $objModelo->idCanal = htmlspecialchars($objModelo->idCanal);
    $objModelo->suscriptores = htmlspecialchars($objModelo->suscriptores);
    $objModelo->esActivo = htmlspecialchars($objModelo->esActivo);

    if(strtolower($metodo) == "registrar" || strtolower($metodo) == "actualizar"){
        // //VALIDACIONES DE DATOS OBLIGATORIOS
        if(empty($objModelo->nombre_canal)){
            echo json_encode(["success" => "false", "mensaje" => "Favor Ingresar una Nombre del Canal", "error" => "Datos Faltantes", "datos" => []]);
            exit;        
        }else if(empty($objModelo->url_canal)){
            echo json_encode(["success" => "false", "mensaje" => "Favor de Ingresar un enlace del Canal", "error" => "Datos Faltantes", "datos" => []]);
            exit;
        }else if(empty($objModelo->id_usuario)){
            $objModelo->id_usuario = $IdUser;
        }

        $regex = "/^[^\"'\/\\\\\!\=\{\}\>\<\|\°\;\(\\^)]+$/u";
        if (!preg_match($regex, $objModelo->nombre_canal)) {
            echo json_encode(["success" => "false", "mensaje" => "El Nombre del Canal Contiene Caracteres no Válidos", "error" => "Datos Faltantes", "datos" => []]);
            exit;
        }

    }

    if(!empty($objModelo->id_usuario)){
        if($objModelo->id_usuario != $IdUser){
            echo json_encode(["success" => "false", "mensaje" => "Usuario no es Valido para la Acción",  "error" => "Faltan datos", "datos" => []]);
                    exit;
        }
    }

    if(empty($objModelo->id_usuario)){
        $objModelo->id_usuario = $IdUser;
    }

        switch (strtolower($metodo)) { 
            case 'registrar':
                // Aquí creas tu objeto Usuario, lo llenas y lo guardas
                if (!isset($objModelo->nombre_canal) || !isset($objModelo->url_canal)) {
                    echo json_encode(["success" => "false", "mensaje" => "Favor de Ingresar datos Necesario para el Registro",  "error" => "Faltan datos", "datos" => []]);
                    exit;
                }  
                $objController->insertarCanal($objModelo);
                break;

            case 'actualizar':
                // lógica para editar 
                if (!isset($objModelo->id_canal_youtube) || !isset($objModelo->id_usuario)) {
                    echo json_encode(["error" => "Faltan datos"]);
                    exit;
                }

                $objController->actualizarCanal($objModelo);
                break;

            case 'eliminar':
                if (!$objModelo->id_canal_youtube) {
                    echo json_encode(["error" => "Faltan datos "]);
                    exit;
                }
                $objController->eliminarCanal($objModelo->id_canal_youtube);
                break;

            case 'listar':
                $objController->obtenerTodos($objModelo->id_usuario);
                break;
        
            case 'obtenerid':
                if (!$objModelo->id_canal_youtube) {
                    echo json_encode(["error" => "Faltan datos"]);
                    exit;
                }
            
                $objController->obtenerPorId($objModelo->id_canal_youtube);
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


