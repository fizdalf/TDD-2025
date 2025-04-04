export function SaveGrid(grid) {
    const token = localStorage.getItem("token") || sessionStorage.getItem("token")

    return fetch('/grids', {
        method: 'POST',
        headers: {'Content-Type': 'application/json',
            'Authorization' : `Bearer ${token}`
        },
        body: JSON.stringify({grid})
    }).then(response => response.json());
}