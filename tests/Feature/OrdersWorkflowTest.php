<?php

namespace Tests\Feature;

use App\Enums\KategoriProduk;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrdersWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private const STATUSES = [
        'pending',
        'dibayar',
        'sedang_dibuat',
        'siap_diambil',
        'selesai',
        'cancel',
    ];

    public function test_orders_are_grouped_in_requested_status_order_with_labels(): void
    {
        $barista = User::factory()->create(['role' => 'barista']);
        foreach (self::STATUSES as $index => $status) {
            $this->createOrder($barista, $status, 'ORD' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT));
        }

        $response = $this->actingAs($barista)->get(route('pos.orders'));

        $response->assertOk()->assertSeeInOrder([
            'Pending',
            'Sudah Dibayar',
            'Sedang Dibuat',
            'Siap Diambil',
            'Selesai',
            'Cancel',
        ]);
    }

    public function test_each_status_preview_is_capped_at_five_and_view_all_is_paginated_by_five(): void
    {
        $barista = User::factory()->create(['role' => 'barista']);
        for ($index = 1; $index <= 6; $index++) {
            $this->createOrder($barista, 'pending', 'PEND' . $index);
        }

        $preview = $this->actingAs($barista)->get(route('pos.orders'));
        $preview->assertOk()->assertSee('View All');
        $preview->assertSee('PEND1')->assertSee('PEND5')->assertDontSee('PEND6');

        $viewAll = $this->get(route('pos.orders', ['status' => 'pending', 'view' => 'all']));
        $viewAll->assertOk()->assertSee('PEND1')->assertSee('PEND5')->assertDontSee('PEND6');
        $viewAll->assertSee('page=2', false);
    }

    public function test_orders_live_filter_searches_customer_name_and_status(): void
    {
        $barista = User::factory()->create(['role' => 'barista']);
        $this->createOrder($barista, 'dibayar', 'MATCH001', 'Rina Pelanggan');
        $this->createOrder($barista, 'pending', 'MATCH002', 'Budi Pelanggan');

        $response = $this->actingAs($barista)->getJson(route('pos.orders', [
            'search' => 'Rina',
            'status' => 'dibayar',
        ]));

        $response->assertOk()->assertJsonStructure(['html']);
        $this->assertStringContainsString('Rina Pelanggan', $response->json('html'));
        $this->assertStringNotContainsString('Budi Pelanggan', $response->json('html'));
        $this->assertStringNotContainsString('MATCH002', $response->json('html'));
    }

    public function test_pos_saves_customer_name_but_transaction_history_does_not_display_it(): void
    {
        $barista = User::factory()->create(['role' => 'barista']);
        $produk = Produk::create([
            'kode' => 'PRD700',
            'nama' => 'Latte Test',
            'kategori' => KategoriProduk::Kopi->value,
            'harga' => 21000,
            'stok' => 5,
            'satuan' => 'pcs',
            'is_active' => true,
            'promo_percent' => 0,
            'promo_active' => false,
        ]);

        $this->actingAs($barista)->post(route('pos.store'), [
            'nama_pelanggan' => 'Pelanggan Rahasia',
            'items' => [['produk_id' => $produk->id, 'jumlah' => 1]],
            'payment_method' => 'qris',
        ])->assertRedirect();

        $transaksi = Transaksi::where('nama_pelanggan', 'Pelanggan Rahasia')->firstOrFail();
        $this->assertSame('Pelanggan Rahasia', $transaksi->nama_pelanggan);

        $history = $this->get(route('transaksi.index'));
        $history->assertOk()->assertDontSee('Pelanggan Rahasia');

        $orders = $this->get(route('pos.orders'));
        $orders->assertOk()->assertSee('Pelanggan Rahasia');
    }

    public function test_barista_can_only_swipe_forward_one_status_step(): void
    {
        $barista = User::factory()->create(['role' => 'barista']);
        $order = $this->createOrder($barista, 'dibayar', 'STEP001');
        $this->actingAs($barista);

        $this->post(route('pos.orders.status', $order), ['order_status' => 'siap_diambil'])
            ->assertSessionHasErrors('order_status');
        $this->assertSame('dibayar', $order->fresh()->order_status);

        $this->post(route('pos.orders.status', $order), ['order_status' => 'sedang_dibuat'])
            ->assertRedirect(route('pos.orders'))
            ->assertSessionHas('success');
        $this->assertSame('sedang_dibuat', $order->fresh()->order_status);

        $this->post(route('pos.orders.status', $order), ['order_status' => 'dibayar'])
            ->assertSessionHasErrors('order_status');
        $this->assertSame('sedang_dibuat', $order->fresh()->order_status);

        $this->post(route('pos.orders.status', $order), ['order_status' => 'siap_diambil'])
            ->assertRedirect(route('pos.orders'));
        $this->post(route('pos.orders.status', $order), ['order_status' => 'selesai'])
            ->assertRedirect(route('pos.orders'));

        $this->get(route('pos.orders.show', $order))
            ->assertOk()
            ->assertDontSee('data-order-status-range');
    }

    public function test_only_barista_can_advance_or_cancel_orders(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $order = $this->createOrder($admin, 'dibayar', 'ROLE001');

        $this->actingAs($admin)
            ->post(route('pos.orders.status', $order), ['order_status' => 'sedang_dibuat'])
            ->assertForbidden();

        $this->post(route('pos.orders.cancel', $order))->assertForbidden();
        $this->assertSame('dibayar', $order->fresh()->order_status);
    }

    public function test_barista_can_cancel_from_detail_and_returns_to_orders(): void
    {
        $barista = User::factory()->create(['role' => 'barista']);
        $order = $this->createOrder($barista, 'siap_diambil', 'CANCEL001');

        $this->actingAs($barista)
            ->get(route('pos.orders.show', $order))
            ->assertOk()
            ->assertSee('aria-label="Cancel pesanan"', false)
            ->assertSee('confirm(', false);

        $this->post(route('pos.orders.cancel', $order))
            ->assertRedirect(route('pos.orders'))
            ->assertSessionHas('success');

        $this->assertSame('cancel', $order->fresh()->order_status);
        $this->assertSame('cancel', $order->fresh()->status);
    }

    public function test_debit_is_removed_for_new_sales_but_historical_debit_transactions_still_render(): void
    {
        $barista = User::factory()->create(['role' => 'barista']);
        $produk = Produk::create([
            'kode' => 'PRD701',
            'nama' => 'Latte Debit Test',
            'kategori' => KategoriProduk::Kopi->value,
            'harga' => 21000,
            'stok' => 5,
            'satuan' => 'pcs',
            'is_active' => true,
            'promo_percent' => 0,
            'promo_active' => false,
        ]);

        $this->actingAs($barista)
            ->get(route('pos.index'))
            ->assertOk()
            ->assertDontSee('Kartu Debit');

        $saleInput = [
            'items' => [['produk_id' => $produk->id, 'jumlah' => 1]],
            'payment_method' => 'debit',
            'bayar' => 21000,
        ];

        $this->post(route('pos.store'), $saleInput)->assertSessionHasErrors('payment_method');
        $this->post(route('transaksi.store'), $saleInput)->assertSessionHasErrors('payment_method');
        $this->assertDatabaseCount('transaksi', 0);
        $this->assertSame(5, $produk->fresh()->stok);

        $legacy = $this->createOrder($barista, 'selesai', 'LEGACY001');
        $legacy->update(['payment_method' => 'debit']);

        $this->get(route('pos.struk', $legacy))
            ->assertOk()
            ->assertSee('KOPI KAMA')
            ->assertSee('Kartu Debit');

        $this->get(route('pos.orders.show', $legacy))
            ->assertOk()
            ->assertSee('Kartu Debit');

        $this->assertSame('debit', $legacy->fresh()->payment_method);
    }

    public function test_legacy_payment_method_labels_keep_their_readable_format(): void
    {
        $this->assertSame('QRIS', (new Transaksi(['payment_method' => 'QRIS']))->payment_method_label);
        $this->assertSame('Tunai', (new Transaksi(['payment_method' => 'Tunai']))->payment_method_label);
        $this->assertSame('Kartu Debit', (new Transaksi(['payment_method' => 'debit']))->payment_method_label);
    }

    private function createOrder(User $barista, string $status, string $code, ?string $customerName = null): Transaksi
    {
        return Transaksi::create([
            'kode_transaksi' => $code,
            'user_id' => $barista->id,
            'nama_pelanggan' => $customerName,
            'total_harga' => 25000,
            'bayar' => 25000,
            'kembalian' => 0,
            'payment_method' => 'cash',
            'status' => $status === 'cancel' ? 'cancel' : 'selesai',
            'order_status' => $status,
            'tanggal_transaksi' => now(),
        ]);
    }
}
