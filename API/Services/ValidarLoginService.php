<?php
require_once '../models/AuthUsuario.php';
require_once '../models/Usuarios.php';
require_once '../Interfaces/IAuthUsuario.php';


class ValidarLoginService {
 
    private IAuthUsuario $interface;

    public function __construct(IAuthUsuario $repository){
        $this->interface = $repository;
    }

    public function validarLogin(Usuarios $usuario) {
        return $this->interface->ValidarLogin($usuario);
    }




}
