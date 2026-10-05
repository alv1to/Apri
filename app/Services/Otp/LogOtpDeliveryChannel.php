<?php

namespace App\Services\Otp;

use Illuminate\Support\Facades\Log;

class LogOtpDeliveryChannel implements OtpDeliveryChannel
{
    public function send(string $phoneNumber, string $code): void
    {
        Log::info('Barista login OTP issued.', [
            'no_telepon' => $phoneNumber,
            'otp' => $code,
        ]);
    }
}