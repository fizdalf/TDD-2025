const columnNumber = 4
const rowNumber = 4

const contenedor = document.querySelector('.contenedor');

const gridManager = new GridManager(rowNumber, columnNumber);
const button = document.getElementById('comprobar');

gridManager.createEmptyGrid();
gridManager.addEventListener("cellChange", updateCell);
gridManager.addEventListener("gameStateChange", (state) => {
    button.disabled = !state;
});
gridManager.addEventListener("reset", resetGrid);

let activeTool = null;
const Tile = {
    player: 'P',
    treasure: 'T',
    wall: '#',
    path: '.',
}

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

function resolveGrid(grid) {
    return fetch('/index.php?action=play', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(grid)
    })
        .then(response => response.json());
}


function applyColor(movements, index) {
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
    resolveGrid(gridManager.grid)
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

function login(event) {
    event.preventDefault();

    let usernameInput = document.querySelector(".caja-input input[type='text']").value;
    let passwordInput = document.querySelector(".caja-input input[type='password']").value;
    let rememberMe = document.querySelector("input[type='checkbox']").checked;

    let username = usernameInput;
    let password = passwordInput;

    console.log("Nombre de usuario: ", username);
    console.log("Contraseña: ", password);

    return fetch('/index.php?action=login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({username, password})
    })
        .then(response => response.json())
        .then(data => {
            if (data.token) {

                if (rememberMe) {
                    localStorage.setItem('token', data.token);
                }
                sessionStorage.setItem('token', data.token);

                cerrar();
                checkLoginStatus();

                return true;
            } else {
                alert('Credenciales incorrectas');
                return false;
            }
        })
        .catch(error => {
            console.error('Error en la solicitud de login:', error);
            alert('Hubo un error al intentar iniciar sesión');
            return false;
        })

        .finally(() => {
            clearInputs();
        });

    function clearInputs() {
        usernameInput.value = "";
        passwordInput.value = "";
    }
}

function checkLoginStatus() {
    const token = localStorage.getItem('token') || sessionStorage.getItem('token');
    const loginButton = document.querySelector(".botonLogin-popup");

    if (token) {
        console.log("Estás iniciado sesión.");
        if (loginButton) {
            sesion_iniciada();
        }
    } else {
        console.log("No estás iniciado sesión.");
        if (loginButton) {
            sesion_no_iniciada()
        }
    }

    function sesion_iniciada() {
        loginButton.style.display = "none";
        document.querySelector('nav').style.width = '85%';
        document.querySelector('#contenedor-boton').style.width = '85%';
        document.querySelector('#grid').style.width = '60%';
    }

    function sesion_no_iniciada() {
        loginButton.style.display = "block";
        document.querySelector('nav').style.width = '';
        document.querySelector('#contenedor-boton').style.width = '100%';
        document.querySelector('#grid').style.width = '78%';
    }
}

checkLoginStatus()

const botonLogin = document.querySelector(".boton-login");
if (botonLogin) {
    botonLogin.addEventListener("click", login)
}

function saveGrid(grid) {

    {
        const token = localStorage.getItem('token');


        return fetch('/index.php?action=save-grid', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + token
            },
            body: JSON.stringify({grid: grid})
        }).then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Grid guardado exitosamente');
                } else {
                    alert('Error al guardar el grid');
                }
            })
            .catch(error => {
                console.error('Error al intentar guardar el grid:', error);
                alert('Hubo un error al guardar el grid');
            });
    }
}


document.Tester.registerTest('[ResolveGrid] it should a list of steps for a solved laberynth', async function () {
    const grid = [
        ['P', '#', '#', '#'],
        ['.', '#', '#', 'T'],
        ['.', '#', '#', '.'],
        ['.', '.', '.', '.'],
    ]
    const result = await resolveGrid(grid);

    const expected = [
        {
            playerPosition: {
                x: 0,
                y: 1
            },
            direction: 'Down',
        },
        {
            playerPosition: {
                x: 0,
                y: 2
            },
            direction: 'Down',
        },
        {
            playerPosition: {
                x: 0,
                y: 3
            },
            direction: 'Down',
        },
        {
            playerPosition: {
                x: 1,
                y: 3
            },
            direction: 'Right',
        },
        {
            playerPosition: {
                x: 2,
                y: 3
            },
            direction: 'Right',
        },
        {
            playerPosition: {
                x: 3,
                y: 3
            },
            direction: 'Right',
        },
        {
            playerPosition: {
                x: 3,
                y: 2
            },
            direction: 'Up',
        },
        {
            playerPosition: {
                x: 3,
                y: 1
            },
            direction: 'Up',
        },

    ]

    const expectedText = JSON.stringify(expected);
    const resultText = JSON.stringify(result);
    if (expectedText !== resultText) {
        throw new Error(expectedText + ' is not equals to ' + resultText);
    }

})

document.Tester.registerTest('[updateCell] it should update the grid correctly when a player moves', async function () {
    const gridManager = new GridManager(4,4);

    gridManager.updateCell(0,0, 'P');

    const gridAfterPlayerMove = gridManager.getGrid();
    if (gridAfterPlayerMove[0][0] !== 'P') {
        throw new Error("El jugador no se ha actualizado")
    }

    gridManager.updateCell(1,1,'P');

    if (gridAfterPlayerMove[1][1] !== 'P') {
        throw new Error("No se movio al lugar correcto")
    }

    if (gridAfterPlayerMove[0][0] !== null) {
        throw new Error("La posicion anterior no ha sido limpiada")
    }
})

document.Tester.registerTest('[GameStateChange] it should update the game state when player and treasure exists', async function () {
    const gridManager = new GridManager(4, 4);


    gridManager.updateCell(0,0,'P');
    gridManager.updateCell(3,3, 'T');

    let gameStatedUpdated = false;
    gridManager.addEventListener("gameStateChange", (state) => {
        gameStatedUpdated = state;
    });

    gridManager.updateCell(0, 0, 'P'); 
    gridManager.updateCell(0, 1, '#');
    gridManager.updateCell(0, 2, '#');
    gridManager.updateCell(0, 3, '#');

    gridManager.updateCell(1, 0, '.');
    gridManager.updateCell(1, 1, '#');
    gridManager.updateCell(1, 2, '#');
    gridManager.updateCell(1, 3, 'T');

    gridManager.updateCell(2, 0, '.');
    gridManager.updateCell(2, 1, '#');
    gridManager.updateCell(2, 2, '#');
    gridManager.updateCell(2, 3, '.');

    gridManager.updateCell(3, 0, '.');
    gridManager.updateCell(3, 1, '.');
    gridManager.updateCell(3, 2, '.');
    gridManager.updateCell(3, 3, '.');


    await new Promise(resolve => setTimeout(resolve, 500));

    if (!gameStatedUpdated) {
        throw new Error("El estado del juego no se actualizo")
    }
});