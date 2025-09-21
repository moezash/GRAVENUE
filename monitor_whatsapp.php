<?php
/**
 * Script monitoring real-time untuk notifikasi WhatsApp
 * Gravenue - SMKN 4 Malang
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

function clearScreen() {
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        system('cls');
    } else {
        system('clear');
    }
}

function displayHeader() {
    echo "🏫 GRAVENUE - SMKN 4 Malang\n";
    echo "📱 WhatsApp Notification Monitor\n";
    echo "⏰ " . date('Y-m-d H:i:s') . "\n";
    echo str_repeat("=", 50) . "\n\n";
}

function displayStats() {
    $total = DB::table('notification_logs')->count();
    $sent = DB::table('notification_logs')->where('status', 'sent')->count();
    $failed = DB::table('notification_logs')->where('status', 'failed')->count();
    $pending = DB::table('notification_logs')->where('status', 'pending')->count();
    
    echo "📊 STATISTIK NOTIFIKASI:\n";
    echo "Total: {$total} | ✅ Terkirim: {$sent} | ❌ Gagal: {$failed} | ⏳ Pending: {$pending}\n\n";
}

function displayRecentNotifications() {
    echo "📋 NOTIFIKASI TERBARU (5 terakhir):\n";
    echo str_repeat("-", 50) . "\n";
    
    $notifications = DB::table('notification_logs')
        ->join('bookings', 'notification_logs.booking_id', '=', 'bookings.id')
        ->select(
            'notification_logs.*',
            'bookings.user_name',
            'bookings.event_name'
        )
        ->orderBy('notification_logs.created_at', 'desc')
        ->limit(5)
        ->get();
    
    foreach ($notifications as $notif) {
        $status_icon = match($notif->status) {
            'sent' => '✅',
            'failed' => '❌',
            'pending' => '⏳',
            default => '❓'
        };
        
        $type_icon = match($notif->type) {
            'booking_approved' => '🎉',
            'booking_rejected' => '❌',
            default => '📱'
        };
        
        echo "{$status_icon} {$type_icon} Booking #{$notif->booking_id}\n";
        echo "   User: {$notif->user_name}\n";
        echo "   Event: {$notif->event_name}\n";
        echo "   Phone: {$notif->phone_number}\n";
        echo "   Time: {$notif->created_at}\n";
        
        if ($notif->response) {
            $response = json_decode($notif->response, true);
            if (isset($response['process'])) {
                echo "   Process: {$response['process']}\n";
            }
        }
        echo "\n";
    }
}

function displayQueueStatus() {
    $queueJobs = DB::table('jobs')->count();
    echo "🔄 QUEUE STATUS:\n";
    echo "Jobs pending: {$queueJobs}\n";
    
    if ($queueJobs > 0) {
        echo "⚠️  Ada job yang belum diproses! Jalankan: php artisan queue:work\n";
    } else {
        echo "✅ Semua job sudah diproses\n";
    }
    echo "\n";
}

// Main monitoring loop
echo "Starting WhatsApp Notification Monitor...\n";
echo "Press Ctrl+C to stop\n\n";

while (true) {
    clearScreen();
    displayHeader();
    displayStats();
    displayQueueStatus();
    displayRecentNotifications();
    
    echo "🔄 Auto refresh dalam 5 detik...\n";
    echo "Tekan Ctrl+C untuk keluar\n";
    
    sleep(5);
}
