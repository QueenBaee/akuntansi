@echo off
echo ========================================
echo    Starting Development Servers
echo ========================================

echo.
echo Starting Laravel development server...
start "Laravel Server" cmd /k "php artisan serve"

timeout /t 2 /nobreak >nul

echo.
echo Starting Vite development server...
start "Vite Dev Server" cmd /k "npm run dev"

echo.
echo ========================================
echo     Development Servers Started!
echo ========================================
echo.
echo Laravel: http://localhost:8000
echo Vite: http://localhost:5173
echo.
echo Press any key to close this window...
pause >nul