<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\BundleController;

/*
|--------------------------------------------------------------------------
| Redirect root ke login
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Auth routes (login, logout) — generate otomatis oleh Breeze
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| Routes yang butuh login
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    /*
    |----------------------------------------------------------------------
    | Dashboard — admin & barista bisa akses, tapi barista tidak redirect
    | ke sini setelah login (mereka langsung ke /pos)
    |----------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    |----------------------------------------------------------------------
    | POS (Point of Sale) — halaman kasir, landing page barista setelah login
    | Admin juga bisa akses untuk monitoring
    |----------------------------------------------------------------------
    */
    Route::get('/pos',         [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/transaksi', [PosController::class, 'store'])->name('pos.store');
    Route::get('/pos/produk',  [PosController::class, 'getProduk'])->name('pos.produk');
    Route::get('/pos/orders',  [PosController::class, 'orders'])->name('pos.orders');
    Route::get('/pos/orders/{transaksi}', [PosController::class, 'showOrder'])->name('pos.orders.show');
    Route::post('/pos/orders/{transaksi}/status', [PosController::class, 'updateOrderStatus'])->name('pos.orders.status');
    Route::get('/pos/struk/{transaksi}', [PosController::class, 'struk'])->name('pos.struk');

    /*
    |----------------------------------------------------------------------
    | Produk — lihat: semua; CRUD: admin only
    |----------------------------------------------------------------------
    */
    Route::get('/produk',              [ProdukController::class, 'index'])->name('produk.index');
    Route::get('/produk/{produk}',     [ProdukController::class, 'show'])->name('produk.show');

    Route::middleware(['role:admin'])->group(function () {
        Route::get('/produk/create',          [ProdukController::class, 'create'])->name('produk.create');
        Route::post('/produk',                [ProdukController::class, 'store'])->name('produk.store');
        Route::get('/produk/{produk}/edit',   [ProdukController::class, 'edit'])->name('produk.edit');
        Route::put('/produk/{produk}',        [ProdukController::class, 'update'])->name('produk.update');
        Route::delete('/produk/{produk}',     [ProdukController::class, 'destroy'])->name('produk.destroy');
    });

    /*
    |----------------------------------------------------------------------
    | Transaksi — riwayat: semua; detail: semua
    |----------------------------------------------------------------------
    */
    Route::get('/transaksi',           [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::get('/transaksi/{transaksi}', [TransaksiController::class, 'show'])->name('transaksi.show');
    Route::get('/transaksi/create',    [TransaksiController::class, 'create'])->name('transaksi.create');
    Route::post('/transaksi',          [TransaksiController::class, 'store'])->name('transaksi.store');

    /*
    |----------------------------------------------------------------------
    | Laporan — lihat: semua; export: admin only
    |----------------------------------------------------------------------
    */
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/laporan/export/pdf',   [LaporanController::class, 'exportPdf'])->name('laporan.export.pdf');
        Route::get('/laporan/export/excel', [LaporanController::class, 'exportExcel'])->name('laporan.export.excel');
    });
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');

    /*
    |----------------------------------------------------------------------
    | Bundle rekomendasi — admin only
    |----------------------------------------------------------------------
    */
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/bundle',                    [BundleController::class, 'index'])->name('bundle.index');
        Route::post('/bundle/generate',          [BundleController::class, 'generate'])->name('bundle.generate');
        Route::get('/bundle/{bundle}',           [BundleController::class, 'show'])->name('bundle.show');
        Route::post('/bundle/{bundle}/aktifkan', [BundleController::class, 'aktifkan'])->name('bundle.aktifkan');
        Route::delete('/bundle/{bundle}',        [BundleController::class, 'destroy'])->name('bundle.destroy');
    });
});
