<!DOCTYPE html>
<html lang="es">

<?php 
// include_once __DIR__ . '/../../API/middleware/validaSesion.php';
$generales->ObtenerEstilosWeb(3);
$current = basename($_SERVER['PHP_SELF']);
// if($current != "menu_generar_vistas.php" && $current != "vista_video.php"){
//     echo '<script>var apiKey ="'.TOKENWEB.'";</script>';
// }
// echo '<script>var IdUser = "'.$IdUser.'";</script>';
// echo '<script>localStorage.setItem("tipoSeleccionado", "");</script>';

?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Iconos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>var tooglepass = false;</script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="../css/estyle_dashboard.css" rel="stylesheet">
    <script>var tooglepass = false;</script>
    
</head>