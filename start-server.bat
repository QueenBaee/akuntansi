@echo off
echo Stopping any running PHP processes...
taskkill /f /im php.exe 2>nul
taskkill /f /im php-cgi.exe 2>nul

echo.
echo Starting Laravel server on port 8080...
php artisan serve --host=127.0.0.1 --port=8080

pause