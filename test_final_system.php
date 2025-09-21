<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Booking;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

echo "🎯 FINAL SYSTEM TEST - COMPLETE AUTO NOTIFICATION\n";
echo "=================================================\n\n";

// Get a test booking
$booking = Booking::orderBy('created_at', 'desc')->first();

if (!$booking) {
    echo "❌ No bookings found for testing\n";
    exit;
}

// Set to pending first
$booking->update(['status' => 'pending']);

echo "📋 Test Booking: #{$booking->id}\n";
echo "User: {$booking->user_name}\n";
echo "Phone: {$booking->user_phone}\n";
echo "Current Status: {$booking->status}\n\n";

// Check initial state
$jobsBefore = DB::table('jobs')->count();
$logsBefore = DB::table('notification_logs')->count();

echo "📊 Initial State:\n";
echo "Pending jobs: $jobsBefore\n";
echo "Notification logs: $logsBefore\n\n";

// Test 1: Approve booking
echo "🧪 TEST 1: APPROVING BOOKING\n";
echo "============================\n";

// Simulate admin controller action
$controller = new AdminController();
$request = new Request();
$request->merge(['status' => 'approved']);

echo "Simulating admin approval...\n";

try {
    // This should:
    // 1. Update booking status (trigger Observer)
    // 2. Observer creates job
    // 3. Controller auto-processes job
    // 4. Notification sent immediately
    
    $response = $controller->updateBookingStatus($request, $booking->id);
    
    sleep(2); // Wait for processing
    
    $jobsAfter1 = DB::table('jobs')->count();
    $logsAfter1 = DB::table('notification_logs')->count();
    
    echo "Results after approval:\n";
    echo "Pending jobs: $jobsAfter1 (change: " . ($jobsAfter1 - $jobsBefore) . ")\n";
    echo "Notification logs: $logsAfter1 (change: " . ($logsAfter1 - $logsBefore) . ")\n";
    
    if ($logsAfter1 > $logsBefore) {
        echo "✅ SUCCESS! Notification sent automatically\n";
        
        // Check the notification
        $latestLog = DB::table('notification_logs')
            ->where('booking_id', $booking->id)
            ->orderBy('created_at', 'desc')
            ->first();
        
        if ($latestLog) {
            echo "📱 Notification details:\n";
            echo "   Type: {$latestLog->type}\n";
            echo "   Status: {$latestLog->status}\n";
            echo "   Phone: {$latestLog->phone_number}\n";
            echo "   Time: {$latestLog->created_at}\n";
        }
    } else {
        echo "❌ FAILED! No notification sent\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";

// Test 2: Reject booking
echo "🧪 TEST 2: REJECTING BOOKING\n";
echo "============================\n";

$jobsBefore2 = DB::table('jobs')->count();
$logsBefore2 = DB::table('notification_logs')->count();

$request2 = new Request();
$request2->merge(['status' => 'rejected']);

echo "Simulating admin rejection...\n";

try {
    $response = $controller->updateBookingStatus($request2, $booking->id);
    
    sleep(2);
    
    $jobsAfter2 = DB::table('jobs')->count();
    $logsAfter2 = DB::table('notification_logs')->count();
    
    echo "Results after rejection:\n";
    echo "Pending jobs: $jobsAfter2 (change: " . ($jobsAfter2 - $jobsBefore2) . ")\n";
    echo "Notification logs: $logsAfter2 (change: " . ($logsAfter2 - $logsBefore2) . ")\n";
    
    if ($logsAfter2 > $logsBefore2) {
        echo "✅ SUCCESS! Rejection notification sent automatically\n";
        
        $latestLog = DB::table('notification_logs')
            ->where('booking_id', $booking->id)
            ->orderBy('created_at', 'desc')
            ->first();
        
        if ($latestLog) {
            echo "📱 Notification details:\n";
            echo "   Type: {$latestLog->type}\n";
            echo "   Status: {$latestLog->status}\n";
            echo "   Message preview: " . substr($latestLog->message, 0, 50) . "...\n";
        }
    } else {
        echo "❌ FAILED! No rejection notification sent\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

// Final summary
echo "\n📊 FINAL SUMMARY:\n";
echo "=================\n";

$totalLogs = DB::table('notification_logs')->where('booking_id', $booking->id)->count();
$pendingJobs = DB::table('jobs')->count();

echo "Total notifications for this booking: $totalLogs\n";
echo "Remaining pending jobs: $pendingJobs\n";

if ($totalLogs >= 2 && $pendingJobs == 0) {
    echo "\n🎉 PERFECT! SYSTEM IS FULLY AUTOMATIC!\n";
    echo "✅ Observer triggers on status change\n";
    echo "✅ Jobs are created automatically\n";
    echo "✅ Jobs are processed immediately\n";
    echo "✅ Notifications are sent in real-time\n";
} else {
    echo "\n⚠️ System needs attention:\n";
    if ($totalLogs < 2) {
        echo "❌ Not all notifications were sent\n";
    }
    if ($pendingJobs > 0) {
        echo "❌ Jobs are not being processed automatically\n";
    }
}

echo "\n🎯 TEST COMPLETE!\n";
