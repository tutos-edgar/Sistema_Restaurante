<?php 

require_once  '../models/Usuarios.php';

  interface IAuthUsuario{

    public function ValidarLogin(Usuarios $usuario);

 }

?>