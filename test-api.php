<?php

// Simple API test script
$baseUrl = 'http://localhost:8000/api';

echo "Testing Sistem Akuntansi API...\n\n";

// Test 1: Login
echo "1. Testing Login...\n";
$loginData = [
    'email' => 'admin@example.com',
    'password' => 'password123'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/auth/login');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 200) {
    $data = json_decode($response, true);
    $token = $data['data']['token'];
    echo "✓ Login successful! Token: " . substr($token, 0, 20) . "...\n\n";
    
    // Test 2: Get Accounts
    echo "2. Testing Get Accounts...\n";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/accounts');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200) {
        echo "✓ Accounts endpoint working!\n";
        $accounts = json_decode($response, true);
        echo "Found " . count($accounts['data']) . " accounts\n\n";
    } else {
        echo "✗ Accounts endpoint failed: HTTP $httpCode\n";
        echo $response . "\n\n";
    }
    
} else {
    echo "✗ Login failed: HTTP $httpCode\n";
    echo $response . "\n\n";
}

echo "API test completed.\n";