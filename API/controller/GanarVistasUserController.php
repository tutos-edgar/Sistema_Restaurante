<?php
require_once '../models/GanarVistasUser.php';

class GanarVistasUserController {
    private $modelo;

    public function __construct($db) {
        $this->modelo = new GanarVistasUser($db);
    }
    
    public function ObtenerCardVideosVistas(GanarVistasUser $usuario) {
        $modelo = $this->modelo->ObtenerCardVideosVistas($usuario);
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
                exit;
            }
            
        }
        echo json_encode(["success" => "false", "mensaje" => "No se encontraron Datos", "dato" => []]);
        exit;
       
    }

    public function GenerarVistasUser(GanarVistasUser $data) {

        try {
            $modelo = $this->modelo->AsignacionVistasGeneradas($data);
            if (isset($modelo['success']) && $modelo['success'] === true) {
                echo json_encode(["success" => "true","mensaje" => "El Dato Fue Registrado Correctamente"]);
            } else {
                if (isset($modelo['duplicado']) && $modelo['duplicado'] === true) {
                    echo json_encode(["success" => "false", "mensaje" => "El Alias ya esta Registrado", "datos" => []]);
                }else if (isset($modelo['codigo']) && $modelo['codigo'] === true) {
                    echo json_encode(["success" => "false","mensaje" => "El Código de Producto Ya Existe", "datos" => []]);
                } elseif (isset($modelo['error'])) {
                    echo json_encode(["success" => false, "mensaje" => $modelo['mensaje'], "error" => "Ocurrio una Excepcion al Obtener los Datos ", "datos" => []]);
                    // echo json_encode(["error" => "Error: " . $modelo['error']]);
                } else {
                   
                    echo json_encode(["success" => false, "mensaje" => "No se Pudo Crear la Cuenta", "error" => "Ocurrio una Excepcion al Obtener los Datos ", "datos" => []]);
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

    //Auto guardado de mis canales
    public function AutoGestionadoGuardado(GanarVistasUser $data) {

        try {
            $modelo = $this->modelo->AutoAsignacionGuardado($data);
            if (isset($modelo['success']) && $modelo['success'] === true) {
                echo json_encode(["success" => "true","mensaje" => "El Dato Fue Registrado Correctamente"]);
            } else {
                if (isset($modelo['duplicado']) && $modelo['duplicado'] === true) {
                    echo json_encode(["success" => "false", "mensaje" => "El Alias ya esta Registrado", "datos" => []]);
                }else if (isset($modelo['codigo']) && $modelo['codigo'] === true) {
                    echo json_encode(["success" => "false","mensaje" => "El Código de Producto Ya Existe", "datos" => []]);
                } elseif (isset($modelo['error'])) {
                    echo json_encode(["success" => false, "mensaje" => $modelo['mensaje'], "error" => "Ocurrio una Excepcion al Obtener los Datos ", "datos" => []]);
                    // echo json_encode(["error" => "Error: " . $modelo['error']]);
                } else {
                   
                    echo json_encode(["success" => false, "mensaje" => "No se Pudo Crear la Cuenta", "error" => "Ocurrio una Excepcion al Obtener los Datos ", "datos" => []]);
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








    

    public function ValidarVideoDeVista(GanarVistasUser $usuario) {
        $modelo = $this->modelo->ValidarTipoVistasUsuario($usuario);
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

   
    


}
