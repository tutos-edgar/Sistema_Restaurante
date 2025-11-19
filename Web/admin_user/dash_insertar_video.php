<div class="row g-3">

                    <div class="row justify-content-center">
                        <div class="col-md-8 col-lg-6">
                            <div class="card shadow-lg rounded-4">
                                <div class="card-header text-center bg-danger text-white rounded-top-4">
                                    <i class="bi bi-camera-video fs-1 text-white"></i>
                                    <h3 class="mb-0">Registrar Videos de YouTube</h3>
                                </div>

                                <div class="card-body p-4">
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

                                        <!-- Botón -->
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-danger btn-lg rounded-pill">
                                                <i class="bi bi-plus-circle me-2"></i>
                                                Registrar Video
                                            </button>
                                        </div>

                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>