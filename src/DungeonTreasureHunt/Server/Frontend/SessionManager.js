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

    saveSession(username, token, rememberMe = false) {
        const storage = rememberMe ? localStorage : sessionStorage;

        this.token = token;
        this.username = username;

        storage.setItem("token", token);
        storage.setItem("username", username);
    }

    logout() {
        this.token = null;
        this.username = null;

        localStorage.removeItem("token");
        localStorage.removeItem("username");
        sessionStorage.removeItem("token");
        sessionStorage.removeItem("username");
    }

    getAuthHeaders() {
        return {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${this.token}`
        };
    }
}
