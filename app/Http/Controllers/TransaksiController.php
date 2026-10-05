<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Produk;
use App\Models\DetailTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaksi::with(['user', 'details'])
                          ->orderByDesc('tanggal_transaksi');

        if ($request->filled('search')) {
            $query->where('kode_transaksi', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_transaksi', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_transaksi', '<=', $request->tanggal_sampai);
        }

        $transaksi = $query->paginate(15)->withQueryString();

        return view('transaksi.index', compact('transaksi'));
    }

    public function show(Transaksi $transaksi)
    {
        $transaksi->load(['user', 'details.produk']);
        return view('transaksi.show', compact('transaksi'));
    }

    public function create()
    {
        $produk = Produk::aktif()->orderBy('kategori')->orderBy('nama')->get();
        return view('transaksi.create', compact('produk'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items'             => 'required|array|min:1',
            'items.*.produk_id' => 'required|exists:produk,id',
            'items.*.jumlah'    => 'required|integer|min:1',
            'bayar'             => 'required_if:payment_method,cash|numeric|min:0',
            'payment_method'    => ['required', Rule::in(Transaksi::PAYMENT_METHODS)],
        ]);

        DB::beginTransaction();
        try {
            $totalHarga = 0;
            $details    = [];

            foreach ($request->items as $item) {
                $produk = Produk::lockForUpdate()->findOrFail($item['produk_id']);

                if (! $produk->is_active) {
                    throw new \Exception("Produk {$produk->nama} tidak tersedia.");
                }

                if ($produk->stok < $item['jumlah']) {
                    throw new \Exception("Stok {$produk->nama} tidak mencukupi. Tersisa {$produk->stok}.");
                }

                $rawSubtotal = $produk->harga * $item['jumlah'];
                $diskon = 0;
                if (!empty($produk->promo_active) && !empty($produk->promo_percent)) {
                    $diskonPerUnit = ($produk->harga * ($produk->promo_percent / 100));
                    $diskon = round($diskonPerUnit * $item['jumlah'], 2);
                }

                $subtotal = $rawSubtotal - $diskon;
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

            return redirect()->route('transaksi.show', $transaksi)
                ->with('success', 'Transaksi berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }
}
