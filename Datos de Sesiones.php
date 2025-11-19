
-- Actualizar Ultimo Acceso
UPDATE usuarios
SET ultimo_acceso = NOW(), estado_sesion = 'ACTIVA'
WHERE id = ?;

-- VALIDAR SESION EXPIRADA 15 minutos
SELECT CASE 
         WHEN TIMESTAMPDIFF(MINUTE, ultimo_acceso, NOW()) > 15 THEN 'CERRADA'
         ELSE 'ACTIVA'
       END AS estado_sesion
FROM usuarios
WHERE id = ?;


<!-- VALIDACION SESION EXPIRADA EN PHP -->

<!-- session_start(); -->
$usuario_id = $_SESSION['usuario_id']; // id del usuario logueado
$limite_minutos = 15;

// Conectar a MySQL
$pdo = new PDO("mysql:host=localhost;dbname=tu_db", "usuario", "pass");

// Obtener último acceso
$stmt = $pdo->prepare("SELECT ultimo_acceso FROM usuarios WHERE id = ?");
$stmt->execute([$usuario_id]);
$ultimo = $stmt->fetchColumn();

if ($ultimo) {
    $tiempo_inactivo = (time() - strtotime($ultimo)) / 60; // minutos
    if ($tiempo_inactivo > $limite_minutos) {
        // Cerrar sesión
        $_SESSION = [];
        session_destroy();
        echo "Sesión expirada, por favor inicia sesión de nuevo.";
        exit;
    } else {
        // Actualizar último acceso
        $stmt = $pdo->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?");
        $stmt->execute([$usuario_id]);
    }
}

SESIONES POR NAVEGADOR 
<!-- session_start(); -->
$id_usuario = 1; // ejemplo
$id_sesion = bin2hex(random_bytes(32)); // token único

$_SESSION['id_sesion'] = $id_sesion;
$_SESSION['usuario_id'] = $id_usuario;

// Guardar en BD
$stmt = $pdo->prepare("INSERT INTO sesiones_usuario 
    (id_sesion, id_usuario, navegador, ip, fecha_inicio, ultimo_acceso) 
    VALUES (?, ?, ?, ?, NOW(), NOW())");
$stmt->execute([
    $id_sesion,
    $id_usuario,
    $_SERVER['HTTP_USER_AGENT'],
    $_SERVER['REMOTE_ADDR']
]);
<!-- VALIDA SESIONES POR CADA REQUEST -->
<!-- session_start(); -->
$id_sesion = $_SESSION['id_sesion'] ?? null;

if (!$id_sesion) {
    die("No tienes sesión activa");
}

// Consultar estado en BD
$stmt = $pdo->prepare("SELECT estado FROM sesiones_usuario WHERE id_sesion = ?");
$stmt->execute([$id_sesion]);
$estado = $stmt->fetchColumn();

if ($estado !== 'ACTIVA') {
    session_destroy();
    die("Sesión cerrada desde otro dispositivo");
}

// Actualizar último acceso
$stmt = $pdo->prepare("UPDATE sesiones_usuario SET ultimo_acceso = NOW() WHERE id_sesion = ?");
$stmt->execute([$id_sesion]);


// CERRAR SESION MANUALMENTE
$id_sesion_a_cerrar = 'token_de_la_sesion_firefox';
$stmt = $pdo->prepare("UPDATE sesiones_usuario SET estado = 'CERRADA' WHERE id_sesion = ?");
$stmt->execute([$id_sesion_a_cerrar]);



function getIpPublica() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

$ip = getIpPublica();


CREATE TABLE sesiones_usuario (
    id_sesion VARCHAR(64) PRIMARY KEY,
    id_usuario INT NOT NULL,
    navegador VARCHAR(100),
    ip_publica VARCHAR(50),
    fecha_inicio DATETIME NOT NULL,
    ultimo_acceso DATETIME NOT NULL,
    estado ENUM('ACTIVA','CERRADA') DEFAULT 'ACTIVA',
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);


$stmt = $pdo->prepare("INSERT INTO sesiones_usuario 
    (id_sesion, id_usuario, navegador, ip_publica, fecha_inicio, ultimo_acceso) 
    VALUES (?, ?, ?, ?, NOW(), NOW())");
$stmt->execute([
    $id_sesion,
    $id_usuario,
    $_SERVER['HTTP_USER_AGENT'],
    $ip
]);



<!-- OBTENER GOLOCAILIZACION -->
 $ip = 'IP_DEL_USUARIO';
$geo = @file_get_contents("http://ip-api.com/json/{$ip}");
$geoData = json_decode($geo, true);

$lat = $geoData['lat'] ?? null;
$lon = $geoData['lon'] ?? null;
$ciudad = $geoData['city'] ?? null;
$pais = $geoData['country'] ?? null;

<!-- MOSTRAR EN GOOGLE MAPS -->

<div id="map" style="width: 100%; height: 400px;"></div>

<script>
function initMap() {
    var ubicacion = { lat: <?= $lat ?>, lng: <?= $lon ?> };
    var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 10,
        center: ubicacion
    });
    var marker = new google.maps.Marker({
        position: ubicacion,
        map: map,
        title: 'Usuario está aquí'
    });
}
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=TU_API_KEY&callback=initMap" async defer></script>



<?php
// session_start();
$id_sesion = $_SESSION['id_sesion'];

$conexion = new mysqli("localhost", "root", "", "mi_basedatos");

// Verificar estado en la BD
$sql = "SELECT estado FROM sesiones WHERE id_sesion = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $id_sesion);
$stmt->execute();
$stmt->bind_result($estado);
$stmt->fetch();

if ($estado !== "ACTIVA") {
    // Destruir la sesión local si está expirada
    session_unset();
    session_destroy();
    echo "Sesión expirada. Por favor inicia de nuevo.";
    exit();
} else {
    // Refrescar último movimiento
    $sql = "UPDATE sesiones SET ultimo_movimiento = NOW() WHERE id_sesion = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $id_sesion);
    $stmt->execute();
}
?>


2. Detectar si no está en la pestaña activa de tu web

Con JavaScript puedes detectar visibilidad de la pestaña.
Por ejemplo:

<script>
let timer;
const maxInactividad = 5 * 60 * 1000; // 5 minutos

function cerrarSesion() {
  fetch("logout.php"); // llamas a tu PHP para destruir sesión
  alert("Tu sesión ha expirado por inactividad.");
  window.location.href = "login.php";
}

// Reinicia el contador cada vez que hay actividad
function reiniciarContador() {
  clearTimeout(timer);
  timer = setTimeout(cerrarSesion, maxInactividad);
}

// Detectar actividad del usuario
document.onmousemove = reiniciarContador;
document.onkeypress = reiniciarContador;
document.onclick = reiniciarContador;
document.onscroll = reiniciarContador;

// Detectar si la pestaña está oculta (usuario cambió de tab o minimizó)
document.addEventListener("visibilitychange", function() {
  if (document.hidden) {
    // empieza a contar tiempo si la pestaña está oculta
    reiniciarContador();
  } else {
    // cuando vuelve, reinicia contador
    reiniciarContador();
  }
});

// iniciar el contador cuando cargue la página
reiniciarContador();
</script>


Con esto:
✅ Si el usuario no toca nada → se cierra sesión después de 5 min.
✅ Si cambia de pestaña → se activa el mismo temporizador.
✅ Si regresa antes de que expire → el contador se reinicia.

detectar si se paso a otra pagina -
<script>
document.addEventListener("visibilitychange", function() {
  if (document.hidden) {
    console.log("El usuario se fue a otra pestaña o minimizó la ventana 🚪");
  } else {
    console.log("El usuario volvió a tu página 👋");
  }
});
</script>

Ejemplo con temporizador para expirar sesión si está en otra pestaña
<script>
let timer;
const tiempoMax = 2 * 60 * 1000; // 2 minutos

function cerrarSesion() {
  fetch("logout.php", { method: "POST" })
    .then(() => {
      alert("Tu sesión expiró porque dejaste de usar la pestaña.");
      window.location.href = "login.php";
    });
}

document.addEventListener("visibilitychange", function() {
  if (document.hidden) {
    // Usuario se fue de la pestaña → empezar cuenta regresiva
    timer = setTimeout(cerrarSesion, tiempoMax);
  } else {
    // Usuario regresó antes de que expire → cancelar temporizador
    clearTimeout(timer);
  }
});
</script>



