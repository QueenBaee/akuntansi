<?php
// Simple test file
echo "PHP is working!<br>";
echo "Current directory: " . __DIR__ . "<br>";
echo "PHP version: " . phpversion() . "<br>";

// Test Laravel
if (file_exists('../vendor/autoload.php')) {
    echo "Laravel vendor found<br>";
    require_once '../vendor/autoload.php';
    
    if (class_exists('Illuminate\Foundation\Application')) {
        echo "Laravel classes loaded<br>";
    }
} else {
    echo "Laravel vendor NOT found<br>";
}
?>