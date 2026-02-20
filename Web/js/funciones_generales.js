// FUNCIONES PARA EL LOGIN
// Mostrar / Ocultar contraseña

var urlApi = "http://localhost:5080/dashboard/proyectos/Sistema_Restaurante/API/view/";
var metodoProceso = "";
var urlPeticiones = "";
var idEnvio;
var limiteCantidadPass = 4;
var tiempoEsperaMensaje = 2000;

var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
})

function scrollToSection(id) {
    document.getElementById(id).scrollIntoView({
        behavior: 'smooth'
    });
}

function togglePasswordVisibility(tooglepass) {
    if (tooglepass) {
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        togglePassword.addEventListener('click', () => {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            togglePassword.innerHTML = type === 'password' ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
        });
    }
}


function convertirImagenABase64(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = () => resolve(reader.result); // base64 completo
        reader.onerror = reject;
        reader.readAsDataURL(file);
    });
}


function validarCorreo(mail) {

    let regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!regex.test(mail)) {
        // alert("❌ Correo inválido");
        return false;
    }
    //   alert("✅ Correo válido");
    return true;
}

function validarCantidadTexto(texto, min) {

    if (texto.length < min) {
        return false;
    }
    //   if (texto.length > max) {
    //     alert("❌ El texto no puede superar " + max + " caracteres");
    //     return false;
    //   }
    return true;
}


function validarCaracteresEspeciales(texto) {

    let regex = /^[^\"'\/\\\\\!\=\{\}\>\<\|\°\;\(\\^)]+$/

    if (!regex.test(texto)) {
        // alert("❌ El texto contiene caracteres especiales no permitidos");
        return true;
    }

    // alert("✅ Texto válido: " + texto);
    return false;
}


function showLoading() {
    $("#loadingModal").css("display", "flex");
    $("#loadingModal").css("z-index", 9999);
}

function hideLoading() {
    $("#loadingModal").hide();
}

function calcularEdad(fechaNacimiento) {
    if (!fechaNacimiento) return null; // si está vacío o null

    let hoy = new Date();
    let nacimiento = new Date(fechaNacimiento);

    let edad = hoy.getFullYear() - nacimiento.getFullYear();
    let mes = hoy.getMonth() - nacimiento.getMonth();

    // si todavía no cumplió años este año
    if (mes < 0 || (mes === 0 && hoy.getDate() < nacimiento.getDate())) {
        edad--;
    }

    return edad;
}

function mostrarError(mensaje) {
    Swal.fire({
        icon: 'error',
        title: mensaje,
        showConfirmButton: false,
        timer: tiempoEsperaMensaje
    });
}


function mostrarWarning(mensaje) {
    Swal.fire({
        icon: 'warning',
        title: mensaje,
        showConfirmButton: false,
        timer: tiempoEsperaMensaje
    });
}