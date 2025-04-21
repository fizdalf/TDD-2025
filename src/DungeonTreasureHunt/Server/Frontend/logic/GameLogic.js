export function resolveGrid(grid) {
    return fetch('/play', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(grid)
    }).then(response => response.json());
}