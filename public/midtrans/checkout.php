<?php
/**
 * Midtrans Checkout PHP
 * File ini digunakan untuk membuat Snap Token menggunakan Midtrans Server Key
 * Token ini akan digunakan untuk membuka popup pembayaran Midtrans
 */

// Set header untuk JSON response
header('Content-Type: application/json');

// Konfigurasi Midtrans
$server_key = 'your-server-key-here'; // Server Key Midtrans Sandbox
$is_production = false; // Set ke true jika menggunakan production
$api_url = $is_production ? 'https://app.midtrans.com/snap/v1/transactions' : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

// Ambil data dari POST request
$input = json_decode(file_get_contents('php://input'), true);

// Data transaksi default jika tidak ada input
if (!$input) {
    $input = [
        'order_id' => 'ORDER-' . time(),
        'gross_amount' => 100000,
        'customer_name' => 'John Doe',
        'customer_email' => 'john@example.com',
        'customer_phone' => '081234567890'
    ];
}

// Parameter transaksi yang akan dikirim ke Midtrans
$transaction_details = [
    'order_id' => $input['order_id'],
    'gross_amount' => $input['gross_amount']
];

// Detail item yang dibeli (opsional)
$item_details = [
    [
        'id' => 'item1',
        'price' => $input['gross_amount'],
        'quantity' => 1,
        'name' => 'Pembayaran Fasilitas Gravenue'
    ]
];

// Detail customer
$customer_details = [
    'first_name' => $input['customer_name'],
    'email' => $input['customer_email'],
    'phone' => $input['customer_phone']
];

// Parameter lengkap untuk Midtrans Snap
$params = [
    'transaction_details' => $transaction_details,
    'item_details' => $item_details,
    'customer_details' => $customer_details,
    'enabled_payments' => ['credit_card', 'mandiri_clickpay', 'cimb_clicks', 'bca_klikbca', 'bca_klikpay', 'bri_epay', 'echannel', 'permata_va', 'bca_va', 'bni_va', 'other_va', 'gopay', 'indomaret', 'danamon_online', 'akulaku'],
    'vtweb' => []
];

// Encode parameter ke JSON
$json_params = json_encode($params);

// Buat authorization header dengan Server Key
$auth_string = base64_encode($server_key . ':');

// Setup cURL untuk request ke Midtrans API
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $api_url,
    CURLOPT_HTTPHEADER => [
        'Accept: application/json',
        'Content-Type: application/json',
        'Authorization: Basic ' . $auth_string
    ],
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $json_params,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false, // Untuk development/sandbox
    CURLOPT_TIMEOUT => 30
]);

// Eksekusi request
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

// Handle error cURL
if ($curl_error) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => 'cURL Error: ' . $curl_error
    ]);
    exit;
}

// Handle response dari Midtrans
if ($http_code == 201) {
    // Sukses - return snap token
    $result = json_decode($response, true);
    echo json_encode([
        'error' => false,
        'snap_token' => $result['token'],
        'redirect_url' => $result['redirect_url']
    ]);
} else {
    // Error dari Midtrans API
    http_response_code($http_code);
    echo json_encode([
        'error' => true,
        'message' => 'Midtrans API Error',
        'response' => json_decode($response, true)
    ]);
}
?>
