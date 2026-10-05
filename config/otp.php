<?php

return [
    'driver' => env('OTP_DRIVER', 'log'),
    'length' => 6,
    'expires_minutes' => 5,
    'max_attempts' => 5,
    'resend_cooldown_seconds' => 60,
    'max_sends_per_hour' => 5,
    'max_verifications_per_hour' => 20,
    'message' => 'Kode OTP Kopi Kama: :code. Berlaku selama 5 menit.',
    'drivers' => [
        'whatsapp' => [
            'endpoint' => env('OTP_WHATSAPP_ENDPOINT'),
            'token' => env('OTP_WHATSAPP_TOKEN'),
            'timeout' => 10,
        ],
        'sms' => [
            'endpoint' => env('OTP_SMS_ENDPOINT'),
            'token' => env('OTP_SMS_TOKEN'),
            'timeout' => 10,
        ],
    ],
];