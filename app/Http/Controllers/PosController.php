<?php

namespace App\Http\Controllers;

use App\Models\DetailTransaksi;
use App\Models\HasilBundle;
use App\Models\Produk;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PosController extends Controller
{
    private const ORDER_STATUS_LABELS = [
        'pending' => 'Pending',
        'dibayar' => 'Sudah Dibayar',
        'sedang_dibuat' => 'Sedang Dibuat',
        'siap_diambil' => 'Siap Diambil',
        'selesai' => 'Selesai',
        'cancel' => 'Cancel',
    ];

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
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(Transaksi::ORDER_STATUSES)],
            'view' => ['nullable', Rule::in(['all'])],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);
        $statusCounts = Transaksi::selectRaw('order_status, COUNT(*) as total')
            ->groupBy('order_status')
            ->pluck('total', 'order_status')
            ->toArray();

        $visibleStatuses = ! empty($filters['status']) ? [$filters['status']] : Transaksi::ORDER_STATUSES;
        $viewAll = ! empty($filters['status']) && ($filters['view'] ?? null) === 'all';
        $ordersByStatus = [];

        foreach ($visibleStatuses as $status) {
            $query = Transaksi::with(['user', 'details.produk'])
                ->where('order_status', $status)
                ->when(! empty($filters['search']), function ($query) use ($filters) {
                    $search = $filters['search'];
                    $query->where(function ($query) use ($search) {
                        $query->where('kode_transaksi', 'like', '%' . $search . '%')
                            ->orWhere('nama_pelanggan', 'like', '%' . $search . '%')
                            ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', '%' . $search . '%'));
                    });
                })
                ->orderByDesc('tanggal_transaksi');

            $total = (clone $query)->count();
            $rows = $viewAll
                ? $query->paginate(5)->withQueryString()
                : $query->limit(5)->get();

            $ordersByStatus[$status] = [
                'rows' => $rows,
                'total' => $total,
            ];
        }

        $statusLabels = self::ORDER_STATUS_LABELS;

        if ($request->expectsJson()) {
            return response()->json([
                'html' => view('pos._orders', compact('ordersByStatus', 'statusLabels', 'viewAll'))->render(),
            ]);
        }

        return view('pos.orders', compact('ordersByStatus', 'statusCounts', 'statusLabels', 'viewAll'));
    }

    public function updateOrderStatus(Request $request, Transaksi $transaksi)
    {
        $validated = $request->validate([
            'order_status' => ['required', Rule::in(['sedang_dibuat', 'siap_diambil', 'selesai'])],
        ]);

        $nextStatus = DB::transaction(function () use ($transaksi, $validated) {
            $order = Transaksi::lockForUpdate()->findOrFail($transaksi->id);
            $allowedNextStatus = [
                'dibayar' => 'sedang_dibuat',
                'sedang_dibuat' => 'siap_diambil',
                'siap_diambil' => 'selesai',
            ][$order->order_status] ?? null;

            if ($allowedNextStatus !== $validated['order_status']) {
                throw ValidationException::withMessages([
                    'order_status' => 'Status hanya dapat dimajukan satu tahap.',
                ]);
            }

            $order->update(['order_status' => $allowedNextStatus]);

            return $allowedNextStatus;
        });

        return redirect()->route('pos.orders')
            ->with('success', 'Status pesanan diperbarui: ' . self::ORDER_STATUS_LABELS[$nextStatus] . '.');
    }

    public function cancelOrder(Transaksi $transaksi)
    {
        DB::transaction(function () use ($transaksi) {
            $order = Transaksi::lockForUpdate()->findOrFail($transaksi->id);

            if ($order->order_status === 'cancel') {
                throw ValidationException::withMessages([
                    'order_status' => 'Pesanan yang sudah di-cancel tidak dapat dibatalkan lagi.',
                ]);
            }

            $order->update([
                'order_status' => 'cancel',
                'status' => 'cancel',
            ]);
        });

        return redirect()->route('pos.orders')->with('success', 'Pesanan berhasil di-cancel.');
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
        $validated = $request->validate([
            'nama_pelanggan'   => 'nullable|string|max:100',
            'items'             => 'required|array|min:1',
            'items.*.produk_id' => 'required|exists:produk,id',
            'items.*.jumlah'    => 'required|integer|min:1',
            'bayar'             => 'required_if:payment_method,cash|numeric|min:0',
            'payment_method'    => ['required', Rule::in(Transaksi::PAYMENT_METHODS)],
            'bundle_id'         => 'nullable|exists:hasil_bundle,id',
            'bundle_price'      => 'nullable|numeric|min:0',
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

                                // Jika tidak ada yang didistribusikan karena pembulatan, coba alokasikan sisa ke item pertama yang masih punya kapasitas.
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

            // Untuk metode non-tunai QRIS, anggap bayar = totalHarga.
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
                'nama_pelanggan'    => $validated['nama_pelanggan'] ?? null,
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
