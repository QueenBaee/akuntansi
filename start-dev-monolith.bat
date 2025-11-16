@echo off
echo ========================================
echo    Starting Fullstack Monolith Servers
echo ========================================

echo.
echo Starting Laravel server on port 8080...
start "Laravel Server" cmd /k "php artisan serve --port=8080"

timeout /t 3 /nobreak >nul

echo.
echo Starting Vite dev server...
start "Vite Dev Server" cmd /k "npm run dev"

echo.
echo ========================================
echo     Development Servers Started!
echo ========================================
echo.
echo Laravel: http://localhost:8080
echo Vite: http://localhost:5173
echo.
echo Access application at: http://localhost:8080
echo Login: admin@example.com / password123
echo.
echo Press any key to close this window...
pause >nul