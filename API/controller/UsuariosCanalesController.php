<?php
require_once '../models/UsuariosCanales.php';

class UsuariosCanalesController {
    private $modelo;

    public function __construct($db) {
        $this->modelo = new UsuariosCanales($db);
    }

    public function obtenerTodos($usuario) {
        $modelo = $this->modelo->obtenerTodos($usuario);
        if(!empty($modelo)){

            if(is_array($modelo) && array_key_exists('success', $modelo)){
                if(isset($modelo['success']) && $modelo['success'] === true){
                    echo json_encode(["success" => "true", "mensaje" => "Datos Econtrado", "datos" => $modelo]);
                    exit();
                }
            }

            if(is_array($modelo) && array_key_exists('success', $modelo)){
                if(isset( $modelo['success']) && $modelo['success'] === false){
                    if(is_array($modelo) && array_key_exists('error', $modelo)){
                        if(isset( $modelo['error']) && $modelo['error'] === true){
                            if(is_array($modelo) && array_key_exists('mensaje', $modelo)){
                                if(isset( $modelo['mensaje'])){
                                    echo json_encode(["success" => "false", "mensaje" => $modelo['mensaje'], "datos" => []]);
                                    exit();
                                }else{
                                    echo json_encode(["success" => "false", "mensaje" => "Ha ocurrido un tipo de excepcion", "datos" => []]);
                                    exit();
                                }
                            }                            
                        }
                    }                    
                    echo json_encode(["success" => "false", "mensaje" => "No se pudo comunicar con el Servidor", "datos" => []]);
                    exit();
                }
            }

            if(is_array($modelo)){
               echo json_encode(["success" => "true", "mensaje" => "Datos Econtrado", "datos" => $modelo]);
               exit(); 
            }
            

        }
        echo json_encode(["success" => "false", "mensaje" => "No se encontraron Datos", "datos" => []]);
        exit();
       
    }


    public function insertarCanal(UsuariosCanales $data) {

        try {
            $modelo = $this->modelo->crearCanales($data);
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
                if (isset($modelo['success']) && $modelo['success'] === true) {
                    echo json_encode(["success" => "true","mensaje" => "Dato Registrado Correctamente"]);
                } else {
                    if (isset($modelo['duplicado']) && $modelo['duplicado'] === true) {
                        echo json_encode(["success" => "false", "mensaje" => "El Canal ya esta Registrado", "datos" => []]);
                    }else if (isset($modelo['codigo']) && $modelo['codigo'] === true) {
                        echo json_encode(["success" => "false","mensaje" => "El Registro Ya Existe", "datos" => []]);
                    }else {
                        if(array_key_exists('mensaje', $modelo)){
                            if(isset($modelo['mensaje'])){
                                echo json_encode(["success" => false, "mensaje" => $modelo['mensaje'], "error" =>"false", "datos" => []]);
                            }else{
                                echo json_encode(["success" => false, "mensaje" => "No se Pudo Crear la Cuenta", "error" => "Ocurrio una Excepcion al Obtener los Datos ", "datos" => []]);
                            }
                        }else{
                             echo json_encode(["success" => false, "mensaje" => "Error en el Servidor", "error" => "Error Desconocido ", "datos" => []]);
                        }
                        
                    }
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

    public function actualizarCanal(UsuariosCanales $data) {

        try {

            $modelo = $this->modelo->actualizarCanal($data);

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
                    if(is_array($modelo) && array_key_exists('actualizado', $modelo)){
                        if (isset($modelo['actualizado']) && $modelo['actualizado'] === false) {
                            echo json_encode(["success" => "true", "mensaje" => "No hubo cambio en la Actualización del Registro"]);
                            exit();
                        }
                        echo json_encode(["success" => "true", "mensaje" => "Los Datos Fueron Actualizado"]);
                        exit();
                    }else{
                        echo json_encode(["success" => false, "mensaje" => "Ocurrio Una Exepcion en el Registro", "error" => true, "datos" => []]);
                        exit();
                    }

                }else{

                    if(array_key_exists('existe', $modelo)){
                        if (isset($modelo['existe']) && $modelo['existe'] === false) {
                            echo json_encode(["success" => "false","mensaje" => "Este Registro no Existe"]);
                            exit();
                        }
                    }else if(array_key_exists('duplicado', $modelo)){
                        if (isset($modelo['duplicado']) && $modelo['duplicado'] === true) {
                            echo json_encode(["success" => "false","mensaje" => "Este Registro Ya Existe"]);
                            exit();
                        }
                    }else {
                        echo json_encode(["success" => "false","mensaje" => "No se Pudo Actualizar el Dato"]);
                        exit();
                    }
                }
            }

            echo json_encode(["success" => "false","mensaje" => "Algo Salío Mal Al Actualizar el Dato"]);
            exit();


        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error de base de datos", "detalle" => $e->getMessage()]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => $e->getMessage()]);
        }

    }

    public function eliminarCanal($id) {
        if ($this->modelo->eliminarCanal($id)) {
            echo json_encode(["mensaje" => "Canal Fue Eliminado"]);
        } else {
            echo json_encode(["error" => "Error al eliminar el Canal"]);
        }
    }


    public function obtenerPorId($id) {
        $modelo = $this->modelo->obtenerPorIdCanal($id);
        if(!empty($modelo)){
            if(is_array($modelo) && array_key_exists('success', $modelo)){
                if(isset($modelo['success']) && $modelo['success'] === true){
                    echo json_encode(["success" => "true", "mensaje" => "Datos Econtrado", "datos" => $modelo]);
                    exit();
                }
            }

            if(is_array($modelo) && array_key_exists('success', $modelo)){
                if(isset( $modelo['success']) && $modelo['success'] === false){
                    if(is_array($modelo) && array_key_exists('error', $modelo)){
                        if(isset( $modelo['error']) && $modelo['error'] === true){
                            if(is_array($modelo) && array_key_exists('mensaje', $modelo)){
                                if(isset( $modelo['mensaje'])){
                                    echo json_encode(["success" => "false", "mensaje" => $modelo['mensaje'], "datos" => []]);
                                    exit();
                                }else{
                                    echo json_encode(["success" => "false", "mensaje" => "Ha ocurrido un tipo de excepcion", "datos" => []]);
                                    exit();
                                }
                            }                            
                        }
                    }                    
                    echo json_encode(["success" => "false", "mensaje" => "No se pudo comunicar con el Servidor", "dato" => []]);
                    exit();
                }
            }

            if(is_array($modelo)){
               echo json_encode(["success" => "true", "mensaje" => "Datos Econtrado", "datos" => $modelo]);
               exit(); 
            }
            

        }
        echo json_encode(["success" => "false", "mensaje" => "No se encontraron Datos", "dato" => []]);
        exit();
       
    }
   

}
