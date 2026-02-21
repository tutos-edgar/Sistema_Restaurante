<?php

require_once __DIR__ . '/../Interfaces/IGenerarCifrado.php';


class GenerarCifradoService {
 
    private IGenerarCifrado $interface;

    public function __construct(IGenerarCifrado $repository){
        $this->interface = $repository;
    }

    public function cifrar($textoPlano) {
        return $this->interface->cifrar($textoPlano);
    }

    public function descifrar($textoCifrado) {
        return $this->interface->descifrar($textoCifrado);
    }




}


