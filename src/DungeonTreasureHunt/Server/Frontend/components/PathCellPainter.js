import {getLinearIndexFrom2DGrid} from "../logic/getColorIndices.js";


export function pathCellPainter(movements, gridManager) {
    const duration = 500;
    const cells = document.querySelectorAll('.celda');

    const promises = movements.map(async (movement, index) => {
            await delay(duration * index);
            const linearIndex = getLinearIndexFrom2DGrid(movement.playerPosition.x, movement.playerPosition.y, gridManager.cols);
            let cell = cells[linearIndex];
            if (cell) {
                cell.classList.add('color');
            }
        }
    );

    return Promise.all(promises);
}


function delay(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}