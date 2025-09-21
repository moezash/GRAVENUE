<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== STATUS SISTEM NOTIFIKASI WHATSAPP GRAVENUE ===\n\n";

// 1. Cek pending jobs
echo "1. 📋 Checking pending jobs in queue...\n";
try {
    $pendingJobs = DB::table('jobs')->count();
    $failedJobs = DB::table('failed_jobs')->count();
    
    echo "Pending jobs: $pendingJobs\n";
    echo "Failed jobs: $failedJobs\n";
    
    if ($pendingJobs > 0) {
        echo "⚠️  Ada $pendingJobs job yang menunggu diproses!\n";
        echo "💡 Jalankan: php artisan queue:work\n";
    } else {
        echo "✅ Tidak ada job yang pending\n";
    }
    
    if ($failedJobs > 0) {
        echo "❌ Ada $failedJobs job yang gagal!\n";
        echo "💡 Lihat detail: php artisan queue:failed\n";
        echo "💡 Retry semua: php artisan queue:retry all\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n2. 📊 Recent notification logs (last 10)...\n";
try {
    $logs = DB::table('notification_logs')
        ->join('bookings', 'notification_logs.booking_id', '=', 'bookings.id')
        ->select('notification_logs.*', 'bookings.user_name', 'bookings.event_name')
        ->orderBy('notification_logs.created_at', 'desc')
        ->limit(10)
        ->get();
    
    foreach ($logs as $log) {
        $status = $log->status === 'sent' ? '✅' : ($log->status === 'failed' ? '❌' : '⏳');
        echo "$status Booking #{$log->booking_id} | {$log->user_name} | {$log->phone_number} | {$log->status} | {$log->created_at}\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n3. 🔧 System recommendations...\n";

// Cek apakah ada job pending
$pendingCount = DB::table('jobs')->count();
if ($pendingCount > 0) {
    echo "🚨 URGENT: Ada $pendingCount notifikasi yang belum terkirim!\n";
    echo "🔧 Solusi: Jalankan queue worker dengan perintah:\n";
    echo "   ./start_queue_worker.sh\n";
    echo "   atau\n";
    echo "   php artisan queue:work --verbose\n\n";
}

// Cek notifikasi terakhir
$lastNotification = DB::table('notification_logs')
    ->orderBy('created_at', 'desc')
    ->first();

if ($lastNotification) {
    $lastTime = new DateTime($lastNotification->created_at);
    $now = new DateTime();
    $diff = $now->diff($lastTime);
    
    echo "📅 Notifikasi terakhir: " . $diff->format('%h jam %i menit yang lalu') . "\n";
    echo "📱 Status: " . ($lastNotification->status === 'sent' ? '✅ Berhasil' : '❌ Gagal') . "\n";
}

echo "\n4. 💡 Tips untuk memastikan notifikasi berfungsi:\n";
echo "   • Pastikan queue worker berjalan: ./start_queue_worker.sh\n";
echo "   • Cek token Fonnte masih valid\n";
echo "   • Pastikan nomor HP user valid (format Indonesia)\n";
echo "   • Monitor log: tail -f storage/logs/laravel.log\n";

echo "\n=== STATUS CHECK SELESAI ===\n";
