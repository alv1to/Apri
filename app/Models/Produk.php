<?php

namespace App\Models;

use App\Enums\KategoriProduk;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';

    public const KATEGORI = [
        KategoriProduk::Kopi->value,
        KategoriProduk::PremiumKopi->value,
        KategoriProduk::BukanKopi->value,
        KategoriProduk::NgetehKuy->value,
        KategoriProduk::YangSegerSeger->value,
        KategoriProduk::MakananPendamping->value,
    ];

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

    protected static function booted(): void
    {
        static::creating(function (Produk $produk) {
            if (blank($produk->kode)) {
                $produk->kode = static::generateKode();
            }
        });
    }

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

    public static function generateKode(): string
    {
        $maxSequence = static::query()
            ->where('kode', 'like', 'PRD%')
            ->pluck('kode')
            ->map(function (string $kode) {
                return preg_match('/^PRD(\d+)$/', $kode, $matches) ? (int) $matches[1] : 0;
            })
            ->max() ?? 0;

        do {
            $candidate = 'PRD' . str_pad((string) ++$maxSequence, 3, '0', STR_PAD_LEFT);
        } while (static::where('kode', $candidate)->exists());

        return $candidate;
    }

    public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class);
    }

    public function getHargaFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }

    public function getStatusKetersediaanAttribute(): string
    {
        return $this->is_active ? 'Tersedia' : 'Tidak Tersedia';
    }
}
