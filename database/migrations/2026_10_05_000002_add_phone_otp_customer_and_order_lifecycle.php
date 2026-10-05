<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const OLD_ORDER_STATUSES = ['pending', 'dibayar', 'selesai', 'batal'];

    private const NEW_ORDER_STATUSES = [
        'pending',
        'dibayar',
        'sedang_dibuat',
        'siap_diambil',
        'selesai',
        'cancel',
    ];

    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('no_telepon', 30)->nullable()->unique();
        });

        Schema::create('otp', function (Blueprint $table) {
            $table->id();
            $table->string('no_telepon', 30);
            $table->string('kode_hash', 255);
            $table->timestamp('expired_at');
            $table->unsignedSmallInteger('attempts')->default(0);
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
            $table->index(['no_telepon', 'used_at']);
            $table->index('expired_at');
        });

        Schema::table('transaksi', function (Blueprint $table) {
            $table->string('nama_pelanggan')->nullable();
        });

        $this->alterTransactionEnums(
            ['selesai', 'batal', 'cancel'],
            ['pending', 'dibayar', 'sedang_dibuat', 'siap_diambil', 'selesai', 'batal', 'cancel']
        );

        DB::table('transaksi')
            ->where('order_status', 'batal')
            ->update(['order_status' => 'cancel']);

        DB::table('transaksi')
            ->where('status', 'batal')
            ->update(['status' => 'cancel']);

        DB::table('transaksi')
            ->where('order_status', 'cancel')
            ->update(['status' => 'cancel']);

        $this->alterTransactionEnums(['selesai', 'cancel'], self::NEW_ORDER_STATUSES);
    }

    public function down(): void
    {
        $this->alterTransactionEnums(
            ['selesai', 'batal', 'cancel'],
            array_values(array_unique(array_merge(self::OLD_ORDER_STATUSES, self::NEW_ORDER_STATUSES)))
        );

        DB::table('transaksi')
            ->whereIn('order_status', ['sedang_dibuat', 'siap_diambil'])
            ->update(['order_status' => 'dibayar']);

        DB::table('transaksi')
            ->where('order_status', 'cancel')
            ->update(['order_status' => 'batal']);

        DB::table('transaksi')
            ->where('status', 'cancel')
            ->update(['status' => 'batal']);

        $this->alterTransactionEnums(['selesai', 'batal'], self::OLD_ORDER_STATUSES);

        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropColumn('nama_pelanggan');
        });

        Schema::dropIfExists('otp');

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_no_telepon_unique');
            $table->dropColumn('no_telepon');
        });
    }

    private function alterTransactionEnums(array $statusValues, array $orderStatusValues): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            $status = implode("','", $statusValues);
            $orderStatus = implode("','", $orderStatusValues);

            DB::statement("ALTER TABLE `transaksi` MODIFY `status` ENUM('$status') NOT NULL DEFAULT 'selesai'");
            DB::statement("ALTER TABLE `transaksi` MODIFY `order_status` ENUM('$orderStatus') NOT NULL DEFAULT 'pending'");

            return;
        }

        Schema::table('transaksi', function (Blueprint $table) use ($statusValues, $orderStatusValues) {
            $table->enum('status', $statusValues)->default('selesai')->change();
            $table->enum('order_status', $orderStatusValues)->default('pending')->change();
        });
    }
};