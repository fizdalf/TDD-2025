export function resolveGrid(grid) {
    return fetch('/index.php?action=play', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(grid)
    }).then(response => response.json());
}