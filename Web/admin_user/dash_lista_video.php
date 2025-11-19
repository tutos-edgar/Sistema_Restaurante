<!-- Card con ComboBox -->
                <div class="card p-3 shadow-sm card-custom-title">
                    <div class="row align-items-center">
                        <!-- Título -->
                         <div class="col-12 col-md-8 d-flex align-items-center mb-2 mb-md-0">
                            <i class="bi bi-collection-play fs-1 text-white me-2"></i>
                            <h3 class="m-0">LISTA DE VIDEOS</h3>
                        </div>                        

                        <!-- ComboBox -->
                        <div class="col-12 col-md-4 text-center text-md-end ">
                            <select id="filterType" class="form-select w-100 w-md-auto">
                                <option value="">Mostrar todos</option>
                                <option value="Video">Videos</option>
                                <option value="Short">Shorts</option>
                            </select>
                        </div>
                    </div>
                </div>


                                <!-- Card de la tabla -->
                <div class="card shadow-lg p-4">
                    <div class="table-responsive">
                        <table id="tablaDatos" class="table table-hover align-middle text-center ">
                            <thead class="table-danger ">
                                <tr>
                                  <th>Id</th>
                                    <th>Canal</th>
                                    <th>Título</th>
                                    <th>Descripcion</th>
                                    <th>Tipo</th>
                                    <th>URL</th>
                                    <th>Duración</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>


               <!-- Modal -->
<div class="modal fade" id="modalRegistroCanal" tabindex="-1" aria-labelledby="modalRegistroCanalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content rounded-4 shadow-lg">
      
      <!-- Header -->
      <div class="modal-header bg-danger text-white rounded-top-4">
        <i class="bi bi-broadcast fs-1 me-2"></i>
        <h5 class="modal-title" id="modalRegistroCanalLabel">Registrar Canal de YouTube</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <!-- Body -->
      <div class="modal-body p-4">
        <form id="formRegistro">

<!-- Tipo -->
                                        <div class="mb-3">
                                            <label for="tipo" class="form-label">Tipo</label>
                                            <select class="form-select" id="tipo" required>
                                                <option value="0" selected disabled>Seleccione el tipo</option>
                                                <option value="video">Video</option>
                                                <option value="short">Short</option>
                                            </select>
                                        </div>

        <!-- Título -->
                                        <div class="mb-3">
                                            <label for="titulo" class="form-label">Título del Video</label>
                                            <input type="text" class="form-control" id="nombre" placeholder="Ej: Mi nuevo tutorial" required>
                                        </div>                                        

                                        <!-- Descripción -->
                                        <div class="mb-3">
                                            <label for="descripcion" class="form-label">Descripción</label>
                                            <textarea class="form-control" id="descripcion" rows="3" placeholder="Breve descripción del video..." ></textarea>
                                        </div>
                                        
                                        <?php                                       
                                            // $generales = new FuncionesGenerales(); 
                                            $generales->llenarCanales("listaCanales", $IdUser);                                      
                                        ?>
                                        <!-- URL -->
                                        <div class="mb-3">
                                            <label for="url" class="form-label">URL del Video</label>
                                            <input type="url" class="form-control" id="url" placeholder="https://www.youtube.com/watch?v=XXXXXXX" required>
                                        </div>

                                        <!-- Duración -->
                                        <div class="mb-3">
                                            <label for="duracion" class="form-label">Tiempo de Duración</label>
                                            <input type="time" class="form-control" id="duracion" step="1" required>
                                            <div class="form-text">Formato: hh:mm:ss</div>
                                        </div>
        </form>
      </div>

      <!-- Footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" form="formRegistro" class="btn btn-danger rounded-pill">
          <i class="bi bi-plus-circle me-2"></i> Registrar Canal
        </button>
      </div>

    </div>
  </div>
</div>




                