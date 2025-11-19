<?php

require_once '../models/Personal.php';
date_default_timezone_set('America/Guatemala');

class VideosView
{
    private $conn;
    private $table = "secciones_producto";
    private $funcionGeneral;

    public $id_seccion_producto, $nombre_seccion, $descripcion_seccion; 
    public $foto_seccion;
    private $rutaFotoSeccion;

    private $crearFoto;
    private $tempRuta;
   
    public function __construct($db)
    {
        $this->conn = $db; 
        $this->funcionGeneral = new FuncionesGenerales();   
        $this->tempRuta= "imagenes/fotos_secciones/";
        $this->crearFoto = new FuncionesGenerales();    
    }

    public function obtenerTodos()
    {

        try{
            $query = "SELECT * FROM " . $this->table;
            $stmt  = $this->conn->query($query);
            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
            $protocolo = trim($protocolo);
            $host = trim($_SERVER['HTTP_HOST']);
            $carpetaBase = trim(dirname(dirname($_SERVER['REQUEST_URI'])));
            $codigoBarrasURL = $protocolo . $host . $carpetaBase . '/';
            $codigoBarrasURL = trim($codigoBarrasURL);

            foreach ($datos as &$fila) {
                $idSeccion = $fila['id_seccion_producto'];
                $rol = "ADMINISTRADOR";

                
                $fila['foto_seccion'] = $codigoBarrasURL . str_replace(' ', '', $fila['foto_seccion']);

                if(strtoupper($rol) == "ADMINISTRADOR"){
                    $fila['botones'] = '
                        <div class="text-center">
                            <div class="btn-group">
                                <button data-id="' . $idSeccion . '" class="btn btn-primary btn-sm btnEditar">
                                    <i class="material-icons"></i> Editar
                                </button>
                                <button data-id="' . $idSeccion . '" class="btn btn-danger btn-sm btnEliminar">
                                    <i class="material-icons"></i> Eliminar
                                </button>
                            </div>
                        </div>
                    ';
                }else{
                    $fila['botones'] = '<span class="badge bg-secondary">Sin Rol</span>';
                }
            
            }
            return $datos;

            
        }catch (PDOException $e) {
            return ["success" => false,  "error" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode())];
        } catch (Exception $e) {
            return ["success" => false,  "error" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode())];
        }
        
        
    }

    public function obtenerPorId($id)
    {
        try{
           
            $query = "SELECT * FROM " . $this->table." WHERE id_seccion_producto = ?";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([$id]);
            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);           
            return $datos;
            
        }catch (PDOException $e) {
            echo $e->getMessage();
            return ["success" => false,  "error" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode())];
        } catch (Exception $e) {
            echo $e->getMessage();
            return ["success" => false,  "error" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode())];
        }
    }

    public function obtenerPorIdPersona($id)
    {
        try{
             // $query = "SELECT * FROM " . $this->table . " WHERE id_usuario= ? OR id_persona =?";
            $query = "SELECT u.*, CONCAT(p.nombre_personal, ' ', p.apellido_personal) AS nombres, p.documento_personal FROM " . $this->table;
            $query .= " u INNER JOIN personal p ON u.id_persona = p.id_persona WHERE u.id_persona= ?";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([$id]);
            $datos= $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($datos as &$fila){
                $usuario = $fila['user_usuario'];
                $fila['user_usuario'] = $usuario;
            }

            return $datos;

        }catch (PDOException $e) {
            return ["success" => false,  "error" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode())];
        } catch (Exception $e) {
            return ["success" => false,  "error" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode())];
        }

    }

    public function obtenerPorNombre($id)
    {
        try{
            $query = "SELECT * FROM " . $this->table . " WHERE nombre_seccion= ?";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }catch (PDOException $e) {
            return ["success" => false,  "error" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode()) ];
        } catch (Exception $e) {
            return ["success" => false,  "error" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode())];
        }
    }

    public function buscarDatosGeneralesLike($id){
        try{
            $datosBuscados = $this->busquedaDePersonal('documento_personal', $id);
            if(!$datosBuscados){
                $datosBuscados = $this->busquedaDePersonal('nombre_personal', $id);
                if(!$datosBuscados){
                    $datosBuscados = $this->busquedaDePersonal('apellido_personal', $id);
                }else{

                }
            }
            return $datosBuscados;

        }catch (PDOException $e) {
            return ["success" => false,  "error" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode())];
        } catch (Exception $e) {
            return ["success" => false,  "error" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode())];
        }
    }

    public function busquedaDePersonal($campo, $id)
    {

        try{
            $query = "SELECT * FROM personal WHERE $campo LIKE ?";
            // $query = "SELECT u.*, p.documento_personal, p.nombre_personal, p.apellido_personal, 
            //          CONCAT(p.nombre_personal, ' ', p.apellido_personal) AS nombres 
            //   FROM " . $this->table . " u 
            //   LEFT JOIN personal p ON u.id_persona = p.id_persona 
            //   WHERE  $campo LIKE ?";

            $stmt = $this->conn->prepare($query);
            $searchTerm = '%' . $id . '%';
            $stmt->execute([$searchTerm]);
            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $html = '';
            foreach ($datos as &$fila) {
                $idPersona = $fila['id_persona'];
                $documento = $fila['documento_personal']; // puede venir null si no tiene estado
                $nombre = $fila['nombre_personal'];
                $apellido = $fila['apellido_personal'];
                $html .= '<div class="row-data">
                <div class="cell" style="display:none;">'.$idPersona.'</div>
                <div class="cell">'.$documento.'</div>
                <div class="cell">'.$nombre.'</div>
                <div class="cell">'.$apellido.'</div>
                <div class="cell button">
                        <button class="btn btn-primary btn-sm" id="seleccionarPersona">Seleccionar</button>
                </div>
                </div>';
            }

            return $html;

        }catch (PDOException $e) {
            return ["success" => false,  "error" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode())];
        } catch (Exception $e) {
            return ["success" => false,  "error" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode())];
        }
        
    }

    public function validarExistenciaSeccion($id, $seccion, $insert)
    {
        try{
            if ($insert == true) {
                $query = "SELECT * FROM " . $this->table . " WHERE  nombre_seccion= ? AND id_seccion_producto <> ?";
                $stmt = $this->conn->prepare($query);
            
            } else {
                $query = "SELECT * FROM " . $this->table . " WHERE user_usuario= ? AND id_usuario = ?";
                $stmt = $this->conn->prepare($query);
                
            }
            $stmt->execute([$id, $seccion]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }catch (PDOException $e) {
            return ["success" => false,  "error" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode())];
        } catch (Exception $e) {
            return ["success" => false,  "error" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode())];
        }
        
    }

    public function crearSeccion(Secciones $seccion)
    {

        try {

            $resultado = $this->obtenerPorNombre($seccion->nombre_seccion); 
            // var_dump($resultado);         
            if ($resultado) {
                if(isset($resultado)){
                    if(!is_array($resultado)){
                        if($resultado['success'] == false){
                            return ["success" => false, "mensaje" => $resultado['mensaje'], "error" => "Ocurrio una Excepcion al Obtener los Datos "];
                        }
                    }
                    
                }
                return ["success" => false, "duplicado" => true];
            }

            $seccion->rutaFotoSeccion = $this->tempRuta . trim($seccion->nombre_seccion).".png";
            $query = "INSERT INTO " . $this->table . " (nombre_seccion, descripcion_seccion, foto_seccion) VALUES (?, ?, ?)";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([                
                strtoupper($seccion->nombre_seccion),               
                $seccion->descripcion_seccion,
                $seccion->rutaFotoSeccion,
            ]);

            if ($stmt->rowCount() > 0) {
                $this->crearFoto->crearFotos($this->tempRuta, $seccion->foto_seccion, $seccion->nombre_seccion);
                return ["success" => true];
            } else {
                return ["success" => false];
            }

        } catch (PDOException $e) {
            $rutaImagen = __DIR__ . "/../" .$this->tempRuta.$seccion->nombre_seccion.".png";
            // Validar si el archivo existe antes de borrarlo
            if (file_exists($rutaImagen)) {
                unlink($rutaImagen);
            } 
            return ["success" => false, "error" => "true", "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode())];
        } catch (Exception $e) {
            $rutaImagen = __DIR__ . "/../" .$this->tempRuta.$seccion->nombre_seccion.".png";
            // Validar si el archivo existe antes de borrarlo
            if (file_exists($rutaImagen)) {
                unlink($rutaImagen);
            } 
            return ["success" => false, "error" => "true", "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode())];
        }

    }

    public function actualizarSeccion(Secciones $seccion)
    {

        try {

            $resultado = $this->obtenerPorId($seccion->id_seccion_producto);
            
            if (!$resultado) {
                // Ya existe
                return ["success" => false, "existe" => false];
            }

            $resultado = $this->validarExistenciaSeccion($seccion->id_seccion_producto, $seccion->nombre_seccion, true);           
            if ($resultado) {
                // Ya existe
                return ["success" => false, "duplicado" => true];
            }

            $seccion->rutaFotoSeccion = $this->tempRuta . trim($seccion->nombre_seccion).".png";
            $query = "UPDATE " . $this->table . " SET nombre_seccion = ?, descripcion_seccion = ? WHERE id_seccion_producto = ?";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([
                strtoupper($seccion->nombre_seccion),
                $seccion->descripcion_seccion,
                $seccion->id_seccion_producto,
            ]);

            $actualizadoFoto = $this->crearFoto->crearFotos($this->tempRuta, $seccion->foto_seccion, $seccion->nombre_seccion);
            if ($stmt->rowCount() > 0) {
                return ["success" => true, "actualizado" => true];
            } else {
                if($actualizadoFoto){
                    return ["success" => true, "actualizado" => true];
                }else{
                    return ["success" => true, "actualizado" => false];
                }
            }

        } catch (PDOException $e) {
            return ["success" => false,  "error" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode())];
        } catch (Exception $e) {
            return ["success" => false,  "error" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode())];
        }

    }

    public function eliminarSeccion($id)
    {

        try {

            $resultado = $this->obtenerPorId($id);

            $query = "DELETE FROM " . $this->table . " WHERE id_seccion_producto = ?";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([$id]);

            // Verifica si se eliminó al menos una fila
            if ($stmt->rowCount() > 0) {
                if($resultado){
                    $nombreImg =  str_replace(' ', '', $resultado[0]['nombre_seccion']);
                    $rutaImagen = __DIR__ . "/../" .$this->tempRuta.trim($nombreImg).".png";
                }
                if (file_exists($rutaImagen)) {
                    unlink($rutaImagen);
                } 
                return true; // Eliminado con éxito
            } else {
                return false; // No se eliminó (ID no existe)
            }
        }catch (PDOException $e) {
            return ["success" => false,  "error" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode())];
        } catch (Exception $e) {
            return ["success" => false,  "error" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode())];
        }

    }


    public function esSesionValida($token) {
        try{
            $stmt = $this->conn->prepare("SELECT 1 FROM historial_login WHERE token_sesion = ? AND id_estado_sesion = 1");
            $stmt->execute([$token]);
            return $stmt->fetchColumn() ? true : false;
        }catch (PDOException $e) {
            return ["success" => false,  "error" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode())];
        } catch (Exception $e) {
            return ["success" => false,  "error" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode())];
        }
        
    }

    



}
