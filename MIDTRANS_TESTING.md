# Midtrans Integration Testing Guide

## Status: ✅ FIXED - Database Error Resolved

### Problem Fixed:
- **Error**: `SQLSTATE[HY000]: General error: 1 no such column: notes`
- **Solution**: Added missing `notes` column to `payments` table
- **Migration**: `2025_09_19_053219_add_notes_to_payments_table.php`

### Current Database Structure:
```
payments table:
- id (INTEGER)
- booking_id (INTEGER)
- payment_method (varchar)
- payment_status (varchar)
- payment_amount (numeric)
- payment_date (datetime)
- transaction_id (varchar)
- payment_proof (varchar)
- qr_code_data (TEXT)
- barcode_image (varchar)
- created_at (datetime)
- updated_at (datetime)
- notes (TEXT) ✅ ADDED
```

## Testing Steps:

### 1. Create a Booking
1. Go to: `http://0.0.0.0:8001/booking/2`
2. Fill the form with required fields
3. Submit booking request

### 2. Approve Booking (Admin)
1. Go to: `http://0.0.0.0:8001/admin/login`
2. Login as admin
3. Go to Bookings section
4. Approve the booking

### 3. Test Payment
1. Go to: `http://0.0.0.0:8001/payment/{booking_id}`
2. Click "Bayar Sekarang"
3. Midtrans popup should appear
4. Use test credentials:

### Test Payment Credentials:
- **Credit Card**: 4811 1111 1111 1114 (01/25, CVV: 123)
- **E-Wallet**: 081234567890
- **Bank Transfer**: Use any test account

### Expected Results:
- ✅ Payment popup opens
- ✅ Transaction ID generated
- ✅ Payment status updates
- ✅ Booking status changes to "completed" on success

## API Endpoints:
- **Payment Page**: `GET /payment/{id}`
- **Payment Process**: `POST /payment/{id}`
- **Notification Webhook**: `POST /payment/notification`

## Configuration:
- **Environment**: Sandbox
- **Server Key**: your-server-key-here
- **Client Key**: your-client-key-here
- **Merchant ID**: your-merchant-id-here

## Troubleshooting:
1. **Database Error**: ✅ FIXED - notes column added
2. **Import Error**: ✅ FIXED - Exception class imported
3. **Cache Issues**: Run `php artisan config:clear && php artisan cache:clear`

## Status: 🟢 READY FOR TESTING
