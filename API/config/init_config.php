<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ .'/../config/config.php';
require_once __DIR__.'/../models/FuncionesGenerales.php';
require_once __DIR__.'/../models/Parametros.php';
date_default_timezone_set('America/Guatemala');
?>