import {GridManager} from "./GridManager.js";
import {isUserLoggedIn, cerrarSesion, doLogin} from './Login.js';
import {Tile} from './Tile.js';

const columnNumber = 4

const rowNumber = 4

const gridManager = new GridManager(rowNumber, columnNumber);
const button = document.getElementById('comprobar');

gridManager.createEmptyGrid();
gridManager.addEventListener("cellChange", updateCell);
gridManager.addEventListener("gameStateChange", (state) => {
    button.disabled = !state;
});
gridManager.addEventListener("reset", resetGrid);

let activeTool = null;


function updateCursor() {
    let cursorStyle = activeTool ? "pointer" : "default";
    document.querySelectorAll('.celda').forEach(c => c.style.cursor = cursorStyle);
}

function updateCell(value, row, col) {
    const index = columnNumber * row + col;
    let cell = document.querySelectorAll('.celda')[index];
    if (cell) {
        cell.querySelector('p').textContent = value;
    }
}


document.querySelectorAll('.utiles').forEach(util => {
    util.addEventListener('click', function () {
        if (activeTool === this.dataset.tool) {
            activeTool = null;
            this.classList.remove('util-seleccionado');
        } else {
            document.querySelectorAll('.utiles').forEach(element => element.classList.remove('util-seleccionado'));
            activeTool = this.dataset.tool;
            this.classList.add('util-seleccionado');
        }
        updateCursor();
    })
})

export function resolveGrid(grid) {
    return fetch('/index.php?action=play', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(grid)
    })
        .then(response => response.json());
}


export function applyColor(movements, index) {
    if (index < movements.length) {
        let {x, y} = movements[index].playerPosition;
        let indexCell = gridManager.cols * y + x;
        let cell = document.querySelectorAll('.celda')[indexCell];

        if (cell) {
            cell.classList.add('color');
        }

        setTimeout(() => applyColor(movements, index + 1), 500);
    }

}


document.querySelectorAll('.celda').forEach((celda, index) => {

    const row = Math.floor(index / gridManager.cols);
    const col = index % gridManager.cols;

    celda.addEventListener('click', function () {
        if (!activeTool) return;
        const symbol = Tile[activeTool];
        if (!symbol) {
            throw new Error('Symbol not supported!');
        }

        gridManager.updateCell(row, col, symbol);
    })
})

function printGrid() {
    console.log(gridManager.getGrid());
}

function responseGrid() {
    resolveGrid(gridManager.getGrid())
        .then(movements => {
            if (movements.length === 0) {
                alert("No es posible llegar hasta el tesoro");
                gridManager.resetGrid();
                return;
            }
            applyColor(movements, 0);
        })
        .catch(error => {
            console.error("Error en la resolución del grid:", error);
            alert("Hubo un error en la resolución del grid.");
        });
}

function resetGrid() {
    printGrid();
    document.querySelectorAll('.celda p').forEach(p => p.textContent = "");

    document.querySelectorAll('.utiles').forEach(u => u.classList.remove('util-seleccionado'))
    activeTool = null;
    updateCursor();

    document.querySelectorAll('.color').forEach(element => {
        element.classList.remove('color');
    })
}

document.getElementById('restablecer').addEventListener('click', function () {
    gridManager.resetGrid();
    resetGrid();
})


function checkAlreadyLogged() {
    if (isUserLoggedIn()) {
        sesionIniciada();
    }
}

checkAlreadyLogged();

const botonLogin = document.querySelector(".boton-login");
if (botonLogin) {
    botonLogin.addEventListener("click", (event) => {
        event.preventDefault();

        const usernameInput = document.querySelector(".caja-input input[type='text']");
        const passwordInput = document.querySelector(".caja-input input[type='password']");
        const rememberMeInput = document.querySelector("input[type='checkbox']")

        const rememberMe = rememberMeInput.checked;
        const username = usernameInput.value;
        const password = passwordInput.value;

        doLogin(username, password, rememberMe)
            .then(() => {
                sesionIniciada();
                cerrar();
            })
            .catch(error => {
                alert('Hubo un error al intentar iniciar sesión: ' + error);
            })
            .finally(() => {
                usernameInput.value = "";
                passwordInput.value = "";
                rememberMeInput.checked = false;
            });

    });
}

function sesionIniciada() {
    document.getElementById('iniciar-sesion').style.display = "none";
    document.getElementById('cerrar-sesion').style.display = "block";
    document.querySelector('nav').style.width = '85%';
    document.querySelector('#contenedor-boton').style.width = '85%';
    document.querySelector('#grid').style.width = '60%';
}

document.getElementById('cerrar-sesion').addEventListener("click", () => {
    cerrarSesion();
    sesionNoIniciada();
});


function sesionNoIniciada() {
    document.getElementById('iniciar-sesion').style.display = "block";
    document.getElementById('cerrar-sesion').style.display = "none";
    document.querySelector('nav').style.width = '';
    document.querySelector('#contenedor-boton').style.width = '100%';
    document.querySelector('#grid').style.width = '78%';
}


