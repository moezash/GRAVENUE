<?php

return [
    /**
     * Set your Merchant Server Key
     */
    'server_key' => env('MIDTRANS_SERVER_KEY', 'your-server-key-here'),

    /**
     * Set your Client Key
     */
    'client_key' => env('MIDTRANS_CLIENT_KEY', 'your-client-key-here'),

    /**
     * Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
     */
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),

    /**
     * Set sanitization on (default)
     */
    'is_sanitized' => env('MIDTRANS_IS_SANITIZED', true),

    /**
     * Set 3DS transaction for credit card to true
     */
    'is_3ds' => env('MIDTRANS_IS_3DS', true),

    /**
     * Merchant ID
     */
    'merchant_id' => env('MIDTRANS_MERCHANT_ID', 'your-merchant-id-here'),
];
