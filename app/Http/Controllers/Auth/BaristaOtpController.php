<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\User;
use App\Services\Otp\OtpDeliveryChannel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class BaristaOtpController extends Controller
{
    public function send(Request $request, OtpDeliveryChannel $delivery): RedirectResponse
    {
        $validated = $request->validate([
            'no_telepon' => ['required', 'string', 'max:30', 'regex:/^\\+?[0-9]{8,15}$/'],
        ]);

        $phoneNumber = $validated['no_telepon'];
        $sendKey = $this->key('send', $phoneNumber, $request);
        $cooldown = (int) config('otp.resend_cooldown_seconds', 60);

        if (RateLimiter::tooManyAttempts($sendKey . ':cooldown', 1)) {
            throw ValidationException::withMessages([
                'no_telepon' => 'Tunggu ' . RateLimiter::availableIn($sendKey . ':cooldown') . ' detik sebelum meminta OTP lagi.',
            ]);
        }

        if (RateLimiter::tooManyAttempts($sendKey . ':hour', (int) config('otp.max_sends_per_hour', 5))) {
            throw ValidationException::withMessages([
                'no_telepon' => 'Batas pengiriman OTP tercapai. Coba lagi nanti.',
            ]);
        }

        RateLimiter::hit($sendKey . ':cooldown', $cooldown);
        RateLimiter::hit($sendKey . ':hour', 3600);

        $user = User::where('no_telepon', $phoneNumber)
            ->where('role', 'barista')
            ->where('is_active', true)
            ->first();

        if (! $user) {
            $request->session()->put('barista_otp_phone', $phoneNumber);

            return redirect()->route('login', ['mode' => 'barista'])
                ->with('status', 'Jika nomor terdaftar, kode OTP akan dikirim.');
        }

        Otp::where('no_telepon', $phoneNumber)
            ->whereNull('used_at')
            ->update(['used_at' => now()]);

        $code = str_pad((string) random_int(0, (10 ** (int) config('otp.length', 6)) - 1), (int) config('otp.length', 6), '0', STR_PAD_LEFT);

        $otp = Otp::create([
            'no_telepon' => $phoneNumber,
            'kode_hash' => $code,
            'expired_at' => now()->addMinutes((int) config('otp.expires_minutes', 5)),
            'attempts' => 0,
        ]);

        try {
            $delivery->send($phoneNumber, $code);
        } catch (\Throwable $exception) {
            $otp->update(['used_at' => now()]);
            report($exception);

            throw ValidationException::withMessages([
                'no_telepon' => 'OTP belum dapat dikirim. Coba lagi nanti.',
            ]);
        }

        $request->session()->put('barista_otp_phone', $phoneNumber);

        return redirect()->route('login', ['mode' => 'barista'])
            ->with('status', 'Jika nomor terdaftar, kode OTP akan dikirim.');
    }

    public function verify(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'no_telepon' => ['required', 'string', 'max:30', 'regex:/^\\+?[0-9]{8,15}$/'],
            'otp' => ['required', 'digits:' . (int) config('otp.length', 6)],
        ]);

        $verifyKey = $this->key('verify', $validated['no_telepon'], $request);
        $maxVerifications = (int) config('otp.max_verifications_per_hour', 20);

        if (RateLimiter::tooManyAttempts($verifyKey, $maxVerifications)) {
            throw ValidationException::withMessages([
                'otp' => 'Terlalu banyak percobaan. Coba lagi nanti.',
            ]);
        }

        RateLimiter::hit($verifyKey, 3600);

        $result = DB::transaction(function () use ($validated) {
            $otp = Otp::where('no_telepon', $validated['no_telepon'])
                ->whereNull('used_at')
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            if (! $otp || $otp->expired_at->isPast()) {
                return ['error' => 'Kode OTP tidak valid atau sudah kedaluwarsa.'];
            }

            if ($otp->attempts >= (int) config('otp.max_attempts', 5)) {
                $otp->update(['used_at' => now()]);

                return ['error' => 'Batas percobaan tercapai. Minta OTP baru.'];
            }

            $otp->increment('attempts');
            $otp->refresh();

            if (! Hash::check($validated['otp'], $otp->kode_hash)) {
                if ($otp->attempts >= (int) config('otp.max_attempts', 5)) {
                    $otp->update(['used_at' => now()]);
                }

                return ['error' => $otp->used_at
                    ? 'Batas percobaan tercapai. Minta OTP baru.'
                    : 'Kode OTP tidak valid atau sudah kedaluwarsa.'];
            }

            $user = User::where('no_telepon', $validated['no_telepon'])
                ->where('role', 'barista')
                ->where('is_active', true)
                ->first();

            if (! $user) {
                $otp->update(['used_at' => now()]);

                return ['error' => 'Kode OTP tidak valid atau sudah kedaluwarsa.'];
            }

            $otp->update(['used_at' => now()]);

            return ['user' => $user];
        });

        if (isset($result['error'])) {
            throw ValidationException::withMessages(['otp' => $result['error']]);
        }

        Auth::login($result['user']);
        $request->session()->forget('barista_otp_phone');
        $request->session()->regenerate();
        RateLimiter::clear($verifyKey);

        return redirect()->intended(route('pos.index'));
    }

    private function key(string $action, string $phoneNumber, Request $request): string
    {
        return 'barista-otp:' . $action . ':' . hash('sha256', $phoneNumber . '|' . $request->ip());
    }
}