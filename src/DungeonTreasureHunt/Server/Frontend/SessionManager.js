class SessionManager {
    constructor() {
        this.loadSession();
    }

    loadSession() {
        this.token = localStorage.getItem("token") || sessionStorage.getItem("token");
        this.username = localStorage.getItem("username") || sessionStorage.getItem("username");
    }

    isLoggedIn() {
        return !!this.token;
    }

    async login(username, password, rememberMe = false) {
        try {

            const response = await fetch('/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username, password })
            });

            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status} - ${response.statusText}`);
            }

            const data = await response.json();

            if (!data) {
                throw new Error('La respuesta del servidor no tiene el formato esperado');
            }

            if (data.error) {
                throw new Error(data.error);
            }

            if (!data.token) {
                throw new Error('El servidor no devolvió un token de autenticación');
            }

            const storage = rememberMe ? localStorage : sessionStorage;

            this.token = data.token;
            this.username = username;

            storage.setItem("token", this.token);
            storage.setItem("username", username);

            return data;
        } catch (error) {
            console.error("Error detallado de inicio de sesión:", error);
            throw error;
        }
    }

    logout() {
        this.token = null;
        this.username = null;

        localStorage.removeItem("token");
        localStorage.removeItem("username");
        sessionStorage.removeItem("token");
        sessionStorage.removeItem("username");
        console.log("Sesión cerrada correctamente");
    }

    getAuthHeaders() {
        return {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${this.token}`
        };
    }

    async fetchWithAuth(url, options = {}) {
        if (!this.isLoggedIn()) {
            throw new Error('Usuario no autenticado');
        }

        const headers = {
            ...options.headers,
            ...this.getAuthHeaders()
        };

        try {
            const response = await fetch(url, {
                ...options,
                headers
            });

            if (!response.ok) {
                console.error(`Error en la petición: ${response.status} - ${response.statusText}`);
            }

            return response;
        } catch (error) {
            console.error(`Error al realizar la petición a ${url}:`, error);
            throw error;
        }
    }
}

const sessionManager = new SessionManager();

const login = (username, password, rememberMe) => sessionManager.login(username, password, rememberMe);
const logout = () => sessionManager.logout();
const isAuthenticated = () => sessionManager.isLoggedIn();
const fetchWithAuth = (url, options) => sessionManager.fetchWithAuth(url, options);
const getAuthHeaders = () => sessionManager.getAuthHeaders();

export { sessionManager, login, logout, isAuthenticated, fetchWithAuth, getAuthHeaders };

export default sessionManager;