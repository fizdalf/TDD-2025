import {getColorIndices} from "./getColorIndices";

export function pathCellPainter(movements, gridManager) {

    const indices = getColorIndices(movements, gridManager);

    function colorStep(i) {
        if (i < indices.length) {
            let cell = document.querySelectorAll('.celda')[indices[i]];
            if (cell) {
                cell.classList.add('color');
            }
            setTimeout(() => colorStep(i + 1), 500);
        }
    }

    colorStep(0);
}