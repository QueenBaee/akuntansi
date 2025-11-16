@echo off
echo ========================================
echo    Setup Sistem Akuntansi Fullstack
echo ========================================

echo.
echo [1/6] Installing PHP dependencies...
call composer install

echo.
echo [2/6] Installing Node.js dependencies...
call npm install

echo.
echo [3/6] Setting up environment...
if not exist .env (
    copy .env.example .env
    echo Environment file created
) else (
    echo Environment file already exists
)

echo.
echo [4/6] Generating application key...
call php artisan key:generate

echo.
echo [5/6] Running database migrations and seeders...
call php artisan migrate --seed

echo.
echo [6/6] Building frontend assets...
call npm run build

echo.
echo ========================================
echo           Setup Complete!
echo ========================================
echo.
echo To start the application:
echo   1. Development: php artisan serve
echo   2. Frontend dev: npm run dev (in separate terminal)
echo.
echo Default login:
echo   Email: admin@example.com
echo   Password: password123
echo.
echo Access the application at: http://localhost:8000
echo ========================================

pause