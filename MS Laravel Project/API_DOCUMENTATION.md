# 🔐 Mentors Society API Documentation

## 📋 **Overview**

The Mentors Society API provides authentication and user management endpoints for the organization's web application. This documentation covers all available endpoints, request/response formats, and integration examples.

**Base URL**: `http://localhost:8000/api` (or your domain)

---

## 🔑 **Authentication**

### Authentication Flow
1. **Login** with student number and password
2. **Receive** Bearer token in response
3. **Include** token in `Authorization` header for protected endpoints
4. **Logout** to invalidate token

### Token Format
```
Authorization: Bearer {your_token_here}
```

---

## 📡 **API Endpoints**

### 🔓 **Public Endpoints** (No Authentication Required)

---

### 1. **Login** 
Authenticate user with student number and password.

**Endpoint**: `POST /api/auth/login`

**Request Body**:
```json
{
    "student_number": "2021-00112-TG-0",
    "password": "JanellaAnneBoncodin"
}
```

**Response** (200 OK):
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 291,
            "first_name": "Janella",
            "last_name": "Boncodin",
            "email": "bjanellaanne@gmail.com",
            "student_number": "2021-00112-TG-0"
        },
        "roles": [
            {
                "role_id": 6,
                "role_name": "Student",
                "academic_year_id": 1
            }
        ],
        "token": "1|712b9f1c207a86b56ab8a98a2638e291d1ca8de8986fa3aa3ffade2526ba1366",
        "token_type": "Bearer"
    }
}
```

**Error Responses**:

- **400 Bad Request** (Validation Error):
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "student_number": ["The student number field is required."],
        "password": ["The password field is required."]
    }
}
```

- **401 Unauthorized** (Invalid Credentials):
```json
{
    "success": false,
    "message": "Invalid password"
}
```

- **404 Not Found** (Student Not Found):
```json
{
    "success": false,
    "message": "Student number not found"
}
```

---

### 2. **Forgot Password**
Request password reset email.

**Endpoint**: `POST /api/auth/forgot-password`

**Request Body**:
```json
{
    "email": "bjanellaanne@gmail.com"
}
```

**Response** (200 OK):
```json
{
    "success": true,
    "message": "Password reset email sent successfully"
}
```

---

### 3. **Reset Password**
Reset password using reset token.

**Endpoint**: `POST /api/auth/reset-password`

**Request Body**:
```json
{
    "token": "reset_token_here",
    "email": "bjanellaanne@gmail.com",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
}
```

**Response** (200 OK):
```json
{
    "success": true,
    "message": "Password reset successfully"
}
```

---

### 🔒 **Protected Endpoints** (Authentication Required)

*All protected endpoints require the `Authorization: Bearer {token}` header*

---

### 4. **Get Current User Info**
Retrieve authenticated user's information.

**Endpoint**: `GET /api/auth/me`

**Headers**:
```
Authorization: Bearer 1|712b9f1c207a86b56ab8a98a2638e291d1ca8de8986fa3aa3ffade2526ba1366
```

**Response** (200 OK):
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 291,
            "first_name": "Janella",
            "last_name": "Boncodin",
            "email": "bjanellaanne@gmail.com",
            "student_number": "2021-00112-TG-0"
        }
    }
}
```

**Error Response** (401 Unauthorized):
```json
{
    "success": false,
    "message": "Not authenticated"
}
```

---

### 5. **Logout**
Invalidate current authentication token.

**Endpoint**: `POST /api/auth/logout`

**Headers**:
```
Authorization: Bearer 1|712b9f1c207a86b56ab8a98a2638e291d1ca8de8986fa3aa3ffade2526ba1366
```

**Response** (200 OK):
```json
{
    "success": true,
    "message": "Logout successful"
}
```

**Error Response** (401 Unauthorized):
```json
{
    "success": false,
    "message": "Not authenticated"
}
```

---

### 6. **Refresh Token**
Generate new token and invalidate current one.

**Endpoint**: `POST /api/auth/refresh`

**Headers**:
```
Authorization: Bearer 1|712b9f1c207a86b56ab8a98a2638e291d1ca8de8986fa3aa3ffade2526ba1366
```

**Response** (200 OK):
```json
{
    "success": true,
    "message": "Token refreshed successfully",
    "data": {
        "token": "2|new_token_here",
        "token_type": "Bearer"
    }
}
```

---

### 7. **Change Password**
Change user's password.

**Endpoint**: `POST /api/auth/change-password`

**Headers**:
```
Authorization: Bearer 1|712b9f1c207a86b56ab8a98a2638e291d1ca8de8986fa3aa3ffade2526ba1366
```

**Request Body**:
```json
{
    "current_password": "JanellaAnneBoncodin",
    "new_password": "newpassword123",
    "new_password_confirmation": "newpassword123"
}
```

**Response** (200 OK):
```json
{
    "success": true,
    "message": "Password changed successfully"
}
```

**Error Response** (401 Unauthorized):
```json
{
    "success": false,
    "message": "Current password is incorrect"
}
```

---

## 🎯 **Frontend Integration Examples**

### **JavaScript/Fetch API**

#### Login Example
```javascript
async function login(studentNumber, password) {
    try {
        const response = await fetch('/api/auth/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                student_number: studentNumber,
                password: password
            })
        });

        const data = await response.json();

        if (data.success) {
            // Store token in localStorage or secure storage
            localStorage.setItem('auth_token', data.data.token);
            localStorage.setItem('user', JSON.stringify(data.data.user));
            
            // Redirect to dashboard
            window.location.href = '/dashboard';
        } else {
            // Handle error
            alert(data.message);
        }
    } catch (error) {
        console.error('Login error:', error);
        alert('Login failed. Please try again.');
    }
}

// Usage
login('2021-00112-TG-0', 'JanellaAnneBoncodin');
```

#### Protected API Call Example
```javascript
async function getCurrentUser() {
    const token = localStorage.getItem('auth_token');
    
    if (!token) {
        // Redirect to login
        window.location.href = '/login';
        return;
    }

    try {
        const response = await fetch('/api/auth/me', {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
            }
        });

        const data = await response.json();

        if (data.success) {
            return data.data.user;
        } else {
            // Token might be expired
            localStorage.removeItem('auth_token');
            window.location.href = '/login';
        }
    } catch (error) {
        console.error('Error fetching user:', error);
    }
}
```

#### Logout Example
```javascript
async function logout() {
    const token = localStorage.getItem('auth_token');
    
    try {
        await fetch('/api/auth/logout', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
            }
        });

        // Clear local storage
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user');
        
        // Redirect to login
        window.location.href = '/login';
    } catch (error) {
        console.error('Logout error:', error);
    }
}
```

### **Axios Example**
```javascript
import axios from 'axios';

// Configure axios defaults
axios.defaults.baseURL = 'http://localhost:8000/api';
axios.defaults.headers.common['Accept'] = 'application/json';

// Add request interceptor to include token
axios.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem('auth_token');
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Add response interceptor to handle token expiration
axios.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            localStorage.removeItem('auth_token');
            window.location.href = '/login';
        }
        return Promise.reject(error);
    }
);

// API functions
export const authAPI = {
    login: (studentNumber, password) => 
        axios.post('/auth/login', { student_number: studentNumber, password }),
    
    logout: () => 
        axios.post('/auth/logout'),
    
    getCurrentUser: () => 
        axios.get('/auth/me'),
    
    changePassword: (currentPassword, newPassword) => 
        axios.post('/auth/change-password', {
            current_password: currentPassword,
            new_password: newPassword,
            new_password_confirmation: newPassword
        })
};
```

### **Vue.js Example**
```javascript
// store/auth.js
import { defineStore } from 'pinia';
import { authAPI } from '@/api/auth';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: localStorage.getItem('auth_token') || null,
        isAuthenticated: false
    }),

    actions: {
        async login(studentNumber, password) {
            try {
                const response = await authAPI.login(studentNumber, password);
                const { token, user } = response.data.data;
                
                this.token = token;
                this.user = user;
                this.isAuthenticated = true;
                
                localStorage.setItem('auth_token', token);
                localStorage.setItem('user', JSON.stringify(user));
                
                return { success: true };
            } catch (error) {
                return { 
                    success: false, 
                    message: error.response?.data?.message || 'Login failed' 
                };
            }
        },

        async logout() {
            try {
                await authAPI.logout();
            } catch (error) {
                console.error('Logout error:', error);
            } finally {
                this.token = null;
                this.user = null;
                this.isAuthenticated = false;
                
                localStorage.removeItem('auth_token');
                localStorage.removeItem('user');
            }
        },

        async getCurrentUser() {
            try {
                const response = await authAPI.getCurrentUser();
                this.user = response.data.data.user;
                this.isAuthenticated = true;
                return this.user;
            } catch (error) {
                this.logout();
                throw error;
            }
        }
    }
});
```

### **React Example**
```javascript
// hooks/useAuth.js
import { useState, useEffect, createContext, useContext } from 'react';
import axios from 'axios';

const AuthContext = createContext();

export const AuthProvider = ({ children }) => {
    const [user, setUser] = useState(null);
    const [token, setToken] = useState(localStorage.getItem('auth_token'));
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        if (token) {
            getCurrentUser();
        } else {
            setLoading(false);
        }
    }, [token]);

    const login = async (studentNumber, password) => {
        try {
            const response = await axios.post('/api/auth/login', {
                student_number: studentNumber,
                password: password
            });

            const { token: newToken, user: userData } = response.data.data;
            
            setToken(newToken);
            setUser(userData);
            localStorage.setItem('auth_token', newToken);
            
            return { success: true };
        } catch (error) {
            return { 
                success: false, 
                message: error.response?.data?.message || 'Login failed' 
            };
        }
    };

    const logout = async () => {
        try {
            await axios.post('/api/auth/logout', {}, {
                headers: { Authorization: `Bearer ${token}` }
            });
        } catch (error) {
            console.error('Logout error:', error);
        } finally {
            setToken(null);
            setUser(null);
            localStorage.removeItem('auth_token');
        }
    };

    const getCurrentUser = async () => {
        try {
            const response = await axios.get('/api/auth/me', {
                headers: { Authorization: `Bearer ${token}` }
            });
            setUser(response.data.data.user);
        } catch (error) {
            logout();
        } finally {
            setLoading(false);
        }
    };

    return (
        <AuthContext.Provider value={{ 
            user, 
            token, 
            loading, 
            login, 
            logout, 
            isAuthenticated: !!token 
        }}>
            {children}
        </AuthContext.Provider>
    );
};

export const useAuth = () => useContext(AuthContext);
```

---

## 🛡️ **Security Considerations**

### **Token Storage**
- Store tokens in `localStorage` for development
- Use `httpOnly` cookies for production
- Implement token refresh mechanism
- Clear tokens on logout

### **Error Handling**
- Always handle 401 responses (token expired/invalid)
- Implement automatic logout on authentication errors
- Show user-friendly error messages

### **Password Requirements**
- Minimum 6 characters
- Consider implementing password strength requirements
- Use HTTPS in production

---

## 📊 **Status Codes**

| Code | Description |
|------|-------------|
| 200 | Success |
| 201 | Created |
| 400 | Bad Request (Validation Error) |
| 401 | Unauthorized (Invalid Token/Credentials) |
| 404 | Not Found |
| 422 | Validation Error |
| 500 | Internal Server Error |

---

## 🔧 **Testing**

### **Using Postman/Insomnia**
1. Set base URL: `http://localhost:8000/api`
2. For protected endpoints, add header: `Authorization: Bearer {token}`
3. Set `Content-Type: application/json` for POST requests

### **Using cURL**
```bash
# Login
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"student_number":"2021-00112-TG-0","password":"JanellaAnneBoncodin"}'

# Get user info (with token)
curl -X GET http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

---

## 📝 **Notes**

- All timestamps are in Manila timezone (Asia/Manila)
- Student numbers follow the format: `YYYY-NNNNN-TG-0`
- Passwords are hashed using Laravel's Hash facade
- Tokens are generated using Laravel Sanctum
- All responses include a `success` boolean field

---

*Last Updated: June 30, 2025* 