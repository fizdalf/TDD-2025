
const storageKey = "token";

export function doLogin(username, password, rememberMe) {
    return fetch('/login', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({username, password})
    })
        .then(response => response.json())
        .then(data => {
            if (!data.token) {
                throw new Error('Wrong credentials');
            }
            if (rememberMe) {
                localStorage.setItem(storageKey, data.token);
                return;
            }
            sessionStorage.setItem(storageKey, data.token);
        });
}


export function isUserLoggedIn() {
    return !!(localStorage.getItem(storageKey) || sessionStorage.getItem(storageKey));
}


export function cerrarSesion() {
    localStorage.removeItem(storageKey);
    sessionStorage.removeItem(storageKey);
}


