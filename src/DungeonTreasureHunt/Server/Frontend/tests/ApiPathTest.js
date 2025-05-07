document.Tester.registerTest('[Api][Grid] should save a grid and retrieve it', async function () {

    const loginResponse = await fetch('/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            username: 'admin',
            password: '1234'
        })
    });

    const loginData = await loginResponse.json();
    const token = loginData.token;

    const grid = [
        ['.', '.', '#', '.'],
        ['#', 'P', '#', '.'],
        ['.', '.', 'T', '.'],
        ['.', '.', '.', '.']
    ];

    const gridName = "TestName";

    const response = await fetch('/grids', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify({grid, gridName})
    });

    const data = await response.json();
    if (data.status !== 'success') {
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

    if (retrieveGridData.status !== 'success') {
        throw new Error('Failed to retrieve grid: ' + retrieveGridData.error);
    }

    const retrievedGrid = retrieveGridData.grids.find(g => g.name === gridName);

    if (!retrievedGrid) {
        throw new Error('Saved grid not found in retrieval');
    }

    const expected = [
        {
            id: retrievedGrid.id,
            name: gridName,
            grid: grid
        }
    ];

    if (JSON.stringify([retrievedGrid]) !== JSON.stringify(expected)) {
        throw new Error(`Expected grid ${JSON.stringify(expected)} is not equal to ${JSON.stringify([retrievedGrid])}`);
    }

    const deleteResponse = await fetch(`/grids/${retrievedGrid.id}`, {
        method: 'DELETE',
        headers: {
            'Authorization': `Bearer ${token}`
        }
    });

    const deleteData = await deleteResponse.json();

    if (deleteData.status !== 'success') {
        throw new Error(`Failed to delete grid: ${deleteData.error}`);
    }
});
