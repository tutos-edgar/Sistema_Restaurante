<?php
require_once __DIR__ .'/../config/init_config.php';
require_once '../models/UsuariosYoutube.php';

class PerfilUser extends UsuariosYoutube 
{
    private $conn;
    private $table = "perfiles_usuarios";
    private $funcionGeneral;

    public $id_perfil_usuario, $id_usuario, $nombre_perfil, $apellido_perfil, $email_perfil;
    public $foto_perfil, $bio_perfil;
    public $fecha_nacimiento, $happy_birthday;    
    public $token_recuperacion, $fecha_token_recuperacion;
    public $foto_usuario;
    private $rutaFotousuario;

    private $crearFoto;
    private $tempRuta;
    private $user;
   
    public function __construct($db)
    {
        $this->conn = $db; 
        $this->funcionGeneral = new FuncionesGenerales();
        $this->user = new UsuariosYoutube($db);   
        $this->tempRuta= "imagenes/fotos_perfiles/";
        
    }

    public function __sleep() {
        // Retorna solo las propiedades que quieres serializar
        return [
            "id_prefil_usuario", "id_usuario", "nombre_perfil", "apellido_perfil", "email_perfil",
            "foto_perfil", "bio_perfil", "fecha_nacimiento", "happy_birthday", "token_recuperacion",
            "foto_usuario"
        ];
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
           
            // foreach ($datos as &$fila) {
            //     $idusuario = $fila['id_usuario_producto'];
            //     $rol = "ADMINISTRADOR";

            //     $fila['foto_usuario'] = $codigoBarrasURL . str_replace(' ', '', $fila['foto_usuario']);

            //     if(strtoupper($rol) == "ADMINISTRADOR"){
            //         $fila['botones'] = '
            //             <div class="text-center">
            //                 <div class="btn-group">
            //                     <button data-id="' . $idusuario . '" class="btn btn-primary btn-sm btnEditar">
            //                         <i class="material-icons"></i> Editar
            //                     </button>
            //                     <button data-id="' . $idusuario . '" class="btn btn-danger btn-sm btnEliminar">
            //                         <i class="material-icons"></i> Eliminar
            //                     </button>
            //                 </div>
            //             </div>
            //         ';
            //     }else{
            //         $fila['botones'] = '<span class="badge bg-secondary">Sin Rol</span>';
            //     }
            
            // }
            return $datos;

            
        }catch (PDOException $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
        
        
    }

    public function obtenerIdPerfilUsuario($id, $tipo)
    {
        try{
            sleep(1);
            if($tipo==true){
                $query = "SELECT * FROM " . $this->table." WHERE id_usuario=?";
                $stmt  = $this->conn->prepare($query);
                $stmt->execute([$id]);
            }else{
                $query = "SELECT * FROM " . $this->table." WHERE id_perfil_usuario = ?";
                $stmt  = $this->conn->prepare($query);
                $stmt->execute([$id]);
            }
            
            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);            
            return $datos;
            
        }catch (PDOException $e) {
            echo $e->getMessage();
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            echo $e->getMessage();
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
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
            return ["success" => false,  "error" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            return ["success" => false,  "error" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }

    }

    public function ObtenerIdUsuario(PerfilUser $dato, $tipo)
    {
        try{
            if($tipo == true){
                $query = "SELECT * FROM " . $this->table . " WHERE id_usuario= ?";
                $stmt  = $this->conn->prepare($query);
                $stmt->execute([$dato->id_usuario]);
            }else{
                $query = "SELECT * FROM " . $this->table . " WHERE id_usuario <> ? AND email_perfil = ?";
                $stmt  = $this->conn->prepare($query);
                $stmt->execute([$dato->id_usuario, $dato->email_perfil]);
            }
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }catch (PDOException $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
    }

    public function ActualizarTokenRecuperacion(PerfilUser $usuario){
        try{
            $query = "UPDATE " . $this->table . " SET  token_recuperacion=?, fecha_token_recuperacion=? WHERE id_usuario = ?";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([
                $usuario->token_recuperacion,
                $usuario->fecha_token_recuperacion,
                $usuario->id_usuario
            ]);
            if($stmt->rowCount() > 0){
                return ["success" => true, "mensaje" => "Token Actualizado"];
            }else{
                return ["success" => false, "mensaje" => "No se han Actualizado los Datos"];
            }

        }catch (PDOException $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
    }


    public function ActualizarPasswordUser($usuario){
        $datosActualizarPass = $this->user->CambiarPasswordUser($usuario);
        return $datosActualizarPass;
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
            return ["success" => false,  "error" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            return ["success" => false,  "error" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
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
            return ["success" => false,  "error" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            return ["success" => false,  "error" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
        
    }

    public function validarExistenciausuario($id, $usuario, $insert)
    {
        try{
            if ($insert == true) {
                $query = "SELECT * FROM " . $this->table . " WHERE  nombre_usuario= ? AND id_usuario_producto <> ?";
                $stmt = $this->conn->prepare($query);
            
            } else {
                $query = "SELECT * FROM " . $this->table . " WHERE user_usuario= ? AND id_usuario = ?";
                $stmt = $this->conn->prepare($query);
                
            }
            $stmt->execute([$id, $usuario]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }catch (PDOException $e) {
            return ["success" => false,  "error" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            return ["success" => false,  "error" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
        
    }

    public function IngresarDatosPerfil(PerfilUser $perfil)
    {

        try {

            $resultado = $this->ObtenerIdUsuario($perfil, true);
            $perfil->happy_birthday = date("m-d", strtotime($perfil->fecha_nacimiento));
            if ($resultado) {
                if(isset($resultado)){
                    if(is_array($resultado) && array_key_exists('error', $resultado)){                      
                        if(isset($resultado['error']) && $resultado['error'] === true){
                            return ["success" => false, "mensaje" => $resultado['mensaje'], "error" => true];                    
                        }                        
                    }                    
                }
                
                $resultado = $this->ObtenerIdUsuario($perfil, true);
                if (!$resultado) {                    
                    return ["success" => false, "existe" => false];
                }

                $resultado = $this->ObtenerIdUsuario($perfil, false);
                
                if ($resultado) {    
                    if(is_array($resultado) && array_key_exists('error', $resultado)){                      
                        if(isset($resultado['error']) && $resultado['error'] === true){
                            return ["success" => false, "mensaje" => $resultado['mensaje'], "error" => true];                    
                        }                        
                    }                
                    return ["success" => false, "correo" => true];
                }
                
                return $this->ActualizarPerfil($perfil);
            }else{                
                $resultado = $this->ObtenerIdUsuario($perfil, true);
                if ($resultado) {                    
                    return ["success" => false, "existe" => false];
                }
                return $this->CrearPerfil($perfil);
            }

        } catch (PDOException $e) {
           return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }

    }

    public function CrearPerfil(PerfilUser $perfil)
    {

        try {

            // $resultado = $this->ObtenerAliasUsuario($perfil->alias_usuario);
                    
            // if ($resultado) {
            //     if(isset($resultado)){
            //         if(!is_array($resultado)){
            //             if($resultado['success'] == false){
            //                 return ["success" => false, "mensaje" => $resultado['mensaje'], "error" => "Ocurrio una Excepcion al Obtener los Datos "];
            //             }
            //         }
                    
            //     }
            //     return ["success" => false, "duplicado" => true];
            // }

            // $perfil->rutaFotousuario = $this->tempRuta . trim($perfil->nombre_perfil).".png";
            $query = "INSERT INTO " . $this->table . " (nombre_perfil, apellido_perfil, email_perfil, foto_perfil, bio_perfil, fecha_nacimiento, happy_birthday) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([                
                strtoupper($perfil->nombre_usuario),
                strtoupper($perfil->apellido_usuario),               
                $perfil->email_usuario,
                $perfil->foto_perfil,
                $perfil->bio_perfil,
                $perfil->fecha_nacimiento,
                $perfil->happy_birthday
               
            ]);

            if ($stmt->rowCount() > 0) {
                // $this->funcionGeneral->crearFotos($this->tempRuta, $perfil->foto_perfil, $perfil->nombre_perfil);
                return ["success" => true];
            } else {
                return ["success" => false];
            }

        } catch (PDOException $e) {
            $rutaImagen = __DIR__ . "/../" .$this->tempRuta.$perfil->nombre_perfil.".png";
            // Validar si el archivo existe antes de borrarlo
            if (file_exists($rutaImagen)) {
                unlink($rutaImagen);
            } 
           return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            $rutaImagen = __DIR__ . "/../" .$this->tempRuta.$perfil->nombre_usuario.".png";
            // Validar si el archivo existe antes de borrarlo
            if (file_exists($rutaImagen)) {
                unlink($rutaImagen);
            } 
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }

    }

    public function actualizarPerfil(PerfilUser $perfil)
    {

        try {
           
            // $perfil->rutaFotousuario = $this->tempRuta . trim($perfil->nombre_usuario).".png";
            $query = "UPDATE " . $this->table . " SET nombre_perfil = ?, apellido_perfil = ?, email_perfil = ?, fecha_nacimiento=?, happy_birthday=? WHERE id_perfil_usuario = ?";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([
                strtoupper($perfil->nombre_perfil),
                strtoupper($perfil->apellido_perfil),
                $perfil->email_perfil,
                $perfil->fecha_nacimiento,
                $perfil->happy_birthday,
                $perfil->id_perfil_usuario,
            ]);

            // $actualizadoFoto = $this->crearFoto->crearFotos($this->tempRuta, $perfil->foto_usuario, $perfil->nombre_usuario);
            if ($stmt->rowCount() > 0) {
                return ["success" => true, "actualizado" => true];
            } else {
                // if($actualizadoFoto){
                //     return ["success" => true, "actualizado" => true];
                // }else{
                //     return ["success" => true, "actualizado" => false];
                // }
                return ["success" => true, "actualizado" => false];
            }

        } catch (PDOException $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }

    }

    public function eliminarPerfil($id)
    {

        try {

            $resultado = $this->obtenerPorId($id);

            $query = "DELETE FROM " . $this->table . " WHERE id_perfil_usuario = ?";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([$id]);

            // Verifica si se eliminó al menos una fila
            if ($stmt->rowCount() > 0) {
                if($resultado){
                    $nombreImg =  str_replace(' ', '', $resultado[0]['nombre_usuario']);
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
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }

    }

    public function BuscarDatos($campo, $id)
    {

        try{
            $query = "SELECT * FROM ".$this->table." WHERE $campo = ?";
            
            $stmt = $this->conn->prepare($query);
            $searchTerm = '%' . $id . '%';
            $stmt->execute([$searchTerm]);
            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // $html = '';
            // foreach ($datos as &$fila) {
            //     $idPersona = $fila['id_persona'];
            //     $documento = $fila['documento_personal']; // puede venir null si no tiene estado
            //     $nombre = $fila['nombre_personal'];
            //     $apellido = $fila['apellido_personal'];
            //     $html .= '<div class="row-data">
            //     <div class="cell" style="display:none;">'.$idPersona.'</div>
            //     <div class="cell">'.$documento.'</div>
            //     <div class="cell">'.$nombre.'</div>
            //     <div class="cell">'.$apellido.'</div>
            //     <div class="cell button">
            //             <button class="btn btn-primary btn-sm" id="seleccionarPersona">Seleccionar</button>
            //     </div>
            //     </div>';
            // }

            return $datos;

        }catch (PDOException $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
        
    }

    public function esSesionValida($token) {
        try{
            $stmt = $this->conn->prepare("SELECT 1 FROM historial_login WHERE token_sesion = ? AND id_estado_sesion = 1");
            $stmt->execute([$token]);
            return $stmt->fetchColumn() ? true : false;
        }catch (PDOException $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
        
    }

    



}
