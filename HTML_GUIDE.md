# Panduan HTML/CSS - Sistem Akuntansi

## 🎨 Pure HTML/CSS/JavaScript Implementation

Sistem ini menggunakan teknologi web murni tanpa framework:
- **Backend**: Laravel 10 (API)
- **Frontend**: HTML5 + CSS3 + Vanilla JavaScript
- **Database**: MySQL/PostgreSQL
- **Styling**: CSS murni dengan responsive design
- **Routing**: Client-side routing dengan JavaScript

## 📁 Struktur File Frontend

```
public/
├── css/
│   └── app.css              # CSS murni dengan responsive design
├── js/
│   ├── app.js              # Utilities dan API functions
│   └── router.js           # Client-side routing
└── index.php               # Laravel entry point

resources/
├── views/
│   ├── app.blade.php       # Main application template
│   └── login.blade.php     # Login page template
├── css/
│   └── app.css             # Source CSS file
└── js/
    ├── app.js              # Source JavaScript file
    └── router.js           # Source router file
```

## 🚀 Quick Start

### 1. Setup Aplikasi
```bash
# Jalankan setup otomatis
setup-html.bat

# Atau manual:
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed

# Copy assets ke public
copy resources\css\app.css public\css\app.css
copy resources\js\app.js public\js\app.js
copy resources\js\router.js public\js\router.js
```

### 2. Jalankan Server
```bash
php artisan serve
```

### 3. Akses Aplikasi
- URL: http://localhost:8000
- Login: admin@example.com / password123

## 🎨 CSS Architecture

### Reset & Base Styles
```css
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    line-height: 1.6;
    color: #333;
    background-color: #f5f5f5;
}
```

### Component Classes
```css
/* Buttons */
.btn {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 6px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-primary { background: #2563eb; color: white; }
.btn-secondary { background: #e5e7eb; color: #374151; }
.btn-danger { background: #dc2626; color: white; }

/* Cards */
.card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

/* Forms */
.form-input {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 1rem;
}

.form-input:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}
```

### Responsive Design
```css
@media (max-width: 768px) {
    .nav-menu {
        flex-direction: column;
        gap: 1rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .card-header {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
}
```

## 📱 JavaScript Architecture

### API Helper Functions
```javascript
// API Base configuration
const API_BASE = '/api';
let authToken = localStorage.getItem('auth_token');

// Generic API call function
async function apiCall(endpoint, options = {}) {
    const url = API_BASE + endpoint;
    const config = {
        headers: window.defaultHeaders || {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        ...options
    };

    const response = await fetch(url, config);
    const data = await response.json();
    
    if (!response.ok) {
        throw new Error(data.message || 'API Error');
    }
    
    return data;
}
```

### Authentication
```javascript
async function login(email, password) {
    const data = await apiCall('/auth/login', {
        method: 'POST',
        body: JSON.stringify({ email, password })
    });
    
    const token = data.data.token;
    localStorage.setItem('auth_token', token);
    setAuthHeader(token);
    
    return data;
}

function requireAuth() {
    if (!isAuthenticated()) {
        window.location.href = '/login';
        return false;
    }
    return true;
}
```

### Client-Side Routing
```javascript
class Router {
    constructor() {
        this.routes = {};
        this.currentPath = window.location.pathname;
    }
    
    route(path, handler) {
        this.routes[path] = handler;
    }
    
    navigate(path, pushState = true) {
        this.currentPath = path;
        
        if (pushState) {
            history.pushState(null, '', path);
        }
        
        const handler = this.routes[path] || this.routes['*'];
        if (handler) {
            handler();
        }
    }
}
```

## 🔧 Utility Functions

### Formatting
```javascript
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR'
    }).format(amount);
}

function formatDate(date) {
    return new Date(date).toLocaleDateString('id-ID');
}
```

### Modal Management
```javascript
function showModal(modalId) {
    document.getElementById(modalId).classList.add('show');
}

function hideModal(modalId) {
    document.getElementById(modalId).classList.remove('show');
}
```

### Form Helpers
```javascript
function getFormData(formId) {
    const form = document.getElementById(formId);
    const formData = new FormData(form);
    const data = {};
    
    for (let [key, value] of formData.entries()) {
        data[key] = value;
    }
    
    return data;
}
```

## 📄 HTML Templates

### Main Application Layout
```html
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistem Akuntansi</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-content">
                <div class="nav-brand">Sistem Akuntansi</div>
                <ul class="nav-menu">
                    <li><a href="/" class="nav-link">Dashboard</a></li>
                    <li><a href="/accounts" class="nav-link">Chart of Accounts</a></li>
                    <li><a href="/cash-transactions" class="nav-link">Transaksi Kas</a></li>
                    <li><a href="/reports" class="nav-link">Laporan</a></li>
                </ul>
            </div>
        </div>
    </nav>
    
    <main class="container">
        <div id="content">
            <!-- Dynamic content loaded here -->
        </div>
    </main>
    
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/router.js') }}"></script>
</body>
</html>
```

### Modal Template
```html
<div id="accountModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Tambah Akun</h3>
            <button class="close" onclick="hideModal('accountModal')">&times;</button>
        </div>
        <form id="accountForm">
            <div class="form-group">
                <label class="form-label">Nama</label>
                <input type="text" name="name" class="form-input" required>
            </div>
            <div class="flex justify-between gap-2">
                <button type="button" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
```

## 🎯 Features Implemented

### 1. Dashboard
- Statistics cards dengan data real-time
- Recent transactions table
- Responsive grid layout

### 2. Chart of Accounts
- CRUD operations dengan modal forms
- Data table dengan sorting
- Form validation

### 3. Cash Transactions
- Transaction input form
- Transaction listing dengan filters
- Type-based styling (income/expense)

### 4. Reports
- Trial Balance dengan totals
- Income Statement structure
- Balance Sheet layout
- Export functionality (planned)

## 🔐 Security Features

### CSRF Protection
```javascript
// Auto-include CSRF token in requests
const csrfToken = document.querySelector('meta[name="csrf-token"]');
if (csrfToken) {
    window.defaultHeaders = {
        ...window.defaultHeaders,
        'X-CSRF-TOKEN': csrfToken.getAttribute('content')
    };
}
```

### Authentication
- Token-based authentication
- Auto-redirect untuk unauthorized access
- Secure token storage di localStorage

## 📊 Performance Optimizations

### CSS
- Minimal CSS dengan utility classes
- Efficient selectors
- Mobile-first responsive design
- CSS Grid dan Flexbox untuk layouts

### JavaScript
- Vanilla JS tanpa dependencies
- Event delegation untuk dynamic content
- Lazy loading untuk routes
- Minimal DOM manipulation

### Network
- API calls dengan error handling
- Loading states untuk UX
- Caching dengan localStorage
- Optimized asset delivery

## 🚨 Browser Support

### Modern Browsers
- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+

### Features Used
- ES6+ JavaScript (async/await, classes, arrow functions)
- CSS Grid dan Flexbox
- Fetch API
- LocalStorage
- History API

## 🛠 Development Workflow

### 1. CSS Development
```bash
# Edit CSS
nano resources/css/app.css

# Copy to public
copy resources\css\app.css public\css\app.css
```

### 2. JavaScript Development
```bash
# Edit JS
nano resources/js/app.js

# Copy to public
copy resources\js\app.js public\js\app.js
```

### 3. Testing
- Browser DevTools untuk debugging
- Network tab untuk API monitoring
- Console untuk JavaScript errors
- Responsive design testing

## 📚 Best Practices

### CSS
- BEM methodology untuk naming
- Mobile-first responsive design
- Consistent spacing dengan rem units
- Semantic color variables

### JavaScript
- Async/await untuk API calls
- Error handling dengan try/catch
- Event delegation untuk performance
- Modular function organization

### HTML
- Semantic HTML5 elements
- Accessible form labels
- ARIA attributes untuk screen readers
- SEO-friendly structure

Sistem ini memberikan pengalaman modern dengan teknologi web fundamental yang ringan dan cepat!