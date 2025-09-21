# Midtrans Integration Testing - Gravenue

## Konfigurasi yang Telah Disetup

### Environment Variables (.env)
```
MIDTRANS_SERVER_KEY=your_server_key_here
MIDTRANS_CLIENT_KEY=your_client_key_here
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=true
MIDTRANS_MERCHANT_ID=your_merchant_id_here
```

**Note**: Ganti `your_server_key_here`, `your_client_key_here`, dan `your_merchant_id_here` dengan kredensial Midtrans yang sebenarnya dari dashboard Midtrans Anda.

### ✅ MASALAH TERATASI
**Status**: Midtrans integration berhasil! Snap token berhasil di-generate dengan kredensial yang benar.

### Cara Mendapatkan Kredensial Midtrans yang Benar:

1. **Daftar/Login ke Midtrans Dashboard**
   - Sandbox: https://dashboard.sandbox.midtrans.com/
   - Production: https://dashboard.midtrans.com/

2. **Ambil Kredensial dari Dashboard**
   - Masuk ke menu **Settings** → **Access Keys**
   - Copy **Server Key** (format: `SB-Mid-server-xxxxxxxxxx`)
   - Copy **Client Key** (format: `SB-Mid-client-xxxxxxxxxx`)

3. **Update file .env dengan kredensial yang benar**
   ```
   MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxxxx
   MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxxxx
   ```

### Status Konfigurasi
- ✅ Package `midtrans/midtrans-php` terinstall
- ✅ Konfigurasi Midtrans di `config/midtrans.php` 
- ✅ Environment variables sudah diset
- ✅ Laravel config cache sudah di-refresh
- ✅ File `checkout.php` sudah diupdate untuk menggunakan Laravel config

## Testing Steps

### 1. Test Konfigurasi
```bash
php artisan tinker --execute="echo config('midtrans.server_key');"
```
**Expected Result:** `SB-Mid-server-G189666331`

### 2. Test Midtrans Package
```bash
php artisan tinker --execute="\\Midtrans\\Config::\$serverKey = config('midtrans.server_key'); echo 'Midtrans Config OK';"
```

### 3. Test Payment Flow
1. Buat booking baru melalui website
2. Admin approve booking
3. User akses halaman payment
4. Snap token harus ter-generate
5. Payment popup harus muncul

## Troubleshooting

### Jika Midtrans masih tidak berfungsi:

1. **Periksa Server Key Format**
   - Pastikan format: `SB-Mid-server-xxxxxxxx` untuk sandbox
   - Atau `Mid-server-xxxxxxxx` untuk production

2. **Periksa Client Key**
   - Format: `SB-Mid-client-xxxxxxxx` untuk sandbox

3. **Periksa Network/Firewall**
   - Pastikan server bisa akses ke `api.sandbox.midtrans.com`

4. **Periksa Error Logs**
   ```bash
   tail -f storage/logs/laravel.log
   ```

## Test Credentials (Sandbox)

### Test Credit Card
- **Card Number:** 4811 1111 1111 1114
- **CVV:** 123
- **Exp Date:** 01/25

### Test Bank Transfer
- Gunakan virtual account yang di-generate
- Simulasi pembayaran melalui Midtrans simulator

## Next Steps
1. Test payment flow end-to-end
2. Implement webhook notification handler
3. Add proper error handling
4. Setup production credentials when ready