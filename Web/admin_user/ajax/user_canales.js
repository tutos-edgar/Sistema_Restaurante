urlPetiones = urlApi + "datosUsuariosCanales.php";
metodoProceso = "registrar";

$(document).ready(function() {

    nombreCanal = document.getElementById("nombre");
    urlCanal = document.getElementById("url");
    descripcionCanal = document.getElementById("descripcion");

    // document.getElementById("bio").value = usuario.bio;
    // fotoPerfil = document.querySelector(".profile-img").src = usuario.foto;
    $(document).ready(function() {

        var table = $('#tablaDatos').DataTable({
            responsive: true,
            pageLength: 5,
            lengthMenu: [5, 10, 25],
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
                paginate: {
                    previous: "<<",
                    next: ">>"
                },
                "sProcessing": "Procesando...",
            },
            ajax: {
                url: urlPetiones,
                method: 'POST',
                contentType: 'application/json',
                beforeSend: function() {
                    showLoading();
                },
                data: function(d) {
                    return JSON.stringify({
                        metodo: "listar",
                        apiKey: apiKey,
                    });
                },
                dataSrc: function(json) {
                    hideLoading();
                    console.log(json);
                    if (!json) {
                        Swal.fire({
                            position: "center",
                            icon: "error",
                            title: "No hay Datos Disponible",
                            showConfirmButton: false,
                            timer: 1500

                        });
                        return [];
                    }

                    if (json.error === true) {
                        // console.error("Error del servidor:", json.message || "Error desconocido");
                        return [];
                    }

                    if (json && json.success) {
                        if (json.success === false) {
                            return [];
                        }
                    }

                    // Verificamos si hay datos y si la propiedad 'datos' existe
                    if (json && json.datos && Array.isArray(json.datos)) {
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
                    return [];
                },
                complete: function() {
                    hideLoading();
                    return [];
                },
                error: function(xhr, status, error) {
                    // console.error("Error en la solicitud AJAX:", {
                    //     status: status,
                    //     error: error,
                    //     responseText: xhr.responseText
                    // });

                    hideLoading();
                    return [];
                }
            },
            columns: [
                { data: "id_canal_youtube" },
                { data: "nombre_canal" },
                { data: "descripcion_canal" },
                { data: "url_canal" },
                { data: "suscriptores_count" },
                // {
                //     data: "codigo_barra",
                //     render: function(data, type, row) {
                //         // Verifica que haya una ruta válida
                //         if (data && data !== "") {
                //             return `<img src="${data}" alt="Código de Barras" style="width: 100px; height: 70px; border-radius: 5px;" />`;
                //         } else {
                //             return '<span class="text-muted">No generado</span>';
                //         }
                //     }
                // },
                { data: "botones" },
            ]

        });

        // Filtro por tipo
        $('#filterType').on('change', function() {
            var val = $(this).val();
            table.column(1).search(val).draw();
        });

        $('#formRegistro').submit(e => {
            e.preventDefault();

            if (nombreCanal.value == "") {
                Swal.fire({
                    icon: 'warning',
                    title: "Favor de Ingresar Un Nombre de Canal",
                    showConfirmButton: false,
                    timer: tiempoEsperaMensaje
                });
                return;
            }

            if (validarCaracteresEspeciales(nombreCanal.value)) {
                Swal.fire({
                    icon: 'warning',
                    title: "El Nombre de Canal contiene caracteres no validos",
                    showConfirmButton: false,
                    timer: tiempoEsperaMensaje
                });
                return;
            }

            if (urlCanal.value == "") {
                Swal.fire({
                    icon: 'warning',
                    title: "Favor de Ingresar una Url del Canal",
                    showConfirmButton: false,
                    timer: tiempoEsperaMensaje
                });
                return;
            }

            let jsonData = {
                metodo: metodoProceso,
                datoRecibido: {
                    id_canal_youtube: idEnvio,
                    nombre_canal: $("#nombre").val(),
                    url_canal: $("#url").val(),
                    descripcion: $("#descripcion").val(),
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
                            $("#formRegistro")[0].reset();
                            $('#modalRegistroCanal').modal('hide');
                            // window.location.reload();

                            // obtenerDatosPerfil();
                        });
                        table.ajax.reload(null, false);

                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: datos.mensaje || "No se pudo realizar la Acción",
                            showConfirmButton: false,
                            timer: tiempoEsperaMensaje
                        }).then(() => {
                            // window.location.reload();
                            $("#formRegistro")[0].reset();
                            $('#modalRegistroCanal').modal('hide');
                            return;
                            // obtenerDatosPerfil();
                        });
                        table.ajax.reload(null, false);
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

        /***************************************************************************/
        //                      ACTIVACION DE EDITAR                                //
        /***************************************************************************/
        $(document).on("click", ".btnEditar", function() {

            idEnvio = $(this).data('id');
            metodoProceso = "obtenerid";
            let jsonData = {
                metodo: metodoProceso,
                datoRecibido: {
                    id_canal_youtube: idEnvio,
                    apiKey: apiKey,
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
                success: function(response) {
                    hideLoading();
                    if (response.success === "true" && response.datos) {
                        // let datos = typeof response === 'string' ? JSON.parse(response) : response;
                        var respuesta = response.datos;
                        metodoProceso = "actualizar";
                        $("#nombre").val(respuesta.nombre_canal);
                        $("#url").val(respuesta.url_canal);
                        $("#descripcion").val(respuesta.descripcion_canal);

                    }
                    $('#modalRegistroCanal').modal('show');
                }

            });

            hideLoading();

        });

        /***************************************************************************/
        //                      ELIMINACION DE CLIENTE                             //
        /***************************************************************************/
        $(document).on("click", ".btnEliminar", function() {

            var idEnvio = $(this).data('id');
            var newEstado = "";
            var Mensaje = "";
            var Texto = "";
            var ButtonText = "";
            var Mensaje1 = "";
            var Mensaje2 = "";

            Mensaje = '¿Está seguro que desea Eliminar este Canal?';
            Texto = "¡El Canal ya no formara parte de su Administracion!";
            ButtonText = "Si, Eliminar!";
            Mensaje1 = "Eliminado";
            Mensaje2 = "El Canal ha sido Eliminado Exitosamente";

            Swal.fire({
                title: Mensaje,
                text: Texto,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: ButtonText

            }).then((result) => {

                if (result.isConfirmed) {
                    metodoProceso = "eliminar";
                    let jsonData = {
                        metodo: metodoProceso,
                        datoRecibido: {
                            id_canal_youtube: idEnvio,
                            id_usuario: idEnvio,
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

                        success: function(data) {
                            showLoading();

                            if (data.error) {
                                Swal.fire(
                                    'Error al procesar la peticion',
                                    'Mensaje',
                                    'warning').then(() => {
                                    table.ajax.reload(null, false);
                                });
                                table.ajax.reload(null, false);

                            } else {
                                showLoading();
                                Swal.fire(
                                    Mensaje1,
                                    Mensaje2,
                                    'success').then(() => {
                                    table.ajax.reload(null, false);
                                });
                                table.ajax.reload(null, false);
                            }

                        }

                    });
                    showLoading();
                }

            });



        });

    });




});