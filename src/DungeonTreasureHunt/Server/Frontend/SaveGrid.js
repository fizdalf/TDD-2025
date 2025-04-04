import {getCurrentToken} from "./Login.js";

export function SaveGrid(grid, gridName) {
    const token = getCurrentToken();

    if (!token) {
        throw new Error('User should be authenticated');
    }

    return fetch('/grids', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify({grid, gridName})
    }).then(response => response.json());
}