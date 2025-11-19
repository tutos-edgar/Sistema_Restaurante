<?php
header("Content-Type: application/json");
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['metodo'])) {
    echo json_encode(["success" => "false", "mensaje" => "Faltan datos del metodo", "dato" => []]);
    exit;
}

try {
    $metodo = $data['metodo'];
    if($metodo != "listar" && $metodo != "listarprecios"){
        if(!isset($data['datoRecibido'])){
            echo json_encode(["success" => "false", "mensaje" => "Faltan datos"]);
            exit;
        }
         $datosRecibidos = $data['datoRecibido'];
    }

    require_once '../models/canales.php';
    require_once '../controller/canalesController.php';
    require_once '../config/database.php';

    $database = new Database();
    $db = $database->connect();

    // Obtén la ruta de la URL
    $uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

    $objModelo = new Canales($db); // $db es la conexión PDO
    $objController = new CanalesController($db);

    $objModelo->id_canal = (isset($datosRecibidos['id_canal'])) ?  $datosRecibidos['id_canal'] : '';
    $objModelo->nombre_canal = (isset($datosRecibidos['nombre_canal'])) ?  $datosRecibidos['nombre_canal'] : '';
    $objModelo->descripcion_canal = (isset($datosRecibidos['descripcion_canal'])) ?  $datosRecibidos['descripcion_canal'] : '';
    $objModelo->url_canal = (isset($datosRecibidos['link_canal'])) ?  $datosRecibidos['link_canal'] : '';
    

    // if(strtolower($metodo) == "registrar" || strtolower($metodo) == "actualizar"){
    //     // //VALIDACIONES DE DATOS OBLIGATORIOS
    //     if(empty($objModelo->fecha_compra)){
    //         echo json_encode(["success" => "false", "mensaje" => "Favor Ingresar una Fecha para la Compra", "error" => "Datos Faltantes"]);
    //         exit;
    //     }else if (!DateTime::createFromFormat('Y-m-d', $objModelo->fecha_compra) !== false) {
    //         echo json_encode(["success" => "false", "mensaje" => "Formato de Fecha no Valida", "error" => "Datos Faltantes"]);
    //         exit;
    //     }else if (DateTime::createFromFormat('Y-m-d', $objModelo->fecha_compra) !== false) {
    //         $fechaActual = date('Y-m-d');
    //         if (strtotime($objModelo->fecha_compra) > strtotime($fechaActual)) {
    //             echo json_encode(["success" => "false", "mensaje" => "La Fecha es Mayor a la Actual", "error" => "Datos Faltantes"]);
    //             exit;
    //         } 
    //     }else if(empty($objModelo->id_proveedor)){
    //         echo json_encode(["success" => "false", "mensaje" => "Favor de Ingresar un Proveedor", "error" => "Datos Faltantes"]);
    //         exit;
    //     }else if($objModelo->id_proveedor <= 0 ){
    //         echo json_encode(["success" => "false", "mensaje" => "Favor de Seleccionar un Proveedor", "error" => "Datos Faltantes"]);
    //         exit;
    //     }else if(empty($objModelo->nombre_proveedor)){
    //         echo json_encode(["success" => "false", "mensaje" => "Favor de Ingresar un nombre del Proveedor", "error" => "Datos Faltantes"]);
    //         exit;
    //     }else if(empty($objModelo->encargado_proveedor)){
    //         echo json_encode(["success" => "false", "mensaje" => "Favor de Ingresar un Encargado del Proveedor", "error" => "Datos Faltantes"]);
    //         exit;
    //     }else if(empty($objModelo->telefono_proveedor)){
    //         echo json_encode(["success" => "false", "mensaje" => "Favor de Ingresar un Teléfono del Proveedor", "error" => "Datos Faltantes"]);
    //         exit;
    //     }else if(empty($objModelo->direccion_proveedor)){
    //         echo json_encode(["success" => "false", "mensaje" => "Favor de Ingresar una Dirección del Proveedor", "error" => "Datos Faltantes"]);
    //         exit;
    //     }

    //     if (!is_array($objModelo->detalle_compra)) {
    //         echo json_encode(["success" => "false", "mensaje" => "El Dato del Detalle no es una Lista", "error" => "Datos Faltantes"]);
    //         exit;
            
    //     }else if (empty($objModelo->detalle_compra)) {
    //         echo json_encode(["success" => "false", "mensaje" => "La Lista de Productos esta Vacía", "error" => "Datos Faltantes"]);
    //         exit;
    //     } elseif (is_object($objModelo->detalle_compra)) {
    //         if (empty((array)$objModelo->detalle_compra)) {
    //             echo json_encode(["success" => "false", "mensaje" => "El Objeto de lista esta Vacía", "error" => "Datos Faltantes"]);
    //             exit;
    //         }
    //     } else {
    //         if (count($objModelo->detalle_compra) == 0) {
    //             echo json_encode(["success" => "false", "mensaje" => "La Lista de Productos no tiene Productos", "error" => "Datos Faltantes"]);
    //             exit;
    //         }
    //     }
    // }
    

    switch (strtolower($metodo)) { 
        // case 'registrar':
        //     // Aquí creas tu objeto Usuario, lo llenas y lo guardas
        //     if (!isset($objModelo->id_proveedor) || !isset($objModelo->detalle_compra)) {
        //         echo json_encode(["error" => "Faltan datos"]);
        //         exit;
        //     }  
        //     $objController->crearCompra($objModelo);
        //     break;

        // case 'actualizar':
        //     // lógica para editar 
        //     if (!isset($objModelo->nombre_producto) || !isset($objModelo->codigo_barras)) {
        //         echo json_encode(["error" => "Faltan datos"]);
        //         exit;
        //     }

        //     $objController->actualizarProducto($objModelo);
        //     break;

        // case 'eliminar':
        //     if (!$objModelo->id_compra) {
        //         echo json_encode(["error" => "Faltan datos"]);
        //         exit;
        //     }
        //     $objController->eliminarCompra($objModelo->id_compra);
        //     break;

        case 'listar':
            $objController->obtenerTodos();
            break;
        case 'mivideo':
            $objController->MisVideosRandom();
            break;
        // case 'obtenerid':
        //     if (!$objModelo->id_producto) {
        //         echo json_encode(["error" => "Faltan datos"]);
        //         exit;
        //     }
           
        //     $objController->obtenerPorId($objModelo->id_producto);
        //     break;  
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
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error de base de datos", "detalle" => $e->getMessage()]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
        



?>


