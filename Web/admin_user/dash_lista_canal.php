<!-- Card con ComboBox -->
                <div class="card p-3 shadow-sm card-custom-title">
                    <div class="row align-items-center">
                        <!-- Título -->
                        <div class="col-12 col-md-8 d-flex align-items-center mb-2 mb-md-0">
                            <i class="bi bi-card-list fs-1 text-white me-2"></i>
                            <h3 class="m-0 text-white">LISTA DE CANALES</h3>
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
                                    <th>Título</th>
                                    <th>Descripcion</th>
                                    <th>URL</th>
                                    <th>Suscriptores</th>
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
          
          <!-- Nombre -->
          <div class="mb-3">
            <label for="nombre" class="form-label">Nombre del Canal</label>
            <input type="text" class="form-control" id="nombre" placeholder="Ej: Mi Canal Tech" required>
          </div>

          <!-- URL -->
          <div class="mb-3">
            <label for="url" class="form-label">URL del Canal</label>
            <input type="url" class="form-control" id="url" placeholder="https://www.youtube.com/@mi_canal" required>
          </div>
          
          <!-- Descripción -->
          <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion" rows="3" placeholder="Describe tu canal..."></textarea>
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




