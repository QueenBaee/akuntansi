// API Base URL
const API_BASE = '/api';

// Auth Token
let authToken = localStorage.getItem('auth_token');

// Set axios defaults
if (authToken) {
    setAuthHeader(authToken);
}

function setAuthHeader(token) {
    authToken = token;
    // Set default header for fetch requests
    window.defaultHeaders = {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`,
        'X-Requested-With': 'XMLHttpRequest'
    };
}

// API Helper
async function apiCall(endpoint, options = {}) {
    const url = API_BASE + endpoint;
    const config = {
        headers: window.defaultHeaders || {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        ...options
    };

    try {
        const response = await fetch(url, config);
        const data = await response.json();
        
        if (!response.ok) {
            throw new Error(data.message || 'API Error');
        }
        
        return data;
    } catch (error) {
        console.error('API Error:', error);
        throw error;
    }
}

// Auth Functions
async function login(email, password) {
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        const response = await fetch('/api/auth/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ email, password })
        });
        
        if (!response.ok) {
            const errorText = await response.text();
            let errorMessage = 'Login failed';
            
            try {
                const errorData = JSON.parse(errorText);
                errorMessage = errorData.message || errorMessage;
            } catch (e) {
                console.error('Response is not JSON:', errorText);
                errorMessage = 'Server error occurred';
            }
            
            throw new Error(errorMessage);
        }
        
        const data = await response.json();
        const token = data.data.token;
        localStorage.setItem('auth_token', token);
        setAuthHeader(token);
        
        return data;
    } catch (error) {
        throw error;
    }
}

async function logout() {
    try {
        await apiCall('/auth/logout', { method: 'POST' });
    } catch (error) {
        console.error('Logout error:', error);
    } finally {
        localStorage.removeItem('auth_token');
        authToken = null;
        window.defaultHeaders = null;
        window.location.href = '/login';
    }
}

// Check if user is authenticated
function isAuthenticated() {
    return !!authToken;
}

// Redirect if not authenticated
function requireAuth() {
    if (!isAuthenticated()) {
        window.location.href = '/login';
        return false;
    }
    return true;
}

// Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR'
    }).format(amount);
}

// Format date
function formatDate(date) {
    return new Date(date).toLocaleDateString('id-ID');
}

// Modal functions
function showModal(modalId) {
    document.getElementById(modalId).classList.add('show');
}

function hideModal(modalId) {
    document.getElementById(modalId).classList.remove('show');
}

// Form helpers
function getFormData(formId) {
    const form = document.getElementById(formId);
    const formData = new FormData(form);
    const data = {};
    
    for (let [key, value] of formData.entries()) {
        data[key] = value;
    }
    
    return data;
}

function resetForm(formId) {
    document.getElementById(formId).reset();
}

// Show loading state
function showLoading(elementId) {
    const element = document.getElementById(elementId);
    element.innerHTML = '<div class="text-center">Loading...</div>';
}

// Show error message
function showError(message, containerId = 'error-container') {
    const container = document.getElementById(containerId);
    if (container) {
        container.innerHTML = `<div class="alert alert-danger">${message}</div>`;
        setTimeout(() => {
            container.innerHTML = '';
        }, 5000);
    }
}

// Show success message
function showSuccess(message, containerId = 'success-container') {
    const container = document.getElementById(containerId);
    if (container) {
        container.innerHTML = `<div class="alert alert-success">${message}</div>`;
        setTimeout(() => {
            container.innerHTML = '';
        }, 3000);
    }
}

// Initialize app
document.addEventListener('DOMContentLoaded', function() {
    // Add CSRF token to all requests
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        window.defaultHeaders = {
            ...window.defaultHeaders,
            'X-CSRF-TOKEN': csrfToken.getAttribute('content')
        };
    }
    
    // Close modals when clicking outside
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal')) {
            e.target.classList.remove('show');
        }
    });
    
    // Handle logout buttons
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('logout-btn')) {
            e.preventDefault();
            logout();
        }
    });
});