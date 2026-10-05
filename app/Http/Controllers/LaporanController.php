<?php

namespace App\Http\Controllers;

use App\Exports\LaporanExport;
use App\Models\DetailTransaksi;
use App\Models\Transaksi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $dari   = $request->filled('dari')   ? $request->dari   : now()->startOfMonth()->toDateString();
        $sampai = $request->filled('sampai') ? $request->sampai : now()->toDateString();

        $ringkasan = Transaksi::whereBetween(DB::raw('DATE(tanggal_transaksi)'), [$dari, $sampai])
            ->where('status', 'selesai')
            ->selectRaw('COUNT(*) as total_transaksi, SUM(total_harga) as total_omzet, AVG(total_harga) as rata_transaksi')
            ->first();

        $produkTerlaris = DetailTransaksi::with('produk')
            ->whereHas('transaksi', fn($q) =>
                $q->whereBetween(DB::raw('DATE(tanggal_transaksi)'), [$dari, $sampai])
                  ->where('status', 'selesai')
            )
            ->select('produk_id',
                DB::raw('SUM(jumlah) as total_terjual'),
                DB::raw('SUM(subtotal) as total_pendapatan')
            )
            ->groupBy('produk_id')
            ->orderByDesc('total_terjual')
            ->limit(10)
            ->get();

        $perKategori = DetailTransaksi::join('produk', 'detail_transaksi.produk_id', '=', 'produk.id')
            ->whereHas('transaksi', fn($q) =>
                $q->whereBetween(DB::raw('DATE(transaksi.tanggal_transaksi)'), [$dari, $sampai])
                  ->where('transaksi.status', 'selesai')
            )
            ->select('produk.kategori',
                DB::raw('SUM(detail_transaksi.jumlah) as total_terjual'),
                DB::raw('SUM(detail_transaksi.subtotal) as total_pendapatan')
            )
            ->groupBy('produk.kategori')
            ->orderByDesc('total_pendapatan')
            ->get();

        $harian = Transaksi::whereBetween(DB::raw('DATE(tanggal_transaksi)'), [$dari, $sampai])
            ->where('status', 'selesai')
            ->select(
                DB::raw('DATE(tanggal_transaksi) as tanggal'),
                DB::raw('COUNT(*) as jumlah_transaksi'),
                DB::raw('SUM(total_harga) as omzet')
            )
            ->groupBy(DB::raw('DATE(tanggal_transaksi)'))
            ->orderBy('tanggal')
            ->get();

        return view('laporan.index', compact(
            'dari', 'sampai', 'ringkasan',
            'produkTerlaris', 'perKategori', 'harian'
        ));
    }

    public function exportExcel(Request $request)
    {
        $dari   = $request->dari   ?? now()->startOfMonth()->toDateString();
        $sampai = $request->sampai ?? now()->toDateString();

        return Excel::download(
            new LaporanExport($dari, $sampai),
            'laporan-penjualan-' . $dari . '-sd-' . $sampai . '.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        $dari   = $request->dari   ?? now()->startOfMonth()->toDateString();
        $sampai = $request->sampai ?? now()->toDateString();

        $produkTerlaris = DetailTransaksi::with('produk')
            ->whereHas('transaksi', fn($q) =>
                $q->whereBetween(DB::raw('DATE(tanggal_transaksi)'), [$dari, $sampai])
                  ->where('status', 'selesai')
            )
            ->select('produk_id',
                DB::raw('SUM(jumlah) as total_terjual'),
                DB::raw('SUM(subtotal) as total_pendapatan')
            )
            ->groupBy('produk_id')
            ->orderByDesc('total_terjual')
            ->limit(10)
            ->get();

        $ringkasan = Transaksi::whereBetween(DB::raw('DATE(tanggal_transaksi)'), [$dari, $sampai])
            ->where('status', 'selesai')
            ->selectRaw('COUNT(*) as total_transaksi, SUM(total_harga) as total_omzet')
            ->first();

        $pdf = Pdf::loadView('laporan.pdf', compact('dari', 'sampai', 'ringkasan', 'produkTerlaris'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download('laporan-' . $dari . '-sd-' . $sampai . '.pdf');
    }
}
