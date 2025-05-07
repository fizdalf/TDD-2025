export class AuthService {

    #sessionManager;

    /**
     * @param sessionManager SessionManager
     */
    constructor(sessionManager) {
        this.#sessionManager = sessionManager;
    }

    async login(username, password, rememberMe = false) {
        const response = await fetch('/login', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({username, password})
        });

        if (!response.ok) {
            throw new Error(`HTTP error: ${response.status} - ${response.statusText}`);
        }

        const data = await response.json();

        if (data.error) {
            throw new Error(data.error);
        }

        if (!data.token) {
            throw new Error('No se recibió token');
        }

        this.#sessionManager.saveSession(username, data.token, rememberMe);
        return data;
    }

    async register(username, password){
        const response = await fetch('/register', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({username, password}),
        });

        if (!response.ok){
            throw new Error(`HTTP error: ${response.status} - ${response.statusText}`);
        }

        const data = await response.json();

        if (data.error){
            throw new Error(data.error);
        }

        return data;
    }

    async fetchWithAuth(url, options = {}) {
        console.log("Token al enviar petición:", this.#sessionManager.getToken());


        if (!this.#sessionManager.isLoggedIn()) {
            throw new Error('Usuario no autenticado');
        }

        const authHeaders = {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${this.#sessionManager.getToken()}`,
        };

        const headers = {
            ...authHeaders,
            ...options.headers,
        };

        console.log("enviando token", this.#sessionManager.getToken());
        console.log("Headers:", headers);

        try {
            const response = await fetch(url, {
                ...options,
                headers: {
                    ...options.headers,
                    'Authorization': `Bearer ${this.#sessionManager.getToken()}`
                }
            });

            if (!response.ok) {
                console.log(Error(`HTTP error: ${response.status}`));
            }

            return response;
        } catch (error) {
            console.error('Error en la solicitud:', error);
            throw error;
        }

    }

}
