urlPetiones = urlApi + "datosPrincipales.php";
metodoProceso = "obtenerDatosPrincipales";

$(document).ready(function() {


    obtenerDatosPrincipales();

    // $('#formRegistro').submit(e => {
    //     e.preventDefault();
    //     metodoProceso = "registrar";

    //     if (nombrePerfil.value == "") {
    //         Swal.fire({
    //             icon: 'warning',
    //             title: "Favor de Ingresar Un Nombre de Perfil",
    //             showConfirmButton: false,
    //             timer: tiempoEsperaMensaje
    //         });
    //         return;
    //     }

    //     if (validarCaracteresEspeciales(nombrePerfil.value)) {
    //         Swal.fire({
    //             icon: 'warning',
    //             title: "El Nombre de Perfil contiene caracteres no validos",
    //             showConfirmButton: false,
    //             timer: tiempoEsperaMensaje
    //         });
    //         return;
    //     }

    //     if (apellidoPerfil.value == "") {
    //         Swal.fire({
    //             icon: 'warning',
    //             title: "Favor de Ingresar un Apellido de Perfil",
    //             showConfirmButton: false,
    //             timer: tiempoEsperaMensaje
    //         });
    //         return;
    //     }

    //     if (validarCaracteresEspeciales(apellidoPerfil.value)) {
    //         Swal.fire({
    //             icon: 'warning',
    //             title: "El Nombre de Perfil contiene caracteres no validos",
    //             showConfirmButton: false,
    //             timer: tiempoEsperaMensaje
    //         });
    //         return;
    //     }

    //     if (mailPerfil.value == "") {
    //         Swal.fire({
    //             icon: 'warning',
    //             title: "Favor de Ingresar un Correo de Perfil",
    //             showConfirmButton: false,
    //             timer: tiempoEsperaMensaje
    //         });
    //         return;
    //     }

    //     if (validarCorreo(mailPerfil.value) == false) {
    //         Swal.fire({
    //             icon: 'warning',
    //             title: "Favor de Ingresar un Correo Valido",
    //             showConfirmButton: false,
    //             timer: tiempoEsperaMensaje
    //         });
    //         return;
    //     }

    //     let jsonData = {
    //         metodo: metodoProceso,
    //         datoRecibido: {
    //             id_usuario: $("#idUser").val(),
    //             id_perfil_usuario: $("#idPerfil").val(),
    //             nombre_perfil: $("#nombre").val(),
    //             apellido_perfil: $("#apellido").val(),
    //             correo_perfil: $("#correo").val(),
    //             fecha_nacimiento: $("#fechaNacimiento").val()
    //         }
    //     };

    //     $.ajax({
    //         url: urlPetiones,
    //         type: "POST",
    //         data: JSON.stringify(jsonData),
    //         contentType: "application/json",
    //         beforeSend: function() {
    //             showLoading();
    //         },
    //         success: function(res) {
    //             hideLoading();
    //             let datos = typeof res === 'string' ? JSON.parse(res) : res;

    //             if (datos.success === "true") {
    //                 Swal.fire({
    //                     icon: 'success',
    //                     title: datos.mensaje || "Datos Guardados Correctamente",
    //                     showConfirmButton: false,
    //                     timer: tiempoEsperaMensaje,
    //                 }).then(() => {
    //                     // $("#formRegistro")[0].reset();
    //                     window.location.reload();

    //                     // obtenerDatosPerfil();
    //                 });

    //             } else {
    //                 Swal.fire({
    //                     icon: 'warning',
    //                     title: datos.mensaje || "Ocurrió un error en la Petición",
    //                     showConfirmButton: false,
    //                     timer: tiempoEsperaMensaje
    //                 }).then(() => {
    //                     window.location.reload();
    //                 });
    //             }
    //         },
    //         error: function(xhr, status, error) {
    //             hideLoading();
    //             console.error("Error en la solicitud AJAX:", {
    //                 status: status,
    //                 error: error,
    //                 responseText: xhr.responseText
    //             });
    //             Swal.fire({
    //                 icon: 'error',
    //                 title: 'ERROR al crear la petición',
    //                 showConfirmButton: false,
    //                 timer: tiempoEsperaMensaje
    //             });
    //         }
    //     });

    // });

    function obtenerDatosPrincipales() {
        motodoProceso = "obtenerDatosPrincipales";
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
                    $("#conteGeneral").html('');
                    $("#conteGeneral").html(res.datos.datos);
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