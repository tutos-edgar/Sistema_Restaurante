<?php 
ob_start();

include_once __DIR__ . '/../API/models/FuncionesGenerales.php';

$generales = new FuncionesGenerales();
include_once 'header.php'; 
$generales->ObtenerEstilosWeb(0);

?>



<body>

    <?php 
        include_once 'navbar_superior.php';
        include_once 'hero_container.php';
    ?>

    

    <!-- PROMOCIONES -->
    <section class="py-5">
        <div class="container text-center">
            <h2 class="fw-bold mb-5">🍔 Nuestro Menú</h2>

            <div class="row g-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow">
                        <img src="https://images.unsplash.com/photo-1550547660-d9450f859349" class="card-img-top">
                        <div class="card-body">
                            <h5>Hamburguesa Clásica</h5>
                            <p>Carne, lechuga, tomate y queso</p>
                            <p class="price">Q25.00</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow">
                        <img src="https://images.unsplash.com/photo-1568901346375-23c9450c58cd" class="card-img-top">
                        <div class="card-body">
                            <h5>Hamburguesa BBQ</h5>
                            <p>Carne doble y salsa BBQ</p>
                            <p class="price">Q35.00</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow">
                        <img src="https://images.unsplash.com/photo-1606755962773-d324e0a13086" class="card-img-top">
                        <div class="card-body">
                            <h5>Hamburguesa Doble</h5>
                            <p>Doble carne y queso cheddar</p>
                            <p class="price">Q40.00</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow">
                        <img src="https://images.unsplash.com/photo-1553979459-d2229ba7433b" class="card-img-top">
                        <div class="card-body">
                            <h5>Hamburguesa Especial</h5>
                            <p>Carne premium + tocino</p>
                            <p class="price">Q45.00</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        
    </section>

    <section class="py-5">
        <div class="container text-center">
            <h2 class="fw-bold mb-5">🧋 Bebidas</h2>

            <div class="row g-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow">
                        <img src="https://images.unsplash.com/photo-1550547660-d9450f859349" class="card-img-top">
                        <div class="card-body">
                            <h5>Hamburguesa Clásica</h5>
                            <p>Carne, lechuga, tomate y queso</p>
                            <p class="price">Q25.00</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow">
                        <img src="https://images.unsplash.com/photo-1568901346375-23c9450c58cd" class="card-img-top">
                        <div class="card-body">
                            <h5>Hamburguesa BBQ</h5>
                            <p>Carne doble y salsa BBQ</p>
                            <p class="price">Q35.00</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow">
                        <img src="https://images.unsplash.com/photo-1606755962773-d324e0a13086" class="card-img-top">
                        <div class="card-body">
                            <h5>Hamburguesa Doble</h5>
                            <p>Doble carne y queso cheddar</p>
                            <p class="price">Q40.00</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow">
                        <img src="https://images.unsplash.com/photo-1553979459-d2229ba7433b" class="card-img-top">
                        <div class="card-body">
                            <h5>Hamburguesa Especial</h5>
                            <p>Carne premium + tocino</p>
                            <p class="price">Q45.00</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        
    </section>

    <?php 
        include_once 'footer.php';
    ?>

    <!-- Bootstrap JS -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/funcion_index_principal.js">
    </script> -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
