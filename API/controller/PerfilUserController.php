<?php

require_once '../models/PerfilUser.php';
require_once '../Interfaces/IGenerarDatos.php';
require_once '../Interfaces/IBuscarDatos.php';

class PerfilUserControllerr implements IGenerarDatos {
    private $modelo;

    public function __construct($db) {
        $this->modelo = new PerfilUser($db);
    }
    
    public function listarTodos() {
        
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
                echo json_encode(["success" => "true", "mensaje" => "Datos Econtrado", "datos" => $modelo['datos']]);
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

    public function guardar($data) {
        try {
            $modelo = $this->modelo->guardar($data);
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

    public function modificar($data) {
        try {
            $modelo = $this->modelo->modificar($data);
            if (isset($modelo['success']) && $modelo['success'] === true) {
                echo json_encode(["success" => "true","mensaje" => "Cuenta Actualizada Correctamente"]);
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

    public function eliminar($data) {
        try {
            $modelo = $this->modelo->eliminar($data);
            if (isset($modelo['success']) && $modelo['success'] === true) {
                echo json_encode(["success" => "true","mensaje" => "Cuenta Eliminada Correctamente"]);
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

    public function buscarId($id) {
        // $modelo = $this->modelo->obtenerId($id);
        // if(!empty($modelo)){
        //     echo json_encode(["success" => "true", "mensaje" => "Datos Econtrado", "datos" => $modelo]);
        //     exit();
        // }
        // echo json_encode(["success" => "false", "mensaje" => "Perfil no encontrado ", "datos" => []]);
        // exit();
    }

    public function buscarPorCampo($campo, $id) {
    //     $modelo = $this->modelo->obtenerPorCampo($campo, $id);
    }

    public function buscarTodos() {
        // $modelo = $this->modelo->obtenerTodos();
        // if(!empty($modelo)){
        //     echo json_encode(["success" => "true", "mensaje" => "Datos Econtrado", "datos" => $modelo]);
        //     exit();
        // }
        // echo json_encode(["success" => "false", "mensaje" => "No se encontraron Datos", "datos" => []]);
        // exit();
    }

    public function buscarPorLike($condicion) {
        // $modelo = $this->modelo->obtenerPorLike($condicion);
        // if(!empty($modelo)){
        //     echo json_encode(["success" => "true", "mensaje" => "Datos Econtrado", "datos" => $modelo]);
        //     exit();
        // }
        // echo json_encode(["success" => "false", "mensaje" => "No se encontraron Datos", "datos" => []]);
        // exit();
    }


    // public function obtenerIdPerfilUsuario($id) {
    //     $modelo = $this->modelo->obtenerIdPerfilUsuario($id, true);
    //     if(!empty($modelo)){
    //         if(is_array($modelo) && array_key_exists('error', $modelo)){                      
    //                 if(isset($modelo['error']) && $modelo['error'] === true){
    //                     if(is_array($modelo) && array_key_exists('mensaje', $modelo)){
    //                         echo json_encode(["success" => false, "mensaje" => $modelo['mensaje'], "error" => true, "datos" => []]);
    //                         exit();
    //                     }else{
    //                         echo json_encode(["success" => false, "mensaje" => "Ocurrio Una Exepcion en el Registro", "error" => true, "datos" => []]);
    //                         exit();
    //                     }
                                            
    //                 }                        
    //             }
            
    //         if(is_array($modelo) && array_key_exists('success', $modelo)){                      
    //                 if(isset($modelo['success']) && $modelo['success'] === true){
    //                     if(is_array($modelo) && array_key_exists('mensaje', $modelo)){
    //                         echo json_encode(["success" => "true", "mensaje" => "Datos Econtrado", "datos" => $modelo]);
    //                          exit();
    //                     }else{
    //                         echo json_encode(["success" => "false", "mensaje" => "Perfil no encontrado", "datos" => []]);
    //                          exit();
    //                     }
                                            
    //                 }                        
    //             }
    //         if(is_array($modelo)){                      
    //             echo json_encode(["success" => "true", "mensaje" => "Datos Econtrado", "datos" => $modelo]);
    //             exit();
    //         }
            
           
    //     }
           
    //     echo json_encode(["success" => "false", "mensaje" => "Perfil no encontrado ", "datos" => []]);
    //     exit();
    // }

    // public function insertarPerfil(PerfilUser $data) {

    //     try {
    //         $modelo = $this->modelo->IngresarDatosPerfil($data);
    //         if (isset($modelo['success']) && $modelo['success'] === true) {
    //             echo json_encode(["success" => "true","mensaje" => "Cuenta Registrada Correctamente"]);
    //             exit();
    //         } else {
    //             if(is_array($modelo) && array_key_exists('error', $modelo)){                      
    //                 if(isset($modelo['error']) && $modelo['error'] === true){
    //                     if(is_array($modelo) && array_key_exists('mensaje', $modelo)){
    //                         echo json_encode(["success" => false, "mensaje" => $modelo['mensaje'], "error" => true]);
    //                         exit();
    //                     }else{
    //                         echo json_encode(["success" => false, "mensaje" => "Ocurrio Una Exepcion en el Registro", "error" => true]);
    //                         exit();
    //                     }
                                            
    //                 }                        
    //             }else if(is_array($modelo) && array_key_exists('duplicado', $modelo)){                      
    //                 if(isset($modelo['duplicado']) && $modelo['duplicado'] === true){
    //                    echo json_encode(["success" => "false", "mensaje" => "El Alias ya esta Registrado", "datos" => []]); 
    //                    exit();                  
    //                 }                        
    //             }else if(is_array($modelo) && array_key_exists('correo', $modelo)){                      
    //                 if(isset($modelo['correo']) && $modelo['correo'] === true){
    //                    echo json_encode(["success" => "false","mensaje" => "El Correo Ya Esta Registrado", "datos" => []]); 
    //                    exit();                 
    //                 }                        
    //             } else {
                   
    //                 echo json_encode(["success" => false, "mensaje" => "No se Pudo Crear la Cuenta", "error" => "Ocurrio una Excepcion al Obtener los Datos ", "datos" => []]);
    //                 exit();
    //             }
    //         }
    
    //     } catch (PDOException $e) {            
    //         http_response_code(500);           
    //         return ["success" => false, "error" => "true", "mensaje" => "No se comunico con el Servidor"];
           
    //     } catch (Exception $e) {           
    //         http_response_code(500);           
    //         return ["success" => false, "error" => "true", "mensaje" =>  "No se comunico con el Servidor"];
            
    //     }
        
    // }

    // public function CambiarPasswordUser(PerfilUser $data) {
    //     try {
    //         $modelo = $this->modelo->ActualizarPasswordUser($data);
    //         if (isset($modelo['success']) && $modelo['success'] === true) {
    //             if(is_array($modelo) && array_key_exists('datos', $modelo)){
    //                 echo json_encode(["success" => "true","mensaje" => "Datos Registrados Correctamente", "datos"=>$modelo['datos']]);
    //                 exit();
    //             }
    //             echo json_encode(["success" => "true","mensaje" => "Datos Registrados Correctamente"]);
    //             exit();
    //         } else {
    //             if(is_array($modelo) && array_key_exists('error', $modelo)){                      
    //                 if(isset($modelo['error']) && $modelo['error'] === true){
    //                     if(is_array($modelo) && array_key_exists('mensaje', $modelo)){
    //                         echo json_encode(["success" => false, "mensaje" => $modelo['mensaje'], "error" => true]);
    //                         exit();
    //                     }else{
    //                         echo json_encode(["success" => false, "mensaje" => "Ocurrio Una Exepcion en el Registro", "error" => true]);
    //                         exit();
    //                     }
                                            
    //                 }else{
    //                     if(is_array($modelo) && array_key_exists('mensaje', $modelo)){
    //                         echo json_encode(["success" => false, "mensaje" => $modelo['mensaje'], "error" => false]);
    //                         exit();
    //                     }else{
    //                         echo json_encode(["success" => false, "mensaje" => "Ocurrio Una Exepcion en el Registro", "error" => false]);
    //                         exit();
    //                     }
    //                 }
    //             }else if(is_array($modelo) && array_key_exists('duplicado', $modelo)){                      
    //                 if(isset($modelo['duplicado']) && $modelo['duplicado'] === true){
    //                    echo json_encode(["success" => "false", "mensaje" => "El Alias ya esta Registrado", "datos" => []]); 
    //                    exit();                  
    //                 }                        
    //             }else if(is_array($modelo) && array_key_exists('correo', $modelo)){                      
    //                 if(isset($modelo['correo']) && $modelo['correo'] === true){
    //                    echo json_encode(["success" => "false","mensaje" => "El Correo Ya Esta Registrado", "datos" => []]); 
    //                    exit();                 
    //                 }                        
    //             } else {
                   
    //                 echo json_encode(["success" => false, "mensaje" => "No se Pudo Crear la Cuenta", "error" => "Ocurrio una Excepcion al Obtener los Datos ", "datos" => []]);
    //                 exit();
    //             }
    //         }
    
    //     } catch (PDOException $e) {            
    //         http_response_code(500);           
    //         return ["success" => false, "error" => "true", "mensaje" => "No se comunico con el Servidor"];
           
    //     } catch (Exception $e) {           
    //         http_response_code(500);           
    //         return ["success" => false, "error" => "true", "mensaje" =>  "No se comunico con el Servidor"];
            
    //     }
    // }










}
