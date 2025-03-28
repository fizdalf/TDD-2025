document.Tester.registerTest('[GridManager][GameStateChange] it should raise a gameStateChange event when the grid is full and it contains a player and at least one treasure', async function () {
    const gridManager = new GridManager(4, 4);

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

    const result = await new Promise((resolve, reject) => {
        gridManager.addEventListener("gameStateChange", (state) => {
            resolve(state);
        });
        gridManager.updateCell(0, 0, 'P');
        setTimeout(() => reject("Timed out while waiting for the state to be updated"), 500);
    });

    if (result !== true) {
        throw new Error('Failed to assert the game state is true');
    }
});

document.Tester.registerTest('[GridManager][updateCell] it should update the grid correctly when a player moves', async function () {
    const gridManager = new GridManager(4, 4);

    gridManager.updateCell(0, 0, 'P');

    const gridAfterPlayerMove = gridManager.getGrid();
    if (gridAfterPlayerMove[0][0] !== 'P') {
        throw new Error("El jugador no se ha actualizado")
    }

    gridManager.updateCell(1, 1, 'P');

    if (gridAfterPlayerMove[1][1] !== 'P') {
        throw new Error("No se movio al lugar correcto")
    }

    if (gridAfterPlayerMove[0][0] !== null) {
        throw new Error("La posicion anterior no ha sido limpiada")
    }
});

document.Tester.registerTest('[GridManager][createEmptyGrid]it should initialize an empty grid', async function () {
    const gridManger = new GridManager(4, 4);

    const grid = gridManger.getGrid();
    const expectedGrid = [
        [null, null, null, null],
        [null, null, null, null],
        [null, null, null, null],
        [null, null, null, null],
    ]

    if (JSON.stringify(grid) !== JSON.stringify(expectedGrid)) {
        throw new Error("El grid no es el esperado")
    }
});

document.Tester.registerTest('[GridManager][informCellChanges] it should call informCellChanges when a cell changes',async function () {
    const gridManager = new GridManager(4,4);

    let llamation = false;
    let valorEsperado = 'P';
    let rowEsperado = 2;
    let colEsperado = 1;

    gridManager.addEventListener("cellChange",(value,row,col) => {
        if (value === valorEsperado && row === rowEsperado && col === colEsperado) {
            llamation = true;
        }
    });

    gridManager.updateCell(2,1,'P');

    if (!llamation) {
        throw new Error("no se llamo a informaChanges")
    }
});


document.Tester.registerTest('[GridManager][informCellChanges] it should clear previous P when moved',async function () {
    const gridManager = new GridManager(4,4);

    gridManager.updateCell(0,0,'P');

    let anteriorLimpiado = false;
    let nuevaPosicion = false;

    gridManager.addEventListener("cellChange",(value,row,col) => {
        if (value === null && row === 0 && col === 0) {
            anteriorLimpiado = true;
        }
        if (value === 'P' && row === 1 && col === 1) {
            nuevaPosicion = true;
        }
    });
        gridManager.updateCell(1,1,'P');

        if (!anteriorLimpiado) {
            throw new Error('El anterior no fue limpiado')
        }
        if (!nuevaPosicion) {
            throw new Error('No se puse la nueva posicion')
        }
    });

document.Tester.registerTest('[GirdManager][informReset] it should call informReset',async function () {
    const gridManager = new GridManager(4,4);

    gridManager.updateCell(0,0,'P');
    gridManager.updateCell(0,0,'T');

    let resetCalled = false;

    gridManager.addEventListener("reset", () => {
        resetCalled = true;
    });

    gridManager.resetGrid();

    if (!resetCalled) {
        throw new Error('No se informo al informReset')
    }
})

