#!/bin/bash

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
