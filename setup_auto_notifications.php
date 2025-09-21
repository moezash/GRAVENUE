<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🚀 SETUP AUTO NOTIFICATIONS - GRAVENUE\n";
echo "=====================================\n\n";

// 1. Process any pending notifications first
$pendingJobs = DB::table('jobs')->count();

if ($pendingJobs > 0) {
    echo "📋 Found $pendingJobs pending notification(s)\n";
    echo "🔄 Processing all pending notifications...\n";
    
    // Process all pending jobs
    exec('php artisan queue:work --stop-when-empty --quiet', $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "✅ All pending notifications processed!\n";
    } else {
        echo "❌ Error processing notifications\n";
    }
} else {
    echo "✅ No pending notifications\n";
}

// 2. Setup auto-processing
echo "\n🔧 SETTING UP AUTO-PROCESSING...\n";

// Create a background process script
$autoProcessScript = '#!/bin/bash

# Auto Process Notifications for Gravenue
# This script runs in background and processes notifications immediately

echo "🚀 Starting Gravenue Auto Notification Processor..."
echo "📱 Notifications will be sent immediately when booking status changes"
echo "⏹️  Press Ctrl+C to stop"

cd /Applications/XAMPP/xamppfiles/htdocs/Gravenue

# Run queue worker with restart every 100 jobs to prevent memory issues
while true; do
    echo "$(date): Starting queue worker..."
    php artisan queue:work --verbose --sleep=1 --tries=3 --max-jobs=100 --timeout=60
    echo "$(date): Queue worker restarted"
    sleep 2
done
';

file_put_contents('auto_notifications.sh', $autoProcessScript);
chmod('auto_notifications.sh', 0755);

echo "✅ Created auto_notifications.sh\n";

// 3. Create a simple checker script
$checkerScript = '#!/bin/bash

# Quick check and process any pending notifications

cd /Applications/XAMPP/xamppfiles/htdocs/Gravenue

PENDING=$(php -r "
require_once \"vendor/autoload.php\";
\$app = require_once \"bootstrap/app.php\";
\$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
echo DB::table(\"jobs\")->count();
")

if [ "$PENDING" -gt 0 ]; then
    echo "📋 Processing $PENDING pending notification(s)..."
    php artisan queue:work --stop-when-empty --quiet
    echo "✅ Done!"
else
    echo "✅ No pending notifications"
fi
';

file_put_contents('check_notifications.sh', $checkerScript);
chmod('check_notifications.sh', 0755);

echo "✅ Created check_notifications.sh\n";

// 4. Test the system
echo "\n🧪 TESTING AUTO SYSTEM...\n";

// Create a test notification
$testBooking = DB::table('bookings')->orderBy('created_at', 'desc')->first();

if ($testBooking) {
    echo "Testing with booking #{$testBooking->id}\n";
    
    // Simulate status change
    $currentStatus = $testBooking->status;
    $newStatus = $currentStatus === 'pending' ? 'approved' : 'pending';
    
    if (in_array($newStatus, ['approved', 'rejected'])) {
        echo "Simulating status change: $currentStatus → $newStatus\n";
        
        $jobsBefore = DB::table('jobs')->count();
        
        // Update booking
        DB::table('bookings')->where('id', $testBooking->id)->update(['status' => $newStatus]);
        
        sleep(1);
        
        $jobsAfter = DB::table('jobs')->count();
        
        if ($jobsAfter > $jobsBefore) {
            echo "✅ Observer working! Job created automatically\n";
            
            // Process the job
            echo "🔄 Processing notification...\n";
            exec('php artisan queue:work --once --quiet');
            
            $finalJobs = DB::table('jobs')->count();
            if ($finalJobs < $jobsAfter) {
                echo "✅ Notification sent successfully!\n";
            }
        } else {
            echo "❌ Observer not working\n";
        }
    } else {
        echo "Skipping test (status would be $newStatus, not approved/rejected)\n";
    }
}

// 5. Instructions
echo "\n📋 SETUP COMPLETE! INSTRUCTIONS:\n";
echo "================================\n";
echo "1. For AUTOMATIC processing (recommended):\n";
echo "   ./auto_notifications.sh\n";
echo "   (This runs in background and processes notifications immediately)\n\n";

echo "2. For MANUAL processing after each booking update:\n";
echo "   ./check_notifications.sh\n";
echo "   (Run this after changing booking status)\n\n";

echo "3. For ONE-TIME processing of pending notifications:\n";
echo "   php instant_notification.php\n\n";

echo "4. To check system status:\n";
echo "   php check_queue_status.php\n\n";

echo "🎯 RECOMMENDATION:\n";
echo "Run ./auto_notifications.sh in a separate terminal window\n";
echo "and keep it running for real-time notifications!\n\n";

echo "✅ AUTO NOTIFICATION SYSTEM READY!\n";
