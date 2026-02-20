<div class="sidebar" id="sidebar">

        <!-- DASHBOARD -->
        <!-- <a href="#" onclick="mostrar('dashboard')">📊 Dashboard</a> -->
        <a href="#" onclick="showSection('dashboard')"><i class="bi bi-speedometer2"></i> Dashboard</a>

        <!-- USUARIOS CON SUBMENU -->
        <a data-bs-toggle="collapse" href="#submenuUsuarios" role="button" aria-expanded="false">
            <i class="bi bi-file-person"></i> Usuarios
            <i class="bi bi-chevron-down float-end"></i>
        </a>

        <div class="collapse submenu" id="submenuUsuarios">
            <a href="registro_personal.php"><i class="bi bi-person-lines-fill"></i> Registrar Datos</a>
            <a href="registro_usuario.php"><i class="bi bi-person-check"></i> Crear Usuario</a>
            <a href="registro_roles.php"><i class="bi bi-person-circle"></i> Crear Roles</a>            
        </div>

        <!-- PRODUCTOS CON SUBMENU -->
        <a data-bs-toggle="collapse" href="#submenuProductos" role="button">
            <i class="bi bi-box"></i> Productos
            <i class="bi bi-chevron-down float-end"></i>
        </a>

        <div class="collapse submenu" id="submenuProductos">
            <a href="registro_categoria.php"><i class="bi bi-tags"></i> Categorías</a>
            <a href="registro_producto.php"><i class="bi bi-plus-circle"></i> Agregar producto</a>
            <a href="registro_promocion.php"><i class="bi bi-star"></i> Agregar Promoción</a>
            <a href="#"><i class="bi bi-list"></i> Listar productos</a>
        </div>

        <!-- MESAS CON SUBMENU -->
        <a data-bs-toggle="collapse" href="#submenuMesas" role="button">
            <i class="bi bi-table"></i> Mesas
            <i class="bi bi-chevron-down float-end"></i>
        </a>

        <div class="collapse submenu" id="submenuMesas">
            <a href="#"><i class="bi bi-fork-knife"></i> Agregar mesa</a>
            <a href="#"><i class="bi bi-egg-fried"></i> Lista de mesas</a>
        </div>

        <!-- PEDIDOS CON SUBMENU -->
        <a data-bs-toggle="collapse" href="#submenuPedidos" role="button" aria-expanded="false">
            <i class="bi bi-person-video2"></i> Pedidos
            <i class="bi bi-chevron-down float-end"></i>
        </a>

        <div class="collapse submenu" id="submenuPedidos">
            <a href="#" onclick="mostrar('pedidos')"><i class="bi bi-egg-fried"></i> Crear Pedidos</a>
            <a href="#" onclick="mostrar('pedidos')"><i class="bi bi-clipboard-check"></i> Todos los Pedidos</a>
            <a href="#"><i class="bi bi-receipt"></i> Pendientes</a>
            <a href="#"><i class="bi bi-fire"></i> En preparación</a>
            <a href="#"><i class="bi bi-check-circle"></i> Entregados</a>
            <a href="#"><i class="bi bi-x-circle"></i> Cancelados</a>
            <a href=""><i class="bi bi-credit-card"></i> Pagados</a>
        </div>



        <!-- CLIENTES -->
        <a href="#" onclick="mostrar('clientes')">
            <i class="bi bi-people"></i> Clientes
        </a>

        <!-- REPARTIDORES -->
        <a href="#" onclick="mostrar('repartidores')">
            <i class="bi bi-bicycle"></i> Repartidores
        </a>

        <!-- MESAS CON SUBMENU -->
        <a data-bs-toggle="collapse" href="#submenuConfiguracion" role="button">
            <i class="bi bi-gear"></i> Configuración
            <i class="bi bi-chevron-down float-end"></i>
        </a>

        <div class="collapse submenu" id="submenuConfiguracion">
            <a href="#"><i class="bi bi-person-gear"></i> Perfil</span></a>
            <a href="#"><i class="bi bi-shield-check"></i> Cambiar Password</a>
            <a href="#"><i class="bi bi-cpu"></i> Token Acceso</a>
            <a href="#"><i class="bi bi-pen"></i> Pregunta Secreta</a>
        </div>

    </div>