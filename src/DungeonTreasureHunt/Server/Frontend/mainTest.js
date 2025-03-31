import { getColorIndices } from "./main.js";

document.Tester.registerTest('[applyColor] it should calculate correct indices', function () {
    const gridManager = { cols: 4 };

    let movements = [
        { playerPosition: { x: 0, y: 0 } },
        { playerPosition: { x: 1, y: 0 } },
        { playerPosition: { x: 2, y: 0 } },
        { playerPosition: { x: 3, y: 0 } },
        { playerPosition: { x: 3, y: 1 } },
        { playerPosition: { x: 3, y: 2 } },
        { playerPosition: { x: 3, y: 3 } }
    ];

    const expectedIndices = [0, 1, 2, 3, 7, 11, 15];
    const result = getColorIndices(movements, gridManager);

    if (JSON.stringify(result) !== JSON.stringify(expectedIndices)) {
        throw new Error(`Los índices esperados no coinciden con los obtenidos: ${result}`);
    }
});
