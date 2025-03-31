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