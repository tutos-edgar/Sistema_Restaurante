<?php 

require_once '../Interfaces/IGenerarTokens.php';

class AccessJWT implements IGenerarTokens {

    private $tiempoExpiraToken;

    public function GenerarToken($idUsuario, $tokenDB, $expiraEnSegundos = 3600) {

        $secretKey = getenv('KEY_SECRET_JWT').$idUsuario;  //Busca en Variables de Entorno en .htaccess y Combina la clave secreta con el ID del usuario
        $header = [
            "alg" => "HS256",
            "typ" => "JWT"
        ];

        $payload = [
            "iss" => "tutos-edgar",              // issuer
            "sub" => $idUsuario,            // usuario
            "token_sesion" => $tokenDB,
            "iat" => time(),                // fecha emisión
            "exp" => time() + $expiraEnSegundos          // expira en 1 hora  3600 segundos  1/2 hora 1800 segundos
        ];
        $this->tiempoExpiraToken = time() + 3600; // 1 hora
       $headerEncoded = $this->base64UrlEncode(json_encode($header));
        $payloadEncoded = $this->base64UrlEncode(json_encode($payload));

        $signature = hash_hmac(
            'sha256',
            $headerEncoded . "." . $payloadEncoded,
            $secretKey,
            true
        );

        $signatureEncoded = $this->base64UrlEncode($signature);

        return $headerEncoded . "." . $payloadEncoded . "." . $signatureEncoded;

    }

    public function GetExpiraToken() {
        return $this->tiempoExpiraToken;
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

    function validarJWT($jwt, $secretKey) {

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
        $payload = $this->validarJWT($token, getenv('KEY_SECRET_JWT').$idUsuario);
        if (!$payload) {
            http_response_code(401);            
            exit(json_encode(["success" => false,  "error" => false, "mensaje" => "Token no válido o expirado"]));
        }

        //$validacion = ($payload ? 1 : 0);
        return $payload;
    }
    

}

?>
