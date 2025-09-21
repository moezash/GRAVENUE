<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Booking;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\DB;

echo "=== TEST REJECT NOTIFICATION ===\n\n";

// Ambil booking #10 untuk test
$booking = Booking::find(10);

if (!$booking) {
    echo "❌ Booking #10 tidak ditemukan\n";
    exit;
}

echo "📋 Testing dengan Booking #10:\n";
echo "User: {$booking->user_name}\n";
echo "Phone: {$booking->user_phone}\n";
echo "Status: {$booking->status}\n";
echo "Fasilitas: " . $booking->facility->name . "\n";
echo "Acara: {$booking->event_name}\n\n";

// Test WhatsApp Service langsung
echo "🧪 Testing WhatsApp Service untuk REJECTED...\n";
$whatsappService = new WhatsAppService();

// Test method createRejectedMessage
$reflection = new ReflectionClass($whatsappService);
$method = $reflection->getMethod('createRejectedMessage');
$method->setAccessible(true);
$rejectedMessage = $method->invoke($whatsappService, $booking);

echo "📝 Pesan REJECTED yang akan dikirim:\n";
echo "----------------------------------------\n";
echo $rejectedMessage;
echo "\n----------------------------------------\n\n";

// Bandingkan dengan pesan APPROVED
$method2 = $reflection->getMethod('createApprovedMessage');
$method2->setAccessible(true);
$approvedMessage = $method2->invoke($whatsappService, $booking);

echo "📝 Pesan APPROVED (untuk perbandingan):\n";
echo "----------------------------------------\n";
echo $approvedMessage;
echo "\n----------------------------------------\n\n";

// Cek perbedaan header
echo "🔍 Analisis Header Pesan:\n";
$rejectedHeader = substr($rejectedMessage, 0, 50);
$approvedHeader = substr($approvedMessage, 0, 50);

echo "REJECTED Header: $rejectedHeader\n";
echo "APPROVED Header: $approvedHeader\n\n";

if (strpos($rejectedMessage, 'DITOLAK') !== false) {
    echo "✅ Pesan REJECTED mengandung kata 'DITOLAK'\n";
} else {
    echo "❌ Pesan REJECTED TIDAK mengandung kata 'DITOLAK'\n";
}

if (strpos($rejectedMessage, 'DISETUJUI') !== false) {
    echo "❌ MASALAH: Pesan REJECTED mengandung kata 'DISETUJUI'\n";
} else {
    echo "✅ Pesan REJECTED tidak mengandung kata 'DISETUJUI'\n";
}

echo "\n=== TEST SELESAI ===\n";
