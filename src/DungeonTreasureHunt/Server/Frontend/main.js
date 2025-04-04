import {GridManager} from "./GridManager.js";
import {isUserLoggedIn, cerrarSesion, doLogin} from './Login.js';
import {Tile} from './Tile.js';
import {resolveGrid} from "./GameLogic.js";
import {pathCellPainter} from "./PathCellPainter.js";
import {SaveGrid} from "./SaveGrid.js";


const columnNumber = 4;
const rowNumber = 4;
const gridManager = new GridManager(rowNumber, columnNumber);
const button = document.getElementById('comprobar');
let activeTool = null;

gridManager.createEmptyGrid();

gridManager.addEventListener("cellChange", (value, row, col) => updateCell(value, row, col, columnNumber));
gridManager.addEventListener("gameStateChange", (state) => {
    button.disabled = !state;
});
gridManager.addEventListener("reset", resetGridUI);

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
        updateCursor(activeTool);
    });
});

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
    });
});

document.getElementById('restablecer').addEventListener('click', function () {
    gridManager.resetGrid();
    resetGridUI();
});

document.getElementById('cierre').addEventListener('click', function () {
    cerrar();
});

document.getElementById('cierre-input').addEventListener('click', function () {
    cerrar_input();
});

document.getElementById('comprobar').addEventListener('click', function () {
    responseGrid();

});

document.getElementById('cerrar-sesion').addEventListener("click", () => {
    cerrarSesion();
    sesionNoIniciada();
});

const boton_comprobar_input = document.querySelector(".boton-comprobar-input");
if (boton_comprobar_input) {
    boton_comprobar_input.addEventListener("click", (event) => {
        event.preventDefault();
        const name = document.querySelector("#nombre-input input[type='text']");
        SaveCurrentGrid(name.value);
    })
}

const botonLogin = document.querySelector(".boton-login");
if (botonLogin) {
    botonLogin.addEventListener("click", (event) => {
        event.preventDefault();
        const usernameInput = document.querySelector(".caja-input input[type='text']");
        const passwordInput = document.querySelector(".caja-input input[type='password']");
        const rememberMeInput = document.querySelector("input[type='checkbox']");

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
                passwordInput.checked = false;
            });
    });
}

function checkAlreadyLogged() {
    if (isUserLoggedIn()) {
        sesionIniciada();
    }
}

checkAlreadyLogged();
getStoredGrids();

function responseGrid() {
    resolveGrid(gridManager.getGrid())
        .then(movements => {
            if (movements.length === 0) {
                alert("No es posible llegar hasta el tesoro");
                gridManager.resetGrid();
                return;
            }

            pathCellPainter(movements, gridManager);
        })
        .catch(error => {
            console.error("Error en la resolución del grid:", error);
            alert("Hubo un error en la resolución del grid.");
        });
}

function SaveCurrentGrid(gridName) {
    const grid = gridManager.getGrid()

    if (!gridName) {
        alert("El grid no tiene nombre, no se guardará.");
        return;
    }
    cerrar_input();
    SaveGrid(grid, gridName)
        .then(response => {
            if (response.error) {
                console.error("Error al guardar el grid: ", response.error);
                alert("Hubo un error al guardar el grid");
            } else {
                getStoredGrids();
            }
        })
        .catch(error => {
            console.error(error);
            alert("Hubo un error en la solicitud");
        });
}

function getStoredGrids() {
    const token = localStorage.getItem("token") || sessionStorage.getItem("token");
    if (!token) {
        console.error("No se encontró el token de autenticación.");
        return;
    }

    const headers = {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
    };

    fetch('/grids', {
        method: 'GET',
        headers: headers
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const contenedor = document.getElementById("contenedor-laberintos-guardados");
                contenedor.innerHTML = "";
                if (contenedor) {
                    Object.keys(data.grids).forEach(id => {
                        const gridInfo = data.grids[id];
                        const gridName = gridInfo.gridName;
                        const grid = gridInfo.grid;

                        const gridDiv = document.createElement("div");
                        gridDiv.classList.add("laberinto-guardado");
                        gridDiv.innerHTML = `<p> ${gridName} </p>
                    <div class="edit"><img src="iconos/create-outline.svg" alt="edit"></div>
                    <span class="vacio"></span> 
                    <div class="delete"><img src="iconos/trash-outline.svg" alt="delete"></div>
                    <span class="vacio"></span> `;

                        gridDiv.addEventListener("click", () => {
                            gridManager.setGrid(grid)

                        })

                        contenedor.appendChild(gridDiv);
                    });
                } else {
                    console.error("contenedor no existe")
                }
            } else {
                console.error("Error al obtener los grids:", data.error);
            }
        })
        .catch(error => {
            console.error("Error en la solicitud:", error);
        });
}


function updateCursor(activeTool) {
    let cursorStyle = activeTool ? "pointer" : "default";
    document.querySelectorAll('.celda').forEach(c => c.style.cursor = cursorStyle);
}

function updateCell(value, row, col, columnNumber) {
    const index = columnNumber * row + col;
    let cell = document.querySelectorAll('.celda')[index];
    if (cell) {
        cell.querySelector('p').textContent = value;
    }
}

function resetGridUI() {
    document.querySelectorAll('.celda p').forEach(p => p.textContent = "");
    document.querySelectorAll('.utiles').forEach(u => u.classList.remove('util-seleccionado'));
    document.querySelectorAll('.color').forEach(element => {
        element.classList.remove('color');
    });
}

function sesionIniciada() {
    document.getElementById('contenedor-laberintos').querySelectorAll('h3').forEach(h3 => h3.remove());
    document.getElementById('contenedor-laberintos-guardados').style.display = 'flex';
    document.getElementById('iniciar-sesion').style.display = "none";
    document.getElementById('cerrar-sesion').style.display = "block";
    document.getElementById('boton-input').disabled = false;
}

function sesionNoIniciada() {
    const parrafo = document.createElement('h3');
    document.getElementById('contenedor-laberintos-guardados').style.display = 'none';
    parrafo.textContent = "Inicia sesión para poder guardar laberintos";
    document.getElementById('iniciar-sesion').style.display = "block";
    document.getElementById('cerrar-sesion').style.display = "none";
    document.getElementById('contenedor-laberintos').appendChild(parrafo);
    document.getElementById('boton-input').disabled = true;
}

document.Tester.registerTest('[Grids][Success] should save a grid and retrieve it', async function () {

    const loginResponse = await fetch('/login', {
            method: 'POST',
            headers:
                {
                    'Content-Type': 'application/json',
                },
            body: JSON.stringify({
                username: 'admin',
                password: '1234'
            })
        }
    );

    const loginData = await loginResponse.json();

    const token = loginData.token;


    const grid = [
        ['.', '.', '#', '.'],
        ['#', 'P', '#', '.'],
        ['.', '.', 'T', '.'],
        ['.', '.', '.', '.']
    ]

    const gridName = "TestName"

    const response = await fetch('/grids', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': token
        },
        body: JSON.stringify({grid, gridName})
    });

    const data = await response.json();
    if (!data.success) {
        throw new Error('Failed to save grid: ' + data.error);
    }


    const retrieveGridResponse = await fetch('/grids', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': token
        },
    });

    const retrieveGridData = await retrieveGridResponse.json();

    if (!retrieveGridData.success) {
        throw new Error('Failed to retrieve grid: ' + retrieveGridData.error);
    }

    const expected = {
        "1": {
            "gridName": gridName,
            grid
        }
    }


    if (JSON.stringify(retrieveGridData.grids) !== JSON.stringify(expected)) {
        throw new Error(`Expected grid ${JSON.stringify(expected)} is not equals to ${JSON.stringify(retrieveGridData.grids)}`);
    }

    // añadir borrar el grid creado y confirmar que está borrado
});

