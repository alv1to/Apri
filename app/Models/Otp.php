<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected $table = 'otp';

    protected $fillable = [
        'no_telepon',
        'kode_hash',
        'expired_at',
        'attempts',
        'used_at',
    ];

    protected $hidden = [
        'kode_hash',
    ];

    protected function casts(): array
    {
        return [
            'kode_hash' => 'hashed',
            'expired_at' => 'datetime',
            'attempts' => 'integer',
            'used_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'no_telepon', 'no_telepon');
    }
}