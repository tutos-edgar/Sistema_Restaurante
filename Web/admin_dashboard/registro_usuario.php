<?php
// Asegúrate de que no haya espacios, líneas en blanco o HTML antes de esto
// header("Location: ../index.php");
// exit; // Siempre usar exit después de header para detener la ejecución
include_once 'header_dashboard.php';
?>


<body>

    <?php

        include_once 'nav_bar_dashboard.php';
        include_once 'side_bar_dashboard.php';
    ?>


    <!-- CONTENT -->
    <main class="content">

        <!-- REGISTRO EMPLEADO -->
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3><i class="bi bi-person-check"></i>  Usuarios</h3>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalEmpleado">
                <i class="bi bi-plus-circle"></i> Nuevo
            </button>
        </div>

        <div class="card p-3 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover" id="tablaEmpleados">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Documento</th>
                            <th>Nombre</th>
                            <th>Apellido</th>                            
                            <th>Correo</th>
                            <th>Foto</th>
                            <th>Usuario</th>                            
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL EMPLEADO -->
    <div class="modal fade" id="modalEmpleado" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title"><i class="bi bi-person-check"></i> Registro de Usuario</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <form id="fromRegistro">
                <input type="hidden" name="id" id="empleadoId">

                <div class="row g-3">
                    <!-- <div class="col-md-6">
                        <label class="form-label">Documento</label>
                        <input type="text" class="form-control" name="documento" required>
                    </div>                   
                     -->

                    <div class="col-md-6">
                        <label class="form-label">Usuario</label>
                        <input type="email" class="form-control" name="correo" required>
                    </div>
                    
                    <div class="col-md-6">
                    <label for="password" class="form-label">Contraseña</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" placeholder="********" required />                        
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="confirmPassword" class="form-label">Confirmar contraseña</label>
                    <input type="password" class="form-control" id="confirmPassword" placeholder="********" required />
                </div>

                    <div class="col-md-6">
                        <label class="form-label">Rol</label>
                        <select class="form-select" name="rol" required>
                            <option value="">Seleccionar</option>
                            <option>Administrador</option>
                            <option>Cajero</option>
                            <option>Cocinero</option>
                            <option>Repartidor</option>
                        </select>
                    </div>

                    
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" form="formEmpleado" class="btn btn-success">Guardar</button>
        </div>
        </div>
    </div>
    </div>



    </main>

    <?php

        include_once 'footer.php';
    ?>

      

</body>

</html> 