@echo off
echo Setting up Sistem Akuntansi Laravel...

echo.
echo 1. Installing Composer dependencies...
composer install

echo.
echo 2. Setting up environment...
if not exist .env copy .env.example .env

echo.
echo 3. Generating application key...
php artisan key:generate

echo.
echo 4. Please configure your database in .env file
echo Then run: php artisan migrate --seed

echo.
echo 5. To start the server, run: php artisan serve
echo.
pause