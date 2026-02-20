<?php 

require_once '../Interfaces/IGenerarTokens.php';
require_once '../models/Usuarios.php';

class AccessDB implements IGenerarTokens {

    private $tiempoExpiraToken;
    private $parametros;
    private $tokenSesion;
    private $conn;
    private $table;
    private $funcionesGenerales;
    public $usuario;

    public function __construct($db)
    {
        $this->conn = $db;
        $this->table = "token_sesion";
        $this->funcionesGenerales = new FuncionesGenerales(); 
        $this->parametros = new Parametros($db);
        $this->usuario = new Usuarios($this->conn);
    }

    public function GenerarToken($idUsuario, $db, $expiraEnSegundos = 3600) {
        try {
          
           do {
                // Generar token aleatorio
                $token = hash('sha256', bin2hex(random_bytes(32))); // 32 caracteres hexadecimales con hash SHA-256 (64 caracteres hexadecimales) 
                $query = "SELECT COUNT(*) as total FROM token_sesion WHERE token_generado = ? AND estado_token = 'A'";
                $stmt = $db->prepare($query);                
                $stmt->execute([$token]);                
                $existe = $stmt->fetchColumn() > 0;   // Obtener el resultado

            } while ($existe);
            
            $this->usuario->id_usuario = $idUsuario;
            $this->usuario->token_sesion = $token;
            $this->tokenSesion = $token;
            $tiempoSesion = TIEMPOEXPIRASESIONLOGIN; // en minutos
            $this->tiempoExpiraToken = TIEMPOEXPIRASESIONLOGIN;
            $fechaVencimiento = date("Y-m-d H:i:s", strtotime("+$tiempoSesion minutes"));
            
            $query = "INSERT INTO " . $this->table . " (id_usuario, token_generado, tiempo_duracion, dispositivo, id_dispositivo, fecha_vence) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([                
                $this->usuario->id_usuario,
                $this->usuario->token_sesion,                             
                $tiempoSesion,
                strtoupper($this->usuario->tipo_sistema),
                $this->usuario->id_dispositivo,
                $fechaVencimiento,
            ]);
            
            if ($stmt->rowCount() > 0) {
                // $this->crearFoto->crearFotos($this->tempRuta, $usuario->foto_usuario, $usuario->nombre_usuario);
                return ["success" => true, "token" => $token];
            } else {
                return ["success" => false];
            }

        } catch (PDOException $e) {   
            http_response_code(500);    
            return ["success" => false, "error" => true, "mensaje" => $this->funcionesGenerales->validarCodigoDeError($e->getCode(), $e->getMessage())];
           
        } catch (Exception $e) {  
            http_response_code(500);         
            return ["success" => false, "error" => true, "mensaje" => $this->funcionesGenerales->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }

    }

    public function GetExpiraToken() {
        return $this->tiempoExpiraToken;
    }

    public function GetTokenSesion() {
        return $this->tokenSesion;
    }

    // public function GetExpiraToken($jwt) {
    //     $partes = explode('.', $jwt);

    //     if(count($partes) !== 3){
    //         return false;
    //     }

    //     list($headerEncoded, $payloadEncoded, $signatureEncoded) = $partes;

    //     $payload = json_decode($this->base64UrlDecode($payloadEncoded), true);

    //     return isset($payload['exp']) ? $payload['exp'] : null;
    // }

    private function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function base64UrlDecode($data) {
        return base64_decode(strtr($data, '-_', '+/'));
    }


    public function validarSesion(int $idUsuario, string $tokenSesion): bool {

        $stmt = $this->conn->prepare(
            "SELECT id FROM {$this->table}
             WHERE id_usuario = ?
             AND token_generado = ?
             AND estado_token = 'A'
             AND fecha_vence > NOW()"
        );

        $stmt->execute([$idUsuario, $tokenSesion]);

        return $stmt->rowCount() > 0;
    }

    public function cerrarSesionesUsuario(int $idUsuario): void {

        $stmt = $this->conn->prepare(
            "UPDATE {$this->table}
             SET estado_token = 'I'
             WHERE id_usuario = ?"
        );

        $stmt->execute([$idUsuario]);
    }

    public function cerrarSesionIndividual(string $tokenSesion): void {

        $stmt = $this->conn->prepare(
            "UPDATE {$this->table}
             SET estado_token = 'I'
             WHERE token_generado = ?"
        );

        $stmt->execute([$tokenSesion]);
    }


    

    /// FUNCIONES PARA VALIDAR JWT Y VALIDAR SESION EN BASE DE DATOS

    function validarDB($jwt, $secretKey) {

        $partes = explode('.', $jwt);

        if(count($partes) !== 3){
            return false;
        }

        list($headerEncoded, $payloadEncoded, $signatureEncoded) = $partes;

        $signature = $this->base64UrlDecode($signatureEncoded);

        $firmaValida = hash_hmac(
            'sha256',
            $headerEncoded . "." . $payloadEncoded,
            $secretKey,
            true
        );

        if(!hash_equals($firmaValida, $signature)){
            return false;
        }

        $payload = json_decode($this->base64UrlDecode($payloadEncoded), true);

        if($payload['exp'] < time()){
            return false; // Token expirado
        }

        return $payload;
    }


    function validarToken($idUsuario) {

        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            http_response_code(401);            
            exit(json_encode(["success" => false,  "error" => false, "mensaje" => "El Usuario no esta Activo"]));
        }

        $token = str_replace("Bearer ", "", $headers['Authorization']);
        $payload = $this->validarDB($token, getenv('KEY_SECRET_JWT').$idUsuario);
        if (!$payload) {
            http_response_code(401);            
            exit(json_encode(["success" => false,  "error" => false, "mensaje" => "Token no válido o expirado"]));
        }

        $validacion = ($payload ? 1 : 0);
        return $validacion;
    }
    

}

?>
