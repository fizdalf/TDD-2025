const contenedorInput = document.querySelector('.contenedor-input');

function abrir_input() {
    contenedorInput.classList.add("active-popup");
}

function cerrar_input() {
    contenedorInput.classList.remove("active-popup");
    setTimeout(function () {
        contenedorInput.classList.remove("active");
    }, 500);
}