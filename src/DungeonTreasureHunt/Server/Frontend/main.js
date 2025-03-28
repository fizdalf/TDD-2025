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


function login(event) {
    event.preventDefault();

    let usernameInput = document.querySelector(".caja-input input[type='text']");
    let passwordInput = document.querySelector(".caja-input input[type='password']");
    let rememberMe = document.querySelector("input[type='checkbox']").checked;

    let username = usernameInput.value;
    let password = passwordInput.value;

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
                sesion_iniciada();

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

function isUserLoggedIn() {
    const token = localStorage.getItem('token') || sessionStorage.getItem('token');
    const isLoggedIn = !token
}

function checkAlreadyLogged() {
    if (!isUserLoggedIn()) {
        return;
    }
    sesion_iniciada();
}

function sesion_iniciada() {
    document.getElementById('iniciar-sesion').style.display = "none";
    document.getElementById('cerrar-sesion').style.display = "block";
    document.querySelector('nav').style.width = '85%';
    document.querySelector('#contenedor-boton').style.width = '85%';
    document.querySelector('#grid').style.width = '60%';
}

function sesion_no_iniciada() {
    document.getElementById('iniciar-sesion').style.display = "block";
    document.getElementById('cerrar-sesion').style.display = "none";
    document.querySelector('nav').style.width = '';
    document.querySelector('#contenedor-boton').style.width = '100%';
    document.querySelector('#grid').style.width = '78%';
}

function cerrar_sesion() {
    localStorage.removeItem('token');
    sessionStorage.removeItem('token');
    sesion_no_iniciada();
}

checkAlreadyLogged()

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


document.Tester.registerTest('[applyColor] it should apply color to all movement cells', async function () {

    const cols = 4;

    let movements = [
        {playerPosition: {x: 0, y: 0}},
        {playerPosition: {x: 1, y: 0}},
        {playerPosition: {x: 2, y: 0}},
        {playerPosition: {x: 3, y: 0}},
        {playerPosition: {x: 3, y: 1}},
        {playerPosition: {x: 3, y: 2}},
        {playerPosition: {x: 3, y: 3}}
    ];

    applyColor(movements, 0);

    await new Promise(resolve => setTimeout(resolve, movements.length * 500 + 500))

    movements.forEach(({playerPosition}) => {
        let {x, y} = playerPosition;
        let indexCell = cols * y + x;
        let cell = document.querySelectorAll('.celda')[indexCell];

        if (!cell.classList.contains('color')) {
            throw new Error(`La celda (${x},${y})no se coloreo`)
        }
    });
});

