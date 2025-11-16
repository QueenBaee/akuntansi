<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'number',
        'type',
        'cash_account_id',
        'contra_account_id',
        'cashflow_category_id',
        'amount',
        'description',
        'reference',
        'journal_id',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function cashAccount()
    {
        return $this->belongsTo(Account::class, 'cash_account_id');
    }

    public function contraAccount()
    {
        return $this->belongsTo(Account::class, 'contra_account_id');
    }

    public function cashflowCategory()
    {
        return $this->belongsTo(CashflowCategory::class);
    }

    public function journal()
    {
        return $this->belongsTo(Journal::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }
}