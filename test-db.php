<?php

echo "Testing database connection...\n";

$host = '127.0.0.1';
$port = 3306;
$username = 'root';
$password = '';

try {
    // Test connection without database
    $pdo = new PDO("mysql:host=$host;port=$port", $username, $password);
    echo "✓ MySQL connection successful\n";
    
    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS akuntansi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✓ Database 'akuntansi' created/verified\n";
    
    // Test connection with database
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=akuntansi", $username, $password);
    echo "✓ Database connection successful\n";
    
    echo "\nYou can now run: php artisan migrate --seed\n";
    
} catch (PDOException $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
    echo "\nPlease check:\n";
    echo "1. MySQL service is running\n";
    echo "2. Username/password is correct\n";
    echo "3. Port 3306 is available\n";
}