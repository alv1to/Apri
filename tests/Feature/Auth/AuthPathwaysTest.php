<?php

namespace Tests\Feature\Auth;

use App\Models\Otp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthPathwaysTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_login_with_password_but_barista_cannot_use_that_path(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $barista = User::factory()->create(['role' => 'barista']);

        $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticatedAs($admin);
        $this->post('/logout');

        $this->post('/login', [
            'email' => $barista->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_phone_otp_response_does_not_reveal_whether_an_account_exists(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'no_telepon' => '081234567890',
        ]);

        $registeredResponse = $this->post('/login/barista/otp', ['no_telepon' => $admin->no_telepon]);
        $unknownResponse = $this->post('/login/barista/otp', ['no_telepon' => '081234567894']);

        $registeredResponse->assertRedirect(route('login', ['mode' => 'barista']))
            ->assertSessionHas('status', 'Jika nomor terdaftar, kode OTP akan dikirim.');
        $unknownResponse->assertRedirect(route('login', ['mode' => 'barista']))
            ->assertSessionHas('status', 'Jika nomor terdaftar, kode OTP akan dikirim.');

        $this->assertDatabaseCount('otp', 0);
    }

    public function test_barista_cannot_access_admin_dashboard_directly(): void
    {
        $barista = User::factory()->create(['role' => 'barista']);

        $this->actingAs($barista)
            ->get(route('dashboard'))
            ->assertForbidden();
    }

    public function test_barista_can_login_with_a_delivered_otp(): void
    {
        config([
            'otp.driver' => 'sms',
            'otp.drivers.sms.endpoint' => 'https://otp.test/messages',
            'otp.drivers.sms.token' => 'test-token',
        ]);
        Http::fake(['https://otp.test/*' => Http::response([], 200)]);

        $barista = User::factory()->create([
            'role' => 'barista',
            'no_telepon' => '081234567891',
        ]);

        $this->post('/login/barista/otp', ['no_telepon' => $barista->no_telepon])
            ->assertRedirect(route('login', ['mode' => 'barista']));

        $otp = Otp::where('no_telepon', $barista->no_telepon)->firstOrFail();
        $this->assertTrue($otp->expired_at->isFuture());
        $this->assertSame(0, $otp->attempts);
        $this->assertNotSame('000000', $otp->kode_hash);

        $code = null;
        Http::assertSent(function (ClientRequest $request) use (&$code) {
            preg_match('/([0-9]{6})/', $request['message'], $matches);
            $code = $matches[1] ?? null;

            return $request->url() === 'https://otp.test/messages'
                && $request->hasHeader('Authorization', 'Bearer test-token');
        });

        $this->assertNotNull($code);
        $this->assertTrue(Hash::check($code, $otp->kode_hash));

        $this->post('/login/barista/verify', [
            'no_telepon' => $barista->no_telepon,
            'otp' => $code,
        ])->assertRedirect(route('pos.index', absolute: false));

        $this->assertAuthenticatedAs($barista);
        $this->assertNotNull($otp->fresh()->used_at);
        $this->assertSame(1, $otp->fresh()->attempts);
    }

    public function test_barista_otp_has_resend_cooldown_and_attempt_limit(): void
    {
        $barista = User::factory()->create([
            'role' => 'barista',
            'no_telepon' => '081234567892',
        ]);

        $this->post('/login/barista/otp', ['no_telepon' => $barista->no_telepon])
            ->assertRedirect();

        $this->post('/login/barista/otp', ['no_telepon' => $barista->no_telepon])
            ->assertSessionHasErrors('no_telepon');

        $otp = Otp::where('no_telepon', $barista->no_telepon)->firstOrFail();
        $this->assertDatabaseCount('otp', 1);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post('/login/barista/verify', [
                'no_telepon' => $barista->no_telepon,
                'otp' => '999999',
            ])->assertSessionHasErrors('otp');
        }

        $this->assertSame(5, $otp->fresh()->attempts);
        $this->assertNotNull($otp->fresh()->used_at);
        $this->assertGuest();
    }

    public function test_expired_barista_otp_is_rejected(): void
    {
        $barista = User::factory()->create([
            'role' => 'barista',
            'no_telepon' => '081234567893',
        ]);

        Otp::create([
            'no_telepon' => $barista->no_telepon,
            'kode_hash' => '123456',
            'expired_at' => now()->subMinute(),
            'attempts' => 0,
        ]);

        $this->post('/login/barista/verify', [
            'no_telepon' => $barista->no_telepon,
            'otp' => '123456',
        ])->assertSessionHasErrors('otp');

        $this->assertGuest();
    }

    public function test_password_reset_is_available_to_admin_only_and_uses_generic_response(): void
    {
        Notification::fake();
        $admin = User::factory()->create(['role' => 'admin']);
        $barista = User::factory()->create(['role' => 'barista']);

        $adminResponse = $this->from('/forgot-password')->post('/forgot-password', [
            'email' => $admin->email,
        ]);
        $adminResponse->assertSessionHas('status', 'Jika akun Admin dengan email tersebut terdaftar, tautan reset akan dikirim.');
        Notification::assertSentTo($admin, \Illuminate\Auth\Notifications\ResetPassword::class);

        Notification::assertNotSentTo($barista, \Illuminate\Auth\Notifications\ResetPassword::class);

        $unknownResponse = $this->from('/forgot-password')->post('/forgot-password', [
            'email' => 'unknown@example.test',
        ]);
        $unknownResponse->assertSessionHas('status', 'Jika akun Admin dengan email tersebut terdaftar, tautan reset akan dikirim.');
    }
}