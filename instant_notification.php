<?php

// Script untuk memproses notifikasi secara instant
// Jalankan setiap kali mengubah status booking

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "⚡ INSTANT NOTIFICATION PROCESSOR\n";
echo "⏰ " . date('Y-m-d H:i:s') . "\n\n";

// Proses semua pending jobs
$pendingJobs = DB::table('jobs')->count();

if ($pendingJobs > 0) {
    echo "🔄 Processing $pendingJobs pending notification(s)...\n";
    
    // Jalankan queue worker sampai semua job selesai
    exec('php artisan queue:work --stop-when-empty --quiet', $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "✅ All notifications processed successfully!\n";
    } else {
        echo "❌ Error processing notifications\n";
    }
    
    // Show hasil
    echo "\n📊 Recent notifications:\n";
    $recentLogs = DB::table('notification_logs')
        ->join('bookings', 'notification_logs.booking_id', '=', 'bookings.id')
        ->select('notification_logs.*', 'bookings.user_name')
        ->orderBy('notification_logs.created_at', 'desc')
        ->limit(3)
        ->get();
    
    foreach ($recentLogs as $log) {
        $status = $log->status === 'sent' ? '✅' : '❌';
        $type = $log->type === 'booking_approved' ? '🎉 APPROVED' : '❌ REJECTED';
        echo "$status {$log->user_name} | Booking #{$log->booking_id} | $type | {$log->created_at}\n";
    }
    
} else {
    echo "✅ No pending notifications\n";
}

echo "\n🎯 Done!\n";
