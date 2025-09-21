# 📱 PANDUAN SISTEM NOTIFIKASI WHATSAPP GRAVENUE

## 🎯 Status Sistem: **BERFUNGSI DENGAN BAIK** ✅

Sistem notifikasi WhatsApp Gravenue sudah berfungsi dengan baik. Jika notifikasi tidak terkirim, kemungkinan besar karena **queue worker tidak berjalan**.

---

## 🚀 CARA MENJALANKAN NOTIFIKASI OTOMATIS

### 1. **Jalankan Queue Worker (WAJIB)**
```bash
# Masuk ke directory project
cd /Applications/XAMPP/xamppfiles/htdocs/Gravenue

# Jalankan queue worker (pilih salah satu)
./start_queue_worker.sh
# atau
php artisan queue:work --verbose
```

### 2. **Cek Status Sistem**
```bash
php check_queue_status.php
```

---

## 🔍 TROUBLESHOOTING

### ❌ **Masalah: Notifikasi tidak terkirim**

**Penyebab paling umum:** Queue worker tidak berjalan

**Solusi:**
1. Cek pending jobs: `php check_queue_status.php`
2. Jika ada pending jobs, jalankan: `php artisan queue:work --once`
3. Untuk otomatis: `./start_queue_worker.sh`

### ❌ **Masalah: Notifikasi gagal (failed)**

**Kemungkinan penyebab:**
- Token Fonnte expired
- Nomor HP tidak valid
- API Fonnte down

**Solusi:**
1. Test token: `php test_fonnte_api.php`
2. Cek nomor HP di database (harus format Indonesia: 08xxx)
3. Retry failed jobs: `php artisan queue:retry all`

---

## 📊 MONITORING SISTEM

### **File Monitoring:**
- **Log Laravel:** `storage/logs/laravel.log`
- **Status Check:** `php check_queue_status.php`
- **Test API:** `php test_fonnte_api.php`

### **Database Tables:**
- **notification_logs:** Riwayat semua notifikasi
- **jobs:** Antrian notifikasi yang belum diproses
- **failed_jobs:** Notifikasi yang gagal

---

## ⚙️ KONFIGURASI SISTEM

### **Environment Variables (.env):**
```
FONNTE_TOKEN=5v58dAsXwc4PgNi8zKHf
QUEUE_CONNECTION=database
```

### **Komponen Sistem:**
1. **BookingObserver** - Mendeteksi perubahan status booking
2. **SendWhatsAppNotification Job** - Mengirim notifikasi via queue
3. **WhatsAppService** - Service untuk komunikasi dengan API Fonnte
4. **notification_logs table** - Menyimpan riwayat notifikasi

---

## 🎯 CARA KERJA SISTEM

1. **Admin mengubah status booking** (pending → approved/rejected)
2. **BookingObserver mendeteksi perubahan** dan dispatch job
3. **Job masuk ke queue** (table: jobs)
4. **Queue worker memproses job** dan kirim via WhatsApp
5. **Log tersimpan** di notification_logs

---

## 📱 FORMAT PESAN NOTIFIKASI

### **Booking Disetujui:**
```
🎉 BOOKING DISETUJUI 🎉

Halo [Nama User],

Pengajuan penyewaan fasilitas Anda telah DISETUJUI!

📋 Detail Booking:
• Fasilitas: [Nama Fasilitas]
• Acara: [Nama Acara]
• Tanggal: [DD/MM/YYYY]
• Waktu: [HH:MM] - [HH:MM]
• Peserta: [Jumlah] orang
• Total Biaya: Rp [Jumlah]

💳 Silakan lakukan pembayaran sesuai dengan total biaya di atas.

📞 Jika ada pertanyaan, hubungi admin SMKN 4 Malang.

Terima kasih! 🙏
SMKN 4 Malang
```

### **Booking Ditolak:**
```
❌ BOOKING DITOLAK ❌

Halo [Nama User],

Mohon maaf, pengajuan penyewaan fasilitas Anda DITOLAK.

📋 Detail Booking:
• Fasilitas: [Nama Fasilitas]
• Acara: [Nama Acara]
• Tanggal: [DD/MM/YYYY]
• Waktu: [HH:MM] - [HH:MM]

📝 Silakan ajukan kembali dengan waktu atau tanggal yang berbeda.

📞 Untuk informasi lebih lanjut, hubungi admin SMKN 4 Malang.

Terima kasih atas pengertiannya. 🙏
SMKN 4 Malang
```

---

## 🛠️ MAINTENANCE RUTIN

### **Harian:**
- Cek pending jobs: `php check_queue_status.php`
- Monitor log: `tail -f storage/logs/laravel.log`

### **Mingguan:**
- Clear old logs: `php artisan log:clear`
- Restart queue worker untuk refresh memory

### **Bulanan:**
- Cek quota Fonnte
- Backup notification_logs table
- Update token jika diperlukan

---

## 🚨 EMERGENCY COMMANDS

```bash
# Cek status cepat
php check_queue_status.php

# Proses semua pending jobs
php artisan queue:work --stop-when-empty

# Retry semua failed jobs
php artisan queue:retry all

# Clear semua jobs (HATI-HATI!)
php artisan queue:clear

# Test API Fonnte
php test_fonnte_api.php
```

---

## 📞 KONTAK SUPPORT

Jika masih ada masalah:
1. Cek file log: `storage/logs/laravel.log`
2. Jalankan diagnostic: `php check_queue_status.php`
3. Test manual: `php test_fonnte_api.php`

**Sistem ini dibuat untuk SMKN 4 Malang dengan ❤️**
