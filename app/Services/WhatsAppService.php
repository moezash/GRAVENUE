<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private $apiUrl = 'https://api.fonnte.com/send';
    private $token;

    public function __construct()
    {
        // Ambil token dari environment variable
        $this->token = env('FONNTE_TOKEN', 'your_fonnte_token_here');
    }

    /**
     * Kirim notifikasi WhatsApp untuk booking yang disetujui
     */
    public function sendBookingApprovedNotification($bookingId)
    {
        try {
            $booking = Booking::with(['facility', 'user'])->find($bookingId);
            
            if (!$booking) {
                Log::error("Booking not found: {$bookingId}");
                return false;
            }

            // Format nomor telepon (hapus karakter non-digit dan tambah 62)
            $phone = $this->formatPhoneNumber($booking->user_phone);
            
            // Buat pesan
            $message = $this->createApprovedMessage($booking);
            
            // Simpan log notifikasi
            $notificationId = $this->createNotificationLog($booking->id, $phone, $message, 'booking_approved');
            
            // Kirim pesan
            $response = $this->sendMessage($phone, $message);
            
            // Update log notifikasi
            $this->updateNotificationLog($notificationId, $response);
            
            return $response['success'] ?? false;
            
        } catch (\Exception $e) {
            Log::error("WhatsApp notification error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Kirim notifikasi WhatsApp untuk booking yang ditolak
     */
    public function sendBookingRejectedNotification($bookingId)
    {
        try {
            $booking = Booking::with(['facility', 'user'])->find($bookingId);
            
            if (!$booking) {
                Log::error("Booking not found: {$bookingId}");
                return false;
            }

            $phone = $this->formatPhoneNumber($booking->user_phone);
            $message = $this->createRejectedMessage($booking);
            
            $notificationId = $this->createNotificationLog($booking->id, $phone, $message, 'booking_rejected');
            $response = $this->sendMessage($phone, $message);
            $this->updateNotificationLog($notificationId, $response);
            
            return $response['success'] ?? false;
            
        } catch (\Exception $e) {
            Log::error("WhatsApp notification error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Format nomor telepon ke format internasional
     */
    private function formatPhoneNumber($phone)
    {
        // Hapus semua karakter non-digit
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Jika dimulai dengan 0, ganti dengan 62
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }
        
        // Jika belum dimulai dengan 62, tambahkan 62
        if (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }
        
        return $phone;
    }

    /**
     * Buat pesan untuk booking yang disetujui
     */
    private function createApprovedMessage($booking)
    {
        return "🎉 *BOOKING DISETUJUI* 🎉\n\n" .
               "Halo {$booking->user_name},\n\n" .
               "Pengajuan penyewaan fasilitas Anda telah *DISETUJUI*!\n\n" .
               "📋 *Detail Booking:*\n" .
               "• Fasilitas: {$booking->facility->name}\n" .
               "• Acara: {$booking->event_name}\n" .
               "• Tanggal: " . $booking->booking_date->format('d/m/Y') . "\n" .
               "• Waktu: {$booking->start_time} - {$booking->end_time}\n" .
               "• Peserta: {$booking->participants} orang\n" .
               "• Total Biaya: Rp " . number_format($booking->total_price, 0, ',', '.') . "\n\n" .
               "💳 Silakan lakukan pembayaran sesuai dengan total biaya di atas.\n\n" .
               "📞 Jika ada pertanyaan, hubungi admin SMKN 4 Malang.\n\n" .
               "Terima kasih! 🙏\n" .
               "*SMKN 4 Malang*";
    }

    /**
     * Buat pesan untuk booking yang ditolak
     */
    private function createRejectedMessage($booking)
    {
        return "❌ *BOOKING DITOLAK* ❌\n\n" .
               "Halo {$booking->user_name},\n\n" .
               "Mohon maaf, pengajuan penyewaan fasilitas Anda *DITOLAK*.\n\n" .
               "📋 *Detail Booking:*\n" .
               "• Fasilitas: {$booking->facility->name}\n" .
               "• Acara: {$booking->event_name}\n" .
               "• Tanggal: " . $booking->booking_date->format('d/m/Y') . "\n" .
               "• Waktu: {$booking->start_time} - {$booking->end_time}\n\n" .
               "📝 Silakan ajukan kembali dengan waktu atau tanggal yang berbeda.\n\n" .
               "📞 Untuk informasi lebih lanjut, hubungi admin SMKN 4 Malang.\n\n" .
               "Terima kasih atas pengertiannya. 🙏\n" .
               "*SMKN 4 Malang*";
    }

    /**
     * Kirim pesan WhatsApp via API Fonnte dengan retry mechanism
     */
    private function sendMessage($phone, $message)
    {
        // Check if simulation mode is enabled
        if (env('WHATSAPP_SIMULATION_MODE', false)) {
            return $this->simulateMessage($phone, $message);
        }
        
        $maxRetries = 3;
        $retryDelay = 2; // seconds
        
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            Log::info("🔄 Attempt $attempt/$maxRetries to send WhatsApp to $phone");
            
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->apiUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 60, // Increased timeout to 60 seconds
                CURLOPT_CONNECTTIMEOUT => 30, // Increased connection timeout to 30 seconds
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_SSL_VERIFYPEER => false, // Disable SSL verification for problematic networks
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_USERAGENT => 'Gravenue-WhatsApp/1.0',
                CURLOPT_POSTFIELDS => array(
                    'target' => $phone,
                    'message' => $message,
                    'countryCode' => '62',
                    'schedule' => 0,
                    'typing' => false,
                    'delay' => 1,
                ),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: ' . $this->token,
                    'Accept: application/json'
                ),
            ));

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $error = curl_error($curl);
            $totalTime = curl_getinfo($curl, CURLINFO_TOTAL_TIME);
            curl_close($curl);

            Log::info("📊 API Response - HTTP: $httpCode, Time: {$totalTime}s, Error: " . ($error ?: 'None'));

            if ($error) {
                Log::error("❌ CURL Error (Attempt $attempt): " . $error);
            }

            // If successful, return immediately
            if ($httpCode === 200 && !$error) {
                $result = json_decode($response, true);
                $success = isset($result['status']) && $result['status'] === true;
                
                Log::info($success ? "✅ WhatsApp sent successfully" : "⚠️ API returned error: " . ($result['message'] ?? 'Unknown'));
                
                return [
                    'success' => $success,
                    'response' => $response,
                    'http_code' => $httpCode,
                    'curl_error' => $error,
                    'attempt' => $attempt
                ];
            }
            
            // If not the last attempt, wait before retry
            if ($attempt < $maxRetries) {
                Log::warning("⏳ Retrying in {$retryDelay} seconds...");
                sleep($retryDelay);
                $retryDelay *= 2; // Exponential backoff
            }
        }
        
        // All attempts failed
        Log::error("💥 All $maxRetries attempts failed to send WhatsApp");
        
        return [
            'success' => false,
            'response' => $response ?? '',
            'http_code' => $httpCode ?? 0,
            'curl_error' => $error ?? 'All retry attempts failed',
            'attempt' => $maxRetries
        ];
    }

    /**
     * Simulate WhatsApp message sending (for testing when API is not accessible)
     */
    private function simulateMessage($phone, $message)
    {
        Log::info("🎭 SIMULATION MODE: WhatsApp message to $phone");
        Log::info("📱 Message content:\n" . $message);
        
        // Simulate API response
        $simulatedResponse = [
            'status' => true,
            'message' => 'success! message simulated',
            'id' => ['SIM' . time()],
            'process' => 'simulated',
            'quota' => [
                'simulated' => [
                    'details' => 'simulation mode',
                    'quota' => 999,
                    'remaining' => 998,
                    'used' => 1
                ]
            ],
            'requestid' => rand(100000000, 999999999),
            'target' => [$phone]
        ];
        
        Log::info("✅ WhatsApp SIMULATION successful - Message would be sent to $phone");
        
        return [
            'success' => true,
            'response' => json_encode($simulatedResponse),
            'http_code' => 200,
            'curl_error' => null,
            'attempt' => 1,
            'simulated' => true
        ];
    }

    /**
     * Buat log notifikasi di database
     */
    private function createNotificationLog($bookingId, $phone, $message, $type)
    {
        return DB::table('notification_logs')->insertGetId([
            'booking_id' => $bookingId,
            'phone_number' => $phone,
            'message' => $message,
            'type' => $type,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Update log notifikasi dengan response
     */
    private function updateNotificationLog($notificationId, $response)
    {
        DB::table('notification_logs')
            ->where('id', $notificationId)
            ->update([
                'status' => $response['success'] ? 'sent' : 'failed',
                'response' => $response['response'],
                'sent_at' => now(),
                'updated_at' => now()
            ]);
    }
}
