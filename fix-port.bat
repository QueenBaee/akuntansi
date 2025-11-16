@echo off
echo Checking what's using ports 8000-8010...
netstat -ano | findstr :800

echo.
echo Killing all PHP processes...
taskkill /f /im php.exe 2>nul
taskkill /f /im php-cgi.exe 2>nul
taskkill /f /im httpd.exe 2>nul
taskkill /f /im nginx.exe 2>nul

echo.
echo Trying different ports...
echo Port 5000:
php artisan serve --port=5000 --timeout=5 2>nul &
timeout /t 2 /nobreak >nul

echo Port 4000:
php artisan serve --port=4000 --timeout=5 2>nul &
timeout /t 2 /nobreak >nul

echo.
echo If none work, try manually:
echo php artisan serve --port=XXXX
echo.
echo Available ports to try: 3333, 4444, 5555, 7777, 9999
pause