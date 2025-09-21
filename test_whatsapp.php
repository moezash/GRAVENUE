<?php

require_once 'vendor/autoload.php';

use App\Services\WhatsAppService;
use Illuminate\Support\Facades\DB;

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TEST NOTIFIKASI WHATSAPP GRAVENUE ===\n\n";

// Test 1: Cek token Fonnte
echo "1. Testing Token Fonnte...\n";
$token = env('FONNTE_TOKEN');
echo "Token: " . ($token ? "✓ Ada (***" . substr($token, -4) . ")" : "✗ Tidak ada") . "\n\n";

// Test 2: Test API Fonnte langsung
echo "2. Testing API Fonnte...\n";
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://api.fonnte.com/validate',
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

// Test 3: Cek data booking terbaru
echo "3. Checking recent bookings...\n";
try {
    $bookings = DB::table('bookings')
        ->join('facilities', 'bookings.facility_id', '=', 'facilities.id')
        ->select('bookings.*', 'facilities.name as facility_name')
        ->orderBy('bookings.created_at', 'desc')
        ->limit(3)
        ->get();
    
    foreach ($bookings as $booking) {
        echo "Booking ID: {$booking->id}\n";
        echo "Fasilitas: {$booking->facility_name}\n";
        echo "User: {$booking->user_name}\n";
        echo "Phone: {$booking->user_phone}\n";
        echo "Status: {$booking->status}\n";
        echo "---\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n4. Testing WhatsApp Service...\n";
try {
    $whatsappService = new WhatsAppService();
    
    // Ambil booking terbaru untuk test
    $latestBooking = DB::table('bookings')->orderBy('created_at', 'desc')->first();
    
    if ($latestBooking) {
        echo "Testing dengan booking ID: {$latestBooking->id}\n";
        echo "Phone: {$latestBooking->user_phone}\n";
        
        // Test format nomor telepon
        $reflection = new ReflectionClass($whatsappService);
        $method = $reflection->getMethod('formatPhoneNumber');
        $method->setAccessible(true);
        $formattedPhone = $method->invoke($whatsappService, $latestBooking->user_phone);
        
        echo "Formatted phone: $formattedPhone\n";
        
        // Jangan kirim pesan sebenarnya, hanya test format
        echo "✓ WhatsApp Service dapat diinstansiasi\n";
    } else {
        echo "✗ Tidak ada booking untuk test\n";
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n5. Checking notification logs...\n";
try {
    $logs = DB::table('notification_logs')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get(['id', 'booking_id', 'phone_number', 'status', 'created_at']);
    
    foreach ($logs as $log) {
        echo "Log ID: {$log->id} | Booking: {$log->booking_id} | Phone: {$log->phone_number} | Status: {$log->status} | Time: {$log->created_at}\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== TEST SELESAI ===\n";
