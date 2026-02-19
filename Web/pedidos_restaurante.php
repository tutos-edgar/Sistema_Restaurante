<?php 
ob_start();

include_once __DIR__ . '/../API/models/FuncionesGenerales.php';

$generales = new FuncionesGenerales();
include_once 'header.php'; 
$generales->ObtenerEstilosWeb(0);

?>



<body>

    <?php 
        include_once 'navbar_superior.php';
        // include_once 'hero_container.php';
    ?>

    <div class="container py-5" id="contenido_pedido">
        <h2 class="text-center fw-bold mb-4">🛒 Pedido Online</h2>

        <!-- Selector Categoría -->
        <div class="mb-4 text-center">
            <select class="form-select w-50 mx-auto" id="categoriaSelect">
<option value="menu">🍔 Menú</option>
<option value="bebidas">🥤 Bebidas</option>
<option value="promociones">🔥 Promociones</option>
</select>
        </div>

        <div class="row">
            <!-- Productos -->
            <div class="col-lg-8">
                <div class="row g-4" id="productosContainer"></div>
            </div>

            <!-- Carrito -->
            <div class="col-lg-4">
                <div class="form-box">
                    <h5 class="fw-bold mb-3">📋 Datos del Cliente</h5>

                    <div class="mb-3">
                        <label>Nombre Completo</label>
                        <input type="text" id="nombre" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Teléfono</label>
                        <input type="tel" id="telefono" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Dirección</label>
                        <textarea id="direccion" class="form-control" rows="3" required></textarea>
                    </div>

                    <div class="cart-box ">
                        <h5 class="fw-bold">🧾 Tu Pedido</h5>
                        <ul class="list-group mb-3" id="carritoLista"></ul>
                        <h5>Total: Q<span id="total">0.00</span></h5>
                        <button class="btn btn-warning w-100 mt-3" onclick="enviarPedido()">Confirmar Pedido</button>
                    </div>
                </div>

                <!-- <div class="cart-box ">
                    <h5 class="fw-bold">🧾 Tu Pedido</h5>
                    <ul class="list-group mb-3" id="carritoLista"></ul>
                    <h5>Total: Q<span id="total">0.00</span></h5>
                    <button class="btn btn-warning w-100 mt-3" onclick="enviarPedido()">Confirmar Pedido</button>
                </div> -->
            </div>
        </div>
    </div>

    <script>

       
        const productos = {
            menu: [{
                id: 1,
                nombre: "Hamburguesa Clásica",
                precio: 25,
                img: "https://images.unsplash.com/photo-1550547660-d9450f859349"
            }, {
                id: 2,
                nombre: "Hamburguesa BBQ",
                precio: 35,
                img: "https://images.unsplash.com/photo-1568901346375-23c9450c58cd"
            }, {
                id: 3,
                nombre: "Hamburguesa Doble",
                precio: 40,
                img: "https://images.unsplash.com/photo-1606755962773-d324e0a13086"
            }],
            bebidas: [{
                id: 4,
                nombre: "Coca Cola",
                precio: 10,
                img: "https://images.unsplash.com/photo-1581636625402-29b2a704ef13"
            }, {
                id: 5,
                nombre: "Jugo Natural",
                precio: 12,
                img: "https://images.unsplash.com/photo-1572490122747-3968b75cc699"
            }, {
                id: 6,
                nombre: "Milkshake",
                precio: 18,
                img: "https://images.unsplash.com/photo-1579952363873-27d3bfad9c0d"
            }],
            promociones: [{
                id: 7,
                nombre: "Combo Clásico",
                precio: 35,
                img: "https://images.unsplash.com/photo-1553979459-d2229ba7433b"
            }, {
                id: 8,
                nombre: "Mega Familiar",
                precio: 120,
                img: "https://images.unsplash.com/photo-1600891964599-f61ba0e24092"
            }]
        };

        let carrito = [];

        function cargarProductos() {
            const categoria = document.getElementById("categoriaSelect").value;
            const container = document.getElementById("productosContainer");
            container.innerHTML = "";

            productos[categoria].forEach(p => {
                container.innerHTML += `
<div class="col-md-6">
<div class="card card-product">
<img src="${p.img}" class="product-img w-100">
<div class="card-body text-center">
<h6 class="fw-bold">${p.nombre}</h6>
<p>Q${p.precio}</p>
<button class="btn btn-warning btn-sm" onclick="agregarProducto(${p.id})">Agregar</button>
</div>
</div>
</div>
`;
            });
        }

        function agregarProducto(id) {
            let producto;
            for (let categoria in productos) {
                producto = productos[categoria].find(p => p.id === id);
                if (producto) break;
            }

            let existente = carrito.find(p => p.id === id);
            if (existente) {
                existente.cantidad++;
            } else {
                carrito.push({...producto,
                    cantidad: 1
                });
            }
            actualizarCarrito();
        }

        function actualizarCarrito() {
            const lista = document.getElementById("carritoLista");
            lista.innerHTML = "";
            let total = 0;

            carrito.forEach(p => {
                total += p.precio * p.cantidad;
                lista.innerHTML += `
<li class="list-group-item d-flex justify-content-between align-items-center">
<div>
${p.nombre} <br>
<small>Q${p.precio} x ${p.cantidad}</small>
</div>
<div>
<button class="btn btn-sm btn-danger qty-btn" onclick="cambiarCantidad(${p.id}, -1)">-</button>
<button class="btn btn-sm btn-success qty-btn" onclick="cambiarCantidad(${p.id}, 1)">+</button>
</div>
</li>
`;
            });

            document.getElementById("total").textContent = total.toFixed(2);
        }

        function cambiarCantidad(id, cambio) {
            let producto = carrito.find(p => p.id === id);
            producto.cantidad += cambio;
            if (producto.cantidad <= 0) {
                carrito = carrito.filter(p => p.id !== id);
            }
            actualizarCarrito();
        }

        function enviarPedido() {


            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: "btn btn-success",
                    cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });
            swalWithBootstrapButtons.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel!",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    swalWithBootstrapButtons.fire({
                        title: "Deleted!",
                        text: "Your file has been deleted.",
                        icon: "success"
                    });
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire({
                        title: "Cancelled",
                        text: "Your imaginary file is safe :)",
                        icon: "error"
                    });
                }
            });


            const data = {
                productos: carrito,
                total: carrito.reduce((acc, p) => acc + (p.precio * p.cantidad), 0)
            };

            console.log("Enviar a API:", data);

            /*
            fetch("https://tuapi.com/pedidos", {
            method:"POST",
            headers:{ "Content-Type":"application/json"},
            body: JSON.stringify(data)
            })
            .then(res=>res.json())
            .then(res=>alert("Pedido enviado correctamente"))
            .catch(err=>alert("Error al enviar pedido"));
            */
            alert("Pedido listo para enviar (revisar consola)");
        }

        document.getElementById("categoriaSelect").addEventListener("change", cargarProductos);
        cargarProductos();
    </script>


    <?php 
        include_once 'footer.php';
    ?>

    <script>
         document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("cta-section").remove();
        });
    </script>
    <!-- Bootstrap JS -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/funcion_index_principal.js">
    </script> -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
