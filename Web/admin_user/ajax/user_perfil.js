urlPetiones = urlApi + "datosPerfilUser.php";
metodoProceso = "obtenerdatosperfil";

$(document).ready(function() {

    nombrePerfil = document.getElementById("nombre");
    apellidoPerfil = document.getElementById("apellido");
    mailPerfil = document.getElementById("correo");
    edadPerfil = document.getElementById("edad");
    fechaNacimiento = document.getElementById("fechaNacimiento");
    idPerfil = document.getElementById("idPerfil");
    idUser = document.getElementById("idUser");
    // document.getElementById("bio").value = usuario.bio;
    // fotoPerfil = document.querySelector(".profile-img").src = usuario.foto;

    obtenerDatosPerfil();

    $('#formRegistro').submit(e => {
        e.preventDefault();
        metodoProceso = "registrar";

        if (nombrePerfil.value == "") {
            Swal.fire({
                icon: 'warning',
                title: "Favor de Ingresar Un Nombre de Perfil",
                showConfirmButton: false,
                timer: tiempoEsperaMensaje
            });
            return;
        }

        if (validarCaracteresEspeciales(nombrePerfil.value)) {
            Swal.fire({
                icon: 'warning',
                title: "El Nombre de Perfil contiene caracteres no validos",
                showConfirmButton: false,
                timer: tiempoEsperaMensaje
            });
            return;
        }

        if (apellidoPerfil.value == "") {
            Swal.fire({
                icon: 'warning',
                title: "Favor de Ingresar un Apellido de Perfil",
                showConfirmButton: false,
                timer: tiempoEsperaMensaje
            });
            return;
        }

        if (validarCaracteresEspeciales(apellidoPerfil.value)) {
            Swal.fire({
                icon: 'warning',
                title: "El Nombre de Perfil contiene caracteres no validos",
                showConfirmButton: false,
                timer: tiempoEsperaMensaje
            });
            return;
        }

        if (mailPerfil.value == "") {
            Swal.fire({
                icon: 'warning',
                title: "Favor de Ingresar un Correo de Perfil",
                showConfirmButton: false,
                timer: tiempoEsperaMensaje
            });
            return;
        }

        if (validarCorreo(mailPerfil.value) == false) {
            Swal.fire({
                icon: 'warning',
                title: "Favor de Ingresar un Correo Valido",
                showConfirmButton: false,
                timer: tiempoEsperaMensaje
            });
            return;
        }

        let jsonData = {
            metodo: metodoProceso,
            datoRecibido: {
                id_usuario: $("#idUser").val(),
                id_perfil_usuario: $("#idPerfil").val(),
                nombre_perfil: $("#nombre").val(),
                apellido_perfil: $("#apellido").val(),
                correo_perfil: $("#correo").val(),
                fecha_nacimiento: $("#fechaNacimiento").val()
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
                        // $("#formRegistro")[0].reset();
                        window.location.reload();

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