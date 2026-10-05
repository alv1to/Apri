<?php

namespace App\Http\Controllers;

use App\Models\DetailTransaksi;
use App\Models\HasilBundle;
use App\Models\Produk;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $isAdmin = auth()->user()->isAdmin();

        $hariIni = Transaksi::whereDate('tanggal_transaksi', today())
            ->where('status', 'selesai')
            ->selectRaw('COUNT(*) as total_transaksi, COALESCE(SUM(total_harga),0) as omzet')
            ->first();

        $bulanIni = Transaksi::whereMonth('tanggal_transaksi', now()->month)
            ->whereYear('tanggal_transaksi', now()->year)
            ->where('status', 'selesai')
            ->selectRaw('COUNT(*) as total_transaksi, COALESCE(SUM(total_harga),0) as omzet')
            ->first();

        $produkTerlaris = DetailTransaksi::with('produk')
            ->whereHas('transaksi', fn($q) =>
                $q->whereMonth('tanggal_transaksi', now()->month)
                  ->whereYear('tanggal_transaksi', now()->year)
                  ->where('status', 'selesai')
            )
            ->select('produk_id', DB::raw('SUM(jumlah) as total_terjual'))
            ->groupBy('produk_id')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();

        // Grafik 7 hari terakhir
        $tujuhHari = Transaksi::where('status', 'selesai')
            ->whereBetween('tanggal_transaksi', [now()->subDays(6)->startOfDay(), now()->endOfDay()])
            ->select(
                DB::raw('DATE(tanggal_transaksi) as tanggal'),
                DB::raw('COUNT(*) as jumlah'),
                DB::raw('COALESCE(SUM(total_harga),0) as omzet')
            )
            ->groupBy(DB::raw('DATE(tanggal_transaksi)'))
            ->orderBy('tanggal')
            ->get()
            ->keyBy('tanggal');

        $labels = [];
        $dataTransaksi = [];
        $dataOmzet = [];
        for ($i = 6; $i >= 0; $i--) {
            $tgl             = now()->subDays($i)->toDateString();
            $labels[]        = now()->subDays($i)->format('d M');
            $dataTransaksi[] = $tujuhHari[$tgl]->jumlah ?? 0;
            $dataOmzet[]     = (float) ($tujuhHari[$tgl]->omzet ?? 0);
        }

        $stokMenipis = Produk::aktif()
            ->where('stok', '<', 10)
            ->orderBy('stok')
            ->limit(5)
            ->get();

        $bundleAktif = $isAdmin
            ? HasilBundle::where('is_aktif', true)->orderByDesc('lift')->limit(5)->get()
            : collect();

        $transaksiTerbaru = Transaksi::with(['user', 'details'])
            ->where('status', 'selesai')
            ->orderByDesc('tanggal_transaksi')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'hariIni', 'bulanIni',
            'produkTerlaris',
            'labels', 'dataTransaksi', 'dataOmzet',
            'stokMenipis',
            'bundleAktif',
            'transaksiTerbaru',
            'isAdmin'
        ));
    }
}
