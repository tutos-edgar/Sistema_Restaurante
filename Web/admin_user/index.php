<?php 
include_once __DIR__ . '/../../API/config/config.php';
include_once ROOT_PATH . '/../API/models/FuncionesGenerales.php';

$generales = new FuncionesGenerales();
$current = basename($_SERVER['PHP_SELF']);
include_once 'header_dash.php'; 
?>

<body>

    <?php   
        include_once 'siderbar_lateral.php'; 
    ?>

    <!-- Contenido -->
    <div class="content" id="content">
        
        <?php   
            include_once 'header_bar.php'; 
        ?>

        <!-- Dashboard Content -->
        <div class="content-center" id="content-center">
            <!-- Dashboard Content -->
            <div class="container-fluid mt-4">                
                <?php   
                    include_once 'dash_card.php'; 
                ?>
            </div>
        </div>
    </div>

    <?php   
        include_once 'script_dash.php';
        $generales->ObtenerScriptWeb(11);
    ?>
    
</body>

</html>

