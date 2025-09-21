<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Booking;
use App\Observers\BookingObserver;
use App\Jobs\SendWhatsAppNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

echo "🔍 COMPREHENSIVE NOTIFICATION SYSTEM DEBUG\n";
echo "==========================================\n\n";

// 1. Test Observer Registration
echo "1. 🔧 Testing Observer Registration...\n";
$model = new Booking();
$dispatcher = $model->getEventDispatcher();
echo "Event dispatcher exists: " . ($dispatcher ? '✅ Yes' : '❌ No') . "\n";

// Check if observers are registered
$listeners = $dispatcher ? $dispatcher->getListeners('eloquent.updated: App\Models\Booking') : [];
echo "Update listeners count: " . count($listeners) . "\n\n";

// 2. Test Database Connection
echo "2. 🗄️ Testing Database Connection...\n";
try {
    $bookingCount = Booking::count();
    echo "✅ Database connected. Total bookings: $bookingCount\n";
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

// 3. Test Queue Configuration
echo "\n3. ⚙️ Testing Queue Configuration...\n";
$queueConnection = config('queue.default');
echo "Queue connection: $queueConnection\n";

$pendingJobs = DB::table('jobs')->count();
$failedJobs = DB::table('failed_jobs')->count();
echo "Pending jobs: $pendingJobs\n";
echo "Failed jobs: $failedJobs\n";

// 4. Test Manual Observer Trigger
echo "\n4. 🧪 Testing Manual Observer Trigger...\n";
$testBooking = Booking::orderBy('created_at', 'desc')->first();

if ($testBooking) {
    echo "Using booking ID: {$testBooking->id}\n";
    echo "Current status: {$testBooking->status}\n";
    
    // Simulate status change
    $oldStatus = $testBooking->status;
    $newStatus = $oldStatus === 'pending' ? 'approved' : 'pending';
    
    echo "Simulating status change: $oldStatus → $newStatus\n";
    
    // Clear previous logs
    $logFile = storage_path('logs/laravel.log');
    if (file_exists($logFile)) {
        $logSize = filesize($logFile);
        echo "Log file size before: " . number_format($logSize) . " bytes\n";
    }
    
    // Trigger update
    $testBooking->update(['status' => $newStatus]);
    
    // Check logs
    sleep(1); // Wait for log write
    if (file_exists($logFile)) {
        $newLogSize = filesize($logFile);
        echo "Log file size after: " . number_format($newLogSize) . " bytes\n";
        
        // Read last few lines
        $lastLines = shell_exec("tail -5 '$logFile'");
        echo "Recent log entries:\n$lastLines\n";
    }
    
    // Check if job was created
    $newPendingJobs = DB::table('jobs')->count();
    echo "Jobs after update: $newPendingJobs (was: $pendingJobs)\n";
    
    if ($newPendingJobs > $pendingJobs) {
        echo "✅ Observer triggered! New job created.\n";
    } else {
        echo "❌ Observer NOT triggered! No new job created.\n";
    }
    
} else {
    echo "❌ No bookings found for testing\n";
}

// 5. Test WhatsApp Service
echo "\n5. 📱 Testing WhatsApp Service...\n";
$token = env('FONNTE_TOKEN');
echo "Token configured: " . ($token ? '✅ Yes' : '❌ No') . "\n";

if ($token) {
    // Test API connection
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
    
    echo "API test HTTP code: $httpCode\n";
    if ($httpCode === 200) {
        echo "✅ Fonnte API accessible\n";
    } else {
        echo "❌ Fonnte API issue\n";
    }
}

// 6. Test Job Processing
echo "\n6. 🔄 Testing Job Processing...\n";
if ($pendingJobs > 0) {
    echo "Processing one pending job...\n";
    $output = shell_exec('php artisan queue:work --once --quiet 2>&1');
    echo "Queue work output: $output\n";
    
    $newPendingJobs = DB::table('jobs')->count();
    echo "Jobs remaining: $newPendingJobs\n";
    
    if ($newPendingJobs < $pendingJobs) {
        echo "✅ Job processed successfully\n";
    } else {
        echo "❌ Job processing failed\n";
    }
} else {
    echo "No pending jobs to test\n";
}

// 7. Environment Check
echo "\n7. 🌍 Environment Check...\n";
echo "APP_ENV: " . env('APP_ENV') . "\n";
echo "APP_DEBUG: " . (env('APP_DEBUG') ? 'true' : 'false') . "\n";
echo "QUEUE_CONNECTION: " . env('QUEUE_CONNECTION') . "\n";
echo "DB_CONNECTION: " . env('DB_CONNECTION') . "\n";

// 8. Recommendations
echo "\n8. 💡 RECOMMENDATIONS:\n";
echo "==================\n";

if ($pendingJobs > 0) {
    echo "🚨 URGENT: $pendingJobs pending notifications!\n";
    echo "   Run: php artisan queue:work\n";
}

if (!$token) {
    echo "🚨 URGENT: Fonnte token not configured!\n";
    echo "   Add FONNTE_TOKEN to .env file\n";
}

echo "\n🎯 DEBUG COMPLETE!\n";
