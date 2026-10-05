<?php

namespace App\Http\Controllers;

use App\Models\DetailTransaksi;
use App\Models\HasilBundle;
use App\Models\Produk;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    /**
     * Halaman kasir POS — landing page barista setelah login.
     * Menampilkan grid produk aktif + sidebar bundle promo aktif.
     */
    public function index()
    {
        $produk = Produk::aktif()
            ->orderBy('kategori')
            ->orderBy('nama')
            ->get()
            ->groupBy('kategori');

        // Bundle promo aktif — ditampilkan sebagai saran ke barista
        $bundleAktif = HasilBundle::where('is_aktif', true)
            ->orderByDesc('lift')
            ->get();

        return view('pos.index', compact('produk', 'bundleAktif'));
    }

    /**
     * API JSON — produk aktif untuk pencarian real-time di POS.
     */
    public function getProduk(Request $request)
    {
        $produk = Produk::aktif()
            ->when($request->filled('q'), fn($query) =>
                $query->where('nama', 'like', '%' . $request->q . '%')
                      ->orWhere('kode', 'like', '%' . $request->q . '%')
            )
            ->select('id', 'nama', 'kode', 'harga', 'stok', 'kategori', 'satuan', 'gambar')
            ->select('id', 'nama', 'kode', 'harga', 'stok', 'kategori', 'satuan', 'gambar', 'promo_percent', 'promo_active')
            ->orderBy('nama')
            ->get();

        return response()->json($produk);
    }

    public function orders(Request $request)
    {
        $query = Transaksi::with(['user', 'details.produk'])
            ->orderByDesc('tanggal_transaksi');

        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('kode_transaksi', 'like', '%' . $request->search . '%');
        }

        $transaksi = $query->paginate(15)->withQueryString();

        $statusCounts = Transaksi::selectRaw('order_status, COUNT(*) as total')
            ->groupBy('order_status')
            ->pluck('total', 'order_status')
            ->toArray();

        return view('pos.orders', compact('transaksi', 'statusCounts'));
    }

    public function updateOrderStatus(Request $request, Transaksi $transaksi)
    {
        $validated = $request->validate([
            'order_status' => 'required|in:pending,dibayar,selesai,batal',
        ]);

        $transaksi->update([
            'order_status' => $validated['order_status'],
            'status'       => $validated['order_status'] === 'batal' ? 'batal' : 'selesai',
        ]);

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function showOrder(Transaksi $transaksi)
    {
        $transaksi->load(['user', 'details.produk']);
        return view('pos.order-show', compact('transaksi'));
    }

    /**
     * Proses transaksi dari POS.
     * Sama dengan TransaksiController@store tapi redirect kembali ke POS.
     */
    public function store(Request $request)
    {
        $request->validate([
            'items'             => 'required|array|min:1',
            'items.*.produk_id' => 'required|exists:produk,id',
            'items.*.jumlah'    => 'required|integer|min:1',
            'bayar'             => 'required_if:payment_method,cash|numeric|min:0',
            'payment_method'    => 'required|in:cash,qris,debit',
            'bundle_id'         => 'nullable|exists:hasil_bundle,id',
            'bundle_price'      => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $totalHarga = 0;
            $details    = [];

            foreach ($request->items as $item) {
                $produk = Produk::lockForUpdate()->findOrFail($item['produk_id']);

                if ($produk->stok < $item['jumlah']) {
                    throw new \Exception("Stok {$produk->nama} tidak mencukupi. Tersisa {$produk->stok}.");
                }

                $rawSubtotal = $produk->harga * $item['jumlah'];

                // Hitung diskon jika ada promo aktif
                $diskon = 0;
                if (!empty($produk->promo_active) && !empty($produk->promo_percent)) {
                    $diskonPerUnit = ($produk->harga * ($produk->promo_percent / 100));
                    $diskon = round($diskonPerUnit * $item['jumlah'], 2);
                }

                $subtotal    = $rawSubtotal - $diskon;
                $totalHarga += $subtotal;

                $details[] = [
                    'produk_id'    => $produk->id,
                    'nama_produk'  => $produk->nama,
                    'harga_satuan' => $produk->harga,
                    'jumlah'       => $item['jumlah'],
                    'subtotal'     => $subtotal,
                    'diskon'       => $diskon,
                ];

                $produk->decrement('stok', $item['jumlah']);
            }

            // Jika ada bundle yang diterapkan, sesuaikan total dengan harga bundle
            if ($request->filled('bundle_id')) {
                $bundle = HasilBundle::find($request->bundle_id);
                $desiredPrice = $request->filled('bundle_price') ? (float) $request->bundle_price : ($bundle->harga_bundle ?? null);

                if ($desiredPrice !== null) {
                    $difference = round($totalHarga - $desiredPrice, 2);

                    if ($difference > 0 && $totalHarga > 0) {
                            // Distribusikan pengurangan (extra diskon) proporsional berdasarkan subtotal,
                            // tetapi batasi ekstra potongan per produk maksimal 5% dari (harga_satuan * jumlah).
                            $remaining = $difference;

                            // Siapkan kapasitas maksimum per item
                            $capacities = [];
                            $subtotalsForCapacity = 0.0;
                            foreach ($details as $i => $d) {
                                $maxExtra = round($d['harga_satuan'] * $d['jumlah'] * 0.05, 2); // 5% cap
                                $capacities[$i] = $maxExtra;
                                // gunakan subtotal saat ini sebagai basis proporsi
                                $subtotalsForCapacity += $d['subtotal'];
                            }

                            // Iteratif: alokasikan proporsional tetapi cap setiap item
                            $loopGuard = 0;
                            while ($remaining > 0.001 && array_sum($capacities) > 0 && $loopGuard < 10) {
                                $loopGuard++;
                                $distributed = 0.0;

                                // hitung total subtotal hanya untuk item yang masih memiliki kapasitas
                                $activeSubtotal = 0.0;
                                foreach ($details as $i => $d) {
                                    if ($capacities[$i] > 0) $activeSubtotal += $d['subtotal'];
                                }

                                if ($activeSubtotal <= 0) break;

                                foreach ($details as $i => $d) {
                                    if ($capacities[$i] <= 0) continue;

                                    $proportion = $d['subtotal'] / $activeSubtotal;
                                    $want = round($difference * $proportion, 2);
                                    $alloc = min($capacities[$i], $want, $remaining);

                                    if ($alloc > 0) {
                                        $details[$i]['diskon'] = round($details[$i]['diskon'] + $alloc, 2);
                                        $details[$i]['subtotal'] = round($details[$i]['subtotal'] - $alloc, 2);
                                        $capacities[$i] = round($capacities[$i] - $alloc, 2);
                                        $distributed += $alloc;
                                        $remaining = round($remaining - $alloc, 2);
                                    }
                                }

                                // Jika tidak ada yang didistribusikan karena pembulatan, coba alokasikan sisa ke item pertama yang masih punya kapasitas
                                if ($distributed <= 0) {
                                    foreach ($details as $i => $d) {
                                        if ($capacities[$i] > 0 && $remaining > 0) {
                                            $alloc = min($capacities[$i], $remaining);
                                            $details[$i]['diskon'] = round($details[$i]['diskon'] + $alloc, 2);
                                            $details[$i]['subtotal'] = round($details[$i]['subtotal'] - $alloc, 2);
                                            $capacities[$i] = round($capacities[$i] - $alloc, 2);
                                            $remaining = round($remaining - $alloc, 2);
                                            break;
                                        }
                                    }
                                }
                            }

                            // Rehit totalHarga setelah alokasi (jika remaining > 0 berarti batas 5% mencegah mencapai desiredPrice)
                            $totalHarga = 0;
                            foreach ($details as $d) {
                                $totalHarga += $d['subtotal'];
                            }
                        }
                }
            }

            // Untuk metode non-tunai (qris/debit), anggap bayar = totalHarga
            $bayar = $request->input('bayar');
            if ($request->payment_method !== 'cash') {
                $bayar = $totalHarga;
            }

            if ($bayar < $totalHarga) {
                throw new \Exception('Jumlah bayar kurang dari total harga.');
            }

            $transaksi = Transaksi::create([
                'kode_transaksi'    => Transaksi::generateKode(),
                'user_id'           => auth()->id(),
                'total_harga'       => $totalHarga,
                'bayar'             => $bayar,
                'kembalian'         => $bayar - $totalHarga,
                'payment_method'    => $request->payment_method,
                'status'            => 'selesai',
                'order_status'      => 'dibayar',
                'catatan'           => $request->catatan,
                'tanggal_transaksi' => now(),
            ]);

            foreach ($details as $d) {
                DetailTransaksi::create(array_merge($d, ['transaksi_id' => $transaksi->id]));
            }

            DB::commit();

            return redirect()->route('pos.struk', $transaksi)
                ->with('success', 'Transaksi berhasil.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Halaman struk setelah transaksi POS berhasil.
     */
    public function struk(Transaksi $transaksi)
    {
        $transaksi->load(['user', 'details']);
        return view('pos.struk', compact('transaksi'));
    }
}
