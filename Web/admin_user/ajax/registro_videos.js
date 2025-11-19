var urlPetiones = urlAPI + "datosProductos.php";

$(document).ready(function() {

    var idEnvio = 0;
    var metodoProceso = "";

    /***************************************************************************/
    //                      LLENADO DE DATOS EN TABLA                          //
    /***************************************************************************/
    tablaDatos = $('#tablaRegistros').DataTable({
        "language": {
            "lengthMenu": "Mostrar _MENU_ registros",
            "zeroRecords": "No se encontraron resultados",
            "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "infoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sSearch": "Buscar:",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
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
            },
            error: function(xhr, status, error) {
                // console.error("Error en la solicitud AJAX:", {
                //     status: status,
                //     error: error,
                //     responseText: xhr.responseText
                // });
                hideLoading();

            }
        },
        columns: [
            { data: "codigo_producto" },
            { data: "nombre_producto" },
            { data: "nombre_seccion" },
            { data: "descripcion_producto" },
            {
                data: "codigo_barra",
                render: function(data, type, row) {
                    // Verifica que haya una ruta válida
                    if (data && data !== "") {
                        return `<img src="${data}" alt="Código de Barras" style="width: 100px; height: 70px; border-radius: 5px;" />`;
                    } else {
                        return '<span class="text-muted">No generado</span>';
                    }
                }
            },
            { data: "botones" },
        ]
    });

    /***************************************************************************/
    //                      ACTIVACION DE NUEO                                 //
    /***************************************************************************/
    $("#btnNuevo").click(function() {
        $("#formRegistro").trigger("reset");
        metodoProceso = "registrar";
        document.getElementById('barcode').innerHTML = '';
        $("#btnEnviar").text('Registrar');
        $("#form_usuario").trigger("reset");
        $(".modal-header").css("background-color", "#0b0c42");
        $(".modal-header").css("color", "#EEEEFAFF");
        $(".modal-crud").text("Registrar Producto");
        $('#modalCRUD').modal('show');
    });

    /***************************************************************************/
    //                      ENVIO DEL FORMULARIO                               //
    /***************************************************************************/
    $('#formRegistro').submit(async e => {
        e.preventDefault();
        var codigo = document.getElementById("codigo");
        var nombre = document.getElementById("nombre");

        if (codigo.value == "") {
            Swal.fire({
                icon: 'warning',
                title: "Favor de Ingresar Un Codigo",
                showConfirmButton: false,
                timer: 1500
            });
            return;
        }

        if (nombre.value == "") {
            Swal.fire({
                icon: 'warning',
                title: "Favor de Ingresar un Nombre de Producto",
                showConfirmButton: false,
                timer: 1500
            });
            return;
        }

        var secciones = document.getElementById("seccionProducto");
        var seccionSeleccionada = secciones.options[secciones.selectedIndex].value;

        if (seccionSeleccionada == "0" || seccionSeleccionada == "") {
            Swal.fire({
                icon: 'warning',
                title: "Favor de Seleccionar una Sección de Producto",
                showConfirmButton: false,
                timer: 1500
            });
            return;
        }

        const barcodeElement = document.getElementById("barcode");

        if (!barcodeElement || barcodeElement.children.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: "El Codigo de Barras no Ha Sido Generado",
                showConfirmButton: false,
                timer: 1500
            });
            return;
        }

        var imagenBase64 = await svgToBase64Image(barcodeElement);

        let jsonData = {
            metodo: metodoProceso,
            datoRecibido: {
                id_producto: idEnvio,
                nombre_producto: $("#nombre").val(),
                id_seccion_producto: seccionSeleccionada,
                descripcion_producto: $("#descripcion").val(),
                codigo_producto: $("#codigo").val(),
                codigo_barras: imagenBase64
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
                let dato = typeof res === 'string' ? JSON.parse(res) : res;

                if (dato.success === "true") {
                    Swal.fire({
                        icon: 'success',
                        title: dato.mensaje || "Datos Guardados Correctamente",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        $("#formRegistro").trigger("reset");
                        $('#modalCRUD').modal('hide');
                    });
                    tablaDatos.ajax.reload(null, false);
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: dato.mensaje || "Ocurrió un error 2",
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
                id_producto: idEnvio,
            }
        };

        $.ajax({
            url: urlPetiones,
            type: "POST",
            data: JSON.stringify(jsonData),
            contentType: "application/json",
            success: function(response) {

                if (response.datos && response.datos.length > 0) {
                    var usuario = response.datos[0];
                    metodoProceso = "actualizar";
                    $("#codigo").val(usuario.codigo_producto);
                    $("#nombre").val(usuario.nombre_producto);
                    $("#seccionProducto").val(usuario.id_seccion_producto);
                    $("#descripcion").val(usuario.descripcion_producto);
                    $("#barcode").val(usuario.codigo_barra);
                    var codigoBarra = usuario.codigo_producto;
                    if (codigoBarra) {
                        JsBarcode('#barcode', codigoBarra);
                    } else {
                        document.getElementById('barcode').innerHTML = '';
                        code.focus();
                    }

                }

                $("#btnEnviar").text('Actualizar');
                $(".modal-header").css("background-color", "#16163BFF");
                $(".modal-header").css("color", "white");
                $(".modal-title").text("Actualizar Producto");
                $('#modalCRUD').modal('show');
            }

        });



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

        Mensaje = '¿Está seguro que desea Eliminar este Producto?';
        Texto = "¡El Producto ya no formara parte de su Administracion!";
        ButtonText = "Si, Eliminar!";
        Mensaje1 = "Eliminado";
        Mensaje2 = "El Producto ha sido Eliminado Exitosamente";

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
                        id_producto: idEnvio,
                        codigo_producto: idEnvio,
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
                                tablaDatos.ajax.reload(null, false);
                            });
                            tablaDatos.ajax.reload(null, false);

                        } else {
                            Swal.fire(
                                Mensaje1,
                                Mensaje2,
                                'success').then(() => {
                                tablaDatos.ajax.reload(null, false);
                            });
                            tablaDatos.ajax.reload(null, false);
                        }

                    }

                });

            }

        });



    });

    $(document).on("click", ".btnEstadoUsuario", function() {
        $("#formCambiarEstado").trigger("reset");
        const id = $(this).data('id');
        $('#personaId').val(id); // Guardamos el id en el input hidden dentro del modal
    });

    /***************************************************************************/
    //                      ENVIO DEL FORMULARIO ESTADO                        //
    /***************************************************************************/
    $('#formCambiarEstado').submit(e => {
        e.preventDefault();
        var estados = document.getElementById("estado");
        var estadoSeleccionado = estados.options[estados.selectedIndex].value;

        if (estadoSeleccionado != "0" && estadoSeleccionado != "1") {
            Swal.fire({
                icon: 'warning',
                title: "Favor de Seleccionar un Estado",
                showConfirmButton: false,
                timer: 1500
            });
            return;
        }
        metodoProceso = "actualizarestadousuario"
        idEnvio = $('#personaId').val();
        let jsonData = {
            metodo: metodoProceso,
            datoUsuario: {
                id_usuario: idEnvio,
                id_estado_personal: estadoSeleccionado,
                estado_usuario: estadoSeleccionado
            }
        };

        $.ajax({
            url: urlPetiones,
            type: "POST",
            data: JSON.stringify(jsonData),
            contentType: "application/json",
            success: function(res) {

                let dato = typeof res === 'string' ? JSON.parse(res) : res;

                if (dato.success === "true") {
                    Swal.fire({
                        icon: 'success',
                        title: dato.mensaje || "Datos Guardados Correctamente",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        $("#formCambiarEstado").trigger("reset");
                        $('#modalCambiarEstado').modal('hide');
                    });
                    tablaDatos.ajax.reload(null, false);
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: dato.mensaje || "Ocurrió un error",
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            },
            error: function(xhr, status, error) {
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

    });

    /***************************************************************************/
    //                      BUSQUEDA DE DATOS                                  //
    /***************************************************************************/
    $("#codigo").on("keyup", function() {
        let valorBusqueda = $('#codigo').val().trim();
        if (valorBusqueda == "") {
            document.getElementById('barcode').innerHTML = '';
        }
    });

    /***************************************************************************/
    //                      GENERAR CODIGO DE BARRA                            //
    /***************************************************************************/
    function svgToBase64Image(svgElement) {
        return new Promise((resolve, reject) => {
            const svgData = new XMLSerializer().serializeToString(svgElement);
            const svgBlob = new Blob([svgData], { type: "image/svg+xml;charset=utf-8" });
            const url = URL.createObjectURL(svgBlob);

            const img = new Image();
            img.onload = () => {
                const canvas = document.createElement("canvas");
                canvas.width = img.width;
                canvas.height = img.height;

                const ctx = canvas.getContext("2d");
                ctx.drawImage(img, 0, 0);

                const base64 = canvas.toDataURL("image/png");
                URL.revokeObjectURL(url);
                resolve(base64);
            };
            img.onerror = reject;
            img.src = url;
        });
    }

})