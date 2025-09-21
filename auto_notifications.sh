#!/bin/bash

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
