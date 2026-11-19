<?php

return [
    'qris' => [
        'static_image' => env('QRIS_STATIC_IMAGE'),
    ],
    'midtrans' => [
        'server_key' => env('MIDTRANS_SERVER_KEY'),
        'production' => env('MIDTRANS_PRODUCTION', false),
        'base_url' => env('MIDTRANS_PRODUCTION', false)
            ? 'https://app.midtrans.com'
            : 'https://app.sandbox.midtrans.com',
    ],
];
