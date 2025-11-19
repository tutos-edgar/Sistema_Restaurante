<?php
header("Content-Type: application/json");
$data = json_decode(file_get_contents("php://input"), true);

require_once '../models/GanarVistasUser.php';
require_once '../controller/GanarVistasUserController.php';
require_once '../config/database.php';
include_once '../middleware/obtenerSesion.php';
include_once '../middleware/auth.php';

if (!isset($data['metodo'])) {
    echo json_encode(["success" => "false", "mensaje" => "Faltan datos del metodo", "dato" => []]);
    exit;
}

try{

    $metodo = $data['metodo'];
    // if($metodo != "listar"){
    //     if(!isset($data['datoRecibido'])){
    //         echo json_encode(["success" => "false", "mensaje" => "Faltan datos", "datos" => []]);
    //         exit;
    //     }
    //     $datosRecibidos = $data['datoRecibido'];
    // }
 
    $database = new Database();
    $db = $database->connect();
    
    // // Obtén la ruta de la URL
    // $uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
    $objModelo = new GanarVistasUser($db); // $db es la conexión PDO
    $objController = new GanarVistasUserController($db);

    $objModelo->id_canal_youtube = (isset($data['id_canal_youtube'])) ?  $data['id_canal_youtube'] : '';
    $objModelo->vistas = (isset($data['vistas_video'])) ?  $data['vistas_video'] : '';
    $objModelo->id_usuario = (isset($data['id_usuario'])) ?  $data['id_usuario'] : '';   
    $objModelo->idVideo = (isset($data['idVideo'])) ?  $data['idVideo'] : '';
    $objModelo->tipoVideo = (isset($data['tipo_video'])) ?  $data['tipo_video'] : '';
    $apiKey = (isset($data['apiKey'])) ?  $data['apiKey'] : '';
    
    // if($metodo != "listar"){
    //     $validaWeb = AUTH::ValidarPaginas($apiKey);
    // }
    
    if(strtolower($metodo) == "registrar" || strtolower($metodo) == "actualizar"){
        // //VALIDACIONES DE DATOS OBLIGATORIOS
        if(empty($objModelo->tipoVideo)){
            echo json_encode(["success" => "false", "mensaje" => "Favor Ingresar una Usuario", "error" => "Datos Faltantes", "datos" => []]);
            exit;        
        }else if(empty($objModelo->idVideo)){
            echo json_encode(["success" => "false", "mensaje" => "Favor Ingresar un Id del Video", "error" => "Datos Faltantes", "datos" => []]);
            exit;        
        }else if(empty($objModelo->vistas)){
            echo json_encode(["success" => "false", "mensaje" => "Favor Ingresar Una vista de Video", "error" => "Datos Faltantes", "datos" => []]);
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
                if (!isset($objModelo->id_usuario) || !isset($objModelo->tipoVideo ) || !isset($objModelo->idVideo )) {
                    echo json_encode(["success" => "false", "mensaje" => "Favor de Ingresar datos Necesario",  "error" => "Faltan datos", "datos" => []]);
                    exit;
                }  
                $objController->GenerarVistasUser($objModelo);
                break;

            case 'validarvideodevista':
                // Aquí creas tu objeto Usuario, lo llenas y lo guardas
                if (!isset($objModelo->idVideo)) {
                    echo json_encode(["success" => "false", "mensaje" => "Favor de Ingresar datos Necesario",  "error" => "Faltan datos", "datos" => []]);
                    exit;
                }
               
                $objController->ValidarVideoDeVista($objModelo);
                break;

            case 'obtenercardvideosvistas':                
                $objController->ObtenerCardVideosVistas($objModelo );
                break;
            case 'autoregistrar':                
                $objController->AutoGestionadoGuardado($objModelo);
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


