<?php 

require_once '../models/Usuarios.php';

  interface IGenerarCigrado {

    public function cifrar($textoPlano);

    public function descifrar($textoCifrado);

 }

?>

