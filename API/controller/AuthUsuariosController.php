<?php
require_once '../models/AuthUsuario.php';
require_once '../models/Usuarios.php';
require_once '../interfaces/IAuthUsuario.php';


class AuthUsuariosController implements IAuthUsuario {
    private $modelo;
    private $database; 
    private $db;

    public function __construct($db) {
        $this->database = new Database();
        $this->db = $this->database->connect();
        $this->modelo = new AuthUsuario($this->db);
    }

    public function validarLogin(Usuarios $usuario) {
        $modelo = $this->modelo->ValidarLoginAcceso($usuario);
        
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
