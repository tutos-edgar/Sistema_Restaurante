urlPetiones = urlApi + "datosPerfilUser.php";
metodoProceso = "actualizarpass";



const togglePasswordNew = document.getElementById('togglePasswordnew');
const passwordNew = document.getElementById('passwordnew');
togglePasswordNew.addEventListener('click', () => {
    const type = passwordNew.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordNew.setAttribute('type', type);
    togglePasswordNew.innerHTML = type === 'password' ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
});


const togglePasswordConfirm = document.getElementById('togglePasswordconfirm');
const passwordConfirm = document.getElementById('passwordconfirm');
togglePasswordConfirm.addEventListener('click', () => {
    const type = passwordConfirm.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordConfirm.setAttribute('type', type);
    togglePasswordConfirm.innerHTML = type === 'password' ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
});


$(document).ready(function() {

    $('#formRegistro').submit(e => {
        e.preventDefault();
        metodoProceso = "actualizarpass";

        let aliasUsuario = $('#alias').val().trim();
        let pass = $('#password').val().trim();
        let passNew = $('#passwordnew').val().trim();
        let confirmNew = $('#passwordconfirm').val().trim();

        if (aliasUsuario == "") {
            Swal.fire({
                icon: 'warning',
                title: "Favor de Ingresar Un Alias o Usuario",
                showConfirmButton: false,
                timer: tiempoEsperaMensaje
            });
            return;
        }

        if (validarCaracteresEspeciales(aliasUsuario)) {
            Swal.fire({
                icon: 'warning',
                title: "El Usuario o Alias contiene caracteres no validos",
                showConfirmButton: false,
                timer: tiempoEsperaMensaje
            });
            return;
        }

        if (pass == "") {
            Swal.fire({
                icon: 'warning',
                title: "Favor de Ingresar una Contraseña",
                showConfirmButton: false,
                timer: tiempoEsperaMensaje
            });
            return;
        }

        if (validarCaracteresEspeciales(pass)) {
            Swal.fire({
                icon: 'warning',
                title: "Contraseña contiene caracteres no validos",
                showConfirmButton: false,
                timer: tiempoEsperaMensaje
            });
            return;
        }

        if (passNew == "") {
            Swal.fire({
                icon: 'warning',
                title: "Favor de Ingresar una Nueva Contraseña",
                showConfirmButton: false,
                timer: tiempoEsperaMensaje
            });
            return;
        }

        if (validarCaracteresEspeciales(passNew)) {
            Swal.fire({
                icon: 'warning',
                title: "La nueva Contraseña contiene caracteres no validos",
                showConfirmButton: false,
                timer: tiempoEsperaMensaje
            });
            return;
        }

        if (confirmNew == "") {
            Swal.fire({
                icon: 'warning',
                title: "Favor de Ingresar una Confirmación de Contraseña",
                showConfirmButton: false,
                timer: tiempoEsperaMensaje
            });
            return;
        }

        if (validarCaracteresEspeciales(confirmNew)) {
            Swal.fire({
                icon: 'warning',
                title: "La nueva Confirmación de Contraseña contiene caracteres no validos",
                showConfirmButton: false,
                timer: tiempoEsperaMensaje
            });
            return;
        }

        if (passNew != confirmNew) {
            Swal.fire({
                icon: 'warning',
                title: "La nueva Contraseña no es Igual que la Confirmacion",
                showConfirmButton: false,
                timer: tiempoEsperaMensaje
            });
            return;
        }

        let jsonData = {
            metodo: metodoProceso,
            datoRecibido: {
                alias: aliasUsuario,
                password: pass,
                passwordnew: passNew,
                passwordconfirm: confirmNew,
                apiKey: apiKey
            }
        };

        $.ajax({
            url: urlPetiones,
            type: "POST",
            data: JSON.stringify(jsonData),
            contentType: "application/json",
            beforeSend: function() {
                showLoading();
            },
            success: function(res) {
                hideLoading();
                let datos = typeof res === 'string' ? JSON.parse(res) : res;

                if (datos.success === "true") {
                    Swal.fire({
                        icon: 'success',
                        title: datos.mensaje || "Datos Guardados Correctamente",
                        showConfirmButton: false,
                        timer: tiempoEsperaMensaje,
                    }).then(() => {
                        // // $("#formRegistro")[0].reset();
                        // window.location.reload();
                        window.location.href = datos.datos;
                        // obtenerDatosPerfil();
                    });

                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: datos.mensaje || "Ocurrió un error en la Petición",
                        showConfirmButton: false,
                        timer: tiempoEsperaMensaje
                    }).then(() => {
                        window.location.reload();
                    });
                }
            },
            error: function(xhr, status, error) {
                hideLoading();
                console.error("Error en la solicitud AJAX:", {
                    status: status,
                    error: error,
                    responseText: xhr.responseText
                });
                Swal.fire({
                    icon: 'error',
                    title: 'ERROR al crear la petición',
                    showConfirmButton: false,
                    timer: tiempoEsperaMensaje
                });
            }
        });

    });

    function obtenerDatosPerfil() {
        motodoProceso = "obtenerdatosperfil";
        let jsonData = {
            metodo: metodoProceso,
            datoRecibido: {
                id_perfil_usuario: IdUser,
                id_usuario: IdUser
            }
        };

        $.ajax({
            url: urlPetiones,
            type: "POST",
            data: JSON.stringify(jsonData),
            contentType: "application/json",
            beforeSend: function() {
                showLoading();
            },
            success: function(res) {
                hideLoading();
                let datos = typeof res === 'string' ? JSON.parse(res) : res;

                if (datos.success === "true") {
                    var perfil = datos.datos[0];
                    nombrePerfil.value = perfil.nombre_perfil || "";
                    apellidoPerfil.value = perfil.apellido_perfil || "";
                    mailPerfil.value = perfil.email_perfil || "";
                    var fecha;
                    if (perfil.fecha_nacimiento) {
                        fecha = perfil.fecha_nacimiento.split(" ")[0]; // toma solo la parte de la fecha
                        fechaNacimiento.value = fecha;
                    } else {
                        fechaNacimiento.value = "";
                    }
                    edadPerfil.value = calcularEdad(perfil.fecha_nacimiento);
                    idPerfil.value = perfil.id_perfil_usuario;
                    idUser.value = perfil.id_usuario;
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: datos.mensaje || "Ocurrió un error 2",
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            },
            error: function(xhr, status, error) {
                hideLoading();
                console.error("Error en la solicitud AJAX:", {
                    status: status,
                    error: error,
                    responseText: xhr.responseText
                });
                Swal.fire({
                    icon: 'error',
                    title: 'ERROR al crear la petición',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    }

    $("#fechaNacimiento").on("change", function() {
        let fecha = $(this).val();
        if (fecha) {
            let edad = calcularEdad(fecha);
            $("#edad").val(edad);
        } else {
            $("#edad").val("0");
        }
    });

    // document.getElementById("fechaNacimiento").addEventListener("change", function() {
    //     let fecha = this.value;
    //     if (fecha) {
    //         let edad = calcularEdad(fecha);
    //         document.getElementById("edad").textContent = edad;
    //     } else {
    //         document.getElementById("edad").textContent = "";
    //     }
    // });

});