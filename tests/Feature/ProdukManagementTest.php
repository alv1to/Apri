<?php

namespace Tests\Feature;

use App\Enums\KategoriProduk;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProdukManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_products_with_automatic_unique_codes(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $this->post(route('produk.store'), $this->productInput('Spanish Latte'))
            ->assertRedirect(route('produk.index'));
        $this->post(route('produk.store'), $this->productInput('Latte'))
            ->assertRedirect(route('produk.index'));

        $this->assertSame(['PRD001', 'PRD002'], Produk::orderBy('id')->pluck('kode')->all());
    }

    public function test_product_creation_validates_category_and_required_fields(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->post(route('produk.store'), array_merge($this->productInput('Invalid Category'), [
                'kategori' => 'minuman',
                'harga' => '-1',
            ]))
            ->assertSessionHasErrors(['kategori', 'harga']);

        $this->assertDatabaseCount('produk', 0);
    }

    public function test_admin_can_update_availability_and_deactivate_without_deleting_history(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $produk = $this->createProduct(['kode' => 'PRD900']);

        $this->actingAs($admin)
            ->put(route('produk.update', $produk), array_merge($this->productInput('Latte Baru'), ['is_active' => '0']))
            ->assertRedirect(route('produk.index'));

        $this->assertSame('Tidak Tersedia', $produk->fresh()->status_ketersediaan);

        $this->delete(route('produk.destroy', $produk))->assertRedirect(route('produk.index'));
        $this->assertDatabaseHas('produk', ['id' => $produk->id, 'is_active' => false]);

        $this->patch(route('produk.availability', $produk), ['is_active' => '1'])
            ->assertRedirect();
        $this->assertSame('Tersedia', $produk->fresh()->status_ketersediaan);
    }

    public function test_barista_can_view_products_but_cannot_use_admin_actions(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'barista']));

        $this->get(route('produk.index'))
            ->assertOk()
            ->assertDontSee('Tambah Produk')
            ->assertDontSee('Nonaktifkan');

        $this->get(route('produk.create'))->assertForbidden();
    }

    public function test_ajax_search_and_category_status_filters_return_only_matching_products(): void
    {
        $this->createProduct([
            'kode' => 'PRD901',
            'nama' => 'Spanish Latte',
            'kategori' => KategoriProduk::Kopi->value,
            'is_active' => true,
        ]);
        $this->createProduct([
            'kode' => 'PRD902',
            'nama' => 'Lemon Tea',
            'kategori' => KategoriProduk::NgetehKuy->value,
            'is_active' => true,
        ]);
        $this->createProduct([
            'kode' => 'PRD903',
            'nama' => 'Latte Nonaktif',
            'kategori' => KategoriProduk::Kopi->value,
            'is_active' => false,
        ]);

        $response = $this->actingAs(User::factory()->create(['role' => 'barista']))
            ->getJson(route('produk.index', [
                'search' => 'Latte',
                'kategori' => KategoriProduk::Kopi->value,
                'status' => 'tersedia',
            ]));

        $response->assertOk()->assertJsonStructure(['html']);
        $this->assertStringContainsString('Spanish Latte', $response->json('html'));
        $this->assertStringNotContainsString('Lemon Tea', $response->json('html'));
        $this->assertStringNotContainsString('Latte Nonaktif', $response->json('html'));
        $this->assertStringNotContainsString('Aksi', $response->json('html'));
    }

    public function test_unavailable_products_are_hidden_and_rejected_at_pos_checkout(): void
    {
        $barista = User::factory()->create(['role' => 'barista']);
        $produk = $this->createProduct([
            'kode' => 'PRD904',
            'is_active' => false,
            'stok' => 10,
        ]);

        $this->actingAs($barista)
            ->getJson(route('pos.produk'))
            ->assertJsonMissing(['id' => $produk->id]);

        $this->post(route('pos.store'), [
            'items' => [['produk_id' => $produk->id, 'jumlah' => 1]],
            'payment_method' => 'cash',
            'bayar' => 10000,
        ])->assertSessionHasErrors('error');

        $this->assertDatabaseCount('transaksi', 0);
    }

    private function productInput(string $name): array
    {
        return [
            'nama' => $name,
            'kategori' => KategoriProduk::Kopi->value,
            'harga' => 21000,
            'stok' => 10,
            'satuan' => 'pcs',
            'is_active' => '1',
        ];
    }

    private function createProduct(array $overrides = []): Produk
    {
        return Produk::create(array_merge([
            'kode' => 'PRD999',
            'nama' => 'Produk Test',
            'kategori' => KategoriProduk::Kopi->value,
            'harga' => 15000,
            'stok' => 10,
            'satuan' => 'pcs',
            'is_active' => true,
            'promo_percent' => 0,
            'promo_active' => false,
        ], $overrides));
    }
}
