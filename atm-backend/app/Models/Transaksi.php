<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaksi extends Model
{
    protected $fillable = [
        'rekening_id',
        'jenis',
        'nominal',
        'keterangan',
        'saldo_sebelum',
        'saldo_sesudah',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'saldo_sebelum' => 'decimal:2',
        'saldo_sesudah' => 'decimal:2',
    ];

    public function rekening(): BelongsTo
    {
        return $this->belongsTo(Rekening::class);
    }
}