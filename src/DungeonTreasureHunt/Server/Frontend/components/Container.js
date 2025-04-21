const contenedor = document.querySelector('.contenedor');

function activar() {
    contenedor.classList.add("active");
}

function desactivar() {
    contenedor.classList.remove("active");
}

function abrir() {
    contenedor.classList.add("active-popup");
}

function cerrar() {
    contenedor.classList.remove("active-popup");
    setTimeout(function () {
        contenedor.classList.remove("active");
    }, 500);
}