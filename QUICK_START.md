# Quick Start - Sistem Akuntansi Laravel

## Setup Cepat (5 Menit)

### 1. Persiapan
- Pastikan PHP 8.1+, MySQL, dan Composer sudah terinstall
- Buat database MySQL dengan nama `akuntansi`

### 2. Install & Setup
```bash
# Jalankan script setup otomatis (Windows)
setup.bat

# Atau manual:
composer install
copy .env.example .env
php artisan key:generate
```

### 3. Konfigurasi Database
Edit file `.env`:
```env
DB_DATABASE=akuntansi
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 4. Setup Database
```bash
php artisan migrate --seed
```

### 5. Jalankan Server
```bash
php artisan serve
```

## Test API

### Login
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d "{\"email\":\"admin@example.com\",\"password\":\"password123\"}"
```

### Test dengan PHP Script
```bash
php test-api.php
```

## Default Users
| Email | Password | Role |
|-------|----------|------|
| admin@example.com | password123 | Admin |
| staff@example.com | password123 | Staff |
| manager@example.com | password123 | Manager |

## API Endpoints
- `POST /api/auth/login` - Login
- `GET /api/accounts` - Chart of Accounts
- `POST /api/cash-transactions` - Transaksi Kas
- `GET /api/reports/trial-balance` - Neraca Saldo
- `GET /api/reports/income-statement` - Laba Rugi

## Troubleshooting
- **Error 500**: Cek file `.env` dan database connection
- **Permission denied**: Set permission folder `storage` dan `bootstrap/cache`
- **Class not found**: Jalankan `composer dump-autoload`