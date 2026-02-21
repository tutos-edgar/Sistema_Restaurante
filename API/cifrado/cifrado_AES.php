<?php 

class CifradoAES implements IGenerarCifrado {

    private $claveSecreta;
    private $metodoCifrado;
    private $iv;

    public function __construct() {
        $this->claveSecreta = getenv('KEY_SECRET_AES'); // Busca en Variables de Entorno en .htaccess
        $this->metodoCifrado = 'AES-256-CBC';
        $this->iv = substr(getenv('KEY_IV'), 0, 16); // Busca en Variables de Entorno en .htaccess
    }

    public function cifrar($textoPlano) {
        $cifrado = openssl_encrypt($textoPlano, $this->metodoCifrado, $this->claveSecreta, 0, $this->iv);
        return base64_encode(bin2hex($cifrado));
    }

    public function descifrar($textoCifrado) {
        $cifrado = base64_decode($textoCifrado);
        $cifrado = hex2bin($cifrado);
        return openssl_decrypt($cifrado, $this->metodoCifrado, $this->claveSecreta, 0, $this->iv);
    }
           
}

?>