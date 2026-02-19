<?php 
$uri  = rtrim(dirname(dirname($_SERVER['PHP_SELF'])), '/\\');
define('DOMAIN', 'www.tudominio.com');
define('HOSTNAME', $_SERVER['HTTP_HOST']);
define('URLPRINCIPAL',  "http://".HOSTNAME.$uri."/");
define('URLINICIAL',  "http://".HOSTNAME.$uri."/Web/");
define('URLADMIN',  "http://".HOSTNAME.$uri."/admin_dashboard/");
define('URLUSER',  "http://".HOSTNAME.$uri."/admin_user/");
define('TOKENWEB', 'QMNg6HwgUBJsIhPognlI133uGRGpBJntuhexhRyLf9aJeAhr2T4oegJJUL49');
define('CANTIDADLIMITEPASS', 4);
define('TIEMPOVENCIMIENTOSESION', 4);
define('INTENTOSLOGIN', 5);
define('TIEMPOESPERABLOQUEOSESION', 5);
define('TIEMPOEXPIRASESIONLOGIN', 30); //MINUTOS
define('LIMITES_URL_VIDEO', 5);
define('LIMITES_URL_CANAL', 2);
define('KEY_SECRET_JWT', 'sistema_restaurante_jwt_secret');
// define('KEY_API_YOTUBE', "AIzaSyBCogkb0qgaiW_70I9_xZCuteaaVh9eKLI");
// define('KEY_API_YOTUBE', "AIzaSyBe5Yf5B41L5cOKczaZKHUyp4jkRfRAxY0");
define('KEY_API_YOTUBE', "AIzaSyCrzGxQOGfRy9CIJsQiheX1KtFdx7XDhFM");

define("ACCESS_TIME", 900); // 15 min
define("REFRESH_TIME", 604800); // 7 días

enum EstadoUsuario: int {
    case SIN_ACCESO = 0;
    case ACTIVO = 1;
    case INACTIVO = 2;
    case SUSPENDIDO = 3;
    case BLOQUEADO_X_INTENTOS = 4;
    case BLOQUEADO_TEMPORALMENTE = 5;
    case BLOQUEADO_X_INACTIVIDAD = 6;
}

enum ParametrosTabla: int {
    case INTENTOS_SESSION = 1;
    case CANTIDAD_PASSWORD = 2;
    case TIEMPO_ESPERA_BLOQUEO_SESSION = 3;
    case BLOQUEADO_X_INTENTOS = 4;
    case BLOQUEADO_TEMPORALMENTE = 5;
    case TIEMPO_SESSION_USUARIO = 6;
    case LIMITE_URL_VIDEO = 7;
    case LIMITE_URL_CANAL = 8;
}

enum RolesUsuarios: int {
    case ADMINISTRADOR  = 1;
    case USUARIO = 2;
}

function ObtenerEstadoUsuario($estado){
    switch($estado){
        case EstadoUsuario::SIN_ACCESO->value:
            return "El usuario no tiene Acceso al Sistema";
        case EstadoUsuario::ACTIVO->value:
            return "El usuario Si esta Activo";
        case EstadoUsuario::INACTIVO->value:
            return "El usuario No esta Activo";
        case EstadoUsuario::SUSPENDIDO->value:
            return "El usuario esta suspendido";
        case EstadoUsuario::BLOQUEADO_X_INTENTOS->value:
            return "El usuario esta Bloqueado por varios intentos";
        case EstadoUsuario::BLOQUEADO_TEMPORALMENTE->value:
            return "El usuario esta Bloqueado Temporalmente";
        case EstadoUsuario::BLOQUEADO_X_INACTIVIDAD->value:
            return "El usuario esta Bloqueado Por Inactividad";
        default:
            return "Estado del Usuario es Desconocido";
    }
}


enum TipoUsuariosTabla: int {
    case NORMAL = 0;
    case CLASICO = 1;
    case PROFESIONAL = 2;
    case PLATINIUM = 3;
    case PREMIUN = 4;
    case ORO = 5;
}

$paginasCanal = ['menu_canal.php', 'ingresar_canal.php', 'lista_de_canales.php'];
$paginasVideo  = ['menu_video.php', 'ingresar_video.php', 'lista_de_videos.php'];
$paginasConfirguracion  = ['menu_configuracion_user.php', 'menu_perfil_user.php', 
'menu_cambiar_password.php', 'configurar_antipishing.php', 'configurar_usuario.php', 
'configurar_token_recuperacion.php', 'configurar_captcha.php', 
'configurar_recuperacion.php'];
$paginasNotificaiones  = ['menu_notificaciones_user.php', 'notificaiones_usuario.php'];
// echo json_encode(TOKENWEB);
?>