<?php

require_once '../models/FuncionesGenerales.php';
require_once '../config/config.php';
// require __DIR__ .'/../config/database.php';
date_default_timezone_set( 'America/Guatemala' );

ini_set('display_errors', 0);  // Desactiva la visualización de errores
error_reporting(E_ALL); 

class AUTH {

    // $funcionesGenerales = new FuncionesGenerales();

    function limpiarEntrada( $input ) {
        $input = trim($input); // Quita espacios
        $input = strip_tags($input);  // Elimina HTML y JS
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');  // Escapa caracteres
        return $input;
    }


    function obtenerToken(){
        return bin2hex(random_bytes(32));
    }

    function obtenerTokenSeguro(){
        return hash('sha256', bin2hex(random_bytes(32)));
    }

    function llenarEstadoPersonal($nombreID){
        try{
            $conexion = new Database();
            $db = $conexion->connect();
            $query = "SELECT * FROM estado_personal";
            $stmt = $db->prepare($query);
            $stmt->execute();

            $salida = "";

            if ($stmt->rowCount() > 0) {
                echo '<label for="estadoPersonal" class="col-form-label">Estado Personal</label>
                            <select name="'.$nombreID.'" id="'.$nombreID.'" class="form-control">
                            <option value="0" selected disabled>Seleccione un dato</option>';
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<option value="' . $row['id_estado_personal'] . '">' . $row['descripcion'] . '</option>';
                }
                echo '    </select>
                    ';

            } else {
                echo '
                            <label for="estadoPersonal" class="col-form-label">Estado Personal</label>
                            <select name="'.$nombreID.'" id="'.$nombreID.'" class="form-control">
                                <option value="">NO HAY ESTADOS DISPONIBLES</option>
                            </select>
                    ';
            }
        }catch(Exception $e){
            echo '
                            <label for="estadoPersonal" class="col-form-label">Estado Personal</label>
                            <select name="'.$nombreID.'" id="'.$nombreID.'" class="form-control">
                                <option value="">ESTADOS PERSONAL NO ENCONTRADOS</option>
                            </select>
                    ';
        }
    }

    public static function ValidarPaginas($tokenWeb){
        try{
            
            if($tokenWeb == TOKENWEB){
                return true;
            }
           
            header("Location: ".URLWEB);
            exit;

        }catch(Exception $e){
            return false;
        }
    }

    public static function ValidarPaginasPeticion($tokenWeb){
        try{
            
            if($tokenWeb == TOKENWEB){
                return true;
            }
           
            return false;

        }catch(Exception $e){
            return false;
        }
    }


    // function OptenerValorParametro($id){
    //     try{
    //         $conexion = new Database();
    //         $db = $conexion->connect();
    //         $query = "SELECT * FROM parametros WHERE id_parametro = ?";
    //         $stmt = $db->prepare($query);
    //         $stmt->execute([$id]);
    //         $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //         return $datos;
            
    //     }catch (PDOException $e) {
            
    //         return ["success" => false, "error" => "true", "mensaje" => $this->funcionesGenerales->validarCodigoDeError($e->getCode(), $e->getMessage())];
    //     } catch (Exception $e) {
            
    //         return ["success" => false, "error" => "true", "mensaje" => $this->funcionesGenerales->validarCodigoDeError($e->getCode(), $e->getMessage())];
    //     }
        
    // }


}