<?php
header("Content-Type: application/json");
$data = json_decode(file_get_contents("php://input"), true);
require_once '../models/GeneracionVistas.php';
require_once '../controller/GenerarVistasController.php';
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
    $objModelo = new GeneracionVistas($db); // $db es la conexión PDO
    $objController = new GenerarVistasController($db);

    $objModelo->id_canal_youtube = (isset($data['id_canal_youtube'])) ?  $data['id_canal_youtube'] : '';
    $objModelo->id_usuarioDeudor = (isset($data['usuarioB'])) ?  $data['usuarioB'] : '';
    $objModelo->id_usuario = (isset($data['id_usuario'])) ?  $data['id_usuario'] : '';
   
    $objModelo->idVideo = (isset($data['idVideo'])) ?  $data['idVideo'] : '';
    $objModelo->tipoVideo = (isset($data['tipo_video'])) ?  $data['tipo_video'] : '';
    // $objModelo->esActivo = (isset($datosRecibidos['esActivo'])) ?  $datosRecibidos['esActivo'] : '';
    $apiKey = (isset($data['apiKey'])) ?  $data['apiKey'] : '';
    // if($metodo != "obtenercardcanalesyoutube"){
    //     $validaWeb = AUTH::ValidarPaginas($apiKey);
    // }

    if(strtolower($metodo) == "registrar" || strtolower($metodo) == "actualizar"){
        // //VALIDACIONES DE DATOS OBLIGATORIOS
        if(empty($objModelo->id_usuarioDeudor)){
            echo json_encode(["success" => "false", "mensaje" => "Favor Ingresar una Usuario", "error" => "Datos Faltantes", "datos" => []]);
            exit;        
        }

        if(empty($objModelo->idVideo)){
            echo json_encode(["success" => "false", "mensaje" => "Favor Ingresar un Id del Video", "error" => "Datos Faltantes", "datos" => []]);
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
        // $objModelo->id_usuario = "1";
    }


        switch (strtolower($metodo)) {
            case 'registrar':
                // Aquí creas tu objeto Usuario, lo llenas y lo guardas
                if (!isset($objModelo->id_usuario) || !isset($objModelo->id_usuarioDeudor ) || !isset($objModelo->idVideo )) {
                    echo json_encode(["success" => "false", "mensaje" => "Favor de Ingresar datos Necesario",  "error" => "Faltan datos", "datos" => []]);
                    exit;
                }  
                $objController->GenerarVistasDeudas($objModelo);
                break;

            case 'validarvideodevista':
                // Aquí creas tu objeto Usuario, lo llenas y lo guardas
                if (!isset($objModelo->idVideo)) {
                    echo json_encode(["success" => "false", "mensaje" => "Favor de Ingresar datos Necesario",  "error" => "Faltan datos", "datos" => []]);
                    exit;
                }  
                $objController->ValidarVideoDeVista($objModelo->id_usuario, $objModelo->tipoVideo);
                break;

            case 'obtenercardvideosporcanalyoutube':
                // lógica para editar 
                if (!isset($objModelo->id_canal_youtube)) {
                    echo json_encode(["error" => "Faltan datos"]);
                    exit;
                }
                
                $objController->ObtenerCardVideosPorCanalesYoutube($objModelo->id_canal_youtube );
                break;

            case 'obtenercardshortsyoutube':
                if (!$objModelo->id_usuario) {
                    echo json_encode(["error" => "Faltan datos"]);
                    exit;
                }
                $objModelo->tipoVideo = strtolower($objModelo->tipoVideo);
                $objController->ObtenerCardShortsYoutube($objModelo->id_usuario);
                break;  
                break;

            case 'obtenercardvideosyoutube':
                if (!$objModelo->id_usuario) {
                    echo json_encode(["error" => "Faltan datos"]);
                    exit;
                }
                $objModelo->tipoVideo = strtolower($objModelo->tipoVideo);
                $objController->ObtenerCardVideosYoutube($objModelo->id_usuario);
                break;  
                break;
        
            case 'obtenercardcanalesyoutube':
                if (!$objModelo->id_usuario) {
                    echo json_encode(["error" => "Faltan datos"]);
                    exit;
                }
                $objModelo->tipoVideo = strtolower($objModelo->tipoVideo);
                $objController->ObtenerCardCanalesYoutube($objModelo->id_usuario);
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


