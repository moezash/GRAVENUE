# Midtrans Sandbox Integration - Gravenue

Folder ini berisi implementasi integrasi Midtrans Sandbox untuk sistem pembayaran Gravenue.

## File yang Disertakan

### 1. `checkout.php`
File PHP untuk generate Snap Token menggunakan Midtrans Server Key. File ini berfungsi sebagai:
- API endpoint untuk membuat transaksi baru
- Generator Snap Token untuk popup pembayaran
- Handler komunikasi dengan Midtrans API

### 2. `index.html`
File HTML dengan interface pembayaran yang berisi:
- Form input data customer dan jumlah pembayaran
- Tombol "Bayar Sekarang" yang terintegrasi dengan Midtrans Snap
- Callback handlers untuk success, pending, error, dan close events
- Styling Bootstrap untuk tampilan yang responsif

## Konfigurasi API Keys

### Sandbox Credentials (perlu dikonfigurasi):
- **Merchant ID**: your-merchant-id-here
- **Client Key**: your-client-key-here
- **Server Key**: your-server-key-here

## Cara Menjalankan

1. Pastikan XAMPP Apache sudah running
2. Buka browser dan akses: `http://localhost/Gravenue/midtrans/index.html`
3. Isi form dengan data testing
4. Klik tombol "Bayar Sekarang"
5. Popup Midtrans akan muncul untuk proses pembayaran

## Data Testing Sandbox

### Kartu Kredit Testing:
- **Nomor Kartu**: 4811 1111 1111 1114
- **Expired Date**: 01/25
- **CVV**: 123

### E-Wallet Testing:
- GoPay, DANA, OVO tersedia untuk testing
- Gunakan nomor HP testing: 081234567890

## Struktur Request/Response

### Request ke checkout.php:
```json
{
    "order_id": "ORDER-1632123456789",
    "gross_amount": 100000,
    "customer_name": "John Doe",
    "customer_email": "john@example.com",
    "customer_phone": "081234567890"
}
```

### Response dari checkout.php:
```json
{
    "error": false,
    "snap_token": "aa895c9b-c0cb-4a83-9134-965e1bc0b795",
    "redirect_url": "https://app.sandbox.midtrans.com/snap/v2/vtweb/aa895c9b-c0cb-4a83-9134-965e1bc0b795"
}
```

## Catatan Penting

1. **Environment**: Saat ini dikonfigurasi untuk Sandbox. Untuk production, ubah:
   - `$is_production = true` di `checkout.php`
   - Ganti URL Snap.js ke production
   - Gunakan production API keys

2. **Security**: Untuk production, pastikan:
   - Validasi input yang lebih ketat
   - Implementasi CSRF protection
   - SSL/HTTPS untuk semua komunikasi
   - Server Key tidak boleh expose ke frontend

3. **Integration**: File ini bisa diintegrasikan dengan:
   - Laravel payment controller
   - Database untuk menyimpan transaction log
   - Notification handler untuk callback dari Midtrans

## Troubleshooting

1. **cURL Error**: Pastikan PHP cURL extension enabled
2. **CORS Error**: Pastikan domain sudah didaftarkan di Midtrans Dashboard
3. **Token Invalid**: Periksa Server Key dan pastikan format request sesuai
4. **Popup tidak muncul**: Periksa Client Key dan pastikan Snap.js terload dengan benar
