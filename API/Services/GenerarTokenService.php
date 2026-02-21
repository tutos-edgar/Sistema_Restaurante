<?php

require_once __DIR__ . '/../Interfaces/IGenerarTokens.php';


class GenerarTokenService {
 
    private IGenerarTokens $interface;

    public function __construct(IGenerarTokens $repository){
        $this->interface = $repository;
    }

    public function GenerarToken($idUsuario, $tokenDB, $expiraEnSegundos = 3600){
        return $this->interface->GenerarToken($idUsuario, $tokenDB, $expiraEnSegundos);        
    }

    public function ValidarToken($token){ 
        return $this->interface->ValidarToken($token);
    }


}


?>


