const contenedorConfirmar = document.querySelector('.contenedor-confirmar');
const acceptButton = contenedorConfirmar.querySelector('#deleteGrid');
const closeButton = contenedorConfirmar.querySelector('#cierre-confirmar');
const cancelButton = contenedorConfirmar.querySelector('#cierre-confirmar2');

let cancelFn = null;
let acceptFn = null;

function abrir_confirmar() {

    return new Promise((resolve, reject) => {
        try {

            contenedorConfirmar.classList.add("active-popup");


            cancelFn = function () {
                cerrar_confirmar();
                resolve(false);
            };

            closeButton.addEventListener('click', cancelFn);
            cancelButton.addEventListener('click', cancelFn);

            acceptFn = function () {
                cerrar_confirmar();
                resolve(true);
            }

            acceptButton.addEventListener('click', acceptFn);
        } catch (error) {
            reject(error);
        }
    });
}

function dropEventListeners() {
    closeButton.removeEventListener('click', cancelFn);
    cancelButton.removeEventListener('click', cancelFn);
    acceptButton.removeEventListener('click', acceptFn);
}

function cerrar_confirmar() {
    contenedorConfirmar.classList.remove("active-popup");
    dropEventListeners();
    setTimeout(function () {
        contenedorConfirmar.classList.remove("active");
    }, 500);
}