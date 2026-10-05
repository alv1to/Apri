<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilBundle extends Model
{
    protected $table = 'hasil_bundle';

    protected $fillable = [
        'antecedent',
        'consequent',
        'nama_antecedent',
        'nama_consequent',
        'support',
        'confidence',
        'lift',
        'frekuensi',
        'rentang_dari',
        'rentang_sampai',
        'min_support',
        'min_confidence',
        'is_aktif',
        'harga_bundle',
        'total_transaksi_dianalisa',
        'dibuat_oleh',
        'dianalisa_pada',
    ];

    protected function casts(): array
    {
        return [
            'antecedent'      => 'array',
            'consequent'      => 'array',
            'nama_antecedent' => 'array',
            'nama_consequent' => 'array',
            'support'         => 'decimal:4',
            'confidence'      => 'decimal:4',
            'lift'            => 'decimal:4',
            'harga_bundle'    => 'decimal:2',
            'is_aktif'        => 'boolean',
            'dianalisa_pada'  => 'datetime',
        ];
    }

    public function pembuatBundle()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function getNamaBundleAttribute(): string
    {
        $ant = implode(' + ', $this->nama_antecedent ?? []);
        $con = implode(' + ', $this->nama_consequent ?? []);
        return "{$ant} → {$con}";
    }

    public function getSupportPersenAttribute(): string
    {
        return number_format($this->support * 100, 2) . '%';
    }

    public function getConfidencePersenAttribute(): string
    {
        return number_format($this->confidence * 100, 2) . '%';
    }

    public function getLiftLabelAttribute(): string
    {
        if ($this->lift >= 1.5) return 'Kuat';
        if ($this->lift >= 1.0) return 'Sedang';
        return 'Lemah';
    }

    public function getLiftColorAttribute(): string
    {
        if ($this->lift >= 1.5) return 'green';
        if ($this->lift >= 1.0) return 'amber';
        return 'red';
    }
}
