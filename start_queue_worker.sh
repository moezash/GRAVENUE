#!/bin/bash

# Script untuk menjalankan Laravel Queue Worker
# Untuk sistem notifikasi WhatsApp Gravenue - MODE REAL-TIME

echo "🚀 Starting Gravenue Queue Worker for WhatsApp Notifications..."
echo "📱 Sistem notifikasi WhatsApp akan berjalan otomatis (REAL-TIME MODE)"
echo "⚡ Optimasi untuk pengiriman cepat"
echo "⏹️  Tekan Ctrl+C untuk menghentikan"
echo ""

# Pindah ke directory project
cd /Applications/XAMPP/xamppfiles/htdocs/Gravenue

# Jalankan queue worker dengan optimasi real-time
# --sleep=1: Cek job setiap 1 detik
# --tries=3: Retry 3x jika gagal
# --timeout=60: Timeout 60 detik per job
# --max-jobs=100: Restart setelah 100 job untuk mencegah memory leak
php artisan queue:work --verbose --sleep=1 --tries=3 --timeout=60 --max-jobs=100
