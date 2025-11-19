<?php
    require_once __DIR__ . "/../config/init_config.php";
    $IdUser = "";
    $aliasUsuario = "";
    $rolUsuario = "";
    $respuestaSesion = "";
    // $usuarioValido;
    // if(session_status() == PHP_SESSION_NONE){
    //     return ["success" => "false", "mensaje" => "Sesion No Valida", "dato" => []];
    //     exit;
    // }
   
    if(!isset($_SESSION['UsuarioValido'])){
        $respuesSesion = json_encode(["success" => "false", "error" => "false", "mensaje" => "Usuario No Valido", "dato" => []]);
        echo $respuesSesion;
        exit;
    }

    if(!isset($_SESSION['EstadoSesion']) || $_SESSION['EstadoSesion'] !== true){
        $respuesSesion= json_encode(["success" => "false", "error" => "false", "mensaje" => "Sesion No Activa", "dato" => []]);
        echo $respuesSesion;
        exit;
    }
    
    if(!isset($_SESSION['Rol'])){
        $respuesSesion= json_encode(["success" => "false", "error" => "false",  "success" => "false", "mensaje" => "Rol no Valido", "dato" => []]);
        echo $respuesSesion;
        exit;
    }

    if(isset($_SESSION['UsuarioValido'])){
        if(isset($_SESSION['EstadoSesion']) && $_SESSION['EstadoSesion'] === true){
            $aliasUsuario = $_SESSION['UsuarioValido']['alias'];
            $rolUsuario = $_SESSION['UsuarioValido']['id_rol'];  
            if(isset($_SESSION['Rol'])){
                if($rolUsuario != $_SESSION['Rol']){
                    echo json_encode(["success" => "false", "mensaje" => "Sesion No Valida", "dato" => []]);
                    exit;
                }else{
                    $IdUser = $_SESSION['UsuarioValido']['id_usuario'];
                }
            }            
        }       
    }
  
?>