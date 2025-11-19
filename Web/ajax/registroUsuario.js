urlPeticiones = urlApi + "datosUsuarios.php";
metodoProceso = "registrar";

$(document).ready(function() {

    $('#labelTerminos').click(function(e) {
        // Cambiar color del header del modal

        if ($(e.target).is('a')) {
            $("#btnCerrarModal").text('Cerrar');
            $("#btnCerrarModal").css({ 'background-color': '#0b0c42', 'color': '#fff' });
            $("#form_usuario").trigger("reset");
            $(".modal-header").css("background-color", "#0b0c42");
            $(".modal-header").css("color", "#EEEEFAFF");
            $(".modal-crud").text("Registrar Producto");
            $('#modalCRUD').modal('show')

            $('#termsModal .modal-header').css({
                'background-color': '#ff0000',
                'color': '#fff'
            });

            // Abrir el modal usando Bootstrap 5
            // var termsModal = new bootstrap.Modal(document.getElementById('termsModal'));
            // termsModal.show();
        }




        // Llamada AJAX para obtener datos
        // $.ajax({
        //     url: 'ruta_a_tu_php_o_api.php', // Cambia esto por tu PHP o API
        //     type: 'GET',
        //     dataType: 'html',
        //     success: function(response) {
        //         console.log('Datos recibidos:', response);
        //         // Si quieres actualizar el contenido del modal
        //         // $('#termsModal .modal-body').html(response);
        //     },
        //     error: function(xhr, status, error) {
        //         console.error('Error en la petición AJAX:', error);
        //     }
        // });

    });


    $('#fromEnvio').submit(async e => {
        e.preventDefault();
        var nombre = document.getElementById("nombre").value;
        var apellido = document.getElementById("apellido").value;
        var mail = document.getElementById("email").value;
        var alias = document.getElementById("alias").value;
        var password = document.getElementById("password").value;
        var confirmPassword = document.getElementById("confirmPassword").value;
        var terminos = document.getElementById("terminos");

        if (nombre.length == 0 || nombre.trim() == "") {
            Swal.fire({
                icon: 'warning',
                title: "Favor de Ingresar un Nombre",
                showConfirmButton: false,
                timer: tiempoEsperaMensaje
            });

            return;
        }

        if (apellido.length == 0 || apellido.trim() == "") {
            Swal.fire({
                icon: 'warning',
                title: "Favor de Ingresar un Apellido",
                showConfirmButton: false,
                timer: tiempoEsperaMensaje
            });

            return;
        }

        if (mail.length == 0 || mail.trim() == "") {
            Swal.fire({
                icon: 'warning',
                title: "Favor de Ingresar un Mail",
                showConfirmButton: false,
                timer: tiempoEsperaMensaje
            });

            return;
        }

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

        if (!validarCorreo(mail)) {
            Swal.fire({
                icon: 'warning',
                title: "Favor de Ingresar un Mail Valido",
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


        if (!document.getElementById("terminos").checked) {
            Swal.fire({
                icon: 'warning',
                title: "⚠️ Debes aceptar los términos y condiciones",
                showConfirmButton: false,
                timer: tiempoEsperaMensaje
            });
            return;
        }



        if (confirmPassword.length == 0 || confirmPassword.trim() == "") {
            Swal.fire({
                icon: 'warning',
                title: "Favor de Ingresar una Confrimacion de Contraseña",
                showConfirmButton: false,
                timer: tiempoEsperaMensaje
            });

            return;
        }

        if (password !== confirmPassword) {
            Swal.fire({
                icon: 'warning',
                title: "❌ Las contraseñas no coinciden",
                showConfirmButton: false,
                timer: tiempoEsperaMensaje
            });

            return;

        }

        let jsonData = {
            metodo: metodoProceso,
            datoRecibido: {
                id_usuario: idEnvio,
                nombre_usuario: nombre,
                apellido_usuario: apellido,
                correo_usuario: mail,
                alias_usuario: alias,
                password_usuario: password,
                confirmacion_pass: confirmPassword,
                aceptacion_termino: terminos.checked,
                apiKey: apiKey
            }
        };

        $.ajax({
            url: urlPeticiones,
            type: "POST",
            data: JSON.stringify(jsonData),
            contentType: "application/json",
            beforeSend: function() {
                showLoading();
            },
            success: function(res) {
                hideLoading();
                let dato = typeof res === 'string' ? JSON.parse(res) : res;
                if (dato.success === "true") {
                    Swal.fire({
                        icon: 'success',
                        title: dato.mensaje || "Datos Guardados Correctamente",
                        showConfirmButton: true,
                        confirmButtonText: 'Aceptar'
                            // timer: 1500
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $("#fromEnvio").trigger("reset");
                            window.location.href = "login.php";
                            return;
                        }

                    });

                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: dato.mensaje || "Ocurrió un error 2",
                        showConfirmButton: false,
                        timer: tiempoEsperaMensaje
                    }).then(() => {
                        return;
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


});