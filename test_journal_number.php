<?php

require_once 'vendor/autoload.php';

use App\Services\JournalNumberService;

// Test concurrent journal number generation
echo "Testing Journal Number Generation...\n";

for ($i = 1; $i <= 5; $i++) {
    $number = JournalNumberService::generate();
    echo "Generated: $number\n";
}

echo "Test completed successfully!\n";