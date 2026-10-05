<?php

namespace Tests\Feature;

use App\Enums\KategoriProduk;
use App\Exports\LaporanExport;
use App\Models\DetailTransaksi;
use App\Models\HasilBundle;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PhaseWorkflowAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_to_barista_pos_orders_status_history_and_report_flow(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $barista = User::factory()->create([
            'role' => 'barista',
            'no_telepon' => '081234567899',
        ]);

        $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticatedAs($admin);

        $this->post(route('produk.store'), [
            'nama' => 'Audit Spanish Latte',
            'kategori' => KategoriProduk::Kopi->value,
            'harga' => 24000,
            'stok' => 20,
            'satuan' => 'pcs',
            'is_active' => '1',
        ])->assertRedirect(route('produk.index'));
        $produk = Produk::where('nama', 'Audit Spanish Latte')->firstOrFail();

        $this->post(route('logout'))->assertRedirect('/');
        config([
            'otp.driver' => 'sms',
            'otp.drivers.sms.endpoint' => 'https://otp.audit.test/send',
            'otp.drivers.sms.token' => 'audit-token',
        ]);
        Http::fake(['https://otp.audit.test/*' => Http::response([], 200)]);

        $this->post(route('barista.otp.send'), ['no_telepon' => $barista->no_telepon])
            ->assertRedirect(route('login', ['mode' => 'barista']));

        $otpCode = null;
        Http::assertSent(function (ClientRequest $request) use (&$otpCode) {
            preg_match('/([0-9]{6})/', $request['message'], $matches);
            $otpCode = $matches[1] ?? null;

            return $request->url() === 'https://otp.audit.test/send';
        });
        $this->assertNotNull($otpCode);

        $this->post(route('barista.otp.verify'), [
            'no_telepon' => $barista->no_telepon,
            'otp' => $otpCode,
        ])->assertRedirect(route('pos.index', absolute: false));
        $this->assertAuthenticatedAs($barista);

        $this->get(route('pos.index'))->assertOk()->assertSee('Audit Spanish Latte');
        $this->post(route('pos.store'), [
            'nama_pelanggan' => 'Pelanggan Alur Lengkap',
            'items' => [['produk_id' => $produk->id, 'jumlah' => 1]],
            'payment_method' => 'qris',
        ])->assertRedirect();

        $transaksi = Transaksi::where('nama_pelanggan', 'Pelanggan Alur Lengkap')->firstOrFail();
        $this->assertSame('dibayar', $transaksi->order_status);
        $this->get(route('pos.orders'))->assertOk()->assertSee('Pelanggan Alur Lengkap');

        foreach (['sedang_dibuat', 'siap_diambil', 'selesai'] as $nextStatus) {
            $this->post(route('pos.orders.status', $transaksi), ['order_status' => $nextStatus])
                ->assertRedirect(route('pos.orders'))
                ->assertSessionHas('success');
            $this->assertSame($nextStatus, $transaksi->fresh()->order_status);
        }

        $this->get(route('transaksi.index'))
            ->assertOk()
            ->assertSee($transaksi->kode_transaksi)
            ->assertDontSee('Pelanggan Alur Lengkap');

        $today = today()->toDateString();
        $this->get(route('laporan.index', ['dari' => $today, 'sampai' => $today]))
            ->assertOk()
            ->assertViewHas('ringkasan', fn ($summary) => (int) $summary->total_transaksi === 1
                && (float) $summary->total_omzet === 24000.0);
    }

    public function test_reports_and_apriori_include_active_order_stages_but_exclude_cancel(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $barista = User::factory()->create(['role' => 'barista']);
        $coffee = $this->createProduct('Audit Coffee A');
        $pastry = $this->createProduct('Audit Coffee B');
        $stages = ['dibayar', 'sedang_dibuat', 'siap_diambil', 'selesai'];

        for ($index = 1; $index <= 10; $index++) {
            $order = $this->createTransaction(
                $barista,
                'AUDIT' . str_pad((string) $index, 3, '0', STR_PAD_LEFT),
                $stages[($index - 1) % count($stages)],
                'selesai'
            );
            $this->addDetail($order, $coffee);
            $this->addDetail($order, $pastry);
        }

        $cancelled = $this->createTransaction($barista, 'AUDITCANCEL', 'cancel', 'cancel');
        $this->addDetail($cancelled, $coffee);
        $this->addDetail($cancelled, $pastry);

        $today = today()->toDateString();
        $this->actingAs($admin)
            ->get(route('laporan.index', ['dari' => $today, 'sampai' => $today]))
            ->assertOk()
            ->assertViewHas('ringkasan', fn ($summary) => (int) $summary->total_transaksi === 10
                && (float) $summary->total_omzet === 300000.0);

        $this->assertCount(20, (new LaporanExport($today, $today))->collection());

        $this->post(route('bundle.generate'), [
            'dari' => $today,
            'sampai' => $today,
            'min_support' => 10,
            'min_confidence' => 50,
        ])->assertRedirect(route('bundle.index'))
            ->assertSessionHas('success', fn ($message) => str_contains($message, 'dari 10 transaksi'));

        $this->assertSame(2, HasilBundle::count());
        $this->assertSame([10], HasilBundle::query()->distinct()->pluck('total_transaksi_dianalisa')->all());
    }

    private function createProduct(string $name): Produk
    {
        return Produk::create([
            'nama' => $name,
            'kategori' => KategoriProduk::Kopi->value,
            'harga' => 15000,
            'stok' => 100,
            'satuan' => 'pcs',
            'is_active' => true,
            'promo_percent' => 0,
            'promo_active' => false,
        ]);
    }

    private function createTransaction(User $user, string $code, string $orderStatus, string $status): Transaksi
    {
        return Transaksi::create([
            'kode_transaksi' => $code,
            'user_id' => $user->id,
            'total_harga' => 30000,
            'bayar' => 30000,
            'kembalian' => 0,
            'payment_method' => 'cash',
            'status' => $status,
            'order_status' => $orderStatus,
            'tanggal_transaksi' => now(),
        ]);
    }

    private function addDetail(Transaksi $transaksi, Produk $produk): void
    {
        DetailTransaksi::create([
            'transaksi_id' => $transaksi->id,
            'produk_id' => $produk->id,
            'nama_produk' => $produk->nama,
            'harga_satuan' => 15000,
            'jumlah' => 1,
            'subtotal' => 15000,
            'diskon' => 0,
        ]);
    }
}
