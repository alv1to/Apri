<?php

namespace App\Services\Otp;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class HttpOtpDeliveryChannel implements OtpDeliveryChannel
{
    public function __construct(private readonly array $configuration)
    {
    }

    public function send(string $phoneNumber, string $code): void
    {
        $endpoint = $this->configuration['endpoint'] ?? null;

        if (! $endpoint) {
            throw new RuntimeException('OTP provider endpoint is not configured.');
        }

        $request = Http::timeout($this->configuration['timeout'] ?? 10);

        if (! empty($this->configuration['token'])) {
            $request = $request->withToken($this->configuration['token']);
        }

        $request->post($endpoint, [
            'to' => $phoneNumber,
            'message' => str_replace(':code', $code, config('otp.message')),
        ])->throw();
    }
}