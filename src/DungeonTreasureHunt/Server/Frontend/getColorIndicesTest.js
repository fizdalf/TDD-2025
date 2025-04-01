import { getColorIndices } from "./getColorIndices.js";

document.Tester.registerTest('[GetColorIndices] it should return correct indices from movements', function () {

    const movements = [
        { playerPosition: { x: 0, y: 0 } },
        { playerPosition: { x: 1, y: 0 } },
        { playerPosition: { x: 2, y: 1 } },
        { playerPosition: { x: 3, y: 2 } },
    ];

    const gridManager = {
        cols: 4
    };

    const result = getColorIndices(movements, gridManager);
    const expected = [0, 1, 6, 11];

    const expectedText = JSON.stringify(expected);
    const resultText = JSON.stringify(result);

    if (expectedText !== resultText) {
        throw new Error(expectedText + ' is not equal to ' + resultText);
    }
});
