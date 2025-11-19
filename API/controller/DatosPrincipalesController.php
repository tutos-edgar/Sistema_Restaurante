<?php

require_once '../models/DatosPrincipales.php';

class DatosPrincipalesController {
    private $modelo;

    public function __construct($db) {
        $this->modelo = new DatosPrincipales($db);
    }
    

    public function ObtenerCardPrincipales(DatosPrincipales $datos) {
        $modelo = $this->modelo->ObtenerCardPrincipales($datos);
        if(!empty($modelo)){
            if(is_array($modelo) && array_key_exists('error', $modelo)){                      
                    if(isset($modelo['error']) && $modelo['error'] === true){
                        if(is_array($modelo) && array_key_exists('mensaje', $modelo)){
                            echo json_encode(["success" => false, "mensaje" => $modelo['mensaje'], "error" => true, "datos" => []]);
                            exit();
                        }else{
                            echo json_encode(["success" => false, "mensaje" => "Ocurrio Una Exepcion en el Registro", "error" => true, "datos" => []]);
                            exit();
                        }
                                            
                    }                        
                }
            
            if(is_array($modelo) && array_key_exists('success', $modelo)){                      
                    if(isset($modelo['success']) && $modelo['success'] === true){
                        if(is_array($modelo) && array_key_exists('mensaje', $modelo)){
                            echo json_encode(["success" => "true", "mensaje" => "Datos Econtrado", "datos" => $modelo]);
                             exit();
                        }else{
                            echo json_encode(["success" => "false", "mensaje" => "Perfil no encontrado", "datos" => []]);
                             exit();
                        }
                                            
                    }                        
                }
            if(is_array($modelo)){                      
                echo json_encode(["success" => "true", "mensaje" => "Datos Econtrado", "datos" => $modelo]);
                exit();
            }
            
           
        }
           
        echo json_encode(["success" => "false", "mensaje" => "Perfil no encontrado ", "datos" => []]);
        exit();
    }


}
