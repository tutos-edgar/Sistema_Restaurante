 <div class="row g-3">                    

                    <div class="row justify-content-center">
                        <div class="col-md-8 col-lg-6">
                            <div class="card shadow-lg rounded-4">
                            <div class="card-header text-center bg-danger text-white rounded-top-4">
                                <i class="bi bi-broadcast fs-1 text-white"></i>
                                <h3 class="mb-0">Registrar Canal de YouTube</h3>
                            </div>

                            <div class="card-body p-4">
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
                                    <textarea class="form-control" id="descripcion" rows="3" placeholder="Describe tu canal..." ></textarea>
                                </div>

                                

                                <!-- Suscriptores -->
                                <!-- <div class="mb-3">
                                    <label for="suscriptores" class="form-label">Cantidad de Suscriptores</label>
                                    <input type="number" class="form-control" id="suscriptores" placeholder="Ej: 1200" min="0" required>
                                </div> -->

                                <!-- Botón -->
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-danger btn-lg rounded-pill">
                                        <i class="bi bi-plus-circle me-2"></i>
                                    Registrar Canal
                                    </button>
                                </div>

                                </form>
                            </div>
                            
                            </div>
                        </div>
                    </div>
                </div>