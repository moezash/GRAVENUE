<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Booking;
use Illuminate\Support\Facades\DB;

echo "🧪 REAL OBSERVER TEST - SIMULATING ADMIN ACTION\n";
echo "===============================================\n\n";

// Ambil booking dengan status pending
$booking = Booking::where('status', 'pending')->first();

if (!$booking) {
    echo "❌ No pending bookings found. Creating test scenario...\n";
    
    // Set booking terakhir ke pending
    $booking = Booking::orderBy('created_at', 'desc')->first();
    if ($booking) {
        $booking->update(['status' => 'pending']);
        echo "✅ Set booking #{$booking->id} to pending\n";
    } else {
        echo "❌ No bookings found at all!\n";
        exit;
    }
}

echo "📋 Using booking #{$booking->id}\n";
echo "Current status: {$booking->status}\n";
echo "User: {$booking->user_name}\n";
echo "Phone: {$booking->user_phone}\n\n";

// Check current pending jobs
$jobsBefore = DB::table('jobs')->count();
echo "Jobs before: $jobsBefore\n";

// Simulate admin approving booking
echo "🎯 SIMULATING ADMIN APPROVAL...\n";
echo "Changing status: pending → approved\n";

// Clear recent logs to see new entries
$logFile = storage_path('logs/laravel.log');
$logSizeBefore = file_exists($logFile) ? filesize($logFile) : 0;

// TRIGGER THE UPDATE (this should trigger Observer)
$booking->update(['status' => 'approved']);

// Wait a moment for logs
sleep(1);

// Check results
$jobsAfter = DB::table('jobs')->count();
$logSizeAfter = file_exists($logFile) ? filesize($logFile) : 0;

echo "\nRESULTS:\n";
echo "========\n";
echo "Jobs after: $jobsAfter (change: " . ($jobsAfter - $jobsBefore) . ")\n";
echo "Log size change: " . ($logSizeAfter - $logSizeBefore) . " bytes\n";

if ($jobsAfter > $jobsBefore) {
    echo "✅ SUCCESS! Observer created new job\n";
    
    // Show the new job details
    $latestJob = DB::table('jobs')->orderBy('created_at', 'desc')->first();
    if ($latestJob) {
        $payload = json_decode($latestJob->payload, true);
        echo "Job payload: " . json_encode($payload, JSON_PRETTY_PRINT) . "\n";
    }
} else {
    echo "❌ FAILED! No new job created\n";
}

// Show recent logs
echo "\nRECENT LOGS:\n";
echo "============\n";
$recentLogs = shell_exec("tail -10 '$logFile' | grep -E 'Booking {$booking->id}|WhatsApp.*{$booking->id}'");
echo $recentLogs ?: "No relevant logs found\n";

// Now test rejection
echo "\n" . str_repeat("=", 50) . "\n";
echo "🧪 TESTING REJECTION...\n";

$jobsBefore2 = DB::table('jobs')->count();
echo "Jobs before rejection: $jobsBefore2\n";

// Change to rejected
echo "Changing status: approved → rejected\n";
$booking->update(['status' => 'rejected']);

sleep(1);

$jobsAfter2 = DB::table('jobs')->count();
echo "Jobs after rejection: $jobsAfter2 (change: " . ($jobsAfter2 - $jobsBefore2) . ")\n";

if ($jobsAfter2 > $jobsBefore2) {
    echo "✅ SUCCESS! Rejection also creates job\n";
} else {
    echo "❌ FAILED! Rejection didn't create job\n";
}

echo "\n🎯 TEST COMPLETE!\n";
