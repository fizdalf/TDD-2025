document.Tester.registerTest('[Api][Grid] should save a grid and retrieve it', async function () {

    const loginResponse = await fetch('/login', {
            method: 'POST',
            headers:
                {
                    'Content-Type': 'application/json',
                },
            body: JSON.stringify({
                username: 'admin',
                password: '1234'
            })
        }
    );

    const loginData = await loginResponse.json();

    const token = loginData.token;


    const grid = [
        ['.', '.', '#', '.'],
        ['#', 'P', '#', '.'],
        ['.', '.', 'T', '.'],
        ['.', '.', '.', '.']
    ]

    const gridName = "TestName"

    const response = await fetch('/grids', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify({grid, gridName})
    });

    const data = await response.json();
    if (!data.success) {
        throw new Error('Failed to save grid: ' + data.error);
    }


    const retrieveGridResponse = await fetch('/grids', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
        },
    });

    const retrieveGridData = await retrieveGridResponse.json();

    if (!retrieveGridData.success) {
        throw new Error('Failed to retrieve grid: ' + retrieveGridData.error);
    }

    const expected = {
        "1": {
            "gridName": gridName,
            grid
        }
    }


    if (JSON.stringify(retrieveGridData.grids) !== JSON.stringify(expected)) {
        throw new Error(`Expected grid ${JSON.stringify(expected)} is not equals to ${JSON.stringify(retrieveGridData.grids)}`);
    }

    // añadir borrar el grid creado y confirmar que está borrado
});
