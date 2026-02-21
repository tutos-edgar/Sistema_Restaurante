<?php
header("Content-Type: application/json");
$data = json_decode(file_get_contents("php://input"), true);

require_once '../models/PerfilUser.php';
require_once '../controller/PerfilUserController.php';
require_once '../Services/GenerarDatosService.php';
require_once '../config/database.php';
include_once '../middleware/obtenerSesion.php';
include_once '../middleware/auth.php';

if (!isset($data['metodo'])) {
    echo json_encode(["success" => "false", "mensaje" => "Faltan datos del metodo", "dato" => []]);
    exit;
}

try{

    $metodo = $data['metodo'];
    // if($metodo != "listar" && $metodo != "listarprecios"){
        if(!isset($data['datoRecibido'])){
            echo json_encode(["success" => "false", "mensaje" => "Faltan datos", "datos" => []]);
            exit;
        }
         $datosRecibidos = $data['datoRecibido'];
    // }

    $database = new Database();
    $db = $database->connect();
    
    // Obtén la ruta de la URL
    $uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

    $objModelo = new PerfilUser($db); // $db es la conexión PDO
    $objController = new PerfilUserControllerr($db);
    $servicio = new GenerarDatosService($objController);
    // $funcionesGenerales = new FuncionesGenerales();

    $objModelo->id_perfil_usuario = (isset($datosRecibidos['id_perfil_usuario'])) ?  $datosRecibidos['id_perfil_usuario'] : '';
    $objModelo->id_usuario = (isset($datosRecibidos['id_usuario'])) ?  $datosRecibidos['id_usuario'] : '';
    $objModelo->nombre_perfil = (isset($datosRecibidos['nombre_perfil'])) ?  $datosRecibidos['nombre_perfil'] : '';
    $objModelo->apellido_perfil = (isset($datosRecibidos['apellido_perfil'])) ?  $datosRecibidos['apellido_perfil'] : '';
    $objModelo->email_perfil = (isset($datosRecibidos['correo_perfil'])) ?  $datosRecibidos['correo_perfil'] : '';
    $objModelo->fecha_nacimiento = (isset($datosRecibidos['fecha_nacimiento'])) ?  $datosRecibidos['fecha_nacimiento'] : '';
    $objModelo->happy_birthday = (isset($datosRecibidos['happy_birthday'])) ?  $datosRecibidos['happy_birthday'] : '';
    $objModelo->telefono_perfil = (isset($datosRecibidos['telefono_perfil'])) ?  $datosRecibidos['telefono_perfil'] : '';
    $objModelo->nit = (isset($datosRecibidos['nit'])) ?  $datosRecibidos['nit'] : '';
    $objModelo->documento = (isset($datosRecibidos['documento'])) ?  $datosRecibidos['documento'] : '';
    
    // $objModelo->alias_usuario = (isset($datosRecibidos['alias'])) ?  $datosRecibidos['alias'] : '';
    // $objModelo->pass_usuario = (isset($datosRecibidos['password'])) ?  $datosRecibidos['password'] : '';
    // $objModelo->pass_new = (isset($datosRecibidos['passwordnew'])) ?  $datosRecibidos['passwordnew'] : '';
    // $confirmacion = (isset($datosRecibidos['passwordconfirm'])) ?  $datosRecibidos['passwordconfirm'] : '';

    $apiKey = (isset($datosRecibidos['apiKey'])) ?  $datosRecibidos['apiKey'] : '';
    $validaWeb = AUTH::ValidarPaginasPeticion($apiKey);
    if(!$validaWeb){
        echo json_encode(["success" => "false", "mensaje" => "Petición Negada ", "urlPricipal" => URLWEB, "error" => "Datos Faltantes", "datos" => []]);
        exit;
    }
    
    //CONVERTIR DATOS HTML SCRIPT A TEXTO
    $objModelo->id_perfil_usuario = htmlspecialchars($objModelo->id_perfil_usuario);
    $objModelo->nombre_perfil = htmlspecialchars($objModelo->nombre_perfil);
    $objModelo->apellido_perfil = htmlspecialchars($objModelo->apellido_perfil);
    $objModelo->email_perfil = htmlspecialchars($objModelo->email_perfil);
    $objModelo->happy_birthday = htmlspecialchars($objModelo->happy_birthday);
    $objModelo->fecha_nacimiento = htmlspecialchars($objModelo->fecha_nacimiento);

    // $objModelo->alias_usuario = htmlspecialchars($objModelo->alias_usuario);
    // $objModelo->pass_usuario = htmlspecialchars($objModelo->pass_usuario);
    // $objModelo->pass_new = htmlspecialchars($objModelo->pass_new);
    // $confirmacion = htmlspecialchars($confirmacion);

    if(strtolower($metodo) == "actualizarpass"){
        // //VALIDACIONES DE DATOS OBLIGATORIOS
        if(empty($objModelo->alias_usuario)){
            echo json_encode(["success" => "false", "mensaje" => "Favor Ingresar un Alias o Usuario", "error" => "Datos Faltantes", "datos" => []]);
            exit;        
        }else if(empty($objModelo->pass_usuario)){
            echo json_encode(["success" => "false", "mensaje" => "Favor de Ingresar tu Contraseña Actual", "error" => "Datos Faltantes", "datos" => []]);
            exit;
        }else if(empty($objModelo->pass_new)){
            echo json_encode(["success" => "false", "mensaje" => "Favor de Ingresar una Nueva Contraseña", "error" => "Datos Faltantes", "datos" => []]);
            exit;
        }else if(empty($confirmacion)){
            echo json_encode(["success" => "false", "mensaje" => "Favor de Ingresar una Confirmación de Contraseña", "error" => "Datos Faltantes", "datos" => []]);
            exit;
        }

        $regex = "/^[^\"'\/\\\\\!\=\{\}\>\<\|\°\;\(\\^)]+$/u";
        // if (!preg_match($regex, $objModelo->alias_usuario)) {
        //     echo json_encode(["success" => "false", "mensaje" => "El Alias o Usuario Contiene Caracteres no Válidos", "error" => "Datos Faltantes", "datos" => []]);
        //     exit;
        // }else if (!preg_match($regex, $objModelo->pass_usuario)) {
        //     echo json_encode(["success" => "false", "mensaje" => "La Contraseña Actual Contiene Caracteres no Válidos", "error" => "Datos Faltantes", "datos" => []]);
        //     exit;
        // }else if (!preg_match($regex, $objModelo->pass_new)) {
        //     echo json_encode(["success" => "false", "mensaje" => "La Nueva Contraseña Contiene Caracteres no Válidos", "error" => "Datos Faltantes", "datos" => []]);
        //     exit;
        // }else if (!preg_match($regex, $confirmacion)) {
        //     echo json_encode(["success" => "false", "mensaje" => "La Confirmación de Contraseña Contiene Caracteres no Válidos", "error" => "Datos Faltantes", "datos" => []]);
        //     exit;
        // }

        // if($objModelo->pass_new != $confirmacion){
        //     echo json_encode(["success" => "false", "mensaje" => "La Confirmación y La Nueva Contraseña no son Iguales", "error" => "Datos Faltantes", "datos" => []]);
        //     exit;
        // }

        if(empty($objModelo->id_usuario)){
            $objModelo->id_usuario = $IdUser;
        }
      
    }
   
    
    if(strtolower($metodo) == "registrar" || strtolower($metodo) == "actualizar"){
        // //VALIDACIONES DE DATOS OBLIGATORIOS
        if(empty($objModelo->nombre_perfil)){
            echo json_encode(["success" => "false", "mensaje" => "Favor Ingresar una Nombre", "error" => "Datos Faltantes", "datos" => []]);
            exit;        
        }else if(empty($objModelo->apellido_perfil)){
            echo json_encode(["success" => "false", "mensaje" => "Favor de Ingresar un Apellido", "error" => "Datos Faltantes", "datos" => []]);
            exit;
        }else if(empty($objModelo->email_perfil)){
            echo json_encode(["success" => "false", "mensaje" => "Favor de Ingresar un E-Mail", "error" => "Datos Faltantes", "datos" => []]);
            exit;
        }else if(!filter_var($objModelo->email_perfil, FILTER_VALIDATE_EMAIL)){
            echo json_encode(["success" => "false", "mensaje" => "Favor de Ingresar un E-Mail Valido ", "error" => "Datos Faltantes", "datos" => []]);
            exit;
        }else if(empty($objModelo->documento)){
            echo json_encode(["success" => "false", "mensaje" => "Favor de Ingresar un Documento", "error" => "Datos Faltantes", "datos" => []]);
            exit;
        }else if(empty($objModelo->telefono_perfil)){
            echo json_encode(["success" => "false", "mensaje" => "Favor de Ingresar un Teléfono", "error" => "Datos Faltantes", "datos" => []]);
            exit;
        }

        $regex = "/^[^\"'\/\\\\\!\=\{\}\>\<\|\°\;\(\\^)]+$/u";
        if (!preg_match($regex, $objModelo->nombre_perfil)) {
            echo json_encode(["success" => "false", "mensaje" => "El Nombre Contiene Caracteres no Válidos", "error" => "Datos Faltantes", "datos" => []]);
            exit;
        }else if (!preg_match($regex, $objModelo->apellido_perfil)) {
            echo json_encode(["success" => "false", "mensaje" => "El Apellido Contiene Caracteres no Válidos", "error" => "Datos Faltantes", "datos" => []]);
            exit;
        }else if (!preg_match($regex, $objModelo->email_perfil)) {
            echo json_encode(["success" => "false", "mensaje" => "El Correo Contiene Caracteres no Válidos", "error" => "Datos Faltantes", "datos" => []]);
            exit;
        }else if (!preg_match($regex, $objModelo->documento)) {
            echo json_encode(["success" => "false", "mensaje" => "El Correo Contiene Caracteres no Válidos", "error" => "Datos Faltantes", "datos" => []]);
            exit;
        } else if (!preg_match($regex, $objModelo->telefono_perfil)) {
            echo json_encode(["success" => "false", "mensaje" => "El Teléfono Contiene Caracteres no Válidos", "error" => "Datos Faltantes", "datos" => []]);
            exit;
        }

    }

    if(strtolower($metodo) == "obtenerdatosperfil"){
        if(!empty($IdUser)){
            $objModelo->id_perfil_usuario = $IdUser;
            $objModelo->id_usuario = $IdUser;
        }        
    }
   
        switch (strtolower($metodo)) { 
            case 'registrar':
                // Aquí creas tu objeto Usuario, lo llenas y lo guardas
                if (!isset($objModelo->nombre_perfil) || !isset($objModelo->apellido_perfil ) || !isset($objModelo->email_perfil )) {
                    echo json_encode(["success" => "false", "mensaje" => "Favor de Ingresar datos Necesario para el Registro",  "error" => "Faltan datos", "datos" => []]);
                    exit;
                } 
                
                $servicio->guardar($objModelo);
                break;

            case 'actualizar':
                // lógica para editar 
                if (!isset($objModelo->id_perfil_usuario) || !isset($objModelo->nombre_perfil) || !isset($objModelo->apellido_perfil) || !isset($objModelo->email_perfil) || !isset($objModelo->telefono_perfil)) {
                    echo json_encode(["success" => "false", "mensaje" => "Favor de Ingresar datos Necesario para el Registro",  "error" => "Faltan datos", "datos" => []]);
                    exit;
                }

                $servicio->modificar($objModelo);
                break;

            case 'eliminar':
                if (!isset($objModelo->id_perfil_usuario) || empty($objModelo->id_perfil_usuario)) {
                    echo json_encode(["success" => "false", "mensaje" => "Favor de Ingresar datos Necesario para el Registro",  "error" => "Faltan datos", "datos" => []]);
                    exit;
                }
               
                $servicio->eliminar($objModelo->id_perfil_usuario);
                break;

            case 'listar':
                $servicio->listar();
                break;
        
            case 'obtenerdatosperfil':
                if (!$objModelo->id_usuario || !$objModelo->id_perfil_usuario) {
                    echo json_encode(["error" => "Faltan datos"]);
                    exit;
                }
                // echo json_encode(["success" => "false", "mensaje" => "El Correo Contiene Caracteres no Válidos", "error" => "Datos Faltantes", "datos" => []]);
                // $objController->obtenerIdPerfilUsuario($objModelo->id_perfil_usuario);
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


