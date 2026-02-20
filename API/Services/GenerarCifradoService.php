<?php

require_once '../Interfaces/IGenerarCigrado.php';


class GenerarCifradoService {
 
    private IGenerarCigrado $interface;

    public function __construct(IGenerarCigrado $repository){
        $this->interface = $repository;
    }

    public function cifrar($textoPlano) {
        return $this->interface->cifrar($textoPlano);
    }

    public function descifrar($textoCifrado) {
        return $this->interface->descifrar($textoCifrado);
    }




}


