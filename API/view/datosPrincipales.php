<?php
header("Content-Type: application/json");
$data = json_decode(file_get_contents("php://input"), true);

require_once '../models/DatosPrincipales.php';
require_once '../controller/DatosPrincipalesController.php';
require_once '../config/database.php';
include_once '../middleware/obtenerSesion.php';

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
    
    $uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

    $objModelo = new DatosPrincipales($db); // $db es la conexión PDO
    $objController = new DatosPrincipalesController($db);
    
    if(empty($objModelo->id_usuario)){
        $objModelo->id_usuario = $IdUser;
    }
        switch (strtolower($metodo)) { 
            case 'obtenerdatosprincipales':
                // Aquí creas tu objeto Usuario, lo llenas y lo guardas
                if (!isset($objModelo->id_usuario)) {
                    echo json_encode(["success" => "false", "mensaje" => "Favor de Ingresar datos Necesario",  "error" => "Faltan datos", "datos" => []]);
                    exit;
                }  
                $objController->ObtenerCardPrincipales($objModelo);
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


