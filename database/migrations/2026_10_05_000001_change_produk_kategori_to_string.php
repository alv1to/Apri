<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produk', function (Blueprint $table) {
            $table->string('kategori', 100)->change();
        });
    }

    public function down(): void
    {
        DB::table('produk')
            ->whereIn('kategori', ['Kopi', 'Premium Kopi', 'Bukan Kopi', 'Ngeteh Kuy', 'Yang Seger-Seger'])
            ->update(['kategori' => 'minuman']);

        DB::table('produk')
            ->where('kategori', 'Makanan Pendamping')
            ->update(['kategori' => 'makanan']);

        Schema::table('produk', function (Blueprint $table) {
            $table->enum('kategori', ['minuman', 'makanan', 'snack', 'lainnya'])->change();
        });
    }
};