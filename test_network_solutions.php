<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🌐 NETWORK SOLUTIONS TEST\n";
echo "========================\n\n";

$token = env('FONNTE_TOKEN');
$testPhone = '6281336701608';
$testMessage = "🧪 TEST KONEKSI GRAVENUE\n\nIni adalah test koneksi dari sistem Gravenue.\n\nJika pesan ini sampai, berarti koneksi sudah normal! ✅\n\n*SMKN 4 Malang*";

// Solution 1: Different API endpoint
echo "1. 🔄 Testing alternative API endpoint...\n";
$altUrl = 'https://api.fonnte.com/send';

$curl1 = curl_init();
curl_setopt_array($curl1, array(
    CURLOPT_URL => $altUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 45,
    CURLOPT_CONNECTTIMEOUT => 20,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_POSTFIELDS => json_encode([
        'target' => $testPhone,
        'message' => $testMessage,
        'countryCode' => '62'
    ]),
    CURLOPT_HTTPHEADER => array(
        'Authorization: ' . $token,
        'Content-Type: application/json'
    ),
));

$response1 = curl_exec($curl1);
$httpCode1 = curl_getinfo($curl1, CURLINFO_HTTP_CODE);
$error1 = curl_error($curl1);
curl_close($curl1);

echo "HTTP Code: $httpCode1\n";
echo "Error: " . ($error1 ?: 'None') . "\n";
echo "Response: " . substr($response1, 0, 100) . "\n\n";

// Solution 2: Using different CURL options
echo "2. 🛠️ Testing with different CURL configuration...\n";

$curl2 = curl_init();
curl_setopt_array($curl2, array(
    CURLOPT_URL => 'https://api.fonnte.com/send',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 0, // No timeout
    CURLOPT_CONNECTTIMEOUT => 0,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_0, // Use HTTP/1.0
    CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4, // Force IPv4
    CURLOPT_POSTFIELDS => array(
        'target' => $testPhone,
        'message' => $testMessage,
        'countryCode' => '62'
    ),
    CURLOPT_HTTPHEADER => array(
        'Authorization: ' . $token
    ),
));

$response2 = curl_exec($curl2);
$httpCode2 = curl_getinfo($curl2, CURLINFO_HTTP_CODE);
$error2 = curl_error($curl2);
curl_close($curl2);

echo "HTTP Code: $httpCode2\n";
echo "Error: " . ($error2 ?: 'None') . "\n";
echo "Response: " . substr($response2, 0, 100) . "\n\n";

// Solution 3: Test with proxy (if available)
echo "3. 🔀 Testing system network configuration...\n";

// Check if we can resolve DNS
$ip = gethostbyname('api.fonnte.com');
echo "DNS Resolution: api.fonnte.com -> $ip\n";

// Check if it's a valid IP
if (filter_var($ip, FILTER_VALIDATE_IP)) {
    echo "✅ DNS resolution successful\n";
    
    // Try direct IP connection
    $curl3 = curl_init();
    curl_setopt_array($curl3, array(
        CURLOPT_URL => "https://$ip/send",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_POSTFIELDS => array(
            'target' => $testPhone,
            'message' => $testMessage,
            'countryCode' => '62'
        ),
        CURLOPT_HTTPHEADER => array(
            'Authorization: ' . $token,
            'Host: api.fonnte.com'
        ),
    ));
    
    $response3 = curl_exec($curl3);
    $httpCode3 = curl_getinfo($curl3, CURLINFO_HTTP_CODE);
    $error3 = curl_error($curl3);
    curl_close($curl3);
    
    echo "Direct IP - HTTP Code: $httpCode3\n";
    echo "Direct IP - Error: " . ($error3 ?: 'None') . "\n";
    
} else {
    echo "❌ DNS resolution failed\n";
}

// Solution 4: Check if any method worked
echo "\n4. 📊 SUMMARY:\n";
echo "=============\n";

$solutions = [
    'Alternative endpoint' => $httpCode1,
    'Different CURL config' => $httpCode2,
    'Direct IP' => isset($httpCode3) ? $httpCode3 : 0
];

$workingSolution = null;
foreach ($solutions as $name => $code) {
    $status = $code === 200 ? '✅ SUCCESS' : ($code > 0 ? '⚠️ PARTIAL' : '❌ FAILED');
    echo "$name: $status (HTTP $code)\n";
    
    if ($code === 200 && !$workingSolution) {
        $workingSolution = $name;
    }
}

if ($workingSolution) {
    echo "\n🎉 SOLUTION FOUND: $workingSolution works!\n";
    echo "💡 Update WhatsAppService to use this method\n";
} else {
    echo "\n🚨 ALL METHODS FAILED\n";
    echo "💡 Possible causes:\n";
    echo "   • Network/Firewall blocking external HTTPS\n";
    echo "   • ISP blocking Fonnte domain\n";
    echo "   • Server/hosting restrictions\n";
    echo "   • Fonnte service temporarily down\n";
    echo "\n🔧 Try these solutions:\n";
    echo "   • Use different network/internet connection\n";
    echo "   • Contact hosting provider about external API access\n";
    echo "   • Try from different server/computer\n";
    echo "   • Check Fonnte status page\n";
}

echo "\n🎯 TEST COMPLETE!\n";
