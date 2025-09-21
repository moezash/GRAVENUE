# 📱 Sistem Notifikasi WhatsApp Otomatis - Gravenue

Sistem notifikasi WhatsApp otomatis untuk memberitahu user ketika pengajuan sewa fasilitas telah dikonfirmasi (disetujui/ditolak).

## 🚀 Fitur

- ✅ Notifikasi otomatis ketika booking disetujui
- ❌ Notifikasi otomatis ketika booking ditolak  
- 📊 Logging semua notifikasi yang dikirim
- 🔄 Retry mechanism untuk notifikasi yang gagal
- 📱 Format pesan yang user-friendly dengan emoji
- 🇮🇩 Format nomor telepon Indonesia otomatis

## 📋 Persyaratan

1. **API WhatsApp**: Token dari [Fonnte.com](https://fonnte.com) (gratis)
2. **Laravel Queue**: Untuk mengirim notifikasi secara asynchronous
3. **Database**: Tabel notification_logs untuk logging

## 🛠️ Instalasi

### 1. Jalankan Migration

```bash
php artisan migrate
```

### 2. Daftar di Fonnte.com

1. Kunjungi [https://fonnte.com](https://fonnte.com)
2. Daftar akun gratis
3. Dapatkan token API
4. Scan QR Code untuk menghubungkan WhatsApp

### 3. Konfigurasi Environment

Tambahkan token Fonnte ke file `.env`:

```env
FONNTE_TOKEN=your_fonnte_token_here
```

### 4. Setup Queue (Opsional tapi Direkomendasikan)

Untuk performa yang lebih baik, setup Laravel Queue:

```bash
# Ubah QUEUE_CONNECTION di .env
QUEUE_CONNECTION=database

# Buat tabel jobs
php artisan queue:table
php artisan migrate

# Jalankan queue worker
php artisan queue:work
```

### 5. Instalasi Trigger SQL (Alternatif)

Jika ingin menggunakan trigger SQL langsung:

```bash
# Import file SQL ke database
mysql -u username -p database_name < database/whatsapp_notification_trigger.sql
```

## 📱 Cara Kerja

### Metode 1: Laravel Observer (Direkomendasikan)

1. **BookingObserver** mendeteksi perubahan status booking
2. **SendWhatsAppNotification Job** dijalankan secara asynchronous
3. **WhatsAppService** mengirim pesan via API Fonnte
4. **notification_logs** menyimpan log semua aktivitas

### Metode 2: Trigger SQL

1. **Trigger SQL** mendeteksi UPDATE pada tabel bookings
2. **Stored Procedure** memanggil script PHP
3. **Script PHP** mengirim notifikasi WhatsApp

## 📝 Format Pesan

### Booking Disetujui
```
🎉 BOOKING DISETUJUI 🎉

Halo [Nama User],

Pengajuan penyewaan fasilitas Anda telah DISETUJUI!

📋 Detail Booking:
• Fasilitas: [Nama Fasilitas]
• Acara: [Nama Acara]
• Tanggal: [DD/MM/YYYY]
• Waktu: [HH:MM - HH:MM]
• Peserta: [Jumlah] orang
• Total Biaya: Rp [Nominal]

💳 Silakan lakukan pembayaran sesuai dengan total biaya di atas.

📞 Jika ada pertanyaan, hubungi admin SMKN 4 Malang.

Terima kasih! 🙏
SMKN 4 Malang
```

### Booking Ditolak
```
❌ BOOKING DITOLAK ❌

Halo [Nama User],

Mohon maaf, pengajuan penyewaan fasilitas Anda DITOLAK.

📋 Detail Booking:
• Fasilitas: [Nama Fasilitas]
• Acara: [Nama Acara]
• Tanggal: [DD/MM/YYYY]
• Waktu: [HH:MM - HH:MM]

📝 Silakan ajukan kembali dengan waktu atau tanggal yang berbeda.

📞 Untuk informasi lebih lanjut, hubungi admin SMKN 4 Malang.

Terima kasih atas pengertiannya. 🙏
SMKN 4 Malang
```

## 🧪 Testing

### Test Manual
```bash
# Update status booking untuk trigger notifikasi
UPDATE bookings SET status = 'approved' WHERE id = 1;
```

### Test via Laravel Tinker
```bash
php artisan tinker

# Test service langsung
$service = new App\Services\WhatsAppService();
$service->sendBookingApprovedNotification(1);

# Test job
App\Jobs\SendWhatsAppNotification::dispatch(1, 'approved');
```

## 📊 Monitoring

### Lihat Log Notifikasi
```sql
-- Semua notifikasi
SELECT * FROM notification_logs ORDER BY created_at DESC;

-- Notifikasi yang gagal
SELECT * FROM notification_logs WHERE status = 'failed';

-- Notifikasi hari ini
SELECT * FROM notification_logs WHERE DATE(created_at) = CURDATE();
```

### Laravel Logs
```bash
# Lihat log Laravel
tail -f storage/logs/laravel.log
```

## 🔧 Troubleshooting

### Notifikasi Tidak Terkirim

1. **Cek Token Fonnte**
   ```bash
   # Test token via curl
   curl -X POST https://api.fonnte.com/send \
   -H "Authorization: YOUR_TOKEN" \
   -d "target=6281234567890" \
   -d "message=Test message"
   ```

2. **Cek Format Nomor Telepon**
   - Harus format internasional: 6281234567890
   - Tidak boleh ada spasi atau karakter khusus

3. **Cek Queue Worker**
   ```bash
   # Pastikan queue worker berjalan
   php artisan queue:work
   
   # Cek failed jobs
   php artisan queue:failed
   ```

### Error Permission (Trigger SQL)

Jika menggunakan trigger SQL dan ada error permission:

```sql
-- Grant permission untuk system commands
GRANT PROCESS ON *.* TO 'username'@'localhost';
FLUSH PRIVILEGES;
```

## 🔒 Keamanan

1. **Jangan hardcode token** di kode, gunakan environment variable
2. **Validasi nomor telepon** sebelum mengirim
3. **Rate limiting** untuk mencegah spam
4. **Log semua aktivitas** untuk audit trail

## 🚀 Deployment

### Production Checklist

- [ ] Token Fonnte sudah dikonfigurasi
- [ ] Queue worker berjalan dengan supervisor
- [ ] Log rotation sudah disetup
- [ ] Monitoring notifikasi sudah aktif
- [ ] Backup database notification_logs

### Supervisor Config (untuk Queue Worker)
```ini
[program:gravenue-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/gravenue/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/path/to/gravenue/storage/logs/queue.log
```

## 📞 Support

Jika ada pertanyaan atau masalah:

1. Cek dokumentasi Fonnte: [https://docs.fonnte.com](https://docs.fonnte.com)
2. Cek Laravel Queue documentation
3. Review log files di `storage/logs/`

---

**Dibuat untuk SMKN 4 Malang - Gravenue System** 🏫
