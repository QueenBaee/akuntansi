<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'bank_name',
        'account_number',
        'description',
        'is_active',
        'trial_balance_id'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function trialBalance()
    {
        return $this->belongsTo(TrialBalance::class);
    }

    public function getCurrentBalance()
    {
        // Placeholder - implement actual balance calculation
        return 0;
    }
}