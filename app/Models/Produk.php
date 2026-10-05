<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';

    protected $fillable = [
        'nama',
        'kode',
        'kategori',
        'harga',
        'stok',
        'satuan',
        'deskripsi',
        'gambar',
        'is_active',
        'promo_percent',
        'promo_active',
    ];

    protected function casts(): array
    {
        return [
            'harga'     => 'decimal:2',
            'is_active' => 'boolean',
            'promo_percent' => 'integer',
            'promo_active'  => 'boolean',
        ];
    }

    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class);
    }

    public function getHargaFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }
}
