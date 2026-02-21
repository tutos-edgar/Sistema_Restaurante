<?php 

    interface IBuscarDatos {

    public function buscarId($id);

    public function buscarPorCampo($campo, $id);

    public function buscarTodos();

    public function buscarPorLike($condicion);

 }

?>

