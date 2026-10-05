<?php

namespace Database\Seeders;

use App\Models\DetailTransaksi;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TransaksiSeeder extends Seeder
{
    public function run(): void
    {
        $barista = User::where('role', 'barista')->first();
        $produk  = Produk::all()->keyBy('id');

        // Pola bundle yang disengaja agar Apriori bisa menemukan association rules
        $pola = [
            [1, 6],       // Espresso + Croissant
            [1, 9],       // Espresso + Cookies
            [3, 6],       // Cappuccino + Croissant
            [4, 6],       // Latte + Croissant
            [4, 8],       // Latte + Sandwich
            [5, 10],      // Matcha Latte + Brownies
            [2, 9],       // Americano + Cookies
            [1, 6, 9],    // Espresso + Croissant + Cookies
            [3, 7],       // Cappuccino + Roti Bakar
            [4, 7, 10],   // Latte + Roti Bakar + Brownies
            [1],
            [2],
            [3],
            [5, 10],
            [1, 2],
        ];

        $bobot = [8, 6, 7, 7, 5, 9, 6, 10, 5, 6, 4, 3, 3, 8, 4];

        // Buat pool berdasarkan bobot
        $pool = [];
        foreach ($pola as $i => $p) {
            for ($j = 0; $j < $bobot[$i]; $j++) {
                $pool[] = $p;
            }
        }

        for ($t = 0; $t < 120; $t++) {
            $itemIds = $pool[array_rand($pool)];

            // Tambah item acak sesekali
            if (rand(0, 3) === 0) {
                $itemIds[] = rand(1, 10);
                $itemIds   = array_unique($itemIds);
            }

            $tanggal    = Carbon::now()->subDays(rand(0, 89));
            $kode       = 'TRX-' . $tanggal->format('Ymd') . '-' . str_pad($t + 1, 4, '0', STR_PAD_LEFT);
            $totalHarga = 0;
            $details    = [];

            foreach ($itemIds as $pid) {
                if (!isset($produk[$pid])) continue;
                $p        = $produk[$pid];
                $jumlah   = rand(1, 2);
                $subtotal = $p->harga * $jumlah;
                $totalHarga += $subtotal;

                $details[] = [
                    'produk_id'    => $pid,
                    'nama_produk'  => $p->nama,
                    'harga_satuan' => $p->harga,
                    'jumlah'       => $jumlah,
                    'subtotal'     => $subtotal,
                ];
            }

            $bayar     = ceil($totalHarga / 5000) * 5000 + (rand(0, 1) ? 5000 : 0);
            $transaksi = Transaksi::create([
                'kode_transaksi'    => $kode,
                'user_id'           => $barista->id,
                'nama_pelanggan'    => null,
                'total_harga'       => $totalHarga,
                'bayar'             => $bayar,
                'kembalian'         => $bayar - $totalHarga,
                'payment_method'    => 'cash',
                'status'            => 'selesai',
                'order_status'      => 'selesai',
                'tanggal_transaksi' => $tanggal,
            ]);

            foreach ($details as $d) {
                DetailTransaksi::create(array_merge($d, ['transaksi_id' => $transaksi->id]));
            }
        }
    }
}
