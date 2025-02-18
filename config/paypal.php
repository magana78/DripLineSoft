<?php
/**
 * PayPal Setting & API Credentials
 * Created by Raza Mehdi <srmk@outlook.com>.
 */

 return [
    'mode' => env('PAYPAL_MODE', 'sandbox'),
    'api_url' => env('PAYPAL_API_URL', 'https://api-m.sandbox.paypal.com'),
    'sandbox' => [
        'client_id' => env('PAYPAL_CLIENT_ID', 'AUbPWY96LNcJW662sREzgkRXE15C_-CynMnQywQyr7qgQzfC6RWzyiyZNyPisBVCAQY85kZyNzx-3euu'),
        'client_secret' => env('PAYPAL_CLIENT_SECRET', 'EABsz4mIsE2ftuyQ-xhRISS01-p1SyMsHoBvt6lXzUYivlvMPig82kPS5Ia3QFbuT9itbaiNOCLgdD7t'),
    ]
];




