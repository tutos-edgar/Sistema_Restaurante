<?php
    require_once __DIR__ . '/../config/init_config.php';
    require_once __DIR__ . '/../Interfaces/IGenerarCifrado.php';
    require_once __DIR__ . '/../cifrado/cifrado_AES.php';
    require_once __DIR__ . '/../Services/GenerarCifradoService.php';

    $IdUser = "";
    $aliasUsuario = "";
    $rolUsuario = "";
    $fotoUsuario = "";

    $encriptado = new GenerarCifradoService(new CifradoAES());
    // print_r($_SESSION);
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
                    session_destroy();
                    header("Location: " .URLPRINCIPAL);
                }else{
                    $IdUser = $_SESSION['UsuarioValido']['id_usuario'];
                    if(!isset($_SESSION['UsuarioValido']['foto_usuario']) || empty($_SESSION['UsuarioValido']['foto_usuario'])){
                        $fotoUsuario = 'https://i.pravatar.cc/40';
                    }else{
                        $fotoUsuario = $_SESSION['UsuarioValido']['foto_usuario'];
                    }                        
                }
            }
            
        }       
    }


   
?>