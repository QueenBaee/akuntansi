<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'account_number',
        'description',
        'is_active',
        'cashflow_id'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function cashflow()
    {
        return $this->belongsTo(Cashflow::class);
    }

    public function getCurrentBalance()
    {
        // Placeholder - implement actual balance calculation
        return 0;
    }
}