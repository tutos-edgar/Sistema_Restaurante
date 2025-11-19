const container = document.getElementById("cardsContainer");
const selector = document.getElementById("filterType");


function ObtenerVideosPorCanal(idCanal) {
    metodoProceso = "obtenercardvideosporcanalyoutube";

    let jsonData = {
        metodo: metodoProceso,
        id_canal_youtube: idCanal
            // datoRecibido: {
            //     id_canal_youtube: seccionSeleccionada,
            //     titulo_video: $("#nombre").val(),
            //     descripcion_video: $("#descripcion").val(),
            //     url_video: $("#url").val(),
            //     tiempo_duracion: $("#duracion").val(),
            //     tipo_video: tiposSeleccionado,
            //     apiKey: apiKey
            // }
    };

    $.ajax({
        url: urlPetiones,
        type: 'POST',
        data: JSON.stringify(jsonData),
        contentType: "application/json",
        beforeSend: function() {
            showLoading();
        },
        success: function(res) {
            hideLoading();
            let datos = typeof res === 'string' ? JSON.parse(res) : res;
            if (datos.success === "true") {
                container.innerHTML = "";
                $("#cardsContainer").html('');
                datosRecibidos = datos.datos;
                $("#cardsContainer").html(res.datos.datos);
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: datos.mensaje || "No se pudo realizar la Acción",
                    showConfirmButton: false,
                    timer: tiempoEsperaMensaje
                }).then(() => {
                    container.innerHTML = "";
                    $("#cardsContainer").html('');
                    datosRecibidos = datos.datos;
                    $("#cardsContainer").html(res.datos.datos);
                });
                return;
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
}


metodoProceso = "obtenercardvideosvistas";
urlPetiones = urlApi + "datosGanarVistasUser.php";
var datosRecibidos = [];

$(document).ready(function() {
    // Inicializar con la opción por defecto
    ObtenerCardCanales();


    function ObtenerCardCanales() {
        metodoProceso = "obtenercardvideosvistas";
        let jsonData = {
            metodo: metodoProceso
        };

        $.ajax({
            url: urlPetiones,
            type: 'POST',
            data: JSON.stringify(jsonData),
            contentType: "application/json",
            beforeSend: function() {
                showLoading();
            },
            success: function(res) {
                hideLoading();

                let datos = typeof res === 'string' ? JSON.parse(res) : res;
                if (datos.success === "true") {
                    container.innerHTML = "";
                    $("#cardsContainer").html('');
                    datosRecibidos = datos.datos;
                    $("#cardsContainer").html(res.datos.datos);
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
    }

    function ObtenerCardVideos() {
        metodoProceso = "obtenercardvideosyoutube";
        let jsonData = {
            metodo: metodoProceso
        };

        $.ajax({
            url: urlPetiones,
            type: 'POST',
            data: JSON.stringify(jsonData),
            contentType: "application/json",
            beforeSend: function() {
                showLoading();
            },
            success: function(res) {
                hideLoading();
                let datos = typeof res === 'string' ? JSON.parse(res) : res;
                if (datos.success === "true") {
                    container.innerHTML = "";
                    $("#cardsContainer").html('');
                    datosRecibidos = datos.datos;
                    $("#cardsContainer").html(res.datos.datos);
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: datos.mensaje || "No se pudo realizar la Acción",
                        showConfirmButton: false,
                        timer: tiempoEsperaMensaje
                    }).then(() => {
                        $("#cardsContainer").html('');
                        datosRecibidos = datos.datos;
                        $("#cardsContainer").html(res.datos.datos);
                    });
                    return;
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
    }

    function ObtenerCardShorts() {
        metodoProceso = "obtenercardshortsyoutube";
        let jsonData = {
            metodo: metodoProceso
        };

        $.ajax({
            url: urlPetiones,
            type: 'POST',
            data: JSON.stringify(jsonData),
            contentType: "application/json",
            beforeSend: function() {
                showLoading();
            },
            success: function(res) {
                hideLoading();
                let datos = typeof res === 'string' ? JSON.parse(res) : res;
                if (datos.success === "true") {
                    container.innerHTML = "";
                    $("#cardsContainer").html('');
                    datosRecibidos = datos.datos;
                    $("#cardsContainer").html(res.datos.datos);
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: datos.mensaje || "No se pudo realizar la Acción",
                        showConfirmButton: false,
                        timer: tiempoEsperaMensaje
                    }).then(() => {
                        $("#cardsContainer").html('');
                        datosRecibidos = datos.datos;
                        $("#cardsContainer").html(res.datos.datos);
                    });
                    return;
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
    }



    $("#filterType").on("change", function() {
        let valor = $(this).val();

        if (valor === "canales") {
            metodoProceso = "obtenercardcanalesyoutube";
            ObtenerCardCanales();
        } else if (valor === "videos") {
            metodoProceso = "obtenercardvideosyoutube";
            ObtenerCardVideos();
        } else if (valor === "shorts") {
            metodoProceso = "obtenercardshortsyoutube";
            ObtenerCardShorts();
        } else {
            metodoProceso = "obtenercardcanalesyoutube";
            ObtenerCardCanales();
        }
        localStorage.setItem("tipoSeleccionado", this.value);
    });



});