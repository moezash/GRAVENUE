<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 FONNTE CONNECTION DIAGNOSTIC\n";
echo "===============================\n\n";

$token = env('FONNTE_TOKEN');

// 1. Basic connectivity test
echo "1. 🌐 Testing basic internet connectivity...\n";
$googleTest = @file_get_contents('http://www.google.com', false, stream_context_create([
    'http' => ['timeout' => 5]
]));

if ($googleTest !== false) {
    echo "✅ Internet connection: OK\n";
} else {
    echo "❌ Internet connection: FAILED\n";
}

// 2. Test Fonnte domain
echo "\n2. 🔗 Testing Fonnte domain accessibility...\n";
$fontteTest = @file_get_contents('https://fonnte.com', false, stream_context_create([
    'http' => ['timeout' => 10]
]));

if ($fontteTest !== false) {
    echo "✅ Fonnte.com accessible\n";
} else {
    echo "❌ Fonnte.com NOT accessible\n";
}

// 3. Test API endpoint with detailed CURL
echo "\n3. 🛠️ Testing API endpoint with detailed CURL...\n";
$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://api.fonnte.com/device',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 15,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_SSL_VERIFYPEER => false, // Disable SSL verification for testing
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_USERAGENT => 'Gravenue/1.0',
    CURLOPT_HTTPHEADER => array(
        'Authorization: ' . $token,
        'Content-Type: application/json'
    ),
    CURLOPT_VERBOSE => false
));

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$error = curl_error($curl);
$info = curl_getinfo($curl);

curl_close($curl);

echo "HTTP Code: $httpCode\n";
echo "CURL Error: " . ($error ?: 'None') . "\n";
echo "Response: " . (strlen($response) > 100 ? substr($response, 0, 100) . '...' : $response) . "\n";
echo "Total time: " . $info['total_time'] . " seconds\n";
echo "Connect time: " . $info['connect_time'] . " seconds\n";

// 4. Test with different approach
echo "\n4. 🔄 Testing with alternative method...\n";

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => "Authorization: $token\r\n",
        'timeout' => 15
    ]
]);

$altResponse = @file_get_contents('https://api.fonnte.com/device', false, $context);

if ($altResponse !== false) {
    echo "✅ Alternative method: SUCCESS\n";
    echo "Response: " . substr($altResponse, 0, 100) . "...\n";
} else {
    echo "❌ Alternative method: FAILED\n";
}

// 5. Check token format
echo "\n5. 🔑 Token validation...\n";
echo "Token length: " . strlen($token) . " characters\n";
echo "Token format: " . (preg_match('/^[a-zA-Z0-9]{15,}$/', $token) ? '✅ Valid format' : '❌ Invalid format') . "\n";
echo "Token preview: " . substr($token, 0, 5) . '***' . substr($token, -3) . "\n";

// 6. System info
echo "\n6. 💻 System information...\n";
echo "PHP Version: " . phpversion() . "\n";
echo "CURL Version: " . (function_exists('curl_version') ? curl_version()['version'] : 'Not available') . "\n";
echo "OpenSSL: " . (extension_loaded('openssl') ? '✅ Available' : '❌ Not available') . "\n";
echo "allow_url_fopen: " . (ini_get('allow_url_fopen') ? '✅ Enabled' : '❌ Disabled') . "\n";

// 7. Recommendations
echo "\n7. 💡 RECOMMENDATIONS:\n";
echo "=====================\n";

if ($httpCode === 0) {
    echo "🚨 CONNECTION ISSUE DETECTED:\n";
    echo "   • Check internet connection\n";
    echo "   • Check firewall settings\n";
    echo "   • Try different network\n";
    echo "   • Contact hosting provider\n";
}

if (strlen($token) < 15) {
    echo "🚨 TOKEN ISSUE:\n";
    echo "   • Token seems too short\n";
    echo "   • Get new token from Fonnte dashboard\n";
}

if (!extension_loaded('openssl')) {
    echo "🚨 SSL ISSUE:\n";
    echo "   • OpenSSL extension required\n";
    echo "   • Contact system administrator\n";
}

echo "\n🎯 DIAGNOSTIC COMPLETE!\n";
