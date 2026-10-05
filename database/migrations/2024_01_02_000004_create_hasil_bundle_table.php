<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hasil_bundle', function (Blueprint $table) {
            $table->id();
            $table->json('antecedent');
            $table->json('consequent');
            $table->json('nama_antecedent');
            $table->json('nama_consequent');
            $table->decimal('support',    8, 4);
            $table->decimal('confidence', 8, 4);
            $table->decimal('lift',       8, 4);
            $table->integer('frekuensi')->default(0);
            $table->string('rentang_dari');
            $table->string('rentang_sampai');
            $table->decimal('min_support',    5, 2);
            $table->decimal('min_confidence', 5, 2);
            $table->boolean('is_aktif')->default(false);
            $table->decimal('harga_bundle', 12, 2)->nullable();
            $table->integer('total_transaksi_dianalisa')->default(0);
            $table->foreignId('dibuat_oleh')->constrained('users');
            $table->timestamp('dianalisa_pada')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil_bundle');
    }
};
