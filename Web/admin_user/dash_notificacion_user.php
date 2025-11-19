<div class="container notifications-container">
  <h2 class="mb-4 text-center"><i class="bi bi-bell-fill text-danger"></i> Notificaciones</h2>

  <!-- Buscador -->
  <div class="input-group search-box">
    <span class="input-group-text"><i class="bi bi-search"></i></span>
    <input type="text" id="searchInput" class="form-control" placeholder="Buscar notificaciones...">
  </div>

  <!-- Nuevas Notificaciones -->
  <div>
    <div class="section-title"><i class="bi bi-stars text-primary"></i> Nuevas</div>

    <div class="notification-card" data-text="Nuevo seguidor Juan Pérez">
      <div class="notification-icon bg-primary text-white">
        <i class="bi bi-person-fill"></i>
      </div>
      <div class="notification-text">
        <strong>Nuevo seguidor</strong> — Juan Pérez empezó a seguirte.
        <div class="notification-time">Hace 5 minutos</div>
      </div>
      <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-x"></i></button>
    </div>

    <div class="notification-card" data-text="Tu perfil fue actualizado correctamente">
      <div class="notification-icon bg-success text-white">
        <i class="bi bi-check-circle-fill"></i>
      </div>
      <div class="notification-text">
        Tu perfil fue actualizado correctamente.
        <div class="notification-time">Hace 1 hora</div>
      </div>
      <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-x"></i></button>
    </div>
    
  </div>

  <!-- Notificaciones Vistas -->
  <div>
    <div class="section-title"><i class="bi bi-eye text-secondary"></i> Vistas</div>

    <div class="notification-card seen" data-text="Recuerda verificar tu correo electrónico">
      <div class="notification-icon bg-warning text-dark">
        <i class="bi bi-exclamation-triangle-fill"></i>
      </div>
      <div class="notification-text">
        Recuerda verificar tu correo electrónico.
        <div class="notification-time">Ayer</div>
      </div>
      <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-x"></i></button>
    </div>

    <div class="notification-card seen" data-text="Has completado tu primera tarea">
      <div class="notification-icon bg-info text-white">
        <i class="bi bi-clipboard-check-fill"></i>
      </div>
      <div class="notification-text">
        Has completado tu primera tarea.
        <div class="notification-time">Hace 3 días</div>
      </div>
      <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-x"></i></button>
    </div>


    <!-- Tarjeta de notificación -->
<div class="notification-card d-flex align-items-center p-2 border rounded mb-2" 
     data-text="Nuevo seguidor Juan Pérez" 
     style="cursor:pointer;">

  <!-- Icono -->
  <div class="notification-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width:40px; height:40px;">
    <i class="bi bi-person-fill"></i>
  </div>

  <!-- Texto -->
  <div class="notification-text flex-grow-1">
    <strong>Nuevo seguidor</strong> — Juan Pérez empezó a seguirte.
    <div class="notification-time text-muted small">Hace 5 minutos</div>
  </div>

  <!-- Botón cerrar -->
  <button class="btn btn-sm btn-outline-secondary close-btn">
    <i class="bi bi-x"></i>
  </button>
</div>

  </div>
</div>