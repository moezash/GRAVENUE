<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TEST API FONNTE ===\n\n";

$token = env('FONNTE_TOKEN');
echo "Token: " . ($token ? "✓ Ada" : "✗ Tidak ada") . "\n\n";

// Test 1: Cek device status
echo "1. Checking device status...\n";
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://api.fonnte.com/device',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_HTTPHEADER => array(
        'Authorization: ' . $token
    ),
));

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

echo "HTTP Code: $httpCode\n";
echo "Response: $response\n\n";

// Test 2: Test kirim pesan ke nomor sendiri (untuk test)
echo "2. Testing send message...\n";
$testPhone = '6285162884545'; // Nomor dari database untuk test
$testMessage = "🧪 TEST NOTIFIKASI GRAVENUE\n\nIni adalah pesan test dari sistem notifikasi WhatsApp Gravenue.\n\nJika Anda menerima pesan ini, berarti sistem berfungsi dengan baik! ✅\n\n*SMKN 4 Malang*";

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://api.fonnte.com/send',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => array(
        'target' => $testPhone,
        'message' => $testMessage,
        'countryCode' => '62',
    ),
    CURLOPT_HTTPHEADER => array(
        'Authorization: ' . $token
    ),
));

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$error = curl_error($curl);
curl_close($curl);

echo "HTTP Code: $httpCode\n";
echo "CURL Error: " . ($error ?: 'None') . "\n";
echo "Response: $response\n\n";

$result = json_decode($response, true);
if ($result) {
    echo "Parsed Response:\n";
    echo "Status: " . ($result['status'] ?? 'Unknown') . "\n";
    echo "Message: " . ($result['message'] ?? 'No message') . "\n";
    if (isset($result['detail'])) {
        echo "Detail: " . $result['detail'] . "\n";
    }
} else {
    echo "Failed to parse JSON response\n";
}

echo "\n=== TEST SELESAI ===\n";
