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
    <section class="py-5 bg-light">
        <div class="container text-center">
            <h2 class="fw-bold mb-5">🔥 Promociones Activas</h2>

            <div class="row g-4" id="contenidoPromocion">
                <div class="col-md-4">
                    <div class="card shadow border-0">
                        <img src="https://images.unsplash.com/photo-1568901346375-23c9450c58cd" class="card-img-top">
                        <div class="card-body">
                            <h5 class="fw-bold">Combo Clásico</h5>
                            <p>Hamburguesa + Papas + Bebida</p>
                            <span class="price-badge text-white">Q35.00</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow border-0">
                        <img src="https://images.unsplash.com/photo-1606755962773-d324e0a13086" class="card-img-top">
                        <div class="card-body">
                            <h5 class="fw-bold">Doble Carne</h5>
                            <p>Doble carne + Queso + Papas</p>
                            <span class="price-badge text-white">Q45.00</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow border-0">
                        <img src="https://images.unsplash.com/photo-1553979459-d2229ba7433b" class="card-img-top">
                        <div class="card-body">
                            <h5 class="fw-bold">Mega Familiar</h5>
                            <p>4 Hamburguesas + 2 Papas Grandes</p>
                            <span class="price-badge text-white">Q120.00</span>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </section>

    <div class="container py-5 text-center">
        <h2 class="fw-bold mb-5">🔥 Promociones Activas</h2>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card bg-dark text-white shadow">
                    <div class="card-body">
                        <h4>Combo Martes</h4>
                        <p>2 Hamburguesas + Papas + 2 Bebidas</p>
                        <p class="price_promo">Q60.00</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-danger text-white shadow">
                    <div class="card-body">
                        <h4>Promo Pareja</h4>
                        <p>2 Dobles + 1 Papas Grande</p>
                        <p class="price_promo">Q75.00</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-success text-white shadow">
                    <div class="card-body">
                        <h4>Viernes Familiar</h4>
                        <p>4 Hamburguesas + 2 Papas</p>
                        <p class="price_promo">Q110.00</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- <a href="pedido-online.html" class="btn btn-warning btn-lg mt-5">
        Ordenar Ahora -->
    </a>
    </div>

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
