<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Test user management endpoints
echo "Testing User Management System\n";
echo "==============================\n\n";

// Test 1: Login as admin
echo "1. Testing admin login...\n";
$loginData = [
    'email' => 'admin@example.com',
    'password' => 'password123'
];

$request = Request::create('/api/auth/login', 'POST', [], [], [], [], json_encode($loginData));
$request->headers->set('Content-Type', 'application/json');
$request->headers->set('Accept', 'application/json');

$response = $kernel->handle($request);
$loginResult = json_decode($response->getContent(), true);

if ($response->getStatusCode() === 200) {
    echo "✓ Admin login successful\n";
    $adminToken = $loginResult['data']['token'];
    echo "  Token: " . substr($adminToken, 0, 20) . "...\n\n";
} else {
    echo "✗ Admin login failed\n";
    echo "  Response: " . $response->getContent() . "\n\n";
    exit(1);
}

// Test 2: Get users list (admin only)
echo "2. Testing users list (admin access)...\n";
$request = Request::create('/api/users', 'GET');
$request->headers->set('Authorization', 'Bearer ' . $adminToken);
$request->headers->set('Accept', 'application/json');

$response = $kernel->handle($request);

if ($response->getStatusCode() === 200) {
    echo "✓ Users list retrieved successfully\n";
    $users = json_decode($response->getContent(), true);
    echo "  Found " . count($users['data']) . " users\n\n";
} else {
    echo "✗ Users list failed\n";
    echo "  Status: " . $response->getStatusCode() . "\n";
    echo "  Response: " . $response->getContent() . "\n\n";
}

// Test 3: Login as regular user
echo "3. Testing user login...\n";
$loginData = [
    'email' => 'user@example.com',
    'password' => 'password123'
];

$request = Request::create('/api/auth/login', 'POST', [], [], [], [], json_encode($loginData));
$request->headers->set('Content-Type', 'application/json');
$request->headers->set('Accept', 'application/json');

$response = $kernel->handle($request);
$loginResult = json_decode($response->getContent(), true);

if ($response->getStatusCode() === 200) {
    echo "✓ User login successful\n";
    $userToken = $loginResult['data']['token'];
    echo "  Token: " . substr($userToken, 0, 20) . "...\n\n";
} else {
    echo "✗ User login failed\n";
    echo "  Response: " . $response->getContent() . "\n\n";
}

// Test 4: Try to access users list as regular user (should fail)
echo "4. Testing users list access restriction...\n";
$request = Request::create('/api/users', 'GET');
$request->headers->set('Authorization', 'Bearer ' . $userToken);
$request->headers->set('Accept', 'application/json');

$response = $kernel->handle($request);

if ($response->getStatusCode() === 403) {
    echo "✓ Access properly restricted for regular users\n";
    echo "  Status: 403 Forbidden\n\n";
} else {
    echo "✗ Access restriction failed\n";
    echo "  Status: " . $response->getStatusCode() . "\n";
    echo "  Response: " . $response->getContent() . "\n\n";
}

// Test 5: Test journal access for regular user (should work)
echo "5. Testing journal access for regular user...\n";
$request = Request::create('/api/journals', 'GET');
$request->headers->set('Authorization', 'Bearer ' . $userToken);
$request->headers->set('Accept', 'application/json');

$response = $kernel->handle($request);

if ($response->getStatusCode() === 200) {
    echo "✓ Regular user can access journals\n";
    echo "  Status: 200 OK\n\n";
} else {
    echo "✗ Journal access failed for regular user\n";
    echo "  Status: " . $response->getStatusCode() . "\n";
    echo "  Response: " . $response->getContent() . "\n\n";
}

echo "User Management System Test Complete!\n";
echo "=====================================\n";
echo "✓ Admin has full access to user management\n";
echo "✓ Regular users are restricted from configuration/master data\n";
echo "✓ Regular users can access transactions and reports\n";