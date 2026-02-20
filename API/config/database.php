<?php

class Database {
    private $host = "localhost";
    private $db_name = "db_restaurante";
    private $username = "root";
    private $port = "3399";
    private $password = "";
    public $conn; 
    public $connExtern;   
  
    // private $host = "fdb1029.awardspace.net";
    // private $db_name = "4313596_boost";
    // private $username = "4313596_boost";
    // private $port = "3306";
    // private $password = "J/qmr[s62dJAoPcv";
    // public $conn; 
    // public $connExtern; 

    public function connect() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name, 
                $this->username, 
                $this->password
            );
            $this->conn->exec("set names utf8");
            return $this->conn;
        } catch(PDOException $exception) {
            http_response_code(500);
            // echo "Error de conexión: " . $exception->getMessage();
            // echo json_encode(["success" => false, "mensaje" => "No se pudo comunicar con el Servidor"]);
            $errorCode = $exception->getCode();
            $errorMessage = "Error en la base de datos.";

            if ($errorCode == 1049) {
                $errorMessage = "La base de datos especificada no existe.";
            } elseif ($errorCode == 2002) {
                $errorMessage = "No se pudo comunicar con el servidor de base de datos.";
            } elseif ($errorCode == 1045) {
                $errorMessage = "Usuario o contraseña de base de datos incorrectos.";
            }

            //Devolver en JSON y detener la ejecución
            echo json_encode([
                "success" => false,
                "mensaje" => $errorMessage
            ]);
            exit();
        }
        
    }

    public function connectExtern() {
        $host = "localhost";
        $db_name = "db_youtubeuser";
        $username = "root";
        $port = "3306";
        $password = "";

        $this->connExtern = null;
        
        try {
            $this->connExtern = new PDO(
                "mysql:host=" . $host . ";port=" . $port . ";dbname=" . $db_name, 
                $username, 
                $password
            );
            $this->connExtern->exec("set names utf8");
            return $this->connExtern;
        } catch(PDOException $exception) {
            http_response_code(500);
            // echo "Error de conexión: " . $exception->getMessage();
            // echo json_encode(["success" => false, "mensaje" => "No se pudo comunicar con el Servidor"]);
            $errorCode = $exception->getCode();
            $errorMessage = "Error en la base de datos.";

            if ($errorCode == 1049) {
                $errorMessage = "La base de datos especificada no existe.";
            } elseif ($errorCode == 2002) {
                $errorMessage = "No se pudo comunicar con el servidor de base de datos.";
            } elseif ($errorCode == 1045) {
                $errorMessage = "Usuario o contraseña de base de datos incorrectos.";
            }

            //Devolver en JSON y detener la ejecución
            echo json_encode([
                "success" => false,
                "mensaje" => $errorMessage
            ]);
            exit();
        }
        
    }

}
