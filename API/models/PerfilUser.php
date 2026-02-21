<?php
require_once __DIR__ .'/../config/init_config.php';
require_once '../Interfaces/IGenerarCifrado.php';
require_once '../cifrado/cifrado_AES.php';
require_once '../Services/GenerarCifradoService.php';

class PerfilUser {
    private $conn;
    private $table = "perfiles_usuarios";
    private $funcionGeneral;

    public $id_perfil_usuario, $id_usuario, $nombre_perfil, $apellido_perfil, $email_perfil, $telefono_perfil, $documento, $nit;
    public $foto_perfil, $bio_perfil;
    public $fecha_nacimiento, $happy_birthday;    
    public $token_recuperacion, $fecha_token_recuperacion;
    public $foto_usuario;
    private $rutaFotousuario;
    private $encriptado;

    private $crearFoto;
    private $tempRuta;
    private $user;
   
    public function __construct($db)
    {
        $this->conn = $db; 
        $this->funcionGeneral = new FuncionesGenerales();       
        $this->tempRuta= "imagenes/fotos_perfiles/";
        $this->encriptado = new GenerarCifradoService(new CifradoAES());        
    }

    public function __sleep() {
        // Retorna solo las propiedades que quieres serializar
        return [
            "id_perfil_usuario", "id_usuario", "nombre_perfil", "apellido_perfil", "email_perfil",
            "foto_perfil", "bio_perfil", "fecha_nacimiento", "happy_birthday", "token_recuperacion",
            "foto_usuario"
        ];
    }

    public function obtenerTodos()
    {

        try{
           
            $query = "SELECT pu.id_perfil_usuario, documento, nombre_perfil, apellido_perfil, email_perfil, telefono_perfil, foto_perfil, nit, bio_perfil, fecha_nacimiento, 
            pu.id_estado_perfil, epu.nombre_estado, ru.nombre_rol 
            FROM " . $this->table ." pu INNER JOIN estados_perfil_usuario epu ON pu.id_estado_perfil = epu.id_estado_perfil 
            LEFT JOIN usuarios u ON u.id_perfil_usuario = pu.id_perfil_usuario
            LEFT JOIN roles_usuarios ru ON ru.id_rol = u.id_rol";   
                   
            $stmt  = $this->conn->query($query);
            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
            $protocolo = trim($protocolo);
            $host = trim($_SERVER['HTTP_HOST']);
            $carpetaBase = trim(dirname(dirname($_SERVER['REQUEST_URI'])));
            $codigoBarrasURL = $protocolo . $host . $carpetaBase . '/';
            $codigoBarrasURL = trim($codigoBarrasURL);
           
            foreach ($datos as &$fila) {
                $idusuario = $fila['id_perfil_usuario'];
                $rol =  $fila['nombre_rol']; //"ADMINISTRADOR";

                $fila['foto_usuario'] = $codigoBarrasURL . str_replace(' ', '', $fila['foto_usuario']);

                if(strtoupper($rol) == "ADMINISTRADOR"){
                    $fila['botones'] = '
                        <div class="text-center">
                            <div class="btn-group">
                                <button data-id="' . $idusuario . '" class="btn btn-primary btn-sm btnEditar">
                                    <i class="material-icons"></i> Editar
                                </button>
                                <button data-id="' . $idusuario . '" class="btn btn-danger btn-sm btnEliminar">
                                    <i class="material-icons"></i> Eliminar
                                </button>
                            </div>
                        </div>
                    ';
                }else{
                    $fila['botones'] = '<span class="badge bg-secondary">Sin Rol</span>';
                }
            
            }

            // $filtrados = array_map(function($row) {
            // // Solo selecciona las columnas que quieres
            //     return [
            //         'columna1' => $row['columna1'],
            //         'columna2' => $row['columna2'],
            //         'columna3' => $row['columna3'],
            //     ];
            // }, $datos);
            return ["success" =>true, "error" =>false, "datos" => $datos];

            
        }catch (PDOException $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
        
        
    }

//     public function obtenerIdPerfilUsuario($id, $tipo)
//     {
//         try{
//             sleep(1);
//             if($tipo==true){
//                 $query = "SELECT * FROM " . $this->table." WHERE id_usuario=?";
//                 $stmt  = $this->conn->prepare($query);
//                 $stmt->execute([$id]);
//             }else{
//                 $query = "SELECT * FROM " . $this->table." WHERE id_perfil_usuario = ?";
//                 $stmt  = $this->conn->prepare($query);
//                 $stmt->execute([$id]);
//             }
            
//             $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);            
//             return $datos;
            
//         }catch (PDOException $e) {
//             echo $e->getMessage();
//             return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
//         } catch (Exception $e) {
//             echo $e->getMessage();
//             return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
//         }
//     }

//     public function obtenerPorIdPersona($id)
//     {
//         try{
//              // $query = "SELECT * FROM " . $this->table . " WHERE id_usuario= ? OR id_persona =?";
//             $query = "SELECT u.*, CONCAT(p.nombre_personal, ' ', p.apellido_personal) AS nombres, p.documento_personal FROM " . $this->table;
//             $query .= " u INNER JOIN personal p ON u.id_persona = p.id_persona WHERE u.id_persona= ?";
//             $stmt  = $this->conn->prepare($query);
//             $stmt->execute([$id]);
//             $datos= $stmt->fetchAll(PDO::FETCH_ASSOC);

//             foreach ($datos as &$fila){
//                 $usuario = $fila['user_usuario'];
//                 $fila['user_usuario'] = $usuario;
//             }

//             return $datos;

//         }catch (PDOException $e) {
//             return ["success" => false,  "error" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
//         } catch (Exception $e) {
//             return ["success" => false,  "error" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
//         }

//     }

    

//     public function ActualizarTokenRecuperacion(PerfilUser $usuario){
//         try{
//             $query = "UPDATE " . $this->table . " SET  token_recuperacion=?, fecha_token_recuperacion=? WHERE id_usuario = ?";
//             $stmt  = $this->conn->prepare($query);
//             $stmt->execute([
//                 $usuario->token_recuperacion,
//                 $usuario->fecha_token_recuperacion,
//                 $usuario->id_usuario
//             ]);
//             if($stmt->rowCount() > 0){
//                 return ["success" => true, "mensaje" => "Token Actualizado"];
//             }else{
//                 return ["success" => false, "mensaje" => "No se han Actualizado los Datos"];
//             }

//         }catch (PDOException $e) {
//             return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
//         } catch (Exception $e) {
//             return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
//         }
//     }


//     public function ActualizarPasswordUser($usuario){
//         $datosActualizarPass = $this->user->CambiarPasswordUser($usuario);
//         return $datosActualizarPass;
//     }


//     public function buscarDatosGeneralesLike($id){
//         try{
//             $datosBuscados = $this->busquedaDePersonal('documento_personal', $id);
//             if(!$datosBuscados){
//                 $datosBuscados = $this->busquedaDePersonal('nombre_personal', $id);
//                 if(!$datosBuscados){
//                     $datosBuscados = $this->busquedaDePersonal('apellido_personal', $id);
//                 }else{

//                 }
//             }
//             return $datosBuscados;

//         }catch (PDOException $e) {
//             return ["success" => false,  "error" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
//         } catch (Exception $e) {
//             return ["success" => false,  "error" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
//         }
//     }

    // public function busquedaDePersonal($campo, $id)
    // {

    //     try{
    //         $query = "SELECT * FROM personal WHERE $campo LIKE ?";
    //         // $query = "SELECT u.*, p.documento_personal, p.nombre_personal, p.apellido_personal, 
    //         //          CONCAT(p.nombre_personal, ' ', p.apellido_personal) AS nombres 
    //         //   FROM " . $this->table . " u 
    //         //   LEFT JOIN personal p ON u.id_persona = p.id_persona 
    //         //   WHERE  $campo LIKE ?";

    //         $stmt = $this->conn->prepare($query);
    //         $searchTerm = '%' . $id . '%';
    //         $stmt->execute([$searchTerm]);
    //         $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //         $html = '';
    //         foreach ($datos as &$fila) {
    //             $idPersona = $fila['id_persona'];
    //             $documento = $fila['documento_personal']; // puede venir null si no tiene estado
    //             $nombre = $fila['nombre_personal'];
    //             $apellido = $fila['apellido_personal'];
    //             $html .= '<div class="row-data">
    //             <div class="cell" style="display:none;">'.$idPersona.'</div>
    //             <div class="cell">'.$documento.'</div>
    //             <div class="cell">'.$nombre.'</div>
    //             <div class="cell">'.$apellido.'</div>
    //             <div class="cell button">
    //                     <button class="btn btn-primary btn-sm" id="seleccionarPersona">Seleccionar</button>
    //             </div>
    //             </div>';
    //         }

    //         return $html;

    //     }catch (PDOException $e) {
    //         return ["success" => false,  "error" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
    //     } catch (Exception $e) {
    //         return ["success" => false,  "error" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
    //     }
        
    // }

//     public function validarExistenciausuario($id, $usuario, $insert)
//     {
//         try{
//             if ($insert == true) {
//                 $query = "SELECT * FROM " . $this->table . " WHERE  nombre_usuario= ? AND id_usuario_producto <> ?";
//                 $stmt = $this->conn->prepare($query);
            
//             } else {
//                 $query = "SELECT * FROM " . $this->table . " WHERE user_usuario= ? AND id_usuario = ?";
//                 $stmt = $this->conn->prepare($query);
                
//             }
//             $stmt->execute([$id, $usuario]);
//             return $stmt->fetch(PDO::FETCH_ASSOC);
//         }catch (PDOException $e) {
//             return ["success" => false,  "error" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
//         } catch (Exception $e) {
//             return ["success" => false,  "error" => false, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
//         }
        
//     }

    public function buscarDatosPerfil($campo, $id){
            try{
                $query = "SELECT * FROM ".$this->table." WHERE $campo = ?";            
                $stmt = $this->conn->prepare($query);
                $stmt->execute([$id]);
                $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $datos;
            }catch (PDOException $e) {
                return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
            } catch (Exception $e) {
                return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
            }
    }

    public function guardar(PerfilUser $perfil){

        try {
           
            $resultado = $this->buscarDatosPerfil("documento", $perfil->documento);
            if($resultado){
                if(isset($resultado)){
                    if(is_array($resultado) && array_key_exists('error', $resultado)){                      
                        if(isset($resultado['error']) && $resultado['error'] === true){
                            if(array_key_exists('mensaje', $resultado)){
                                return ["success" => false, "mensaje" => $resultado['mensaje'], "error" => true];
                            }                                                
                        }                        
                    }                    
                }
                return ["success" => false, "mensaje" => "El Documento ya se Encuentra Registrado", "error" => true];
            }

            $resultado = $this->buscarDatosPerfil("email_perfil", $perfil->email_perfil);
            if($resultado){
                if(isset($resultado)){
                    if(is_array($resultado) && array_key_exists('error', $resultado)){                      
                        if(isset($resultado['error']) && $resultado['error'] === true){
                            if(array_key_exists('mensaje', $resultado)){
                                return ["success" => false, "mensaje" => $resultado['mensaje'], "error" => true];
                            }                                                
                        }                        
                    }                    
                }
                return ["success" => false, "mensaje" => "El Correo ya se Encuentra Registrado", "error" => true];
            }

            $perfil->happy_birthday = date("m-d", strtotime($perfil->fecha_nacimiento));
            return $this->CrearPerfil($perfil);
           

        } catch (PDOException $e) {
           return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }

    }

    public function CrearPerfil(PerfilUser $perfil)
    {

        try {

            $token_recuperacion =  $this->encriptado->cifrar($this->funcionGeneral->obtenerTokenSeguro());
            if(empty($perfil->nit)){
                $perfil->nit = "C/F";
            }
        
            $perfil->rutaFotousuario = $this->tempRuta . trim($perfil->documento).".png";
            $query = "INSERT INTO " . $this->table . " (documento, nombre_perfil, apellido_perfil, email_perfil, telefono_perfil, foto_perfil, nit, bio_perfil, fecha_nacimiento, happy_birthday, token_recuperacion) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([                
                strtoupper($perfil->documento),
                strtoupper($perfil->nombre_perfil),
                strtoupper($perfil->apellido_perfil),               
                $perfil->email_perfil,
                $perfil->telefono_perfil,
                $perfil->foto_perfil,
                $perfil->nit,
                $perfil->bio_perfil,
                $perfil->fecha_nacimiento,
                $perfil->happy_birthday,
                $token_recuperacion
               
            ]);

            if ($stmt->rowCount() > 0) {
                // $this->funcionGeneral->crearFotos($this->tempRuta, $perfil->foto_perfil, $perfil->nombre_perfil);
                return ["success" => true];
            } else {
                return ["success" => false];
            }

        } catch (PDOException $e) {
            $rutaImagen = __DIR__ . "/../" .$this->tempRuta.$perfil->documento.".png";
            // Validar si el archivo existe antes de borrarlo
            if (file_exists($rutaImagen)) {
                unlink($rutaImagen);
            } 
           return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            // $rutaImagen = __DIR__ . "/../" .$this->tempRuta.$perfil->nombre_usuario.".png";
            // Validar si el archivo existe antes de borrarlo
            // if (file_exists($rutaImagen)) {
            //     unlink($rutaImagen);
            // } 
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }

    }

    public function modificar(PerfilUser $perfil){

        try {
           
            $resultado = $this->buscarDatosPerfil("documento", $perfil->documento);
            if(!$resultado){
                return ["success" => false, "mensaje" => "El Documento no se Encuentra Registrado", "error" => true];
            }
            if($resultado){
                if(isset($resultado)){
                    if(is_array($resultado) && array_key_exists('error', $resultado)){                      
                        if(isset($resultado['error']) && $resultado['error'] === true){
                            if(array_key_exists('mensaje', $resultado)){
                                return ["success" => false, "mensaje" => $resultado['mensaje'], "error" => true];
                            }                                                
                        }                        
                    }                    
                }                
            }

            $resultado = $this->validarExistenciaPerfil($perfil, true);
            if(!$resultado){
                return ["success" => false, "mensaje" => "El ID no se Encuentra Registrado", "error" => true];
            }
            if($resultado){
                if(isset($resultado)){
                    if(is_array($resultado) && array_key_exists('error', $resultado)){                      
                        if(isset($resultado['error']) && $resultado['error'] === true){
                            if(array_key_exists('mensaje', $resultado)){
                                return ["success" => false, "mensaje" => $resultado['mensaje'], "error" => true];
                            }                                                
                        }                        
                    }                    
                }                
            }

            $resultado = $this->validarExistenciaPerfil($perfil, false);
            if($resultado){
                return ["success" => false, "mensaje" => "El Correo ya se Encuentra Registrado", "error" => true];
            }
            if($resultado){
                if(isset($resultado)){
                    if(is_array($resultado) && array_key_exists('error', $resultado)){                      
                        if(isset($resultado['error']) && $resultado['error'] === true){
                            if(array_key_exists('mensaje', $resultado)){
                                return ["success" => false, "mensaje" => $resultado['mensaje'], "error" => true];
                            }                                                
                        }                        
                    }                    
                }                
            }           

            $perfil->happy_birthday = date("m-d", strtotime($perfil->fecha_nacimiento));
            return $this->actualizarPerfil($perfil);
           

        } catch (PDOException $e) {
           return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }

    }

    public function actualizarPerfil(PerfilUser $perfil)
    {

        try {
           
            // $perfil->rutaFotousuario = $this->tempRuta . trim($perfil->nombre_usuario).".png";
            $query = "UPDATE " . $this->table . " SET nombre_perfil = ?, apellido_perfil = ?, email_perfil = ?, nit=?, telefono_perfil=?, fecha_nacimiento=?, happy_birthday=? WHERE id_perfil_usuario = ?";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([
                strtoupper($perfil->nombre_perfil),
                strtoupper($perfil->apellido_perfil),
                $perfil->email_perfil,
                $perfil->nit,
                $perfil->telefono_perfil,
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

    public function validarExistenciaPerfil(PerfilUser $dato, $tipo)
    {
        try{
            if($tipo == true){
                $query = "SELECT * FROM " . $this->table . " WHERE id_perfil_usuario = ?";
                $stmt  = $this->conn->prepare($query);
                $stmt->execute([$dato->id_perfil_usuario]);
            }else{
                $query = "SELECT * FROM " . $this->table . " WHERE id_perfil_usuario <> ? AND email_perfil = ?";
                $stmt  = $this->conn->prepare($query);
                $stmt->execute([$dato->id_perfil_usuario, $dato->email_perfil]);
            }
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }catch (PDOException $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
    }


    public function eliminar($id)
    {

        try {

            $resultado = $this->buscarDatosPerfil("id_perfil_usuario", $id);
            if(!$resultado){
                return ["success" => false, "mensaje" => "El Perfil de Usuario no se Encuentra Registrado", "error" => true];
            }
            if($resultado){
                if(isset($resultado)){
                    if(is_array($resultado) && array_key_exists('error', $resultado)){                      
                        if(isset($resultado['error']) && $resultado['error'] === true){
                            if(array_key_exists('mensaje', $resultado)){
                                return ["success" => false, "mensaje" => $resultado['mensaje'], "error" => true];
                            }                                                
                        }                        
                    }                    
                }                
            }
   
            $query = "DELETE FROM " . $this->table . " WHERE id_perfil_usuario = ?";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([$id]);

            // Verifica si se eliminó al menos una fila
            if ($stmt->rowCount() > 0) {
                if($resultado){
                    // $nombreImg =  str_replace(' ', '', $resultado[0]['nombre_usuario']);
                    // $rutaImagen = __DIR__ . "/../" .$this->tempRuta.trim($nombreImg).".png";
                }
                // if (file_exists($rutaImagen)) {
                //     unlink($rutaImagen);
                // } 
                return ["success" => true, "actualizado" => true];
            } else {
                return ["success" => true, "actualizado" => false];
            }
        }catch (PDOException $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }

    }

    // public function BuscarDatos($campo, $id)
    // {

    //     try{
    //         $query = "SELECT * FROM ".$this->table." WHERE $campo = ?";
            
    //         $stmt = $this->conn->prepare($query);
    //         $searchTerm = '%' . $id . '%';
    //         $stmt->execute([$searchTerm]);
    //         $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //         // $html = '';
    //         // foreach ($datos as &$fila) {
    //         //     $idPersona = $fila['id_persona'];
    //         //     $documento = $fila['documento_personal']; // puede venir null si no tiene estado
    //         //     $nombre = $fila['nombre_personal'];
    //         //     $apellido = $fila['apellido_personal'];
    //         //     $html .= '<div class="row-data">
    //         //     <div class="cell" style="display:none;">'.$idPersona.'</div>
    //         //     <div class="cell">'.$documento.'</div>
    //         //     <div class="cell">'.$nombre.'</div>
    //         //     <div class="cell">'.$apellido.'</div>
    //         //     <div class="cell button">
    //         //             <button class="btn btn-primary btn-sm" id="seleccionarPersona">Seleccionar</button>
    //         //     </div>
    //         //     </div>';
    //         // }

    //         return $datos;

    //     }catch (PDOException $e) {
    //         return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
    //     } catch (Exception $e) {
    //         return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
    //     }
        
    // }

//     public function esSesionValida($token) {
//         try{
//             $stmt = $this->conn->prepare("SELECT 1 FROM historial_login WHERE token_sesion = ? AND id_estado_sesion = 1");
//             $stmt->execute([$token]);
//             return $stmt->fetchColumn() ? true : false;
//         }catch (PDOException $e) {
//             return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
//         } catch (Exception $e) {
//             return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
//         }
        
//     }

    



}
