<?php 
// require_once __DIR__ . '/../config.php';
require_once '../config/config.php';
require_once '../models/Usuarios.php';
require_once '../middleware/AccessJWT.php';
require_once '../middleware/AccessDB.php';
require_once '../Interfaces/IGenerarTokens.php';

 class AuthUsuario{


    private $parametros;
    private $conn;
    private $table;
    private $funcionesGenerales;

    private AccessJWT $jwt;
    private AccessDB $session;

    public function __construct($db)
    {
        $this->conn = $db;
        $this->table = "usuarios";
        $this->funcionesGenerales = new FuncionesGenerales(); 
        $this->parametros = new Parametros($db);

        $this->jwt = new AccessJWT();
        $this->session = new AccessDB($db);

       
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
            
            $query = "SELECT *, COALESCE(u.id_usuario, pu.id_usuario) AS id_usuario_final, u.fecha_cambio_estado AS cambio_estado_usuario FROM " . $this->table." u LEFT JOIN perfiles_usuarios pu ON u.id_usuario=pu.id_usuario WHERE alias = ?";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([$usuario->alias_usuario]);
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);

            if(!$datos){
                $query = "SELECT *, COALESCE(u.id_usuario, pu.id_usuario) AS id_usuario_final, u.fecha_cambio_estado AS cambio_estado_usuario FROM " . $this->table." u LEFT JOIN perfiles_usuarios pu ON u.id_usuario=pu.id_usuario WHERE u.email = ?";           
                $stmt  = $this->conn->prepare($query);
                $stmt->execute([$usuario->alias_usuario]);
                $datos = $stmt->fetch(PDO::FETCH_ASSOC);                
            }

            if($datos){
               
                if($datos["activo"]=== false){
                    return ["success" => false,  "error" => false, "mensaje" => "El Usuario no esta Activo"];
                }              

                if(($datos['id_estado_perfil'] != EstadoUsuario::ACTIVO->value && $datos['id_estado_perfil'] != EstadoUsuario::BLOQUEADO_X_INTENTOS->value) || empty($datos['id_estado_perfil'])){
                    return ["success" => "false", "error" => false, "mensaje" => ObtenerEstadoUsuario($datos['id_estado_perfil'])];
                }

                $intentosActuales = $datos['intento_login'];                
                // $fechaEstadoUsuario = $datos['fecha_cambio_estado'];
                $fechaEstadoUsuario = $datos['cambio_estado_usuario'];
                
                if(!empty($fechaEstadoUsuario)){
                       
                    if($datos['id_estado_perfil'] == EstadoUsuario::BLOQUEADO_X_INTENTOS->value){                        
                        $tiempoAnterior = new DateTime($fechaEstadoUsuario);
                        $horaActual = new DateTime();
                        $diffMinutes = $horaActual->getTimestamp() - $tiempoAnterior->getTimestamp();
                        $diffMinutes = $diffMinutes / 60; // pasa de segundos a minutos
                       
                        if($intentosActuales >= $valorIntentos){                           
                            if ($diffMinutes < $tiempoEspera) {
                                // return ["success" => "false", "error" => false, "mensaje" => "Usted esta Bloqueado Temporalmente espere ".round($diffMinutes, 2)." Minutos"];
                                return ["success" => "false", "error" => false, "mensaje" => "Usted esta Bloqueado Temporalmente espere ".$tiempoEspera." Minutos"];
                            }else{
                                if($datos['id_estado_perfil'] == EstadoUsuario::BLOQUEADO_X_INTENTOS->value){                                                                 
                                    $this->ActualizarEstadoPerfil(EstadoUsuario::ACTIVO->value, $datos['id_usuario_final']);
                                }
                               
                                $this->actualizarIntentosLogin("0", $datos['id_usuario_final']);
                                // $valorIntentos = 0;
                                $intentosActuales = 0;
                            }
                        }   
                       
                    }

                }
                sleep(1);

                if(password_verify($usuario->pass_usuario, $datos["password_hash"])){
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

                    // $usuarioJWT = new AccessJWT();
                    // // $usuario->token_sesion = $usuarioJWT->generarJWT($usuario->id_usuario, $_ENV['KEY_SECRET_JWT']); //Busca en Variables de Entorno del Sistema
                    // // $usuario->token_sesion = $usuarioJWT->generarJWT($usuario->id_usuario, 'KEY_SECRET_JWT'.$usuario->id_usuario);  //Busca en Constante Definida en Config
                    // $usuario->token_sesion = $usuarioJWT->GenerarToken($usuario->id_usuario, getenv('KEY_SECRET_JWT').$usuario->id_usuario); //Busca en Variables de Entorno en .htaccess
    
                    // $validarToken = $usuarioJWT->validarJWT($usuario->token_sesion, getenv('KEY_SECRET_JWT').$usuario->id_usuario);
                   
                    $usuarioJWT = new AccessDB($this->conn);
                    $usuarioJWT->usuario = $usuario;
                    $tokenGenerado = $usuarioJWT->GenerarToken($usuario->id_usuario, $this->conn, 1800); 
                    $tokenDB = $usuarioJWT->GetTokenSesion();
                                     
                    if($tokenGenerado){
                        if(is_array($tokenGenerado) && array_key_exists('error', $tokenGenerado)){
                            if(isset($resultado['error']) && $resultado['error'] === true){
                                return ["success" => false, "mensaje" => $resultado['mensaje'], "error" => true];                    
                            }
                        }

                        $usuarioJWT = new AccessJWT();
                        // $usuario->token_sesion = $usuarioJWT->generarJWT($usuario->id_usuario, $_ENV['KEY_SECRET_JWT']); //Busca en Variables de Entorno del Sistema
                        // $usuario->token_sesion = $usuarioJWT->generarJWT($usuario->id_usuario, 'KEY_SECRET_JWT'.$usuario->id_usuario);  //Busca en Constante Definida en Config
                        $usuario->token_sesion = $usuarioJWT->GenerarToken($usuario->id_usuario, $tokenDB);

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
                        $envio["id_rol"] = $usuario->id_rol;
                        $envio["id_usuario"] = $usuario->id_usuario;
                        
                        if($datos['id_rol'] == RolesUsuarios::USUARIO->value){
                            $_SESSION['Rol'] = RolesUsuarios::USUARIO->value;                             
                            $envio ["urlPrincipal"]= "admin_user/";
                        }else if($datos['id_rol'] == RolesUsuarios::ADMINISTRADOR->value){
                            $_SESSION['Rol'] = RolesUsuarios::ADMINISTRADOR->value;
                            $envio ["urlPrincipal"]= "admin_dashboard/";
                        }

                        setcookie("access_token", $tokenDB, [
                            "expires"=>time()+1800,
                            "path"=>"/",
                            "secure"=>true,
                            "httponly"=>true,
                            "samesite"=>"Strict"
                        ]);
                        
                        return $envio;
                       
                    }else{
                        return ["success" => "false", "error" => false, "mensaje" => "No se pudo Generar el Token"];
                    }


                }else{                   
                    if($intentosActuales >= $valorIntentos){
                        if(!empty($this->ActualizarEstadoPerfil(EstadoUsuario::BLOQUEADO_X_INTENTOS->value, $datos['id_usuario_final']))){                            
                            return ["success" => "false", "error" => false, "mensaje" => "Ha llegado al Maximo de Itentos Fallido"];                      
                        }
                        return ["success" => "false", "error" => false, "mensaje" => "Ha llegado al Maximo de Itentos Fallido"]; 
                    }

                    $intentosActuales = $intentosActuales + 1;
                    if(!empty($this->actualizarIntentosLogin($intentosActuales, $datos['id_usuario_final']))){
                        return ["success" => "false", "error" => false, "mensaje" => "Datos Credenciales Invalidos"];                       
                    }

                    return ["success" => false,  "error" => false, "mensaje" => "La Contraseña es Incorrecta"];
                }
            }else{
                return ["success" => false,  "error" => false, "mensaje" => "El Usuario ingresado es Invalido"];
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

    public function ActualizarEstadoPerfil($estadoUsuario, $id_usuario) {
        try{
            $fechaActual = date('Y-m-d H:i:s');           
            $query = "UPDATE perfiles_usuarios SET id_estado_perfil = ?, fecha_cambio_estado = ? WHERE id_usuario= ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$estadoUsuario, $fechaActual,  $id_usuario]);
            if($stmt->rowCount() > 0){
                 return ["success" => true, "actualizado" => true, "datos" => []];
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



    //VALIDACIONES Y CIERRE DE SESIONES

    public function validarRequest($idUsuario): bool {


        $payload = $this->jwt->validarToken($idUsuario);

        $valido = $this->session->validarSesion(
            $payload['sub'],
            $payload['token_sesion']
        );

        if (!$valido) {
            throw new Exception("Sesión inválida");
        }

        return $valido;
    }

    public function logoutDesdeAdmin(int $idUsuario): void {
        $this->session->cerrarSesionesUsuario($idUsuario);
    }


 }

?>