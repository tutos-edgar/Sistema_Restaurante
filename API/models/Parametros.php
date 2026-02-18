<?php

class Parametros
{
    private $conn;
    private $table = "parametros";

    public $descripcion_parametro, $id_parametro, $contenido_parametro;
    private $funcionGeneral;

    public function __construct($db)
    {
        $this->conn = $db;
        $this->funcionGeneral =new FuncionesGenerales();
    }

    public function obtenerTodos()
    {
        try{
            $query = "SELECT * FROM " . $this->table;
            $stmt  = $this->conn->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            http_response_code(500);
            return ["success" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage()), "error" => true];
        } catch (Exception $e) {
            http_response_code(500);
            return ["success" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage()), "error" => true];
        }
        
    }

    public function buscarParametros($id)
    {
        try{
            $query = "SELECT * FROM " . $this->table . " WHERE id_parametro= ?";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            http_response_code(500);
            return ["success" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage()), "error" => true];
        } catch (Exception $e) {
            http_response_code(500);
            return ["success" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage()), "error" => true];
        }
    }

    public function obtenerTodosLosParametros($id)
    {
        try{
            $query = "SELECT * FROM " . $this->table;
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([$id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["success" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage()), "error" => true];
        } catch (Exception $e) {
            return ["success" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage()), "error" => true];
        }
    }

    public function crearParametro(Parametros $roles)
    {

        try {

            $query = "INSERT INTO " . $this->table . " (descripcion_parametro, contenido_parametro) VALUES (?, ?)";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([
                $roles->descripcion_parametro, 
                $roles->contenido_parametro
            ]);

            if ($stmt->rowCount() > 0) {
                return ["success" => true];
            } else {
                return ["success" => false];
            }

        } catch (PDOException $e) {
            return ["success" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage()), "error" => true];
        } catch (Exception $e) {
            return ["success" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage()), "error" => true];
        }

    }

    public function actualizarParametro(Parametros $roles)
    {

        try {

            $query = "UPDATE " . $this->table . " SET descripcion_parametro, contenido_parametro = ? WHERE id_parametro = ?";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([
                $roles->descripcion_parametro,
                $roles->contenido_parametro,
                $roles->id_parametro,
            ]);

            if ($stmt->rowCount() > 0) {
                return ["success" => true];
            } else {
                return ["success" => false];
            }

        } catch (PDOException $e) {
            return ["success" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage()), "error" => true];
        } catch (Exception $e) {
            return ["success" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage()), "error" => true];
        }

    }

    public function eliminarParametro($id)
    {

        try {
            $query = "DELETE FROM " . $this->table . " WHERE id_parametro = ?";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([$id]);
            if ($stmt->rowCount() > 0) {
                return true; // Eliminado con éxito
            } else {
                return false; // No se eliminó (ID no existe)
            }
        } catch (PDOException $e) {
            return ["success" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage()), "error" => true];
            return false;
        }

    }
    

}
