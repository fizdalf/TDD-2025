import {resolveGrid} from "../logic/GameLogic.js";

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
                y: 0
            },
            direction: 'Down',
        },
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
            direction: 'Right',
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
            direction: 'Up',
        },
        {
            playerPosition: {
                x: 3,
                y: 2
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