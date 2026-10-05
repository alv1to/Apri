<?php

namespace Database\Seeders;

use App\Models\Produk;
use Illuminate\Database\Seeder;

class ProdukSeeder extends Seeder
{
    public function run(): void
    {
        $produk = [
            ['nama' => 'Spanish Creamy Latte', 'kode' => 'KOP001', 'kategori' => 'Kopi', 'harga' => 24000],
            ['nama' => 'Black Coffee', 'kode' => 'KOP002', 'kategori' => 'Kopi', 'harga' => 16000],
            ['nama' => 'Spanish Latte', 'kode' => 'KOP003', 'kategori' => 'Kopi', 'harga' => 22000],
            ['nama' => 'Macchiato', 'kode' => 'KOP004', 'kategori' => 'Kopi', 'harga' => 22000],
            ['nama' => 'Latte', 'kode' => 'KOP005', 'kategori' => 'Kopi', 'harga' => 21000],
            ['nama' => 'Kop-sus Jelly', 'kode' => 'KOP006', 'kategori' => 'Kopi', 'harga' => 21000],
            ['nama' => 'Kop-sus Aren', 'kode' => 'KOP007', 'kategori' => 'Kopi', 'harga' => 22000],
            ['nama' => 'Kop-sus Macchiato', 'kode' => 'KOP008', 'kategori' => 'Kopi', 'harga' => 24000],
            ['nama' => 'Caramel Latte', 'kode' => 'PKP001', 'kategori' => 'Premium Kopi', 'harga' => 24000],
            ['nama' => 'Vanilla Latte', 'kode' => 'PKP002', 'kategori' => 'Premium Kopi', 'harga' => 24000],
            ['nama' => 'Hazelnut Latte', 'kode' => 'PKP003', 'kategori' => 'Premium Kopi', 'harga' => 24000],
            ['nama' => 'Salted Caramel Latte', 'kode' => 'PKP004', 'kategori' => 'Premium Kopi', 'harga' => 24000],
            ['nama' => 'Pandan Latte', 'kode' => 'PKP005', 'kategori' => 'Premium Kopi', 'harga' => 24000],
            ['nama' => 'Butterscotch Latte', 'kode' => 'PKP006', 'kategori' => 'Premium Kopi', 'harga' => 24000],
            ['nama' => 'Ice Matcha', 'kode' => 'NKO001', 'kategori' => 'Bukan Kopi', 'harga' => 20000],
            ['nama' => 'Chocolate', 'kode' => 'NKO002', 'kategori' => 'Bukan Kopi', 'harga' => 17000],
            ['nama' => 'Vanilla', 'kode' => 'NKO003', 'kategori' => 'Bukan Kopi', 'harga' => 17000],
            ['nama' => 'Matcha Macchiato', 'kode' => 'NKO004', 'kategori' => 'Bukan Kopi', 'harga' => 22000],
            ['nama' => 'Red Velvet', 'kode' => 'NKO005', 'kategori' => 'Bukan Kopi', 'harga' => 17000],
            ['nama' => 'Choco Cookies', 'kode' => 'NKO006', 'kategori' => 'Bukan Kopi', 'harga' => 22000],
            ['nama' => 'Ovaltine', 'kode' => 'NKO007', 'kategori' => 'Bukan Kopi', 'harga' => 17000],
            ['nama' => 'Cokelat Oreo', 'kode' => 'NKO008', 'kategori' => 'Bukan Kopi', 'harga' => 22000],
            ['nama' => 'Taro Krim Keju', 'kode' => 'NKO009', 'kategori' => 'Bukan Kopi', 'harga' => 22000],
            ['nama' => 'Jelly Brown Sugar', 'kode' => 'NKO010', 'kategori' => 'Bukan Kopi', 'harga' => 20000],
            ['nama' => 'Cokelat Macchiato', 'kode' => 'NKO011', 'kategori' => 'Bukan Kopi', 'harga' => 22000],
            ['nama' => 'Choco Hazelnut', 'kode' => 'NKO012', 'kategori' => 'Bukan Kopi', 'harga' => 17000],
            ['nama' => 'Thai Tea Original', 'kode' => 'TEH001', 'kategori' => 'Ngeteh Kuy', 'harga' => 16000],
            ['nama' => 'Lemon Tea', 'kode' => 'TEH002', 'kategori' => 'Ngeteh Kuy', 'harga' => 12000],
            ['nama' => 'Lychee Jelly Squash', 'kode' => 'SEG001', 'kategori' => 'Yang Seger-Seger', 'harga' => 16000],
            ['nama' => 'Manggo Jelly Squash', 'kode' => 'SEG002', 'kategori' => 'Yang Seger-Seger', 'harga' => 16000],
            ['nama' => 'Brownies', 'kode' => 'MPD001', 'kategori' => 'Makanan Pendamping', 'harga' => 13000],
            ['nama' => 'Bolu Marmer', 'kode' => 'MPD002', 'kategori' => 'Makanan Pendamping', 'harga' => 13000],
            ['nama' => 'Banana Cake', 'kode' => 'MPD003', 'kategori' => 'Makanan Pendamping', 'harga' => 12000],
            ['nama' => 'Bolu Lemon', 'kode' => 'MPD004', 'kategori' => 'Makanan Pendamping', 'harga' => 12000],
        ];

        foreach ($produk as $item) {
            $existing = Produk::where('nama', $item['nama'])->first();

            if ($existing) {
                $existing->update([
                    'kategori' => $item['kategori'],
                    'harga' => $item['harga'],
                    'is_active' => true,
                ]);

                continue;
            }

            Produk::create(array_merge($item, [
                'stok' => 0,
                'satuan' => 'pcs',
                'is_active' => true,
            ]));
        }

        Produk::whereNotIn('nama', array_column($produk, 'nama'))
            ->update(['is_active' => false]);
    }
}
