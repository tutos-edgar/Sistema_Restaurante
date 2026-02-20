<?php 


  interface IGenerarTokens {

    public function GenerarToken($idUsuario, $secretKey, $expiraEnSegundos = 3600);

    public function ValidarToken($idUsuario);

 }

 
?>