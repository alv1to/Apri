<?php

namespace App\Providers;

use App\Services\Otp\HttpOtpDeliveryChannel;
use App\Services\Otp\LogOtpDeliveryChannel;
use App\Services\Otp\OtpDeliveryChannel;
use InvalidArgumentException;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(OtpDeliveryChannel::class, function () {
            return match (config('otp.driver')) {
                'log' => new LogOtpDeliveryChannel(),
                'whatsapp', 'sms' => new HttpOtpDeliveryChannel(
                    config('otp.drivers.' . config('otp.driver'), [])
                ),
                default => throw new InvalidArgumentException('Unsupported OTP delivery driver.'),
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
