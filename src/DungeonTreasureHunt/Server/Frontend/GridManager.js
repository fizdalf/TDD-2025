class GridManager {
    #grid;
    #eventListeners = {};
    constructor(rows, cols) {
        this.rows = rows;
        this.cols = cols;
        this.#grid = this.createEmptyGrid();
        this.#eventListeners = {};
    }

    createEmptyGrid() {
        const grid = [];
        for (let i = 0; i < this.rows; i++) {
            const row = [];
            for (let j = 0; j < this.cols; j++) {
                row.push(null);
            }
            grid.push(row);
        }
        return grid;
    }

    updateCell(row, col, value) {

        if (!(row >= 0 && row < this.rows && col >= 0 && col < this.cols)) {
            return;
        }

        if (value === "P") {
            for (let r = 0; r < this.rows; r++) {
                for (let c = 0; c < this.cols; c++) {
                    if (this.#grid[r][c] === "P" ) {
                    // && (
                    //         row === r && col !== c || row !== r && col === c || row !== r && col !== c
                    //     )
                        this.#grid[r][c] = null;
                        this.#informCellChanges(null, r, c);
                        break;
                    }
                }
            }
        }
        this.#grid[row][col] = value;
        this.#informCellChanges(value, row, col);
        this.#checkGameState();
    }

    getGrid() {
        return this.#grid;
    }

    resetGrid() {
        this.#grid = this.createEmptyGrid();
        this.#informGameStateChanges(false);
        this.#informReset();
    }

    #informCellChanges(value, row, col) {
        let eventName = "cellChange";
        if ((eventName in this.#eventListeners)) {
            let listeners = this.#eventListeners[eventName];
            for (const listener of listeners) {
                listener(value, row, col);
            }
        }
    }

    #informReset() {
        let eventName = "reset";
        if ((eventName in this.#eventListeners)) {
            let listeners = this.#eventListeners[eventName];
            for (const listener of listeners) {
                listener();
            }
        }
    }

    #checkGameState() {
        let hasPlayer = false;
        let hasTreasure = false;
        let isFull = true;

        for (let row of this.#grid) {
            for (let cell of row) {
                if (cell === null) {
                    isFull = false;
                }
                if (cell === Tile.player) {
                    hasPlayer = true;
                }
                if (cell === Tile.treasure) {
                    hasTreasure = true;
                }
            }
        }
        this.#informGameStateChanges(isFull && hasPlayer && hasTreasure);
    }

    #informGameStateChanges(state) {
        if ("gameStateChange" in this.#eventListeners) {
            this.#eventListeners["gameStateChange"].forEach(listener => listener(state));
        }
    }

    addEventListener(eventName, listener) {
        if (!(eventName in this.#eventListeners)) {
            this.#eventListeners[eventName] = [];
        }
        this.#eventListeners[eventName].push(listener);
    }
}