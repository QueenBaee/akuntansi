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
        'parent_id',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

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
        
        return match($this->type) {
            'asset', 'expense' => $this->opening_balance + $totalDebit - $totalCredit,
            'liability', 'equity', 'revenue' => $this->opening_balance + $totalCredit - $totalDebit,
        };
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