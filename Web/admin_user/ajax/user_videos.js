urlPetiones = urlApi + "datosUsuariosVideos.php";
metodoProceso = "registrar";

$(document).ready(function() {

    nombreVideo = document.getElementById("nombre");
    urlVideo = document.getElementById("url");
    descripcionVideo = document.getElementById("descripcion");
    tiempoVideo = document.getElementById("duracion");
    var tipoVideoSeleccionado;
    var idCanalSeleccionado;

    // document.getElementById("bio").value = usuario.bio;
    // fotoPerfil = document.querySelector(".profile-img").src = usuario.foto;


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
                    console.error("Error del servidor:", json.message || "Error desconocido");
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
            { data: "id_video" },
            { data: "nombre_canal" },
            { data: "titulo_video" },
            { data: "descripcion_video" },
            { data: "tipo" },
            { data: "url_video" },
            { data: "tiempo_duracion" },
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

    $('#tablaDatos tbody').on('click', 'button', function() {
        // obtener la fila asociada al botón
        var row = table.row($(this).closest('tr')).data();
        // tipoVideoSeleccionado = row.tipo;
        var $tr = $(this).closest('tr');
        tipoVideoSeleccionado = $tr.find('span[data-idtipo]').data('idtipo');

        // tipoVideoSeleccionado = row.find('span[data-idtipo]').data('idtipo');
        idCanalSeleccionado = row.id_canal_youtube;

    });

    // Filtro por tipo
    $('#filterType').on('change', function() {
        var val = $(this).val();
        table.column(4).search(val).draw();
    });

    $('#formRegistro').submit(e => {
        e.preventDefault();

        var tipos = document.getElementById("tipo");
        var tiposSeleccionado = tipos.options[tipos.selectedIndex].value;
        if (tiposSeleccionado == "0" || tiposSeleccionado == "") {
            Swal.fire({
                icon: 'warning',
                title: "Favor de Seleccionar un Tipo de Video",
                showConfirmButton: false,
                timer: tiempoEsperaMensaje
            });
            return;
        }

        if (nombreVideo.value == "") {
            Swal.fire({
                icon: 'warning',
                title: "Favor de Ingresar Un Nombre de Video",
                showConfirmButton: false,
                timer: tiempoEsperaMensaje
            });
            return;
        }

        if (validarCaracteresEspeciales(nombreVideo.value)) {
            Swal.fire({
                icon: 'warning',
                title: "El Nombre de Video contiene caracteres no validos",
                showConfirmButton: false,
                timer: tiempoEsperaMensaje
            });
            return;
        }

        if (urlVideo.value == "") {
            Swal.fire({
                icon: 'warning',
                title: "Favor de Ingresar una Url del Video",
                showConfirmButton: false,
                timer: tiempoEsperaMensaje
            });
            return;
        }

        if (tiempoVideo.value == "") {
            Swal.fire({
                icon: 'warning',
                title: "Favor de Ingresar una Duración Video",
                showConfirmButton: false,
                timer: tiempoEsperaMensaje
            });
            return;
        }

        var secciones = document.getElementById("listaCanales");
        var seccionSeleccionada = secciones.options[secciones.selectedIndex].value;
        if (seccionSeleccionada == "0" || seccionSeleccionada == "") {
            Swal.fire({
                icon: 'warning',
                title: "Favor de Seleccionar un Canal",
                showConfirmButton: false,
                timer: tiempoEsperaMensaje
            });
            return;
        }

        let jsonData = {
            metodo: metodoProceso,
            datoRecibido: {
                id_video: idEnvio,
                id_canal_youtube: seccionSeleccionada,
                titulo_video: $("#nombre").val(),
                descripcion_video: $("#descripcion").val(),
                url_video: $("#url").val(),
                tiempo_duracion: $("#duracion").val(),
                tipo_video: tiposSeleccionado,
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
                id_video: idEnvio,
                id_canal_youtube: idEnvio,
                tipo_video: tipoVideoSeleccionado,
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
                    $("#nombre").val(respuesta.titulo_video);
                    $("#url").val(respuesta.url_video);
                    $("#descripcion").val(respuesta.descripcion_video);
                    $("#duracion").val(respuesta.tiempo_duracion);
                    $("#listaCanales").val(respuesta.id_canal_youtube);
                    $("#tipo").val(respuesta.tipo);
                    // $("#tipo").val(tipoVideoSeleccionado);
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
                        hideLoading();

                        if (data.error) {
                            Swal.fire(
                                'Error al procesar la peticion',
                                'Mensaje',
                                'warning').then(() => {
                                table.ajax.reload(null, false);
                            });
                            table.ajax.reload(null, false);

                        } else {
                            hideLoading();
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
                hideLoading();
            }

        });



    });

    // $("#hora").on("change", function() {
    //     let valor = $(this).val(); // "HH:MM"

    //     if (!valor) {
    //         Swal.fire({
    //             icon: 'warning',
    //             title: 'Dato requerido',
    //             text: 'Debe ingresar una hora válida'
    //         });
    //         return;
    //     }

    //     if (valor === "00:00") {
    //         Swal.fire({
    //             icon: 'error',
    //             title: 'Hora inválida',
    //             text: 'La hora no puede ser 00:00'
    //         });
    //         $(this).val(""); // limpiar campo
    //         return;
    //     }


    // });


});