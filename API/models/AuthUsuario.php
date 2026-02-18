<?php 

require_once '../models/Usuarios.php';

 class AuthUsuario{


    private $parametros;
    private $conn;
    private $table;
    private $funcionesGenerales;

    public function __construct($db)
    {
        $this->conn = $db;
        $this->table = "usuarios";
        $this->funcionesGenerales = new FuncionesGenerales(); 
        $this->parametros = new Parametros($db);
       
    }

    public function ValidarLoginAcceso(Usuarios $usuario){

        try{
            
            $valorIntentos = 0;
            $intentosActuales = 0;
            $tiempoEspera = 0;
            $valoresParametro =  $this->parametros->buscarParametros(ParametrosTabla::INTENTOS_SESSION->value);
            if($valoresParametro){

                if(is_array($valoresParametro) && array_key_exists('error', $valoresParametro)){
                    if(isset($valoresParametro) && $valoresParametro['error'] == true){
                        if(isset($valoresParametro['mensaje'])){
                            return ["success" => false,  "error" => true, "mensaje" => $valoresParametro['mensaje']];
                        }else{
                            return ["success" => false,  "error" => true, "mensaje" => "No se pudo comunicar con el servidor"];
                        }
                    }
                }

               $valorIntentos = $valoresParametro['valor_parametro'];
               
            }else{
                $valorIntentos = INTENTOSLOGIN;
            }

            $valoresParametro =  $this->parametros->buscarParametros(ParametrosTabla::TIEMPO_ESPERA_BLOQUEO_SESSION->value);
            if($valoresParametro){
                if(is_array($valoresParametro) && array_key_exists('error', $valoresParametro)){
                    if(isset($valoresParametro) && $valoresParametro['error'] == true){
                        if(isset($valoresParametro['mensaje'])){
                            return ["success" => false,  "error" => true, "mensaje" => $valoresParametro['mensaje']];
                        }else{
                            return ["success" => false,  "error" => true, "mensaje" => "No se pudo comunicar con el servidor"];
                        }
                    }
                }                

               $tiempoEspera = $valoresParametro['valor_parametro'];
               
            }else{
                $tiempoEspera = TIEMPOESPERABLOQUEOSESION;
            }
            
            // $query = "SELECT * FROM usuarios WHERE alias = ?";
            // $stmt  = $this->conn->prepare($query);
            // $stmt->execute([$usuario->alias_usuario]);

            $query = "SELECT *, COALESCE(u.id_usuario, pu.id_usuario) AS id_usuario_final FROM " . $this->table." u LEFT JOIN perfiles_usuarios pu ON u.id_usuario=pu.id_usuario WHERE alias = ?";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([$usuario->alias_usuario]);
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);
           
            if($datos){
               
                if($datos["es_activo"]=== false){
                    return ["success" => false,  "error" => false, "mensaje" => "El Usuario no esta Activo"];
                }              

                if(($datos['estado_usuario'] != EstadoUsuario::ACTIVO->value && $datos['estado_usuario'] != EstadoUsuario::BLOQUEADO_X_INTENTOS->value) || empty($datos['estado_usuario'])){
                    return ["success" => "false", "error" => false, "mensaje" => ObtenerEstadoUsuario($datos['estado_usuario'])];
                }
                
                $intentosActuales = $datos['intento_login'];
                
                $fechaEstadoUsuario = $datos['fecha_cambio_estado'];
                
                if(!empty($fechaEstadoUsuario)){
                    if($datos['estado_usuario'] == EstadoUsuario::BLOQUEADO_X_INTENTOS->value){
                        $tiempoAnterior = new DateTime($fechaEstadoUsuario);
                        $horaActual = new DateTime();
                        $diffMinutes = $horaActual->getTimestamp() - $tiempoAnterior->getTimestamp();
                        $diffMinutes = $diffMinutes / 60; // pasa de segundos a minutos
                        if($intentosActuales >= $valorIntentos){
                            if ($diffMinutes < $tiempoEspera) {
                                return ["success" => "false", "error" => false, "mensaje" => "Usted esta Bloqueado Temporalmente espere ".$tiempoEspera." Minutos"];
                            }else{
                                if($datos['estado_usuario'] == EstadoUsuario::BLOQUEADO_X_INTENTOS->value){
                                    $this->ActualizarEstadoUsuario(EstadoUsuario::ACTIVO->value, $$datos['id_usuario_final']);
                                 }
                                $this->actualizarIntentosLogin("0", $$datos[0]['id_usuario_final']);
                                $valorIntentos = 0;
                            }
                        }   
                       
                    }

                }
                sleep(1);
                
                if(password_verify($usuario->pass_usuario, $datos["pass_usuario"])){
                   if($intentosActuales >= $valorIntentos){
                        return ["success" => "false", "error" => false, "mensaje" => "Ha llegado al Maximo de Itentos Fallido"];
                    }
                   
                    $this->actualizarIntentosLogin("0", $datos['id_usuario_final']);

                    // Actualiza el objeto usuario con los datos obtenidos de la base de datos            
                    //$tokenGenerado = $this->actualizarTokenLogin($usuario->token_sesion,$datos['id_usuario_final']);
                    
                    foreach ($datos as $key => $value) {
                        // Solo asigna si la propiedad existe en el objeto
                        if (property_exists($usuario, $key)) {
                            $usuario->$key = $value;
                        }
                    }

                    // $usuarioToken = new UsuariosYoutube($datos);
                    $tokenGenerado = $this->CrearTokenUsuario($usuario);                    
                    if($tokenGenerado){
                        if(is_array($tokenGenerado) && array_key_exists('error', $tokenGenerado)){
                            if(isset($resultado['error']) && $resultado['error'] === true){
                                return ["success" => false, "mensaje" => $resultado['mensaje'], "error" => true];                    
                            }
                        }

                        // $this->crearHistorialLogin($datos);
                       
                        // session_start(); 
                        
                        $_SESSION['UsuarioValido'] = [
                        "id_usuario" => $usuario->id_usuario,
                        "id_rol"     => $usuario->id_rol,
                        "nombre"     => $usuario->nombre_usuario,
                        "apellido"     => $usuario->apellido_usuario,
                        "alias"     => $usuario->alias_usuario
                        ];
                        $_SESSION['EstadoSesion'] = true;

                        $envio["success"] = true;
                        $envio["mensaje"] = "Validación Correcta";
                        if($datos['id_rol'] == RolesUsuarios::USUARIO->value){
                            $_SESSION['Rol'] = RolesUsuarios::USUARIO->value;                             
                            $envio ["urlPrincipal"]= "admin_user/";
                        }else if($datos['id_rol'] == RolesUsuarios::ADMINISTRADOR->value){
                            $_SESSION['Rol'] = RolesUsuarios::ADMINISTRADOR->value;
                            $envio ["urlPrincipal"]= "admin_dashboard/";
                        }

                        return $envio;
                       
                    }else{
                        return ["success" => "false", "error" => false, "mensaje" => "No se pudo Generar el Token"];
                    }


                }else{
                    if($intentosActuales >= $valorIntentos){
                        return ["success" => "false", "error" => false, "mensaje" => "Ha llegado al Maximo de Itentos Fallido"];
                    }

                    $intentosActuales = $intentosActuales + 1;
                    if(!empty($this->actualizarIntentosLogin($intentosActuales, $datos['id_usuario_final']))){
                        return ["success" => "false", "error" => false, "mensaje" => "Datos Credenciales Invalidos"];                       
                    }

                    return ["success" => false,  "error" => false, "mensaje" => "La Contraseña es Incorrecta"];
                }
            }else{
                
                $query = "SELECT *, COALESCE(u.id_usuario, pu.id_usuario) AS id_usuario_final FROM " . $this->table." u LEFT JOIN perfiles_usuarios pu ON u.id_usuario=pu.id_usuario WHERE u.email = ?";           
                $stmt  = $this->conn->prepare($query);
                $stmt->execute([$usuario->alias_usuario]);
                $datos = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if($datos){
                    if($datos["es_activo"]=== false){
                        return ["success" => false,  "error" => false, "mensaje" => "El Usuario no esta Activo"];
                    }

                     if($datos["es_activo"]=== false){
                        return ["success" => false,  "error" => false, "mensaje" => "El Usuario no esta Activo"];
                    }                  

                    if(($datos['estado_usuario'] != EstadoUsuario::ACTIVO->value && $datos['estado_usuario'] != EstadoUsuario::BLOQUEADO_X_INTENTOS->value) || empty($datos['estado_usuario'])){
                        return ["success" => "false", "error" => false, "mensaje" => ObtenerEstadoUsuario($datos['estado_usuario'])];
                    }
                    
                    $intentosActuales = $datos['intento_login'];                
                    $fechaEstadoUsuario = $datos['fecha_cambio_estado'];

                    if(!empty($fechaEstadoUsuario)){
                        $tiempoAnterior = new DateTime($fechaEstadoUsuario);
                        $horaActual = new DateTime();
                        $diffMinutes = $horaActual->getTimestamp() - $tiempoAnterior->getTimestamp();
                        $diffMinutes = $diffMinutes / 60; // pasa de segundos a minutos
                        if($intentosActuales >= $valorIntentos){
                            if ($diffMinutes < $tiempoEspera) {
                                $datos["success"] = "false";
                                $datos["error"] = "false";
                                $datos["mensaje"] = "Usted esta Bloqueado Temporalmente espere ".$tiempoEspera." Minutos";
                                return $datos;
                                exit();
                            }else{
                                $this->actualizarIntentosLogin("0", $$datos[0]['id_usuario']);
                                $valorIntentos = 0;
                            }
                        }                    

                    }
                    sleep(1);

                    if(password_verify($usuario->pass_usuario, $datos["pass_usuario"])){
                        if($intentosActuales >= $valorIntentos){
                            return ["success" => "false", "error" => false, "mensaje" => "Ha llegado al Maximo de Itentos Fallido"];
                        }
                        
                        $this->actualizarIntentosLogin("0", $datos['id_usuario_final']);

                        foreach ($datos as $key => $value) {
                            // Solo asigna si la propiedad existe en el objeto
                            if (property_exists($usuario, $key)) {
                                $usuario->$key = $value;
                            }
                        }
                   
                        $tokenGenerado = $this->CrearTokenUsuario($usuario);
                        if($tokenGenerado){

                            if(is_array($tokenGenerado) && array_key_exists('error', $tokenGenerado)){
                                if(isset($resultado['error']) && $resultado['error'] === true){
                                    return ["success" => false, "mensaje" => $resultado['mensaje'], "error" => true];                    
                                }
                            }
                            
                            // $this->crearHistorialLogin($datos);
                            // session_start();
                           $_SESSION['UsuarioValido'] = [
                            "id_usuario" => $usuario->id_usuario,
                            "id_rol"     => $usuario->id_rol,
                            "nombre"     => $usuario->nombre_usuario,
                            "apellido"     => $usuario->apellido_usuario,
                            "alias"     => $usuario->alias_usuario
                            ];
                            $_SESSION['EstadoSesion'] = true;
                            
                            $envio["success"] = true;
                            $envio["mensaje"] = "Validación Correcta";
                            if($datos['id_rol'] == RolesUsuarios::USUARIO->value){  
                                $_SESSION['Rol'] = RolesUsuarios::USUARIO->value;                          
                                $envio ["urlPrincipal"]= "Web/admin_user/";
                            }else if($datos['id_rol'] == RolesUsuarios::ADMINISTRADOR->value){
                                $_SESSION['Rol'] = RolesUsuarios::ADMINISTRADOR->value;
                                $envio ["urlPrincipal"]= "Web/admin_dashboard/index.php";
                            }

                            return $envio;
                        
                        }else{
                            return ["success" => "false", "error" => false, "mensaje" => "No se pudo Generar el Token"];
                        }


                    }else{
                        if($intentosActuales >= $valorIntentos){
                            return ["success" => "false", "error" => false, "mensaje" => "Ha llegado al Maximo de Itentos Fallido"];
                        }

                        $intentosActuales = $intentosActuales + 1;
                        if(!empty($this->actualizarIntentosLogin($intentosActuales, $datos['id_usuario_final']))){
                            return ["success" => "false", "error" => false, "mensaje" => "Datos Credenciales Invalidos"];                       
                        }

                        return ["success" => false,  "error" => false, "mensaje" => "La Contraseña es Incorrecta"];
                    }

                }else{
                    return ["success" => false,  "error" => false, "mensaje" => "El Alias o Correo son invalidos"];
                }
            }
            
        }catch (PDOException $e) {
            http_response_code(500);
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionesGenerales->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            http_response_code(500);
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionesGenerales->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
    }


    public function ActualizarEstadoUsuario($estadoUsuario, $id_usuario) {
        try{
            $fechaActual = date('Y-m-d H:i:s');
            $query = "UPDATE " . $this->table . " SET estado_usuario = ?, fecha_cambio_estado = ? WHERE id_usuario= ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$estadoUsuario, $fechaActual,  $id_usuario]);

            if($stmt->rowCount() > 0){
                 return ["success" => true, "actualizado" => true, "datos" => $stmt->fetch(PDO::FETCH_ASSOC)];
            }else{
                return ["success" => true, "actualizado" => false, "datos" => []];
            }
           
        }catch (PDOException $e) {    
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionesGenerales->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {           
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionesGenerales->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
        
    }

    private function actualizarIntentosLogin($intentos, $id) {
        try{
            $fechaActual = date('Y-m-d H:i:s');
            $query = "UPDATE " . $this->table . " SET intento_login= ?, fecha_cambio_estado = ? WHERE id_usuario= ? ";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$intentos, $fechaActual,  $id]);
            // $datos = $stmt->fetch(PDO::FETCH_ASSOC);   
            $filasAfectadas = $stmt->rowCount();           
            return $filasAfectadas;
        }catch (PDOException $e) {    
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionesGenerales->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {           
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionesGenerales->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
    }


    public function CrearHistorialLogin($id_usuario, $estadoLogin) {
        try{
            $fechaActual = date('Y-m-d H:i:s');
            $this->table = "historial_login";
            $id_estado_sesion = 0;
            $query = "UPDATE " . $this->table . " SET estado_usuario = ?, fecha_fin_sesion = ? WHERE id_usuario= ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id_estado_sesion, $fechaActual,  $id_usuario]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["success" => false, "error" => "true", "mensaje" => $this->funcionesGenerales->validarCodigoDeError($e->getCode(), $e->getMessage())];
           
        } catch (Exception $e) {
            // echo $e->getMessage();  
            // http_response_code(500);           
            return ["success" => false, "error" => "true", "mensaje" => $this->funcionesGenerales->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
        
    }

    public function CrearTokenUsuario(Usuarios $usuario)
    {
        try {

           do {
                // Generar token aleatorio
                $token = bin2hex(random_bytes(32)); // 32 caracteres hexadecimales
                $query = "SELECT COUNT(*) as total FROM tokens_acceso WHERE token_generado = ? AND estado_token = 'A'";
                $stmt = $this->conn->prepare($query);
                // Pasar los parámetros como array
                $stmt->execute([$token]);
                // Obtener el resultado
                $existe = $stmt->fetchColumn() > 0;

            } while ($existe);
           
            $usuario->token_sesion = $token;
            $tiempoSesion = TIEMPOEXPIRASESIONLOGIN; // en minutos
            $fechaVencimiento = date("Y-m-d H:i:s", strtotime("+$tiempoSesion minutes"));
            
            $query = "INSERT INTO " . $this->table . " (id_usuario, token_generado, tiempo_duracion, fecha_vence) VALUES (?, ?, ?, ?)";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([                
                $usuario->id_usuario,
                $usuario->token_sesion,                             
                $tiempoSesion,
                $fechaVencimiento,
            ]);
            
            if ($stmt->rowCount() > 0) {
                // $this->crearFoto->crearFotos($this->tempRuta, $usuario->foto_usuario, $usuario->nombre_usuario);
                return ["success" => true];
            } else {
                return ["success" => false];
            }

        } catch (PDOException $e) {           
            return ["success" => false, "error" => "true", "mensaje" => $this->funcionesGenerales->validarCodigoDeError($e->getCode(), $e->getMessage())];
           
        } catch (Exception $e) {           
            return ["success" => false, "error" => "true", "mensaje" => $this->funcionesGenerales->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }

    }




 }

?>