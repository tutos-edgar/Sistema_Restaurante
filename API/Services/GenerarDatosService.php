<?php

require_once __DIR__ . '/../Interfaces/IGenerarDatos.php';


class GenerarDatosService {
 
    private IGenerarDatos $interface;

    public function __construct(IGenerarDatos $repository){
        $this->interface = $repository;
    }

    public function listar() {
        return $this->interface->listarTodos();
    }

    public function guardar($dato) {
        return $this->interface->guardar($dato);
    }

    public function modificar($dato) {
        return $this->interface->modificar($dato);
    }

    public function eliminar($id) {
        return $this->interface->eliminar($id);
    }




}


