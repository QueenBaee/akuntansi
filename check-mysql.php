<?php

echo "Checking MySQL connection options...\n\n";

$configs = [
    ['host' => '127.0.0.1', 'port' => 3306, 'user' => 'root', 'pass' => ''],
    ['host' => 'localhost', 'port' => 3306, 'user' => 'root', 'pass' => ''],
    ['host' => '127.0.0.1', 'port' => 3307, 'user' => 'root', 'pass' => ''],
    ['host' => '127.0.0.1', 'port' => 3306, 'user' => 'root', 'pass' => 'root'],
];

foreach ($configs as $i => $config) {
    echo "Testing config " . ($i + 1) . ": {$config['host']}:{$config['port']} user={$config['user']}\n";
    
    try {
        $pdo = new PDO(
            "mysql:host={$config['host']};port={$config['port']}", 
            $config['user'], 
            $config['pass']
        );
        
        echo "✓ Connection successful!\n";
        echo "Update your .env file with:\n";
        echo "DB_HOST={$config['host']}\n";
        echo "DB_PORT={$config['port']}\n";
        echo "DB_USERNAME={$config['user']}\n";
        echo "DB_PASSWORD={$config['pass']}\n\n";
        
        // Try to create database
        try {
            $pdo->exec("CREATE DATABASE IF NOT EXISTS akuntansi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            echo "✓ Database 'akuntansi' created successfully!\n";
            echo "Now run: php artisan migrate --seed\n";
        } catch (Exception $e) {
            echo "Database creation failed: " . $e->getMessage() . "\n";
        }
        
        exit(0);
        
    } catch (PDOException $e) {
        echo "✗ Failed: " . $e->getMessage() . "\n\n";
    }
}

echo "No working MySQL configuration found.\n";
echo "Please:\n";
echo "1. Start Laragon\n";
echo "2. Click 'Start All' in Laragon\n";
echo "3. Or install MySQL/XAMPP manually\n";