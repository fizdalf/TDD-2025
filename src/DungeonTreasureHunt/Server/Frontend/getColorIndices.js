export function getColorIndices(movements, gridManager) {
    return movements.map(({ playerPosition }) => {
        let { x, y } = playerPosition;
        return gridManager.cols * y + x;
    });
}