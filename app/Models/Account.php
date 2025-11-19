<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'type',
        'category',
        'opening_balance',
        'is_active',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function journalDetails()
    {
        return $this->hasMany(JournalDetail::class);
    }

    public function cashTransactions()
    {
        return $this->hasMany(CashTransaction::class, 'cash_account_id');
    }

    public function bankTransactions()
    {
        return $this->hasMany(BankTransaction::class, 'bank_account_id');
    }

    public function assets()
    {
        return $this->hasMany(Asset::class, 'asset_account_id');
    }

    public function getCurrentBalance()
    {
        $totalDebit = $this->journalDetails()->sum('debit');
        $totalCredit = $this->journalDetails()->sum('credit');
        
        // Both kas and bank are asset accounts (debit increases balance)
        return $this->opening_balance + $totalDebit - $totalCredit;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function userAccounts()
    {
        return $this->hasMany(UserAccount::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_accounts')
                    ->withPivot('role', 'is_active')
                    ->withTimestamps();
    }
}