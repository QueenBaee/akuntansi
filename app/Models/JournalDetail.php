<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'journal_id',
        'account_id',
        'description',
        'debit',
        'credit',
    ];

    protected $casts = [
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
    ];

    public function journal()
    {
        return $this->belongsTo(Journal::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function scopeDebit($query)
    {
        return $query->where('debit', '>', 0);
    }

    public function scopeCredit($query)
    {
        return $query->where('credit', '>', 0);
    }

    public function getAmount()
    {
        return $this->debit > 0 ? $this->debit : $this->credit;
    }

    public function getType()
    {
        return $this->debit > 0 ? 'debit' : 'credit';
    }
}