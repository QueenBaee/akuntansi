<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashflowCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function cashTransactions()
    {
        return $this->hasMany(CashTransaction::class);
    }

    public function bankTransactions()
    {
        return $this->hasMany(BankTransaction::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}