<?php
require_once '../models/canales.php';

class CanalesController {
    private $modelo;

    public function __construct($db) {
        $this->modelo = new Canales($db);
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



    public function MisVideosRandom() {

        $modelo = $this->modelo->MisVideosRamdom();
        if(!empty($modelo)){

            if(isset( $modelo['success']) && $modelo['success'] === true){
                if(isset( $modelo['datos'])){
                    if(isset( $modelo['datos']['datos']) && $modelo['datos']['datos']== null){
                        echo json_encode(["success" => "true", "mensaje" => "Datos Econtrado", "datos" => []]);
                        exit();
                    }
                    echo json_encode(["success" => "true", "mensaje" => "Datos Econtrado", "datos" => $modelo['datos']]);
                    exit();
                }

                echo json_encode(["success" => "true", "mensaje" => "Datos Econtrado", "datos" => $modelo]);
                exit();
            }

            if(isset( $modelo['success']) && $modelo['success'] === false){
                if(isset( $modelo['error']) && $modelo['error'] === true){
                    if(isset( $modelo['mensaje'])){
                        echo json_encode(["success" => "false", "mensaje" => $modelo['mensaje'], "datos" => []]);
                        exit();
                    }
                    echo json_encode(["success" => "false", "mensaje" => "Ocurrio un Error en el Servidor", "datos" => []]);
                    exit();
                }

                if(isset( $modelo['mensaje'])){
                    echo json_encode(["success" => "false", "mensaje" => $modelo['mensaje'], "datos" => []]);
                    exit();
                }
                echo json_encode(["success" => "false", "mensaje" => "No se pudo comunicar con el Servidor", "datos" => []]);
                exit();
            }
            
        }
        echo json_encode(["success" => "false", "mensaje" => "No se encontraron Datos", "datos" => []]);

        // $modelo = $this->modelo->VieosRamdom();
        // if(!empty($modelo)){
        //     echo json_encode(["success" => "true", "mensaje" => "Datos Econtrado", "datos" => $modelo]);
        //     exit();
        // }
        // echo json_encode(["success" => "false", "mensaje" => "Producto no encontrado"]);
    }

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

    // public function crearCompra(Compras $data) {

    //     try {
    //         $resultado = $this->modelo->crearCompra($data);
    
    //         if (isset($resultado['success']) && $resultado['success'] === true) {
    //             echo json_encode(["success" => "true","mensaje" => "Compra Creada Correctamente"]);
    //         } else {
    //             if (isset($resultado['producto']) && $resultado['producto'] === false) {
    //                 echo json_encode(["success" => "false", "mensaje" => "El Producto del Listado no Existe"]);
    //             }else if (isset($resultado['codigo']) && $resultado['codigo'] === true) {
    //                 echo json_encode(["success" => "false","mensaje" => "El Código de Producto Ya Existe"]);
    //             } elseif (isset($resultado['error'])) {
    //                 echo json_encode(["error" => "Error: " . $resultado['error']]);
    //             } else {
    //                 echo json_encode(["warning" => "No se Pudo Crear el Producto"]);
    //             }
    //         }
    
    //     } catch (Exception $e) {
    //         http_response_code(500);
    //         echo json_encode(["error" => $e->getMessage()]);
    //     }
        
    // }

    // public function actualizarCompra(Compras $data) {

    //     try {

    //         $resultado = $this->modelo->actualizarCompra($data);

    //         if (isset($resultado['success']) && $resultado['success'] === true) {
    //             if (isset($resultado['actualizado']) && $resultado['actualizado'] === false) {
    //                 echo json_encode(["success" => "true", "mensaje" => "No hubo cambio de en la Actualización de Registro"]);
    //                 exit();
    //             }
    //             echo json_encode(["success" => "true", "mensaje" => "Datos de Producto Actualizado"]);
    //         } else {
    //             if (isset($resultado['producto']) && $resultado['producto'] === true) {
    //                 echo json_encode(["success" => "false","mensaje" => "Este Nombre Producto ya esta registrado"]);
    //             }elseif (isset($resultado['codigo']) && $resultado['codigo'] === true) {
    //                 echo json_encode(["success" => "false","mensaje" => "Este Código de Producto ya esta registrado"]);
    //             }elseif (isset($resultado['existe']) && $resultado['existe'] === false) {
    //                 echo json_encode(["success" => "false","mensaje" => "Este Producto no Existe"]);
    //             } elseif (isset($resultado['error'])) {
    //                 echo json_encode(["error" => "Error: " . $resultado['error']]);
    //             } else {
    //                 echo json_encode(["warning" => "No se Pudo Actualizar el Proveedor"]);
    //             }
    //         }
    //     } catch (PDOException $e) {
    //         http_response_code(500);
    //         echo json_encode(["error" => "Error de base de datos", "detalle" => $e->getMessage()]);
    //     } catch (Exception $e) {
    //         http_response_code(500);
    //         echo json_encode(["error" => $e->getMessage()]);
    //     }

    // }

    // public function eliminarProducto($id) {
    //     if ($this->modelo->eliminarCompra($id)) {
    //         echo json_encode(["mensaje" => "Producto Eliminado"]);
    //     } else {
    //         echo json_encode(["error" => "Error al eliminar Producto"]);
    //     }
    // }


    // public function buscarDatosGeneralesLike($data) {

    //     try {

    //         $resultado = $this->modelo->buscarDatosGeneralesLike($data);
          
    //         if(!empty($resultado)){
    //             echo json_encode(["success" => "true", "mensaje" => "Datos Econtrado", "datos" => $resultado]);
    //             exit();
    //         }
    //         echo json_encode(["success" => "false", "mensaje" => "Usuario no encontrado"]);
          
    //     } catch (PDOException $e) {
    //         http_response_code(500);
    //         echo json_encode(["error" => "Error de base de datos", "detalle" => $e->getMessage()]);
    //     } catch (Exception $e) {
    //         http_response_code(500);
    //         echo json_encode(["error" => $e->getMessage()]);
    //     }

    // }


}
