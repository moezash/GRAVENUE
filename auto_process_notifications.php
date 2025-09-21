<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🚀 AUTO PROCESS NOTIFICATIONS - GRAVENUE\n";
echo "⏰ " . date('Y-m-d H:i:s') . "\n\n";

// Cek pending jobs
$pendingJobs = DB::table('jobs')->count();

if ($pendingJobs > 0) {
    echo "📋 Found $pendingJobs pending notification(s)\n";
    echo "🔄 Processing all pending notifications...\n\n";
    
    // Process semua pending jobs
    for ($i = 0; $i < $pendingJobs; $i++) {
        echo "Processing job " . ($i + 1) . "/$pendingJobs...\n";
        
        // Jalankan queue worker sekali
        $output = shell_exec('php artisan queue:work --once --quiet 2>&1');
        
        if ($output) {
            echo "Output: $output\n";
        }
        
        // Cek apakah masih ada job
        $remaining = DB::table('jobs')->count();
        if ($remaining == 0) {
            echo "✅ All notifications processed!\n";
            break;
        }
        
        // Delay 1 detik untuk menghindari rate limit
        sleep(1);
    }
    
    echo "\n📊 Final status:\n";
    $finalPending = DB::table('jobs')->count();
    $recentLogs = DB::table('notification_logs')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get(['booking_id', 'type', 'status', 'created_at']);
    
    echo "Remaining pending jobs: $finalPending\n";
    echo "Recent notifications:\n";
    foreach ($recentLogs as $log) {
        $status = $log->status === 'sent' ? '✅' : '❌';
        $type = $log->type === 'booking_approved' ? 'APPROVED' : 'REJECTED';
        echo "$status Booking #{$log->booking_id} - $type - {$log->created_at}\n";
    }
    
} else {
    echo "✅ No pending notifications\n";
    
    // Show recent activity
    echo "\n📊 Recent notification activity:\n";
    $recentLogs = DB::table('notification_logs')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get(['booking_id', 'type', 'status', 'created_at']);
    
    foreach ($recentLogs as $log) {
        $status = $log->status === 'sent' ? '✅' : '❌';
        $type = $log->type === 'booking_approved' ? 'APPROVED' : 'REJECTED';
        echo "$status Booking #{$log->booking_id} - $type - {$log->created_at}\n";
    }
}

echo "\n💡 Tip: Jalankan script ini setiap kali mengubah status booking\n";
echo "💡 Atau jalankan: ./start_queue_worker.sh untuk auto-processing\n";
echo "\n🎯 Script selesai!\n";
