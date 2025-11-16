# Setup Manual - Sistem Akuntansi Laravel

## Persyaratan
- PHP 8.1+ dengan ekstensi: pdo_mysql, mbstring, openssl, tokenizer, xml, ctype, json
- MySQL 5.7+ atau MariaDB 10.3+
- Composer 2.0+
- Web server (Apache/Nginx) atau PHP built-in server

## Langkah Setup

### 1. Install Dependencies
```bash
cd c:\laragon\www\akuntansi
composer install
```

### 2. Setup Environment
```bash
# Copy file environment
copy .env.example .env

# Generate application key
php artisan key:generate
```

### 3. Konfigurasi Database
Edit file `.env` dan sesuaikan dengan database Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=akuntansi
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 4. Buat Database
Buat database MySQL dengan nama `akuntansi`:
```sql
CREATE DATABASE akuntansi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Jalankan Migration dan Seeder
```bash
# Jalankan migration
php artisan migrate

# Jalankan seeder untuk data awal
php artisan db:seed --class=PermissionSeeder
php artisan db:seed --class=AccountSeeder
php artisan db:seed --class=CashflowCategorySeeder
```

### 6. Buat User Admin
```bash
php artisan tinker
```
Kemudian jalankan:
```php
$user = App\Models\User::create([
    'name' => 'Administrator',
    'email' => 'admin@example.com',
    'password' => bcrypt('password123')
]);

$user->assignRole('admin');
```

### 7. Start Development Server
```bash
php artisan serve
```

Aplikasi akan berjalan di: http://localhost:8000

## Testing API

### Login
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password123"
  }'
```

### Test Endpoint dengan Token
```bash
curl -X GET http://localhost:8000/api/accounts \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

## Troubleshooting

### Error: Class not found
```bash
composer dump-autoload
```

### Error: Permission denied
```bash
# Windows (run as administrator)
icacls storage /grant Users:F /T
icacls bootstrap\cache /grant Users:F /T
```

### Error: Database connection
- Pastikan MySQL service berjalan
- Cek kredensial database di file `.env`
- Test koneksi: `php artisan tinker` lalu `DB::connection()->getPdo();`

### Error: Key not set
```bash
php artisan key:generate
```

## File Permissions (Linux/Mac)
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## Production Setup
1. Set `APP_ENV=production` dan `APP_DEBUG=false`
2. Optimize aplikasi:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --optimize-autoloader --no-dev
```

## Queue Worker (Optional)
Jika ingin menggunakan queue untuk proses background:
```bash
# Install Redis atau gunakan database queue
# Edit .env: QUEUE_CONNECTION=database

# Buat tabel jobs
php artisan queue:table
php artisan migrate

# Jalankan worker
php artisan queue:work
```