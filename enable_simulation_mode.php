<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🎭 ENABLING SIMULATION MODE\n";
echo "===========================\n\n";

echo "Karena API Fonnte tidak bisa diakses dari jaringan ini,\n";
echo "saya akan mengaktifkan SIMULATION MODE untuk testing.\n\n";

// Add simulation flag to .env
$envPath = base_path('.env');
$envExamplePath = base_path('.env.example');

// Read current .env or .env.example
$envContent = '';
if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
} elseif (file_exists($envExamplePath)) {
    $envContent = file_get_contents($envExamplePath);
}

// Add simulation mode if not exists
if (strpos($envContent, 'WHATSAPP_SIMULATION_MODE') === false) {
    $envContent .= "\n# WhatsApp Simulation Mode (for testing when API is not accessible)\n";
    $envContent .= "WHATSAPP_SIMULATION_MODE=true\n";
    
    if (file_exists($envPath)) {
        file_put_contents($envPath, $envContent);
        echo "✅ Added WHATSAPP_SIMULATION_MODE=true to .env\n";
    } else {
        file_put_contents($envExamplePath, $envContent);
        echo "✅ Added WHATSAPP_SIMULATION_MODE=true to .env.example\n";
        echo "⚠️  Please copy .env.example to .env\n";
    }
}

echo "\n🎯 SIMULATION MODE ENABLED!\n";
echo "Sistem akan:\n";
echo "• ✅ Tetap menjalankan semua proses normal\n";
echo "• ✅ Menyimpan log notifikasi ke database\n";
echo "• 🎭 Mensimulasikan pengiriman WhatsApp (tidak benar-benar kirim)\n";
echo "• 📝 Menampilkan pesan yang akan dikirim di log\n\n";

echo "Untuk kembali ke mode normal:\n";
echo "• Set WHATSAPP_SIMULATION_MODE=false di .env\n";
echo "• Atau hapus baris tersebut\n\n";

echo "🧪 Test simulation mode:\n";
echo "php test_final_system.php\n\n";

echo "✅ SETUP COMPLETE!\n";
