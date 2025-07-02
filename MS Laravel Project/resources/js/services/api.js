// API Service for handling HTTP requests
class ApiService {
    constructor() {
        this.baseURL = '/api';
        this.token = localStorage.getItem('auth_token');
    }

    // Set auth token
    setToken(token) {
        this.token = token;
        localStorage.setItem('auth_token', token);
    }

    // Remove auth token
    removeToken() {
        this.token = null;
        localStorage.removeItem('auth_token');
    }

    // Get CSRF cookie for Laravel Sanctum
    async getCsrfCookie() {
        try {
            await fetch('/sanctum/csrf-cookie', {
                method: 'GET',
                credentials: 'include',
            });
        } catch (error) {
            console.error('Failed to get CSRF cookie:', error);
        }
    }

    // Get auth headers
    getAuthHeaders() {
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        };

        if (this.token) {
            headers['Authorization'] = `Bearer ${this.token}`;
        }

        return headers;
    }

    // Make HTTP request
    async request(endpoint, options = {}) {
        const url = `${this.baseURL}${endpoint}`;
        const config = {
            headers: this.getAuthHeaders(),
            credentials: 'include', // Include cookies for CSRF
            ...options,
        };

        try {
            const response = await fetch(url, config);
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Request failed');
            }

            return data;
        } catch (error) {
            console.error('API Request Error:', error);
            throw error;
        }
    }

    // Authentication methods
    async login(credentials) {
        // Get CSRF cookie first
        await this.getCsrfCookie();
        
        return this.request('/auth/login', {
            method: 'POST',
            body: JSON.stringify(credentials),
            credentials: 'include', // Include cookies for CSRF
        });
    }

    async logout() {
        const result = await this.request('/auth/logout', {
            method: 'POST',
        });
        this.removeToken();
        return result;
    }

    async getCurrentUser() {
        return this.request('/auth/me');
    }
}

// Create and export a singleton instance
const apiService = new ApiService();
export default apiService; 