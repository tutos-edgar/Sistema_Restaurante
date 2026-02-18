<?php
include_once  __DIR__ .'/../config/init_config.php';

class Usuarios
{
    private $conn;
    private $table = "usuarios_youtube";
    private $tableToken = "tokens_acceso";
    private $funcionGeneral;
    private $parametros;

    public $id_usuario, $nombre_usuario, $apellido_usuario, $email_usuario, $alias_usuario, $pass_new, $pass_usuario, $confirm_pass, $id_prefil_usuario, $id_estado_usuario, $id_rol, $terminos;
    public $tipo_sistema, $ip, $fechaEstadoUsuario, $token_sesion;
    public $foto_usuario;

    private $rutaFotousuario;
    private $crearFoto;
    private $tempRuta;
   
    public function __construct($db)
    {
        $this->conn = $db; 
        $this->funcionGeneral = new FuncionesGenerales();   
        $this->parametros = new Parametros($db);
        $this->tempRuta= "imagenes/fotos_usuarios/";
        $this->crearFoto = new FuncionesGenerales();
    }

    public function __sleep() {
        // Retorna solo las propiedades que quieres serializar
        return [
            "id_usuario",
            "nombre_usuario",
            "apellido_usuario",
            "email_usuario",
            "alias_usuario",
            "pass_usuario",
            "confirm_pass",
            "id_prefil_usuario",      
            "id_estado_usuario",
            "id_rol",
            "terminos"
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

            foreach ($datos as &$fila) {
                $idusuario = $fila['id_usuario_producto'];
                $rol = "ADMINISTRADOR";

                
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
            return $datos;

            
        }catch (PDOException $e) {
            http_response_code(500);
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            http_response_code(500);
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
        
        
    }

    public function obtenerPorId($id)
    {
        try{
           
            $query = "SELECT * FROM " . $this->table." WHERE id_usuario = ?";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([$id]);
            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);           
            return $datos;
            
        }catch (PDOException $e) {
            http_response_code(500);
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            http_response_code(500);
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
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }

    }

    public function ObtenerAliasUsuario($id)
    {
        try{
            $query = "SELECT * FROM " . $this->table . " WHERE alias_usuario= ?";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }catch (PDOException $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage()) ];
        } catch (Exception $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
    }

    public function ObtenerDatosDelUsuario(Usuarios $usuario)
    {
        try{
            $query = "SELECT * FROM " . $this->table . " u LEFT JOIN perfiles_usuairos pu ON u.id_usuario=pu.id_usuario WHERE id_usuario= ?";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([$usuario->id_usuario]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }catch (PDOException $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage()) ];
        } catch (Exception $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
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
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
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
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
        
    }

    public function validarExistenciausuario($alias, $idUsuario, $insert)
    {
        try{
            if ($insert == true) {
                $query = "SELECT * FROM " . $this->table . " WHERE  alias_usuario = ? AND id_usuario <> ?";
                $stmt = $this->conn->prepare($query);
            
            } else {
                $query = "SELECT * FROM " . $this->table . " WHERE alias_usuario= ? AND id_usuario = ?";
                $stmt = $this->conn->prepare($query);
                
            }
            $stmt->execute([$alias, $idUsuario]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }catch (PDOException $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
        
    }

    public function crearUsuario(Usuarios $usuario)
    {
        try {

            $resultado = $this->ObtenerAliasUsuario($usuario->alias_usuario);                 
            if ($resultado) {
                if(is_array($resultado) && array_key_exists('error', $resultado)){
                    if(isset($resultado['error']) && $resultado['error'] === true){
                        return ["success" => false, "mensaje" => $resultado['mensaje'], "error" => true];                    
                    }
                }
                
                return ["success" => false, "duplicado" => true];
            }

            if ($usuario->id_rol == "" || empty($usuario->id_rol)){
                $usuario->id_rol = "1";
            }
           
            // $usuario->rutaFotousuario = $this->tempRuta . trim($usuario->nombre_usuario).".png";
            $query = "INSERT INTO " . $this->table . " (nombre_usuario, apellido_usuario, email_usuario, alias_usuario, pass_usuario, id_rol, terminos) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([                
                strtoupper($usuario->nombre_usuario),
                strtoupper($usuario->apellido_usuario),               
                $usuario->email_usuario,
                $usuario->alias_usuario,               
                password_hash($usuario->pass_usuario, PASSWORD_BCRYPT),
                $usuario->id_rol, 
                $usuario->terminos
            ]);
            
            if ($stmt->rowCount() > 0) {
                // $this->crearFoto->crearFotos($this->tempRuta, $usuario->foto_usuario, $usuario->nombre_usuario);
                return ["success" => true];
            } else {
                return ["success" => false];
            }

        } catch (PDOException $e) {
            // http_response_code(500);           
            return ["success" => false, "error" => "true", "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
           
        } catch (Exception $e) {
            // http_response_code(500);           
            return ["success" => false, "error" => "true", "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }

    }

    public function actualizarUsuario(Usuarios $usuario)
    {

        try {

            $resultado = $this->obtenerPorId($usuario->id_usuario);
            
            if (!$resultado) {
                // Ya existe
                return ["success" => false, "existe" => false];
            }

            $resultado = $this->validarExistenciausuario($usuario->id_usuario, $usuario->nombre_usuario, true);           
            if ($resultado) {
                // Ya existe
                return ["success" => false, "duplicado" => true];
            }

            $usuario->rutaFotousuario = $this->tempRuta . trim($usuario->nombre_usuario).".png";
            $query = "UPDATE " . $this->table . " SET nombre_usuario = ?, apellido_usuario = ?, email_usuario = ?, alias_usuario=?, pass_usuario=? WHERE id_usuario = ?";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([
                strtoupper($usuario->nombre_usuario),
                $usuario->apellido_usuario,
                $usuario->email_usuario,
                $usuario->alias_usuario,
                $usuario->pass_usuario,
                $usuario->id_usuario,
            ]);

            $actualizadoFoto = $this->crearFoto->crearFotos($this->tempRuta, $usuario->foto_usuario, $usuario->nombre_usuario);
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
            http_response_code(500);
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            http_response_code(500);
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }

    }

    public function ValidarLoginAcceso(Usuarios $usuario){

        try{
            sleep(1);
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
            
            $query = "SELECT *, COALESCE(u.id_usuario, pu.id_usuario) AS id_usuario_final FROM " . $this->table." u LEFT JOIN perfiles_usuarios pu ON u.id_usuario=pu.id_usuario WHERE alias_usuario = ?";
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
                
                $query = "SELECT *, COALESCE(u.id_usuario, pu.id_usuario) AS id_usuario_final FROM " . $this->table." u LEFT JOIN perfiles_usuarios pu ON u.id_usuario=pu.id_usuario WHERE email_usuario = ?";           
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
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            http_response_code(500);
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
    }

    public function eliminarusuario($id)
    {

        try {

            $resultado = $this->obtenerPorId($id);

            $query = "DELETE FROM " . $this->table . " WHERE id_usuario = ?";
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
            http_response_code(500);
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
            http_response_code(500);
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }

    }

    public function CambiarPasswordUser(Usuarios $usuario){
        try{

            $resultado = $this->validarExistenciausuario($usuario->alias_usuario, $usuario->id_usuario, true);           
            if ($resultado) {
                // Ya existe
                return ["success" => false, "duplicado" => true];
            }
            
            $existeUsuaio = $this->obtenerPorId($usuario->id_usuario);
            
            if(!$existeUsuaio){
                return ["success" => true, "error" => false, "existe" => false, "mensaje" => "El Usuario no Existe"];
            }else{
                if(is_array($existeUsuaio) && array_key_exists('error', $existeUsuaio)){
                    if(isset($existeUsuaio['error']) && $existeUsuaio['error'] === true){
                        if(array_key_exists('mensaje', $existeUsuaio) && isset($existeUsuaio['mensaje']) && !empty($existeUsuaio['mensaje']) ){
                            return ["success" => false, "mensaje" => $existeUsuaio['mensaje'], "error" => true];
                        }
                        return ["success" => false, "mensaje" => "Ocurrio un Error Intenta Mas tarde", "error" => true];                    
                    }
                }
            }
           
            $resultado = $this->validarExistenciausuario($usuario->id_usuario, $usuario->alias_usuario, true);  
                   
            if ($resultado) {
                if(is_array($resultado) && array_key_exists('error', $resultado)){
                    if(isset($resultado['error']) && $resultado['error'] === true){
                        if(array_key_exists('mensaje', $resultado) && isset($resultado['mensaje']) && !empty($resultado['mensaje']) ){
                            return ["success" => false, "mensaje" => $resultado['mensaje'], "error" => true];
                        }
                        return ["success" => false, "mensaje" => "Ocurrio un Error Intenta Mas tarde", "error" => true];                    
                    }
                }
                return ["success" => false, "duplicado" => true];
            }
        
            $usuarioValido = $existeUsuaio[0];
            if(!password_verify($usuario->pass_usuario, $usuarioValido["pass_usuario"])){
                return ["success" => false, "error" => false, "mensaje" => "La Contraseña Actual no es Correcta"];
            }

            if(password_verify($usuario->pass_new, $usuarioValido["pass_usuario"])){
                return ["success" => false, "error" => false, "mensaje" => "La Nueva Contraseña no puede ser igual a la Actual"];
            }

            $query = "UPDATE " . $this->table . " SET  alias_usuario=?,  pass_usuario=? WHERE id_usuario = ?";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([
                $usuario->alias_usuario,
                password_hash($usuario->pass_new, PASSWORD_BCRYPT),
                $usuario->id_usuario
            ]);
            if($stmt->rowCount() > 0){
                return ["success" => true, "mensaje" => "Contraseña Actualizada", "datos" => URLINICIAL];
            }else{
                return ["success" => false, "mensaje" => "No se han Actualizado los Datos"];
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

    public function ValidarUsuario($datos) {
        try{
            if($datos){

                if($datos["es_activo"]=== false){
                    return ["success" => false,  "error" => false, "mensaje" => "El Usuario no esta Activo"];
                }
                
                if($datos['estado_usuario'] == EstadoUsuario::SIN_ACCESO->value || empty($datos['estado_usuario'])){                  
                    return ["success" => "false", "error" => false, "mensaje" => "Este Usuario no tiene Acceso al Sistema"];
                }  


                if($datos['estado_usuario'] == EstadoUsuario::SIN_ACCESO->value){
                    return ["success" => false,  "error" => false, "mensaje" => "Este Usuario no tiene Acceso al Sistema"];
                }else if($datos['estado_usuario'] == EstadoUsuario::BLOQUEADO_TEMPORALMENTE->value){
                    return ["success" => false,  "error" => false, "mensaje" => "Este Usuario esta Bloqueado Temporalmente"];
                }else if($datos['estado_usuario'] == EstadoUsuario::BLOQUEADO_X_INTENTOS->value){
                    return ["success" => false,  "error" => false, "mensaje" => "Este Usuario esta Bloqueado Por intentos Fallidos"];
                }
                else if($datos['estado_usuario'] == EstadoUsuario::BLOQUEADO_X_INACTIVIDAD->value){
                    return ["success" => false,  "error" => false, "mensaje" => "Este Usuario esta Bloqueado Por Inactividad"];
                }
                return ["success" => true,  "error" => false, "mensaje" => "Usuario Valido de Acceso"];
            }
            // $stmt = $this->conn->prepare("SELECT 1 FROM historial_login WHERE token_sesion = ? AND id_estado_sesion = 1");
            // $stmt->execute([$token]);
            // return $stmt->fetchColumn() ? true : false;
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
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {           
            return ["success" => false,  "error" => true, "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
    }

    private function actualizarTokenLogin($token, $id) {
        $fechaActual = date('Y-m-d H:i:s');
        $query = "UPDATE " . $this->table . " SET sesion_token = ? WHERE user_usuario= ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$token, $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    private function insertarTokens($intentos, $id) {
        $fechaActual = date('Y-m-d H:i:s');
        $query = "UPDATE " . $this->table . " SET sesion_token = ?, fecha_inicio_sesion = ? WHERE user_usuario= ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$intentos, $fechaActual,  $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function ActualizarEstadoUsuario($estadoUsuario, $id_usuario) {
        $fechaActual = date('Y-m-d H:i:s');
        $query = "UPDATE " . $this->table . " SET estado_usuario = ?, fecha_cambio_estado = ? WHERE id_usuario= ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$estadoUsuario, $fechaActual,  $id_usuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function CrearHistorialLogin($id_usuario, $estadoLogin) {
        $fechaActual = date('Y-m-d H:i:s');
        $this->table = "historial_login";
        $id_estado_sesion = 0;
        $query = "UPDATE " . $this->table . " SET estado_usuario = ?, fecha_fin_sesion = ? WHERE id_usuario= ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id_estado_sesion, $fechaActual,  $id_usuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function CrearTokenUsuario(UsuariosYoutube $usuario)
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
            
            $query = "INSERT INTO " . $this->tableToken . " (id_usuario, token_generado, tiempo_duracion, fecha_vence) VALUES (?, ?, ?, ?)";
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
            // http_response_code(500);  
            // echo $e->getMessage();         
            return ["success" => false, "error" => "true", "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
           
        } catch (Exception $e) {
            // echo $e->getMessage();  
            // http_response_code(500);           
            return ["success" => false, "error" => "true", "mensaje" => $this->funcionGeneral->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }

    }

    // public function validarPishing(Usuario $personal) {

    //     try {
    //         $resultado = $this->obtenerPorUsuario($personal->user_usuario);

    //         if ($resultado) {
    //             // Ya existe
    //             return ["success" => false, "duplicado" => true];
    //         }

    //         $query = "INSERT INTO " . $this->table . " (user_usuario, pass_usuario, id_rol, id_persona, estado) VALUES (?, ?, ?, ?, ?)";
    //         $stmt = $this->conn->prepare($query);
    //         $stmt->execute([
    //             $personal->user_usuario,
    //             $personal->pass_usuario,
    //             $personal->id_rol,
    //             $personal->id_persona,
    //             $personal->estado_usuario
    //         ]);

    //         if ($stmt->rowCount() > 0) {
    //             return ["success" => true];
    //         } else {
    //             return ["success" => false];
    //         }

    //     } catch (PDOException $e) {
    //         return ["success" => false, "error" => $e->getMessage()];
    //     } catch (Exception $e) {
    //         return ["success" => false, "error" => $e->getMessage()];
    //     }


    // }

    // public function validarPreguntaSecreta(Usuario $personal) {

    //     try {

    //         $resultado = $this->obtenerPorUsuario($personal->user_usuario);

    //         if ($resultado) {
    //             // Ya existe
    //             return ["success" => false, "duplicado" => true];
    //         }

    //         $query = "UPDATE " . $this->table . " SET user_usuario = ?, pass_usuario = ?, id_rol = ? , id_persona = ?, estado = ? WHERE id_usuario = ? OR id_persona = ?";
    //         $stmt = $this->conn->prepare($query);
    //         $stmt->execute([
    //             $personal->user_usuario,
    //             $personal->pass_usuario,
    //             $personal->id_rol,
    //             $personal->id_persona,
    //             $personal->estado_usuario,
    //             $personal->id_usuario,
    //             $personal->id_persona
    //         ]);

    //         if ($stmt->rowCount() > 0) {
    //             return ["success" => true];
    //         } else {
    //             return ["success" => false];
    //         }

    //     } catch (PDOException $e) {
    //         http_response_code(500);
    //         return ["success" => false, "error" => $e->getMessage()];
    //     } catch (Exception $e) {
    //         http_response_code(500);
    //         return ["success" => false, "error" => $e->getMessage()];
    //     }


    // }
    



}
