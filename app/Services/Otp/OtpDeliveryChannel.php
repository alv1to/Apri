<?php

namespace App\Services\Otp;

interface OtpDeliveryChannel
{
    public function send(string $phoneNumber, string $code): void;
}