const sidebar = document.getElementById("sidebar");
const content = document.querySelector(".content");

document.getElementById("toggleSidebar").onclick = () => {
    sidebar.classList.toggle("collapsed");
    content.classList.toggle("full");
};

function mostrar(id) {
    document.querySelectorAll(".seccion").forEach(s => s.classList.add("d-none"));
    document.getElementById(id).classList.remove("d-none");
}

// PREVISUALIZAR FOTO
document.getElementById("fotoInput").addEventListener("change", function(e) {
    const reader = new FileReader();
    reader.onload = function() {
        document.getElementById("fotoPreview").src = reader.result;
    }
    if (e.target.files[0]) {
        reader.readAsDataURL(e.target.files[0]);
    }
});


// SUBMENU CONFIGURACION FUNCIONAL
// document.querySelectorAll('.dropdown-submenu > a').forEach(item => {
//     item.addEventListener('click', function(e) {
//         e.preventDefault();
//         e.stopPropagation();

//         const parentLi = this.parentElement;
//         parentLi.classList.toggle('show');

//         // cerrar otros submenus
//         document.querySelectorAll('.dropdown-submenu').forEach(li => {
//             if (li !== parentLi) li.classList.remove('show');
//         });
//     });
// });

// BOTON TOGLE SWITCH

const switchInput = document.getElementById('estadoSwitch');
const labelText = document.getElementById('estadoLabel');

switchInput.addEventListener('change', () => {
    if (switchInput.checked) {
        labelText.textContent = ' ACTIVO';
        labelText.style.color = '#28a745';
    } else {
        labelText.textContent = ' INACTIVO';
        labelText.style.color = '#dc3545';
    }
});