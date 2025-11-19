<?php
    require_once __DIR__ . '/../config/init_config.php';
    $IdUser = "";
    $aliasUsuario = "";
    $rolUsuario = "";
    if(!isset($_SESSION['UsuarioValido'])){
        header("Location: " .URLPRINCIPAL);
        exit;
    }

    if(!isset($_SESSION['EstadoSesion']) || $_SESSION['EstadoSesion'] !== true){
        header("Location: " .URLPRINCIPAL);
        exit;
    }
    
    if(!isset($_SESSION['Rol'])){
        header("Location: " .URLPRINCIPAL);
        exit;
    }
    
    if(isset($_SESSION['UsuarioValido'])){
        if(isset($_SESSION['EstadoSesion']) && $_SESSION['EstadoSesion'] === true){            
            $aliasUsuario = $_SESSION['UsuarioValido']['alias'];
            $rolUsuario = $_SESSION['UsuarioValido']['id_rol'];
            if(isset($_SESSION['Rol'])){
                if($rolUsuario != $_SESSION['Rol']){
                    header("Location: " .URLPRINCIPAL);
                }else{
                    $IdUser = $_SESSION['UsuarioValido']['id_usuario'];
                }
            }
            
        }       
    }
  
?>