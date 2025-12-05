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
        $journals = Journal::where(function($query) {
                $query->where('debit_account_id', $this->id)
                      ->orWhere('credit_account_id', $this->id);
            })
            ->where('is_posted', true)
            ->get();
            
        $balance = $this->opening_balance;
        
        foreach ($journals as $journal) {
            if ($journal->debit_account_id == $this->id) {
                $balance += $journal->total_amount;
            }
            if ($journal->credit_account_id == $this->id) {
                $balance -= $journal->total_amount;
            }
        }
        
        return $balance;
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