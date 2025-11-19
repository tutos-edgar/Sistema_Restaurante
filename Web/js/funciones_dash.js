const sidebar = document.getElementById("sidebar");
const content = document.getElementById("content");
const toggleBtn = document.getElementById("toggleBtn");
const titleDash = document.getElementById('title_dash');
const logoSection = document.getElementById('logoSection');

toggleBtn.addEventListener("click", () => {
    sidebar.classList.toggle("collapsed");
    content.classList.toggle("collapsed-sidebar");
    titleDash.classList.toggle('d-none');
});

document.getElementById('logoutLink').addEventListener('click', function(e) {
    e.preventDefault(); // Evita que se vaya directamente al logout.php

    Swal.fire({
        title: '¿Está seguro de que desea cerrar sesión?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, cerrar sesión',
        cancelButtonText: 'No',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Si confirma, redirige al logout.php
            window.location.href = this.href;
        }
        // Si cancela, simplemente no hace nada
    });
});


// Seleccionamos todos los enlaces del sidebar
const sidebarLinks = document.querySelectorAll('.sidebar a');
sidebarLinks.forEach(link => {
    link.addEventListener('click', function() {
        // Primero quitamos 'active' de todos
        sidebarLinks.forEach(l => l.classList.remove('active'));

        // Luego agregamos 'active' al que se hizo clic
        this.classList.add('active');
    });
});

function showLoading() {
    $("#loadingModal").css("display", "flex");
}

function hideLoading() {
    $("#loadingModal").hide();
}

// // FUNCIONES PARA LOS FORMULARIOS
// const sidebar = document.getElementById("sidebar");
// const content = document.getElementById("content");
// const toggleBtn = document.getElementById("toggleBtn");
// const titleDash = document.getElementById('title_dash');
// const logoSection = document.getElementById('logoSection');

// toggleBtn.addEventListener("click", () => {
//     sidebar.classList.toggle("collapsed");
//     content.classList.toggle("collapsed-sidebar");
//     titleDash.classList.toggle('d-none');
// });

// // Seleccionamos todos los enlaces del sidebar
// const sidebarLinks = document.querySelectorAll('.sidebar a');
// sidebarLinks.forEach(link => {
//     link.addEventListener('click', function() {
//         // Primero quitamos 'active' de todos
//         sidebarLinks.forEach(l => l.classList.remove('active'));

//         // Luego agregamos 'active' al que se hizo clic
//         this.classList.add('active');
//     });
// });



// // Inicializar tooltips de Bootstrap
// var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
// var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
//     return new bootstrap.Tooltip(tooltipTriggerEl)
// })

// // NUEVO

// document.addEventListener("DOMContentLoaded", () => {
//     const sidebar = document.getElementById("sidebar");
//     const content = document.getElementById("content");
//     const toggleBtn = document.getElementById("toggleBtn");

//     // === Toggle del sidebar completo ===
//     toggleBtn.addEventListener("click", () => {
//         sidebar.classList.toggle("collapsed");
//         content.classList.toggle("expanded");

//         // Cerrar todos los submenús si el sidebar se colapsa
//         if (sidebar.classList.contains("collapsed")) {
//             document.querySelectorAll(".submenu").forEach(ul => {
//                 ul.style.display = "none";
//             });
//         }
//     });

//     // === Control de submenús ===
//     document.querySelectorAll(".has-submenu").forEach(link => {
//         link.addEventListener("click", (e) => {
//             e.preventDefault();

//             const submenu = link.nextElementSibling;

//             if (submenu && submenu.classList.contains("submenu")) {
//                 // Alternar visibilidad del submenú
//                 submenu.style.display = submenu.style.display === "block" ? "none" : "block";
//             }
//         });
//     });
// });



// // FUNCIONES PARA LOS FORMULARIOS Y SIDEBAR
// const sidebar = document.getElementById("sidebar");
// const content = document.getElementById("content");
// const toggleBtn = document.getElementById("toggleBtn");
// const titleDash = document.getElementById('title_dash');

// // Toggle del sidebar
// toggleBtn.addEventListener("click", () => {
//     sidebar.classList.toggle("collapsed");
//     content.classList.toggle("collapsed-sidebar");
//     titleDash.classList.toggle('d-none');

//     // Cerrar todos los submenús si el sidebar se colapsa
//     if (sidebar.classList.contains("collapsed")) {
//         document.querySelectorAll(".submenu").forEach(ul => {
//             ul.style.display = "none";
//         });
//     }
// });

// // Activar enlace activo
// const sidebarLinks = document.querySelectorAll('.sidebar a');
// sidebarLinks.forEach(link => {
//     link.addEventListener('click', function(e) {
//         // Si es un submenú, no marcar como activo principal
//         if (!link.classList.contains('has-submenu')) {
//             sidebarLinks.forEach(l => l.classList.remove('active'));
//             this.classList.add('active');
//         }
//     });
// });

// // Inicializar tooltips
// var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
// var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
//     return new bootstrap.Tooltip(tooltipTriggerEl)
// });

// // === CONTROL DE SUBMENÚS ===
// document.querySelectorAll('.has-submenu').forEach(link => {
//     link.addEventListener('click', function(e) {
//         e.preventDefault();
//         const parentLi = link.parentElement;
//         const submenu = parentLi.querySelector('.submenu');

//         if (submenu) {
//             // Toggle con animación tipo slide
//             if (submenu.style.display === "block") {
//                 submenu.style.display = "none";
//             } else {
//                 submenu.style.display = "block";
//             }
//         }
//     });
// });


// // Sidebar toggle y submenús
// const sidebar = document.getElementById("sidebar");
// const content = document.getElementById("content");
// const toggleBtn = document.getElementById("toggleBtn");
// const titleDash = document.getElementById('title_dash');

// // Toggle del sidebar
// toggleBtn.addEventListener("click", () => {
//     sidebar.classList.toggle("collapsed");
//     content.classList.toggle("collapsed-sidebar");
//     titleDash.classList.toggle('d-none');

//     // Cerrar todos los submenús si el sidebar se colapsa
//     if (sidebar.classList.contains("collapsed")) {
//         document.querySelectorAll(".submenu").forEach(ul => {
//             ul.style.display = "none";
//         });
//     }
// });

// // Activar enlace activo
// const sidebarLinks = document.querySelectorAll('.sidebar a');
// sidebarLinks.forEach(link => {
//     link.addEventListener('click', function(e) {
//         if (!link.classList.contains('has-submenu')) {
//             sidebarLinks.forEach(l => l.classList.remove('active'));
//             this.classList.add('active');
//         }
//     });
// });

// // Inicializar tooltips
// var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
// var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
//     return new bootstrap.Tooltip(tooltipTriggerEl)
// });

// // Control de submenús anidados
// document.querySelectorAll('.has-submenu').forEach(link => {
//     link.addEventListener('click', function(e) {
//         e.preventDefault();
//         const parentLi = link.parentElement;
//         const submenu = parentLi.querySelector('.submenu');

//         if (submenu) {
//             submenu.style.display = (submenu.style.display === "block") ? "none" : "block";
//         }
//     });
// });


// // Sidebar toggle y submenús
// const sidebar = document.getElementById("sidebar");
// const content = document.getElementById("content");
// const toggleBtn = document.getElementById("toggleBtn");
// const titleDash = document.getElementById('title_dash');

// // Toggle del sidebar
// toggleBtn.addEventListener("click", () => {
//     sidebar.classList.toggle("collapsed");
//     content.classList.toggle("collapsed-sidebar");
//     titleDash.classList.toggle('d-none');

//     // Cerrar todos los submenús si el sidebar se colapsa
//     if (sidebar.classList.contains("collapsed")) {
//         document.querySelectorAll(".submenu").forEach(ul => {
//             ul.style.display = "none";
//         });
//     }
// });

// // Activar enlace activo
// const sidebarLinks = document.querySelectorAll('.sidebar a');
// sidebarLinks.forEach(link => {
//     link.addEventListener('click', function(e) {
//         if (!link.classList.contains('has-submenu')) {
//             sidebarLinks.forEach(l => l.classList.remove('active'));
//             this.classList.add('active');
//         }
//     });
// });

// // Inicializar tooltips
// var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
// var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
//     return new bootstrap.Tooltip(tooltipTriggerEl)
// });

// // Control de submenús anidados
// document.querySelectorAll('.has-submenu').forEach(link => {
//     link.addEventListener('click', function(e) {
//         e.preventDefault();
//         const parentLi = link.parentElement;
//         const submenu = parentLi.querySelector('.submenu');

//         if (submenu) {
//             submenu.style.display = (submenu.style.display === "block") ? "none" : "block";
//         }
//     });
// });