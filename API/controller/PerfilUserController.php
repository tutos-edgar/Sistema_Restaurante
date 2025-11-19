<?php
require_once '../models/UsuariosYoutube.php';

class PerfilUserControllerr {
    private $modelo;

    public function __construct($db) {
        $this->modelo = new PerfilUser($db);
    }
    
    public function obtenerTodos() {
        $modelo = $this->modelo->obtenerTodos();
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
                exit();
            }            
        }
        echo json_encode(["success" => "false", "mensaje" => "No se encontraron Datos", "dato" => []]);
        exit();       
    }

    public function obtenerIdPerfilUsuario($id) {
        $modelo = $this->modelo->obtenerIdPerfilUsuario($id, true);
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

    public function insertarPerfil(PerfilUser $data) {

        try {
            $modelo = $this->modelo->IngresarDatosPerfil($data);
            if (isset($modelo['success']) && $modelo['success'] === true) {
                echo json_encode(["success" => "true","mensaje" => "Cuenta Registrada Correctamente"]);
                exit();
            } else {
                if(is_array($modelo) && array_key_exists('error', $modelo)){                      
                    if(isset($modelo['error']) && $modelo['error'] === true){
                        if(is_array($modelo) && array_key_exists('mensaje', $modelo)){
                            echo json_encode(["success" => false, "mensaje" => $modelo['mensaje'], "error" => true]);
                            exit();
                        }else{
                            echo json_encode(["success" => false, "mensaje" => "Ocurrio Una Exepcion en el Registro", "error" => true]);
                            exit();
                        }
                                            
                    }                        
                }else if(is_array($modelo) && array_key_exists('duplicado', $modelo)){                      
                    if(isset($modelo['duplicado']) && $modelo['duplicado'] === true){
                       echo json_encode(["success" => "false", "mensaje" => "El Alias ya esta Registrado", "datos" => []]); 
                       exit();                  
                    }                        
                }else if(is_array($modelo) && array_key_exists('correo', $modelo)){                      
                    if(isset($modelo['correo']) && $modelo['correo'] === true){
                       echo json_encode(["success" => "false","mensaje" => "El Correo Ya Esta Registrado", "datos" => []]); 
                       exit();                 
                    }                        
                } else {
                   
                    echo json_encode(["success" => false, "mensaje" => "No se Pudo Crear la Cuenta", "error" => "Ocurrio una Excepcion al Obtener los Datos ", "datos" => []]);
                    exit();
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

    public function CambiarPasswordUser(PerfilUser $data) {
        try {
            $modelo = $this->modelo->ActualizarPasswordUser($data);
            if (isset($modelo['success']) && $modelo['success'] === true) {
                if(is_array($modelo) && array_key_exists('datos', $modelo)){
                    echo json_encode(["success" => "true","mensaje" => "Datos Registrados Correctamente", "datos"=>$modelo['datos']]);
                    exit();
                }
                echo json_encode(["success" => "true","mensaje" => "Datos Registrados Correctamente"]);
                exit();
            } else {
                if(is_array($modelo) && array_key_exists('error', $modelo)){                      
                    if(isset($modelo['error']) && $modelo['error'] === true){
                        if(is_array($modelo) && array_key_exists('mensaje', $modelo)){
                            echo json_encode(["success" => false, "mensaje" => $modelo['mensaje'], "error" => true]);
                            exit();
                        }else{
                            echo json_encode(["success" => false, "mensaje" => "Ocurrio Una Exepcion en el Registro", "error" => true]);
                            exit();
                        }
                                            
                    }else{
                        if(is_array($modelo) && array_key_exists('mensaje', $modelo)){
                            echo json_encode(["success" => false, "mensaje" => $modelo['mensaje'], "error" => false]);
                            exit();
                        }else{
                            echo json_encode(["success" => false, "mensaje" => "Ocurrio Una Exepcion en el Registro", "error" => false]);
                            exit();
                        }
                    }
                }else if(is_array($modelo) && array_key_exists('duplicado', $modelo)){                      
                    if(isset($modelo['duplicado']) && $modelo['duplicado'] === true){
                       echo json_encode(["success" => "false", "mensaje" => "El Alias ya esta Registrado", "datos" => []]); 
                       exit();                  
                    }                        
                }else if(is_array($modelo) && array_key_exists('correo', $modelo)){                      
                    if(isset($modelo['correo']) && $modelo['correo'] === true){
                       echo json_encode(["success" => "false","mensaje" => "El Correo Ya Esta Registrado", "datos" => []]); 
                       exit();                 
                    }                        
                } else {
                   
                    echo json_encode(["success" => false, "mensaje" => "No se Pudo Crear la Cuenta", "error" => "Ocurrio una Excepcion al Obtener los Datos ", "datos" => []]);
                    exit();
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









    public function ValidarLoginAcceso(UsuariosYoutube $data) {

        try {

            $modelo = $this->modelo->ValidarLoginAcceso($data);

            if (isset($modelo['success']) && $modelo['success'] === true) {
                if(isset($modelo['mensaje'])){
                    echo json_encode(["success" => "true","mensaje" => $modelo['mensaje'], "dato" => $modelo]);
                    exit();
                }else{
                    echo json_encode(["success" => "true","mensaje" => "Cuenta Creada Correctamente"]);
                    exit();
                }
                exit();
            } else {
                if (isset($modelo['duplicado']) && $modelo['duplicado'] === true) {
                    echo json_encode(["success" => "false", "mensaje" => "El Alias ya esta Registrado", "datos" => []]);
                    exit();
                }else if (isset($modelo['codigo']) && $modelo['codigo'] === true) {
                    echo json_encode(["success" => "false","mensaje" => "El Código de Producto Ya Existe", "datos" => []]);
                    exit();
                } elseif (isset($modelo['error']) && $modelo['error'] === true) {
                    if(isset($modelo['mensaje'])){
                        echo json_encode(["success" => false, "mensaje" => $modelo['mensaje'], "error" => "Ocurrio una Excepcion al Obtener los Datos ", "datos" => []]);
                        exit();
                    }else{
                        echo json_encode(["success" => false, "mensaje" => "Ocurrio una Excepcion al Obtener los Datos", "error" => "Ocurrio una Excepcion al Obtener los Datos ", "datos" => []]);
                        exit();
                    }                   
                } else {
                   if(isset($modelo['mensaje'])){
                        echo json_encode(["success" => false, "mensaje" => $modelo['mensaje'], "error" => "Dato Invalido ", "datos" => []]);
                        exit();
                    }else{
                        echo json_encode(["success" => false, "mensaje" => "No se Pudo validar los datos", "error" => "Ocurrio una Excepcion al Obtener los Datos ", "datos" => []]);
                        exit();
                    }
                    
                }
            }    
          
            if(!empty($modelo)){
                echo json_encode(["success" => "true", "mensaje" => "Datos Econtrado", "datos" => $modelo]);
                exit();
            }
            echo json_encode(["success" => "false", "mensaje" => "Usuario no encontrado"]);
          
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error de base de datos", "detalle" => $e->getMessage()]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => $e->getMessage()]);
        }

    }


}
