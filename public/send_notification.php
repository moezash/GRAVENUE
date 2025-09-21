<?php
/**
 * Script untuk mengirim notifikasi WhatsApp
 * Dipanggil oleh trigger SQL ketika status booking berubah
 */

// Autoload Laravel
require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel application
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;

// Ambil parameter dari command line
$bookingId = $argv[1] ?? null;
$status = $argv[2] ?? null;

if (!$bookingId || !$status) {
    Log::error('Missing parameters for notification script');
    exit(1);
}

try {
    $whatsappService = new WhatsAppService();
    
    if ($status === 'approved') {
        $result = $whatsappService->sendBookingApprovedNotification($bookingId);
        Log::info("Notification sent for approved booking {$bookingId}: " . ($result ? 'success' : 'failed'));
    } elseif ($status === 'rejected') {
        $result = $whatsappService->sendBookingRejectedNotification($bookingId);
        Log::info("Notification sent for rejected booking {$bookingId}: " . ($result ? 'success' : 'failed'));
    }
    
    exit($result ? 0 : 1);
    
} catch (Exception $e) {
    Log::error("Notification script error: " . $e->getMessage());
    exit(1);
}
