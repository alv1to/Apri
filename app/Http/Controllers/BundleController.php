<?php

namespace App\Http\Controllers;

use App\Models\DetailTransaksi;
use App\Models\HasilBundle;
use App\Models\Produk;
use App\Services\AprioriService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BundleController extends Controller
{
    public function index()
    {
        $bundle = HasilBundle::with('pembuatBundle')
            ->orderByDesc('dianalisa_pada')
            ->orderByDesc('lift')
            ->paginate(15);

        $stats = [
            'total_bundle'   => HasilBundle::count(),
            'bundle_aktif'   => HasilBundle::where('is_aktif', true)->count(),
            'lift_tertinggi' => HasilBundle::max('lift') ?? 0,
            'avg_confidence' => HasilBundle::avg('confidence') ?? 0,
        ];

        return view('bundle.index', compact('bundle', 'stats'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'dari'           => 'required|date',
            'sampai'         => 'required|date|after_or_equal:dari',
            'min_support'    => 'required|numeric|min:1|max:100',
            'min_confidence' => 'required|numeric|min:1|max:100',
        ]);

        $dari          = $request->dari;
        $sampai        = $request->sampai;
        $minSupport    = $request->min_support / 100;
        $minConfidence = $request->min_confidence / 100;

        $rawData = DetailTransaksi::whereHas('transaksi', fn($q) =>
                $q->whereBetween(DB::raw('DATE(tanggal_transaksi)'), [$dari, $sampai])
                  ->where('status', 'selesai')
            )
            ->select('transaksi_id', 'produk_id')
            ->get()
            ->groupBy('transaksi_id')
            ->map(fn($items) =>
                $items->pluck('produk_id')
                      ->map(fn($id) => (string) $id)
                      ->unique()
                      ->values()
                      ->toArray()
            )
            ->values()
            ->toArray();

        $totalTransaksi = count($rawData);

        if ($totalTransaksi < 10) {
            return back()->withErrors([
                'error' => "Data transaksi terlalu sedikit ({$totalTransaksi} transaksi). Minimal 10 transaksi. Perluas rentang tanggal."
            ]);
        }

        $apriori = new AprioriService();
        $rules   = $apriori->run($rawData, $minSupport, $minConfidence);

        if (empty($rules)) {
            return back()->withErrors([
                'error' => 'Tidak ada association rules ditemukan. Coba turunkan minimum support atau confidence.'
            ])->withInput();
        }

        $produkIds = collect($rules)->flatMap(fn($r) =>
            array_merge($r['antecedent'], $r['consequent'])
        )->unique()->values();

        $produkMap = Produk::whereIn('id', $produkIds)->pluck('nama', 'id');

        DB::beginTransaction();
        try {
            HasilBundle::where('rentang_dari', $dari)
                ->where('rentang_sampai', $sampai)
                ->where('is_aktif', false)
                ->delete();

            foreach ($rules as $rule) {
                $namaAnt = collect($rule['antecedent'])
                    ->map(fn($id) => $produkMap[$id] ?? "Produk #{$id}")->toArray();
                $namaCon = collect($rule['consequent'])
                    ->map(fn($id) => $produkMap[$id] ?? "Produk #{$id}")->toArray();

                HasilBundle::create([
                    'antecedent'                => $rule['antecedent'],
                    'consequent'                => $rule['consequent'],
                    'nama_antecedent'           => $namaAnt,
                    'nama_consequent'           => $namaCon,
                    'support'                   => $rule['support'],
                    'confidence'                => $rule['confidence'],
                    'lift'                      => $rule['lift'],
                    'frekuensi'                 => $rule['frekuensi'],
                    'rentang_dari'              => $dari,
                    'rentang_sampai'            => $sampai,
                    'min_support'               => $request->min_support,
                    'min_confidence'            => $request->min_confidence,
                    'total_transaksi_dianalisa' => $totalTransaksi,
                    'dibuat_oleh'               => auth()->id(),
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menyimpan: ' . $e->getMessage()]);
        }

        return redirect()->route('bundle.index')
            ->with('success', count($rules) . " association rules berhasil ditemukan dari {$totalTransaksi} transaksi.");
    }

    public function show(HasilBundle $bundle)
    {
        $produkIds = array_merge($bundle->antecedent, $bundle->consequent);

        $frekuensiProduk = DetailTransaksi::whereIn('produk_id', $produkIds)
            ->whereHas('transaksi', fn($q) =>
                $q->whereBetween(DB::raw('DATE(tanggal_transaksi)'), [$bundle->rentang_dari, $bundle->rentang_sampai])
                  ->where('status', 'selesai')
            )
            ->select('produk_id', DB::raw('COUNT(DISTINCT transaksi_id) as frekuensi'))
            ->groupBy('produk_id')
            ->pluck('frekuensi', 'produk_id');

        // Hitung estimasi harga bundle (menggunakan consequent jika ada, otherwise antecedent)
        $targetIds = !empty($bundle->consequent) ? $bundle->consequent : $bundle->antecedent;
        $produk = Produk::whereIn('id', $targetIds)->get();
        $estimated = 0;
        $estimatedBreakdown = [];
        foreach ($produk as $p) {
            $harga = (float) $p->harga;
            $promoPercent = (!empty($p->promo_active) && !empty($p->promo_percent)) ? (int) $p->promo_percent : 0;
            if ($promoPercent > 0) {
                $hargaEfektif = $harga * (1 - ($promoPercent / 100));
            } else {
                $hargaEfektif = $harga;
            }
            $estimated += $hargaEfektif;

            $estimatedBreakdown[] = [
                'id' => $p->id,
                'nama' => $p->nama,
                'harga' => (float) $p->harga,
                'promo_percent' => $promoPercent,
                'harga_efektif' => round($hargaEfektif, 2),
            ];
        }
        $estimatedHargaBundle = round($estimated, 2);

        return view('bundle.show', compact('bundle', 'frekuensiProduk', 'estimatedHargaBundle', 'estimatedBreakdown'));
    }

    public function aktifkan(Request $request, HasilBundle $bundle)
    {
        $request->validate([
            'harga_bundle' => 'nullable|numeric|min:0',
        ]);

        $willActivate = !$bundle->is_aktif;

        $hargaBundle = $request->harga_bundle;

        // Jika akan diaktifkan dan harga tidak diberikan, hitung harga final yang 'aman'
        if ($willActivate && $hargaBundle === null) {
            $targetIds = !empty($bundle->consequent) ? $bundle->consequent : $bundle->antecedent;

            $produk = Produk::whereIn('id', $targetIds)->get();

            $sumFull = 0.0;   // jumlah harga asli
            $sumPromo = 0.0;  // jumlah harga setelah promo per-item
            $sumCost = 0.0;   // jumlah biaya (jika tersedia)

            foreach ($produk as $p) {
                $harga = (float) $p->harga;
                $promoPct = (!empty($p->promo_active) && !empty($p->promo_percent)) ? (int) $p->promo_percent : 0;
                $hargaPromo = $promoPct > 0 ? $harga * (1 - $promoPct / 100) : $harga;

                $sumFull += $harga;
                $sumPromo += $hargaPromo;

                // gunakan kolom `cost` apabila ada; jika tidak ada, tetap 0
                $cost = 0.0;
                if (isset($p->cost) && is_numeric($p->cost)) {
                    $cost = (float) $p->cost;
                }
                $sumCost += $cost;
            }

            // Parameter kebijakan default (boleh dibuat configurable nanti)
            $bundleDiscountPct = 0.10;         // default diskon dari harga penuh (10%)
            $allowedExtraSavingsPct = 0.10;    // ijinkan extra saving maksimal 10% di atas promo per-item
            $minMarginPct = 0.20;              // margin minimal terhadap cost jika cost tersedia (20%)

            // Kandidat harga berdasar diskon dari harga penuh
            $candidate = $sumFull * (1 - $bundleDiscountPct);

            // Jangan biarkan harga bundle menjadi jauh lebih murah dari membeli item saat promo
            $minAllowedFromPromos = $sumPromo * (1 - $allowedExtraSavingsPct);

            // Pastikan margin terhadap cost jika data cost tersedia
            $minFromCost = 0.0;
            if ($sumCost > 0) {
                $minFromCost = $sumCost * (1 + $minMarginPct);
            }

            // Pilih harga final sebagai nilai maksimum dari kandidat yang aman
            $final = max($candidate, $minAllowedFromPromos, $minFromCost);
            $hargaBundle = round($final, 2);
        }

        $bundle->update([
            'is_aktif'     => $willActivate,
            'harga_bundle' => $hargaBundle ?? $bundle->harga_bundle,
        ]);

        $status = $bundle->is_aktif ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Bundle promosi berhasil {$status}.");
    }

    public function destroy(HasilBundle $bundle)
    {
        if ($bundle->is_aktif) {
            return back()->withErrors(['error' => 'Nonaktifkan bundle terlebih dahulu sebelum menghapus.']);
        }

        $bundle->delete();

        return back()->with('success', 'Bundle berhasil dihapus.');
    }
}
