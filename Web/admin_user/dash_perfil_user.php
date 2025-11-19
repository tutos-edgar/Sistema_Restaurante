<div class="row g-3">

                    <div class="row justify-content-center">
                        <div class="col-md-8 col-lg-6">
                            <div class="card shadow-lg rounded-4">
                                <div class="card-header text-center bg-danger text-white rounded-top-4">
                                    <i class="bi bi-person-fill fs-1 text-white"></i>
                                    <h3 class="mb-0">Perfil de Usuario</h3>
                                </div>

                                <div class="card-body p-4">
                                    <form id="formRegistro">
                                        <div class="mb-3">                                           
                                            <input type="text" class="form-control" id="idUser" placeholder="Id User" hidden>
                                        </div>
                                        <div class="mb-3">                                           
                                            <input type="text" class="form-control" id="idPerfil" placeholder="Id Perfil" hidden>
                                        </div>
                                        <!-- Título -->
                                        <div class="mb-3">
                                            <label for="nombre" class="form-label">Nombre</label>
                                            <input type="text" class="form-control" id="nombre" placeholder="Nombre" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="apellido" class="form-label">Apellido</label>
                                            <input type="text" class="form-control" id="apellido" placeholder="Apellido" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="correo" class="form-label">Correo</label>
                                            <input type="email" class="form-control" id="correo" placeholder="Ej: example@gmail.com" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="fechaNacimiento" class="form-label">Fecha Nacimiento</label>
                                            <input type="date" class="form-control" id="fechaNacimiento" placeholder="Ej: " <?php echo $fechaActual;?> required>
                                        </div>
                                       
                                        <div class="mb-3">
                                            <label for="edad" class="form-label">Edad</label>
                                            <input type="number" class="form-control" id="edad" required>
                                        </div>

                                        <!-- Botón -->
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-danger btn-lg rounded-pill">
                                                <i class="bi bi-plus-circle me-2"></i>
                                                Registrar Perfil
                                            </button>
                                        </div>

                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

<!-- <div class="profile-card"> -->
    <!-- <div class="row justify-content-center mb-4">
        <div class="position-relative" style="width: 150px;">
            <img id="previewFoto" src="../img/perfil_user.png" class="rounded-circle border" width="150" height="150" alt="Foto">
             <label for="fotoInput" class="position-absolute bottom-0 end-0 bg-dark rounded-circle p-2" style="cursor: pointer;">
            <i class="fa fa-camera text-white"></i>
            </label>
            <input type="file" id="fotoInput" name="foto" accept="image/*" style="display: none;">
            <input type="hidden" id="fotoRuta" name="fotoRuta">
        </div>
    </div> -->

    <!-- Formulario para crear o editar perfil -->
    <!-- <form id="formPerfil" enctype="multipart/form-data">
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre" required>
            </div>
            <div class="col-md-6">
                <label for="apellido" class="form-label">Apellido</label>
                <input type="text" class="form-control" id="apellido" name="apellido" placeholder="Apellido" required>
            </div>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Correo Electrónico</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="correo@ejemplo.com" required>
        </div>

        <div class="mb-3">
            <label for="bio" class="form-label">Biografía</label>
            <textarea class="form-control" id="bio" name="bio" rows="3" placeholder="Cuéntanos sobre ti..."></textarea>
        </div>      

        <div class="mb-3">
            <label for="fechaNacimiento" class="form-label">Fecha de Nacimiento</label>
            <input type="date" class="form-control" id="fechaNacimiento" name="fechaNacimiento">
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-save btn-lg">Guardar Perfil</button>
        </div>
    </form> -->
<!-- </div> -->

