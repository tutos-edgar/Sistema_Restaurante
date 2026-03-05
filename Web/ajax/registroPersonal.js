urlPeticiones = urlApi + "datosPerfilUser.php";
metodoProceso = "listar";

// Función para obtener los datos con Axios
function cargarDatos() {
    // try {
    //     showLoading();
    //     const response = await axios.post(urlPeticiones, jsonData, {
    //         headers: {
    //             'Content-Type': 'application/json'
    //         }
    //     });

    //     if (response.data) {
    //         if (response.data.success === "true") {
    //             Swal.fire({
    //                 icon: 'success',
    //                 title: response.data.datos.mensaje || "Acceso Concedido Correctamente",
    //                 showConfirmButton: false,
    //                 timer: tiempoEsperaMensaje
    //             }).then(() => {
    //                 window.location.href = response.data.datos.urlPrincipal;
    //                 $("#formEnvio").trigger("reset");
    //             });
    //         } else {
    //             mostrarWarning(response.data.mensaje || "Ocurrió un error al intentar iniciar sesión");
    //             if (response.data.urlPincipal) {
    //                 window.location.href = response.data.urlPrincipal;
    //             }
    //         }
    //     } else {
    //         mostrarWarning(response.data.mensaje || "Ocurrió un error al intentar iniciar sesión");
    //         if (response.data.urlPincipal) {
    //             window.location.href = response.data.urlPrincipal;
    //         }
    //     }

    // } catch (error) {
    //     if (error.response) {
    //         mostrarError(error.response.data.mensaje || "Error al Intentar Iniciar Sesión");
    //     } else if (error.request) {
    //         mostrarError("No se Recibió Respuesta del Servidor");
    //     } else {
    //         mostrarError("Error al Realizar la Solicitud");
    //     }
    // } finally {
    //     hideLoading();
    //     $("#formEnvio").trigger("reset");
    // }

    showLoading();
    return axios.post(urlPeticiones, {
            metodo: "listar", // Cambia según tu API
            datoRecibido: {
                apiKey: apiKey
            }
        })
        .then(response => {
            hideLoading();
            const json = response.data;
            // Validaciones similares a tu dataSrc
            if (!json || !json.datos) {
                Swal.fire({
                    position: "center",
                    icon: "error",
                    title: "No hay Datos Disponible",
                    showConfirmButton: false,
                    timer: 1500
                });
                return [];
            }


            if (json.success === false || json.error === true) {
                if (json.message) {
                    mostrarError(json.mensaje || "Ocurrio un Error al Realizar la Petición");
                }
                return [];
            }

            if (json.datos && Array.isArray(json.datos)) {
                return json.datos;
            } else {
                Swal.fire({
                    position: "center",
                    icon: "error",
                    title: "Formato de datos inesperado o datos vacíos",
                    showConfirmButton: false,
                    timer: 1500
                });
                return [];
            }
        })
        .catch(error => {
            hideLoading();
            if (error.response) {
                mostrarError(error.response.data.mensaje || "Error al Intentar Iniciar Sesión");
            } else if (error.request) {
                mostrarError("No se Recibió Respuesta del Servidor");
            } else {
                mostrarError("Error al Realizar la Solicitud");
            }
            return [];
        }).finally(() => {
            hideLoading(); // Ocultar el spinner cuando la petición se complete
        });
}


$(document).ready(function() {

    // Inicializar DataTable con datos cargados por Axios
    //    tablausuarios.ajax.reload(null, false);
    tablaDatos = cargarDatos().then(data => {

        $('#tablaRegistros').DataTable({
            language: {
                lengthMenu: "Mostrar _MENU_ registros",
                zeroRecords: "No se encontraron resultados",
                info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
                infoFiltered: "(filtrado de un total de _MAX_ registros)",
                sSearch: "Buscar:",
                oPaginate: {
                    sFirst: "Primero",
                    sLast: "Último",
                    sNext: "Siguiente",
                    sPrevious: "Anterior"
                },
                sProcessing: "Procesando..."
            },
            data: data, // <-- Aquí pasamos los datos obtenidos por Axios
            columns: [
                { data: "id_perfil_usuario" },
                { data: "documento" },
                { data: "nombre_perfil" },
                { data: "apellido_perfil" },
                { data: "sexo" },
                { data: "email_perfil" },
                { data: "telefono_perfil" },
                {
                    data: "foto_perfil",
                    render: function(data, type, row) {
                        if (data && data !== "") {
                            return `<img src="../../API/img/${data}" alt="Foto Personal" style="width: 100px; height: 70px; border-radius: 5px;" />`;
                        } else {
                            return '<span class="text-muted">Sin Foto</span>';
                            // return `<img src="../../API/img/perfil_user.png" alt="Foto Personal" style="width: 100px; height: 70px; border-radius: 5px;" />`;
                        }
                    }
                },
                // { data: "nombre_estado" },
                { data: "btnEstadoPersona" },
                { data: "direccion_perfil" },
                { data: "botones" },
                // { data: "fecha_nacimiento" },
                // { data: "edad_personal" },
                // { data: "direccion_personal" }, 
                // { data: "botones" }
            ]
        });
    });

    $("#btnNuevo").click(function() {

        opcion = 1; //Dar de Alta

        cambiarTituloModal("  Registrar Empleado", "bi bi-person-badge");
        $("#btnEnviar").text('Guardar');
        $("#formRegistro").trigger("reset");
        $('#fotoPreview').attr('src', '');
        $(".btn-close").css("color", "white");
        $('#fotoPreview').attr('src', '../img/perfil_user.png');
        // $(".modal-header").css("background-color", "black");
        // $(".modal-header").css("color", "white");
        // $('#modalCRUD').modal('show');

    });


    $("#formDatos").submit(e => {

        e.preventDefault();

        var data = new FormData();

        data.append('documento', $("#documento").val());
        data.append('nit', $("#nit").val());
        data.append('nombre_perfil', $("#nombre").val());
        data.append('apellido_perfil', $("#apellido").val());
        data.append('correio_perfil', $("#correo").val());
        data.append('telefono_perfil', $("#telefono").val());
        data.append('fecha_nacimiento', $("#fecha_nacimiento").val());

        var sexos = document.getElementById("sexo");
        var sexo = sexos.options[sexos.selectedIndex].text;
        data.append('sexo', sexo);

        // Archivo
        var file = $('#foto')[0].files[0];
        if (file) {
            data.append('foto', file);
        }

        data.append('padre', $("#padre").val());
        data.append('madre', $("#madre").val());
        data.append('peso', $("#peso").val());
        data.append('raza', $("#raza").val());
        data.append('estado', $("#estado").val());

        var fincas = document.getElementById("fincas");
        var finca = fincas.options[fincas.selectedIndex].value;
        var textoFincaSeleccionado = $('#fincas option:selected').text();
        data.append('nombrefinca', textoFincaSeleccionado);
        data.append('finca', finca);
        data.append('opcion', opcion);
        data.append('idEnvio', idEnvio);

        $.ajax({

            url: "php/procesos/registro_animal.php",
            type: "POST",
            data: data,
            contentType: false,
            processData: false,
            async: false,

            success: function(res) {

                console.log(res);

                if (!res.error) {

                    var dato = JSON.parse(res);

                    if (dato.result == "correcto") {

                        Swal.fire({

                            position: 'center',

                            icon: 'success',

                            title: 'El Animal se Registro Correctamente',

                            showConfirmButton: false,

                            timer: 1500

                        }).then(function() {

                            tablausuarios.ajax.reload(null, false);

                            opcion = "";

                            $("#form_usuario").trigger("reset");

                            $('#modalCRUD').modal('hide');

                        });

                    } else if (dato.result == "no_user") {

                        Swal.fire({

                            icon: 'warning',

                            title: 'NO Se pudo Crear el Registro',

                            showConfirmButton: false,

                            timer: 1500

                        }).then(function() {

                            tablausuarios.ajax.reload(null, false);

                        });

                        return;

                    } else if (dato.result == "error") {

                        Swal.fire({
                            icon: 'error',
                            title: 'Ocurrio un Error al Registrar los Datos',
                            showConfirmButton: false,
                            timer: 1500

                        }).then(function() {

                            tablaclientes.ajax.reload(null, false);
                            $("#form_usuario").trigger("reset");
                            $('#modalCRUD').modal('hide');

                        });

                        return;

                    } else if (dato.result == "incorrecto") {

                        Swal.fire({
                            icon: 'warning',
                            title: dato.mensaje,
                            showConfirmButton: false,
                            timer: 1500

                        }).then(function() {

                            tablaclientes.ajax.reload(null, false);
                            $("#form_usuario").trigger("reset");
                            $('#modalCRUD').modal('hide');
                        });

                        return;

                    } else {

                        $("#form_usuario").trigger("reset");

                    }

                } else {

                    Swal.fire({

                        icon: 'error',

                        title: 'ERROR al Crear la Peticion',

                        showConfirmButton: false,

                        timer: 1500

                    }).then(function() {

                        tablausuarios.ajax.reload(null, false);

                        opcion = "";

                        $("#form_usuario").trigger("reset");

                    });

                }



            }

        });





    });



})


// $(document).ready(function() {

//     alert("este es otro ");

//     $('#labelTerminos').click(function(e) {
//         // Cambiar color del header del modal

//         if ($(e.target).is('a')) {
//             $("#btnCerrarModal").text('Cerrar');
//             $("#btnCerrarModal").css({ 'background-color': '#0b0c42', 'color': '#fff' });
//             $("#form_usuario").trigger("reset");
//             $(".modal-header").css("background-color", "#0b0c42");
//             $(".modal-header").css("color", "#EEEEFAFF");
//             $(".modal-crud").text("Registrar Producto");
//             $('#modalCRUD').modal('show')

//             $('#termsModal .modal-header').css({
//                 'background-color': '#ff0000',
//                 'color': '#fff'
//             });

//             // Abrir el modal usando Bootstrap 5
//             // var termsModal = new bootstrap.Modal(document.getElementById('termsModal'));
//             // termsModal.show();
//         }




//         // Llamada AJAX para obtener datos
//         // $.ajax({
//         //     url: 'ruta_a_tu_php_o_api.php', // Cambia esto por tu PHP o API
//         //     type: 'GET',
//         //     dataType: 'html',
//         //     success: function(response) {
//         //         console.log('Datos recibidos:', response);
//         //         // Si quieres actualizar el contenido del modal
//         //         // $('#termsModal .modal-body').html(response);
//         //     },
//         //     error: function(xhr, status, error) {
//         //         console.error('Error en la petición AJAX:', error);
//         //     }
//         // });

//     });


//     $('#fromEnvio').submit(async e => {
//         e.preventDefault();
//         var nombre = document.getElementById("nombre").value;
//         var apellido = document.getElementById("apellido").value;
//         var mail = document.getElementById("email").value;
//         var alias = document.getElementById("alias").value;
//         var password = document.getElementById("password").value;
//         var confirmPassword = document.getElementById("confirmPassword").value;
//         var terminos = document.getElementById("terminos");

//         if (nombre.length == 0 || nombre.trim() == "") {
//             Swal.fire({
//                 icon: 'warning',
//                 title: "Favor de Ingresar un Nombre",
//                 showConfirmButton: false,
//                 timer: tiempoEsperaMensaje
//             });

//             return;
//         }

//         if (apellido.length == 0 || apellido.trim() == "") {
//             Swal.fire({
//                 icon: 'warning',
//                 title: "Favor de Ingresar un Apellido",
//                 showConfirmButton: false,
//                 timer: tiempoEsperaMensaje
//             });

//             return;
//         }

//         if (mail.length == 0 || mail.trim() == "") {
//             Swal.fire({
//                 icon: 'warning',
//                 title: "Favor de Ingresar un Mail",
//                 showConfirmButton: false,
//                 timer: tiempoEsperaMensaje
//             });

//             return;
//         }

//         if (alias.length == 0 || alias.trim() == "") {
//             Swal.fire({
//                 icon: 'warning',
//                 title: "Favor de Ingresar un Alias de Usuario",
//                 showConfirmButton: false,
//                 timer: tiempoEsperaMensaje
//             });

//             return;
//         }

//         if (validarCaracteresEspeciales(alias)) {
//             Swal.fire({
//                 icon: 'warning',
//                 title: "El Alias contiene caracteres no válidos",
//                 showConfirmButton: false,
//                 timer: tiempoEsperaMensaje
//             });

//             return;
//         }

//         if (!validarCorreo(mail)) {
//             Swal.fire({
//                 icon: 'warning',
//                 title: "Favor de Ingresar un Mail Valido",
//                 showConfirmButton: false,
//                 timer: tiempoEsperaMensaje
//             });

//             return;
//         }

//         if (password.length == 0 || password.trim() == "") {
//             Swal.fire({
//                 icon: 'warning',
//                 title: "Favor de Ingresar una Contraseña",
//                 showConfirmButton: false,
//                 timer: tiempoEsperaMensaje
//             });

//             return;
//         }

//         if (!validarCantidadTexto(password, limiteCantidadPass)) {
//             Swal.fire({
//                 icon: 'warning',
//                 title: "La Contraseña debe de tener como minimo 4 caracteres",
//                 showConfirmButton: false,
//                 timer: tiempoEsperaMensaje
//             });

//             return;
//         }

//         if (validarCaracteresEspeciales(password)) {
//             Swal.fire({
//                 icon: 'warning',
//                 title: "La Contraseña contiene caracteres no válidos",
//                 showConfirmButton: false,
//                 timer: tiempoEsperaMensaje
//             });

//             return;
//         }


//         if (!document.getElementById("terminos").checked) {
//             Swal.fire({
//                 icon: 'warning',
//                 title: "⚠️ Debes aceptar los términos y condiciones",
//                 showConfirmButton: false,
//                 timer: tiempoEsperaMensaje
//             });
//             return;
//         }



//         if (confirmPassword.length == 0 || confirmPassword.trim() == "") {
//             Swal.fire({
//                 icon: 'warning',
//                 title: "Favor de Ingresar una Confrimacion de Contraseña",
//                 showConfirmButton: false,
//                 timer: tiempoEsperaMensaje
//             });

//             return;
//         }

//         if (password !== confirmPassword) {
//             Swal.fire({
//                 icon: 'warning',
//                 title: "❌ Las contraseñas no coinciden",
//                 showConfirmButton: false,
//                 timer: tiempoEsperaMensaje
//             });

//             return;

//         }

//         let jsonData = {
//             metodo: metodoProceso,
//             datoRecibido: {
//                 id_usuario: idEnvio,
//                 nombre_usuario: nombre,
//                 apellido_usuario: apellido,
//                 correo_usuario: mail,
//                 alias_usuario: alias,
//                 password_usuario: password,
//                 confirmacion_pass: confirmPassword,
//                 aceptacion_termino: terminos.checked,
//                 apiKey: apiKey
//             }
//         };

//         $.ajax({
//             url: urlPeticiones,
//             type: "POST",
//             data: JSON.stringify(jsonData),
//             contentType: "application/json",
//             beforeSend: function() {
//                 showLoading();
//             },
//             success: function(res) {
//                 hideLoading();
//                 let dato = typeof res === 'string' ? JSON.parse(res) : res;
//                 if (dato.success === "true") {
//                     Swal.fire({
//                         icon: 'success',
//                         title: dato.mensaje || "Datos Guardados Correctamente",
//                         showConfirmButton: true,
//                         confirmButtonText: 'Aceptar'
//                             // timer: 1500
//                     }).then((result) => {
//                         if (result.isConfirmed) {
//                             $("#fromEnvio").trigger("reset");
//                             window.location.href = "login.php";
//                             return;
//                         }

//                     });

//                 } else {
//                     Swal.fire({
//                         icon: 'warning',
//                         title: dato.mensaje || "Ocurrió un error 2",
//                         showConfirmButton: false,
//                         timer: tiempoEsperaMensaje
//                     }).then(() => {
//                         return;
//                     });
//                 }
//             },
//             error: function(xhr, status, error) {
//                 hideLoading();
//                 console.error("Error en la solicitud AJAX:", {
//                     status: status,
//                     error: error,
//                     responseText: xhr.responseText
//                 });
//                 Swal.fire({
//                     icon: 'error',
//                     title: 'ERROR al crear la petición',
//                     showConfirmButton: false,
//                     timer: tiempoEsperaMensaje
//                 });
//             }
//         });

//     });


// });