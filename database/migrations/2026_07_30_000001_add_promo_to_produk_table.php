<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('produk', function (Blueprint $table) {
            $table->unsignedTinyInteger('promo_percent')->nullable()->default(0)->after('deskripsi');
            $table->boolean('promo_active')->default(false)->after('promo_percent');
        });
    }

    public function down()
    {
        Schema::table('produk', function (Blueprint $table) {
            $table->dropColumn(['promo_percent', 'promo_active']);
        });
    }
};
