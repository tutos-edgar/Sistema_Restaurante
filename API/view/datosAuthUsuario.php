<?php
header("Content-Type: application/json");
$data = json_decode(file_get_contents("php://input"), true);

require_once '../models/Usuarios.php';
require_once '../controller/AuthUsuariosController.php';
require_once '../config/database.php';
require_once '../Interfaces/IAuthUsuario.php';
require_once '../Services/ValidarLoginService.php';

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

    // $db es la conexión PDO
    $database = new Database();
    $db = $database->connect();
    
    // Obtén la ruta de la URL
    $uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
    $objModelo = new Usuarios($db); 
    $objController = new AuthUsuariosController($db);
    $servicio = new ValidarLoginService($objController);

    $objModelo->id_usuario = (isset($datosRecibidos['id_usuario'])) ?  $datosRecibidos['id_usuario'] : '';
    $objModelo->alias_usuario = (isset($datosRecibidos['alias_usuario'])) ?  $datosRecibidos['alias_usuario'] : '';
    $objModelo->pass_usuario = (isset($datosRecibidos['password_usuario'])) ?  $datosRecibidos['password_usuario'] : '';
    $objModelo->id_rol = (isset($datosRecibidos['id_rol'])) ?  $datosRecibidos['id_rol'] : '';
    
    //CONVERTIR DATOS HTML SCRIPT A TEXTO
    $objModelo->id_usuario = htmlspecialchars($objModelo->id_usuario);
    $objModelo->alias_usuario = htmlspecialchars($objModelo->alias_usuario);
    $objModelo->id_rol = htmlspecialchars($objModelo->id_rol);
    $objModelo->pass_usuario = htmlspecialchars($objModelo->pass_usuario);

    if(strtolower($metodo) == "validarlogin"){
        // //VALIDACIONES DE DATOS OBLIGATORIOS
        if(empty($objModelo->alias_usuario)){
            echo json_encode(["success" => "false", "mensaje" => "Favor Ingresar un Usuairo", "error" => "Datos Faltantes", "datos" => []]);
            exit;        
        }else if(empty($objModelo->pass_usuario)){
            echo json_encode(["success" => "false", "mensaje" => "Favor de Ingresar un Password", "error" => "Datos Faltantes", "datos" => []]);
            exit;
        }else if(strlen($objModelo->pass_usuario) < 4){
            echo json_encode(["success" => "false", "mensaje" => "La Contraseña debe de ser Mayor o Igual a 4", "error" => "Datos Faltantes", "datos" => []]);
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
        echo json_encode(["success" => "false", "mensaje" => "La Contraseña Contiene Caracteres no Válidos", "error" => "Datos Faltantes", "datos" => []]);
        exit;
    }

        switch (strtolower($metodo)) { 
            case 'validarlogin':
                // Aquí creas tu objeto Usuario, lo llenas y lo guardas
                if (!isset($objModelo->alias_usuario) || !isset($objModelo->pass_usuario)) {
                    echo json_encode(["success" => "false", "mensaje" => "Favor de Ingresar datos Necesario para el Registro",  "error" => "Faltan datos", "datos" => []]);
                    exit;
                }  
                // $objController->ValidarLogin($objModelo);
                $servicio->ValidarLogin($objModelo);
                break;

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



?>


