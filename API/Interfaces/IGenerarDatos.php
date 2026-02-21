<?php 

    interface IGenerarDatos {

    public function listarTodos();

    public function guardar($dato);

    public function modificar($dato);

    public function eliminar($id);


 }

?>

