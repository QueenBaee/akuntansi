# Panduan Fullstack Monolith - Sistem Akuntansi

## 🏗 Arsitektur Fullstack

Sistem ini menggunakan arsitektur **fullstack monolith** dengan:
- **Backend**: Laravel 10 (API + Web Routes)
- **Frontend**: Vue.js 3 + Vite (SPA terintegrasi)
- **Database**: MySQL/PostgreSQL
- **Styling**: Tailwind CSS
- **State Management**: Pinia
- **Routing**: Vue Router dengan Laravel routes

## 📁 Struktur Folder Frontend

```
resources/
├── css/
│   └── app.css              # Tailwind CSS + custom styles
├── js/
│   ├── components/          # Vue components
│   │   ├── common/         # Komponen umum (buttons, modals, etc)
│   │   ├── accounting/     # Komponen akuntansi
│   │   └── reports/        # Komponen laporan
│   ├── layouts/            # Layout components
│   │   ├── AuthLayout.vue  # Layout untuk login
│   │   └── DashboardLayout.vue # Layout dashboard
│   ├── pages/              # Page components
│   │   ├── auth/          # Halaman authentication
│   │   ├── dashboard/     # Halaman dashboard
│   │   ├── accounting/    # Halaman akuntansi
│   │   └── reports/       # Halaman laporan
│   ├── stores/            # Pinia stores
│   │   └── auth.js        # Authentication store
│   ├── router/            # Vue Router
│   │   └── index.js       # Route definitions
│   ├── utils/             # Utility functions
│   ├── App.vue            # Root component
│   ├── app.js             # Entry point
│   └── bootstrap.js       # Axios configuration
└── views/
    └── app.blade.php      # SPA template
```

## 🚀 Quick Start

### 1. Setup Lengkap (Pertama Kali)
```bash
# Jalankan setup otomatis
setup-fullstack.bat

# Atau manual:
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build
```

### 2. Development Mode
```bash
# Jalankan development servers
start-dev.bat

# Atau manual (2 terminal):
# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Vite dev server  
npm run dev
```

### 3. Production Build
```bash
npm run build
php artisan serve
```

## 🔧 Konfigurasi

### Environment Variables
```env
# Laravel
APP_NAME="Sistem Akuntansi"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=akuntansi
DB_USERNAME=root
DB_PASSWORD=

# Frontend
VITE_APP_NAME="${APP_NAME}"
VITE_API_URL="${APP_URL}/api"
```

### Vite Configuration
File `vite.config.js` sudah dikonfigurasi untuk:
- Hot Module Replacement (HMR)
- Vue.js support
- Laravel integration
- Asset bundling

## 📱 Fitur Frontend

### 1. Authentication
- Login form dengan validasi
- Token-based authentication (Sanctum)
- Auto-redirect berdasarkan auth status
- Logout functionality

### 2. Dashboard
- Statistik real-time
- Chart dan grafik
- Recent transactions
- Quick actions

### 3. Accounting Modules
- **Chart of Accounts**: CRUD akun dengan modal
- **Cash Transactions**: Input transaksi kas
- **Reports**: Neraca saldo, laba rugi, neraca

### 4. UI Components
- Responsive design (mobile-first)
- Loading states
- Error handling
- Modal dialogs
- Form validation

## 🎨 Styling Guide

### Tailwind Classes
```css
/* Buttons */
.btn { @apply px-4 py-2 rounded-md font-medium transition-colors; }
.btn-primary { @apply bg-primary-600 text-white hover:bg-primary-700; }
.btn-secondary { @apply bg-gray-200 text-gray-900 hover:bg-gray-300; }

/* Cards */
.card { @apply bg-white rounded-lg shadow-md p-6; }

/* Forms */
.form-input { @apply w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500; }

/* Tables */
.table-auto { @apply w-full border-collapse; }
```

### Custom Components
- Semua komponen menggunakan Composition API
- Props validation dengan TypeScript-style
- Emit events untuk parent communication
- Scoped styles untuk komponen spesifik

## 🔄 State Management

### Pinia Stores
```javascript
// stores/auth.js
export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('auth_token'),
    isLoading: false
  }),
  
  getters: {
    isAuthenticated: (state) => !!state.token
  },
  
  actions: {
    async login(credentials) { /* ... */ },
    async logout() { /* ... */ }
  }
});
```

### Usage dalam Components
```vue
<script setup>
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();
const isLoggedIn = computed(() => authStore.isAuthenticated);
</script>
```

## 🛣 Routing

### Vue Router Setup
```javascript
// router/index.js
const routes = [
  {
    path: '/login',
    component: AuthLayout,
    children: [
      { path: '', name: 'login', component: Login }
    ]
  },
  {
    path: '/',
    component: DashboardLayout,
    meta: { requiresAuth: true },
    children: [
      { path: '', name: 'dashboard', component: Dashboard },
      { path: 'accounts', name: 'accounts', component: Accounts }
    ]
  }
];
```

### Laravel Web Routes
```php
// routes/web.php
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
```

## 📡 API Integration

### Axios Configuration
```javascript
// bootstrap.js
import axios from 'axios';

// Set CSRF token
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

// Set auth token
const authToken = localStorage.getItem('auth_token');
if (authToken) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${authToken}`;
}
```

### API Calls dalam Components
```vue
<script setup>
import axios from 'axios';

const fetchAccounts = async () => {
  try {
    const response = await axios.get('/api/accounts');
    accounts.value = response.data.data;
  } catch (error) {
    console.error('Error:', error);
  }
};
</script>
```

## 🧪 Testing

### Frontend Testing
```bash
# Install testing dependencies
npm install --save-dev @vue/test-utils vitest jsdom

# Run tests
npm run test
```

### E2E Testing
```bash
# Install Cypress
npm install --save-dev cypress

# Run E2E tests
npm run cypress:open
```

## 📦 Build & Deployment

### Development
```bash
npm run dev          # Vite dev server dengan HMR
php artisan serve    # Laravel development server
```

### Production
```bash
npm run build        # Build assets untuk production
php artisan optimize # Optimize Laravel untuk production
```

### Assets
- CSS/JS assets di-bundle oleh Vite
- Images dan fonts di folder `public/`
- Cache busting otomatis dengan hash
- Lazy loading untuk routes

## 🔐 Security

### CSRF Protection
- CSRF token otomatis di semua request
- Meta tag di template Blade
- Axios interceptor untuk token

### Authentication
- Sanctum token-based auth
- Token disimpan di localStorage
- Auto-logout jika token expired
- Route guards di Vue Router

## 📊 Performance

### Optimization
- Code splitting per route
- Lazy loading components
- Image optimization
- CSS purging dengan Tailwind
- Gzip compression

### Monitoring
- Laravel Telescope untuk debugging
- Vue DevTools untuk frontend
- Network tab untuk API calls
- Performance metrics

## 🚨 Troubleshooting

### Common Issues

#### 1. Vite Server Error
```bash
# Clear cache
rm -rf node_modules/.vite
npm run dev
```

#### 2. CSRF Token Mismatch
```bash
# Clear Laravel cache
php artisan config:clear
php artisan cache:clear
```

#### 3. Vue Router 404
- Pastikan Laravel web routes mengarah ke SPA
- Check `.htaccess` untuk Apache
- Nginx configuration untuk production

#### 4. API CORS Error
```php
// config/cors.php
'paths' => ['api/*', 'sanctum/csrf-cookie'],
'allowed_origins' => ['http://localhost:3000', 'http://localhost:5173'],
```

## 📚 Resources

### Documentation
- [Vue.js 3](https://vuejs.org/)
- [Vite](https://vitejs.dev/)
- [Tailwind CSS](https://tailwindcss.com/)
- [Pinia](https://pinia.vuejs.org/)
- [Laravel Sanctum](https://laravel.com/docs/sanctum)

### Tools
- Vue DevTools browser extension
- Laravel Telescope
- Vite DevTools
- Tailwind CSS IntelliSense

## 🤝 Development Workflow

1. **Backend First**: Buat API endpoints di Laravel
2. **Frontend Integration**: Buat Vue components dan pages
3. **Testing**: Test API dan frontend integration
4. **Styling**: Apply Tailwind classes dan custom styles
5. **Optimization**: Bundle optimization dan performance tuning

Sistem fullstack monolith ini memberikan pengalaman development yang seamless dengan hot reload, type safety, dan modern tooling.