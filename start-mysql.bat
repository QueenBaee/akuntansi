@echo off
echo Starting MySQL service...

echo Trying different MySQL service names...

net start MySQL80
if %errorlevel% == 0 goto success

net start MySQL
if %errorlevel% == 0 goto success

net start mysqld
if %errorlevel% == 0 goto success

net start "MySQL80"
if %errorlevel% == 0 goto success

echo.
echo MySQL service not found or already running.
echo Please start MySQL manually from Laragon control panel.
echo.
pause
exit

:success
echo MySQL started successfully!
echo.
echo Now you can run: php artisan migrate --seed
echo.
pause