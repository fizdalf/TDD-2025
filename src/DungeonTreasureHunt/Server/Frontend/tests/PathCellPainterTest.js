import {pathCellPainter} from "../components/PathCellPainter.js";
import {getLinearIndexFrom2DGrid} from "../logic/getColorIndices.js";
import {GridManager} from "../logic/GridManager.js";

document.Tester.registerTest('[PathCellPainter] it should color the correct cells in order', async function () {

    const columns = 4;
    const rows = 4;
    const movements = [
        {playerPosition: {x: 0, y: 0}, direction: 'Right'},
        {playerPosition: {x: 0, y: 1}, direction: 'Right'},
        {playerPosition: {x: 0, y: 2}, direction: 'Down'},
        {playerPosition: {x: 1, y: 2}, direction: 'Down'},
    ];

    const gridManager = new GridManager(4, columns);

    const originalBody = document.body.innerHTML;

    document.body.innerHTML = '<div id="grid">' +
        Array(columns * rows).fill('<div class="celda"></div>').join('') + '</div>';

    await pathCellPainter(movements, gridManager);

    const expectedIndices = movements.map(m => getLinearIndexFrom2DGrid(m.playerPosition.x, m.playerPosition.y, columns));
    const coloredIndices = Array.from(document.querySelectorAll('.celda')).reduce(
        (acc, cell, index) => {
            if (cell.classList.contains('color')) {
                acc.push(index);
            }
            return acc;
        },
        []
    );

    const expectedText = JSON.stringify(expectedIndices);
    const resultText = JSON.stringify(coloredIndices);

    document.body.innerHTML = originalBody;
    if (expectedText !== resultText) {
        throw new Error(expectedText + ' is not equal to ' + resultText);
    }
});