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
let player = "P";
let treasure = "T";
let wall = "#";
let path = ".";

function updateCursor() {
    let cursorStyle = activeTool ? "pointer" : "default";
    document.querySelectorAll('.celda').forEach(c => c.style.cursor = cursorStyle);
}

function updateCell(value, row, col) {
    index = columnNumber * row + col;
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
    console.log("Grid enviado:", grid);

    return fetch('/index.php?action=play', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(grid)
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor: ' + response.statusText);
            }
            return response.text(); // Obtener el contenido como texto para ver si está vacío
        })
        .then(responseText => {
            if (responseText.trim() === "") {
                throw new Error('La respuesta del servidor está vacía');
            }
            return JSON.parse(responseText); // Parsear el JSON si no está vacío
        })
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

        let symbol = "";
        switch (activeTool) {
            case "player":
                symbol = player;
                break;
            case "treasure":
                symbol = treasure;
                break;
            case "wall":
                symbol = wall;
                break;
            case "path":
                symbol = path;
                break;
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

function testResolveGrid() {
    console.log('this tests the resolve grid from the server');
}