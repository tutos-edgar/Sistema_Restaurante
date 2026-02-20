urlPeticiones = urlApi + "datosAuthUsuario.php";
metodoProceso = "validarLogin";

$(document).ready(function() {

    $('#formEnvio').submit(async e => {
        e.preventDefault();

        var alias = document.getElementById("alias").value;
        var password = document.getElementById("password").value;

        if (alias.length == 0 || alias.trim() == "") {
            Swal.fire({
                icon: 'warning',
                title: "Favor de Ingresar un Alias de Usuario",
                showConfirmButton: false,
                timer: tiempoEsperaMensaje
            });

            return;
        }

        if (validarCaracteresEspeciales(alias)) {
            Swal.fire({
                icon: 'warning',
                title: "El Alias contiene caracteres no válidos",
                showConfirmButton: false,
                timer: tiempoEsperaMensaje
            });

            return;
        }

        if (password.length == 0 || password.trim() == "") {
            Swal.fire({
                icon: 'warning',
                title: "Favor de Ingresar una Contraseña",
                showConfirmButton: false,
                timer: tiempoEsperaMensaje
            });

            return;
        }

        if (!validarCantidadTexto(password, limiteCantidadPass)) {
            Swal.fire({
                icon: 'warning',
                title: "La Contraseña debe de tener como minimo 4 caracteres",
                showConfirmButton: false,
                timer: tiempoEsperaMensaje
            });

            return;
        }

        if (validarCaracteresEspeciales(password)) {
            Swal.fire({
                icon: 'warning',
                title: "La Contraseña contiene caracteres no válidos",
                showConfirmButton: false,
                timer: tiempoEsperaMensaje
            });

            return;
        }

        let jsonData = {
            metodo: metodoProceso,
            datoRecibido: {
                id_usuario: idEnvio,
                alias_usuario: alias,
                password_usuario: password,
                apiKey: apiKey
            }
        };

        try {
            showLoading();
            const response = await axios.post(urlPeticiones, jsonData, {
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            if (response.data) {
                if (response.data.success === "true") {
                    Swal.fire({
                        icon: 'success',
                        title: response.data.datos.mensaje || "Acceso Concedido Correctamente",
                        showConfirmButton: false,
                        timer: tiempoEsperaMensaje
                    }).then(() => {
                        window.location.href = response.data.datos.urlPrincipal;
                        $("#formEnvio").trigger("reset");
                    });
                } else {
                    mostrarWarning(response.data.mensaje || "Ocurrió un error al intentar iniciar sesión");
                }
            } else {
                mostrarWarning(response.data.mensaje || "Ocurrió un error al intentar iniciar sesión");
            }

        } catch (error) {
            if (error.response) {
                mostrarError(error.response.data.mensaje || "Error al Intentar Iniciar Sesión");
            } else if (error.request) {
                mostrarError("No se Recibió Respuesta del Servidor");
            } else {
                mostrarError("Error al Realizar la Solicitud");
            }
        } finally {
            hideLoading();
            $("#formEnvio").trigger("reset");
        }

        // showLoading();
        // axios.post(urlPeticiones, jsonData)
        //     .then(response => {

        //         console.log("Respuesta de la API:", response.data);
        //     })
        //     .catch(error => {
        //         if (error.response) {
        //             mostrarError(error.response.data.mensaje || "Error al Intentar Iniciar Sesión");
        //         } else if (error.request) {
        //             mostrarError("No se Recibió Respuesta del Servidor");
        //         } else {
        //             mostrarError("Error al Realizar la Solicitud");
        //         }

        //     }).finally(() => {
        //         hideLoading();
        //     });

        // $.ajax({
        //     url: urlPeticiones,
        //     type: "POST",
        //     data: JSON.stringify(jsonData),
        //     contentType: "application/json",
        //     beforeSend: function() {
        //         // mostrar modal antes de enviar
        //         showLoading();
        //     },
        //     success: function(res) {
        //         hideLoading();
        //         let datos = typeof res === 'string' ? JSON.parse(res) : res;

        //         if (datos.success === "true") {
        //             Swal.fire({
        //                 icon: 'success',
        //                 title: datos.mensaje || "Datos Guardados Correctamente",
        //                 showConfirmButton: false,
        //                 // timer: tiempoEsperaMensaje
        //                 // confirmButtonText: 'Aceptar'
        //                 timer: tiempoEsperaMensaje
        //             }).then(() => {
        //                 // if (result.isConfirmed) {
        //                 //     $("#fromEnvio").trigger("reset");
        //                 //     // window.location.href = "login.php";
        //                 // }
        //                 window.location.href = datos.datos.urlPrincipal;
        //                 $("#fromEnvio").trigger("reset");
        //             });
        //             // window.location.href = "login.php";
        //             // window.location.href = dato.urlPrincipal
        //         } else {
        //             Swal.fire({
        //                 icon: 'warning',
        //                 title: datos.mensaje || "Ocurrió un error 2",
        //                 showConfirmButton: false,
        //                 timer: tiempoEsperaMensaje
        //             }).then(() => {
        //                 window.location.reload();
        //             });
        //         }
        //     },
        //     error: function(xhr, status, error) {
        //         hideLoading();
        //         console.error("Error en la solicitud AJAX:", {
        //             status: status,
        //             error: error,
        //             responseText: xhr.responseText
        //         });
        //         Swal.fire({
        //             icon: 'error',
        //             title: 'ERROR al crear la petición',
        //             showConfirmButton: false,
        //             timer: tiempoEsperaMensaje
        //         });
        //     }
        // });

    });


});