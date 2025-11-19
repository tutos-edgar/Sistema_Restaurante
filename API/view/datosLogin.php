<?php
header("Content-Type: application/json");
$data = json_decode(file_get_contents("php://input"), true);

require_once '../models/UsuariosYoutube.php';
require_once '../controller/UsuariosController.php';
require_once '../config/database.php';
require_once '../middleware/auth.php';

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
   
    $objModelo = new UsuariosYoutube($db); // $db es la conexión PDO
    $objController = new UsuariosController($db);

    $objModelo->id_usuario = (isset($datosRecibidos['id_usuario'])) ?  $datosRecibidos['id_usuario'] : '';
    $objModelo->alias_usuario = (isset($datosRecibidos['alias_usuario'])) ?  $datosRecibidos['alias_usuario'] : '';
    $objModelo->pass_usuario = (isset($datosRecibidos['password_usuario'])) ?  $datosRecibidos['password_usuario'] : '';
    $apiKey = (isset($datosRecibidos['apiKey'])) ?  $datosRecibidos['apiKey'] : '';
    $validaWeb = AUTH::ValidarPaginas($apiKey);

    //CONVERTIR DATOS HTML SCRIPT A TEXTO
    $objModelo->id_usuario = htmlspecialchars($objModelo->id_usuario);
    $objModelo->alias_usuario = htmlspecialchars($objModelo->alias_usuario);
    $objModelo->pass_usuario = htmlspecialchars($objModelo->pass_usuario);

    if(strtolower($metodo) == "validarLogin"){
        if(empty($objModelo->alias_usuario)){
            echo json_encode(["success" => "false", "mensaje" => "Favor de Ingresar un Alias de Usuario", "error" => "Datos Faltantes", "datos" => []]);
            exit;
        }else if(strlen($objModelo->pass_usuario) < CANTIDADLIMITEPASS){
            echo json_encode(["success" => "false", "mensaje" => "La Contraseña debe de ser Mayor o Igual a 4", "error" => "Datos Faltantes", "datos" => []]);
            exit;
        }else if(empty($objModelo->pass_usuario)){
            echo json_encode(["success" => "false", "mensaje" => "Favor de Ingresar una Contraseña", "error" => "Datos Faltantes", "datos" => []]);
            exit;
        }
    }

    // VALIDAR CARACTERES
    // $regexEstandar =  "/^(?=.*[A-Z])(?=.*[\$\#\%\/\&])[a-zA-Z0-9\s\$\#\%\/\&]+$/";
    // $regex = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
    $regex = "/^[^\"'\/\\\\\!\=\{\}\>\<\|\°\;\(\\^)]+$/u";
    if (!preg_match($regex, $objModelo->alias_usuario)) {
        echo json_encode(["success" => "false", "mensaje" => "El Nombre Contiene Caracteres no Válidos", "error" => "Datos Faltantes", "datos" => []]);
        exit;
    }else if (!preg_match($regex, $objModelo->pass_usuario)) {
        echo json_encode(["success" => "false", "mensaje" => "El Correo Contiene Caracteres no Válidos", "error" => "Datos Faltantes", "datos" => []]);
        exit;
    }
        switch (strtolower($metodo)) { 
            case 'validarlogin':
                // Aquí creas tu objeto Usuario, lo llenas y lo guardas
                if (!isset($objModelo->alias_usuario) || !isset($objModelo->pass_usuario)) {
                    echo json_encode(["success" => "false", "mensaje" => "Favor de Ingresar datos Necesario para el Registro",  "error" => "Faltan datos", "datos" => []]);
                    exit;
                }  
                $objController->ValidarLoginAcceso($objModelo);
                break;

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


