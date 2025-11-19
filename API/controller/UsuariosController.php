<?php
require_once '../models/UsuariosYoutube.php';

class UsuariosController {
    private $modelo;

    public function __construct($db) {
        $this->modelo = new UsuariosYoutube($db);
    }

    public function obtenerTodos() {
        $modelo = $this->modelo->obtenerTodos();
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


    // public function MisVideosRandom() {

    //     $modelo = $this->modelo->MisVideosRamdom();
    //     if(!empty($modelo)){

    //         if(isset( $modelo['success']) && $modelo['success'] === true){
    //             if(isset( $modelo['datos'])){
    //                 if(isset( $modelo['datos']['datos']) && $modelo['datos']['datos']== null){
    //                     echo json_encode(["success" => "true", "mensaje" => "Datos Econtrado", "datos" => []]);
    //                     exit();
    //                 }
    //                 echo json_encode(["success" => "true", "mensaje" => "Datos Econtrado", "datos" => $modelo['datos']]);
    //                 exit();
    //             }

    //             echo json_encode(["success" => "true", "mensaje" => "Datos Econtrado", "datos" => $modelo]);
    //             exit();
    //         }

    //         if(isset( $modelo['success']) && $modelo['success'] === false){
    //             if(isset( $modelo['error']) && $modelo['error'] === true){
    //                 if(isset( $modelo['mensaje'])){
    //                     echo json_encode(["success" => "false", "mensaje" => $modelo['mensaje'], "datos" => []]);
    //                     exit();
    //                 }
    //                 echo json_encode(["success" => "false", "mensaje" => "Ocurrio un Error en el Servidor", "datos" => []]);
    //                 exit();
    //             }

    //             if(isset( $modelo['mensaje'])){
    //                 echo json_encode(["success" => "false", "mensaje" => $modelo['mensaje'], "datos" => []]);
    //                 exit();
    //             }
    //             echo json_encode(["success" => "false", "mensaje" => "No se pudo comunicar con el Servidor", "datos" => []]);
    //             exit();
    //         }
            
    //     }
    //     echo json_encode(["success" => "false", "mensaje" => "No se encontraron Datos", "datos" => []]);

    //     // $modelo = $this->modelo->VieosRamdom();
    //     // if(!empty($modelo)){
    //     //     echo json_encode(["success" => "true", "mensaje" => "Datos Econtrado", "datos" => $modelo]);
    //     //     exit();
    //     // }
    //     // echo json_encode(["success" => "false", "mensaje" => "Producto no encontrado"]);
    // }

    // public function obtenerPorIdPrecios($id) {
    //     $modelo = $this->modelo->obtenerPorIdPrecios($id);
    //     if(!empty($modelo)){
    //         echo json_encode(["success" => "true", "mensaje" => "Datos Econtrado", "datos" => $modelo]);
    //         exit();
    //     }
    //     echo json_encode(["success" => "false", "mensaje" => "Producto no encontrado"]);
    // }

    // public function obtenerPorNombre($id) {
    //     $modelo = $this->modelo->obtenerPorNombre($id);
    //     echo json_encode($modelo ?: ["error" => "Producto no encontrado"]);
    // }

    public function insertarUsuario(UsuariosYoutube $data) {

        try {
            $modelo = $this->modelo->crearUsuario($data);
            if (isset($modelo['success']) && $modelo['success'] === true) {
                echo json_encode(["success" => "true","mensaje" => "Cuenta Creada Correctamente"]);
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

    
    public function ValidarLoginAcceso(UsuariosYoutube $data) {

        try {

            $modelo = $this->modelo->ValidarLoginAcceso($data);

            if (isset($modelo['success']) && $modelo['success'] === true) {
                if(isset($modelo['mensaje'])){
                    echo json_encode(["success" => "true","mensaje" => $modelo['mensaje'], "datos" => $modelo]);
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
