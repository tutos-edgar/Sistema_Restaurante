<?php
// Asegúrate de que no haya espacios, líneas en blanco o HTML antes de esto
// header("Location: ../index.php");
// exit; // Siempre usar exit después de header para detener la ejecución
include_once __DIR__ . '/../../API/middleware/validaSesion.php';
include_once __DIR__ . '/header_dashboard.php';
?>


<body>

    <?php
        include_once __DIR__ . '/nav_bar_dashboard.php';
        include_once __DIR__ . '/side_bar_dashboard.php';
    ?>


    <!-- CONTENT -->
    <main class="content">

        <div id="dashboard" class="seccion">
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card p-3 text-center">
                        <h6>Total Ventas</h6>
                        <h3 id="totalVentas">$0</h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-3 text-center">
                        <h6>Pedidos Hoy</h6>
                        <h3 id="pedidosHoy">0</h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-3 text-center">
                        <h6>Clientes</h6>
                        <h3 id="totalClientes">0</h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-3 text-center">
                        <h6>Productos</h6>
                        <h3 id="totalProductos">0</h3>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm p-4">
                <h5 class="mb-3">Últimos Pedidos</h5>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Cliente</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>#105</td>
                                <td>Juan Pérez</td>
                                <td>Q65.00</td>
                                <td><span class="badge bg-warning">Pendiente</span></td>
                                <td>Hoy</td>
                            </tr>
                            <tr>
                                <td>#104</td>
                                <td>Ana López</td>
                                <td>Q80.00</td>
                                <td><span class="badge bg-success">Entregado</span></td>
                                <td>Ayer</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card p-4">
                <canvas id="graficoVentas"></canvas>
            </div>
        </div>



        <!-- DASHBOARD -->
        <section id="dashboard" class="seccion">
            <div class="row g-3 mb-4">
                <div class="col-md-3 col-6">
                    <div class="card shadow-sm text-center p-3">
                        <h6>Total Ventas</h6>
                        <h4>$12,450</h4>
                    </div>
                </div>

                <div class="col-md-3 col-6">
                    <div class="card shadow-sm text-center p-3">
                        <h6>Pedidos Hoy</h6>
                        <h4>34</h4>
                    </div>
                </div>

                <div class="col-md-3 col-6">
                    <div class="card shadow-sm text-center p-3">
                        <h6>Clientes</h6>
                        <h4>120</h4>
                    </div>
                </div>

                <div class="col-md-3 col-6">
                    <div class="card shadow-sm text-center p-3">
                        <h6>Productos</h6>
                        <h4>45</h4>
                    </div>
                </div>
            </div>
        </section>

        <!-- PEDIDOS -->
        <section id="pedidos" class="seccion d-none">
            <h4>Pedidos</h4>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Ana López</td>
                            <td>$45</td>
                            <td><span class="badge bg-warning">Pendiente</span></td>
                            <td>Hoy</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

    </main>

    <?php

        include_once 'footer.php';
    ?>

      

</body>

</html> 