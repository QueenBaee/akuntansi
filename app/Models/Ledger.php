<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ledger extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'nama',
        'tipe_akun',
        'grup',
        'saldo_normal'
    ];

    protected $casts = [
        'tipe_akun' => 'string',
        'saldo_normal' => 'string'
    ];
}