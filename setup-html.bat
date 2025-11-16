@echo off
echo ========================================
echo    Setup Sistem Akuntansi HTML/CSS
echo ========================================

echo.
echo [1/4] Installing PHP dependencies...
call composer install

echo.
echo [2/4] Setting up environment...
if not exist .env (
    copy .env.example .env
    echo Environment file created
) else (
    echo Environment file already exists
)

echo.
echo [3/4] Generating application key...
call php artisan key:generate

echo.
echo [4/4] Running database migrations and seeders...
call php artisan migrate --seed

echo.
echo Copying assets to public folder...
copy resources\css\app.css public\css\app.css
copy resources\js\app.js public\js\app.js
copy resources\js\router.js public\js\router.js

echo.
echo ========================================
echo           Setup Complete!
echo ========================================
echo.
echo To start the application:
echo   php artisan serve
echo.
echo Default login:
echo   Email: admin@example.com
echo   Password: password123
echo.
echo Access the application at: http://localhost:8000
echo ========================================

pause