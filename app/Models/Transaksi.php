<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    public const ORDER_STATUSES = [
        OrderStatus::Pending->value,
        OrderStatus::Dibayar->value,
        OrderStatus::SedangDibuat->value,
        OrderStatus::SiapDiambil->value,
        OrderStatus::Selesai->value,
        OrderStatus::Cancel->value,
    ];

    public const PAYMENT_METHODS = ['cash', 'qris'];

    protected $fillable = [
        'kode_transaksi',
        'user_id',
        'nama_pelanggan',
        'total_harga',
        'bayar',
        'kembalian',
        'payment_method',
        'status',
        'order_status',
        'catatan',
        'tanggal_transaksi',
    ];

    protected function casts(): array
    {
        return [
            'total_harga'       => 'decimal:2',
            'bayar'             => 'decimal:2',
            'kembalian'         => 'decimal:2',
            'tanggal_transaksi' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(DetailTransaksi::class);
    }

    public function produk()
    {
        return $this->belongsToMany(Produk::class, 'detail_transaksi')
                    ->withPivot('jumlah', 'harga_satuan', 'subtotal');
    }

    public static function generateKode(): string
    {
        $urutan = self::count() + 1;

        return 'KAMA' . $urutan . now()->format('dmy');
    }

    public function getTotalFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->total_harga, 0, ',', '.');
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match (strtolower(trim((string) $this->payment_method))) {
            'cash' => 'Tunai',
            'qris' => 'QRIS',
            'debit' => 'Kartu Debit',
            default => $this->payment_method ? ucfirst(strtolower($this->payment_method)) : 'Tunai',
        };
    }
}
