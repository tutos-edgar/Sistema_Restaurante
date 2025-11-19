<?php
require_once '../models/UsuariosYoutube.php';

class GenerarVistasController {
    private $modelo;

    public function __construct($db) {
        $this->modelo = new GeneracionVistas($db);
    }
    
    // public function obtenerTodos() {
    //     $modelo = $this->modelo->obtenerTodos();
    //     if(!empty($modelo)){

    //         if(isset( $modelo['success']) && $modelo['success'] === true){
    //             echo json_encode(["success" => "true", "mensaje" => "Datos Econtrado", "datos" => $modelo]);
    //             exit();
    //         }

    //         if(isset( $modelo['success']) && $modelo['success'] === false){
    //             if(isset( $modelo['error']) && $modelo['error'] === true){
    //                 if(isset( $modelo['mensaje'])){
    //                     echo json_encode(["success" => "false", "mensaje" => $modelo['mensaje'], "datos" => []]);
    //                     exit();
    //                 }
    //                 echo json_encode(["success" => "false", "mensaje" => "No se pudo comunicar con el Servidor", "datos" => []]);
    //                 exit();
    //             }
    //             echo json_encode(["success" => "false", "mensaje" => "No se pudo comunicar con el Servidor", "dato" => []]);
    //         }
            
    //     }
    //     echo json_encode(["success" => "false", "mensaje" => "No se encontraron Datos", "dato" => []]);
       
    // }

    public function ValidarVideoDeVista($usuario, $tipo) {
        $modelo = $this->modelo->ValidarTipoVistasUsuario($usuario, $tipo);
        if(!empty($modelo)){

            if(isset( $modelo['success']) && $modelo['success'] === true){
                echo json_encode(["success" => "true", "mensaje" => "Datos Econtrado", "datos" => $modelo]);
                exit();
            }

            if(isset( $modelo['success']) && $modelo['success'] === false){
                if(isset( $modelo['error']) && $modelo['error'] === true){
                    if(isset( $modelo['mensaje'])){
                        echo json_encode(["success" => "false", "mensaje" => $modelo['mensaje'], "datos" => []]);
                        exit();
                    }
                    echo json_encode(["success" => "false", "mensaje" => "No se pudo comunicar con el Servidor", "datos" => []]);
                    exit();
                }

                if (array_key_exists('datos', $modelo) && isset($modelo['datos'])){
                    if (array_key_exists('mensaje', $modelo) && isset($modelo['mensaje'])){
                        echo json_encode(["success" => "false", "mensaje" => $modelo['mensaje'], "datos" => $modelo]);
                        exit();
                    }
                    echo json_encode(["success" => "false", "mensaje" => "No se encontraron Datos", "datos" => $modelo]);
                    exit();
                }

                echo json_encode(["success" => "false", "mensaje" => "No se pudo comunicar con el Servidor", "dato" => []]);
                exit();
            }
            
        }
        echo json_encode(["success" => "false", "mensaje" => "No se encontraron Datos", "dato" => []]);
       
    }

    public function ObtenerCardCanalesYoutube($usuario) {
        $modelo = $this->modelo->ObtenerCardCanalesYoutube($usuario);
        if(!empty($modelo)){

            if(isset( $modelo['success']) && $modelo['success'] === true){
                echo json_encode(["success" => "true", "mensaje" => "Datos Econtrado", "datos" => $modelo]);
                exit();
            }

            if(isset( $modelo['success']) && $modelo['success'] === false){
                if(isset( $modelo['error']) && $modelo['error'] === true){
                    if(isset( $modelo['mensaje'])){
                        echo json_encode(["success" => "false", "mensaje" => $modelo['mensaje'], "datos" => []]);
                        exit();
                    }
                    echo json_encode(["success" => "false", "mensaje" => "No se pudo comunicar con el Servidor", "datos" => []]);
                    exit();
                }
                echo json_encode(["success" => "false", "mensaje" => "No se pudo comunicar con el Servidor", "dato" => []]);
            }
            
        }
        echo json_encode(["success" => "false", "mensaje" => "No se encontraron Datos", "dato" => []]);
       
    }

    public function ObtenerCardVideosYoutube($usuario) {
        $modelo = $this->modelo->ObtenerCardVideosYoutube($usuario);
        if(!empty($modelo)){

            if(isset( $modelo['success']) && $modelo['success'] === true){
                echo json_encode(["success" => "true", "mensaje" => "Datos Econtrado", "datos" => $modelo]);
                exit();
            }

            if(isset( $modelo['success']) && $modelo['success'] === false){
                if(isset( $modelo['error']) && $modelo['error'] === true){
                    if(isset( $modelo['mensaje'])){
                        echo json_encode(["success" => "false", "mensaje" => $modelo['mensaje'], "datos" => []]);
                        exit();
                    }
                    echo json_encode(["success" => "false", "mensaje" => "No se pudo comunicar con el Servidor", "datos" => []]);
                    exit();
                }

                if (array_key_exists('datos', $modelo) && isset($modelo['datos'])){
                    if (array_key_exists('mensaje', $modelo) && isset($modelo['mensaje'])){
                        echo json_encode(["success" => "false", "mensaje" => $modelo['mensaje'], "datos" => $modelo]);
                        exit();
                    }
                    echo json_encode(["success" => "false", "mensaje" => "No se encontraron Datos", "datos" => $modelo]);
                    exit();
                }
                echo json_encode(["success" => "false", "mensaje" => "No se pudo comunicar con el Servidor", "datos" => []]);
                exit();
            }
            
        }
        echo json_encode(["success" => "false", "mensaje" => "No se encontraron Datos", "datos" => []]);
        exit();       
    }

    public function ObtenerCardShortsYoutube($usuario) {
        $modelo = $this->modelo->ObtenerCardShortsYoutube($usuario);
        if(!empty($modelo)){

            if(isset( $modelo['success']) && $modelo['success'] === true){
                echo json_encode(["success" => "true", "mensaje" => "Datos Econtrado", "datos" => $modelo]);
                exit();
            }

            if(isset($modelo['success']) && $modelo['success'] === false){
                if(isset( $modelo['error']) && $modelo['error'] === true){
                    if(isset( $modelo['mensaje'])){
                        echo json_encode(["success" => "false", "mensaje" => $modelo['mensaje'], "datos" => []]);
                        exit();
                    }
                    echo json_encode(["success" => "false", "mensaje" => "No se pudo comunicar con el Servidor", "datos" => []]);
                    exit();
                }

                if (array_key_exists('datos', $modelo) && isset($modelo['datos'])){
                    if (array_key_exists('mensaje', $modelo) && isset($modelo['mensaje'])){
                        echo json_encode(["success" => "false", "mensaje" => $modelo['mensaje'], "datos" => $modelo]);
                        exit();
                    }
                    echo json_encode(["success" => "false", "mensaje" => "No se encontraron Datos", "datos" => $modelo]);
                    exit();
                }
                echo json_encode(["success" => "false", "mensaje" => "No se pudo comunicar con el Servidor", "datos" => []]);
                exit();
            }
            
        }
        echo json_encode(["success" => "false", "mensaje" => "No se encontraron Datos", "dato" => []]);
        exit();       
    }

    public function ObtenerCardVideosPorCanalesYoutube($usuario) {
        $modelo = $this->modelo->ObtenerCardVideosPorCanalesYoutube($usuario);
        if(!empty($modelo)){

            if(isset( $modelo['success']) && $modelo['success'] === true){
                echo json_encode(["success" => "true", "mensaje" => "Datos Econtrado", "datos" => $modelo]);
                exit();
            }

            if(isset( $modelo['success']) && $modelo['success'] === false){
                if(isset( $modelo['error']) && $modelo['error'] === true){
                    if(isset( $modelo['mensaje'])){
                        echo json_encode(["success" => "false", "mensaje" => $modelo['mensaje'], "datos" => []]);
                        exit();
                    }
                    echo json_encode(["success" => "false", "mensaje" => "No se pudo comunicar con el Servidor", "datos" => []]);
                    exit();
                }
                echo json_encode(["success" => "false", "mensaje" => "No se pudo comunicar con el Servidor", "datos" => []]);
            }
            
        }
        echo json_encode(["success" => "false", "mensaje" => "No se encontraron Datos", "datos" => []]);
       
    }



    public function GenerarVistasDeudas(GeneracionVistas $data) {

        try {
            $modelo = $this->modelo->GenerarVistasDeudas($data);
            if (isset($modelo['success']) && $modelo['success'] === true) {
                echo json_encode(["success" => "true","mensaje" => "Resgistro Creado Correctamente"]);
            } else {
                if (isset($modelo['existe']) && $modelo['existe'] === false) {
                    echo json_encode(["success" => "false", "mensaje" => "El Video No esta Registrado", "datos" => []]);
                } elseif (isset($modelo['error'])) {
                    echo json_encode(["success" => false, "mensaje" => $modelo['mensaje'], "error" => "Ocurrio una Excepcion al Obtener los Datos ", "datos" => []]);
                    // echo json_encode(["error" => "Error: " . $modelo['error']]);
                } else {
                   
                    echo json_encode(["success" => false, "mensaje" => "No se Pudo Crear el Dato", "error" => "Ocurrio una Excepcion al Obtener los Datos ", "datos" => []]);
                }
            }
    
        } catch (PDOException $e) {            
            http_response_code(500);           
            return ["success" => false, "error" => "true", "mensaje" => "No se comunico con el Servidor"];
           
        } catch (Exception $e) {           
            http_response_code(500);           
            return ["success" => false, "error" => "true", "mensaje" =>  "No se comunico con el Servidor"];
            
        }
        
    }

    


}
