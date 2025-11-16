<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'number',
        'reference',
        'description',
        'source_module',
        'source_id',
        'total_debit',
        'total_credit',
        'total_amount',
        'is_posted',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'total_debit' => 'decimal:2',
        'total_credit' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'is_posted' => 'boolean',
    ];

    public function details()
    {
        return $this->hasMany(JournalDetail::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function cashTransaction()
    {
        return $this->hasOne(CashTransaction::class);
    }

    public function bankTransaction()
    {
        return $this->hasOne(BankTransaction::class);
    }

    public function depreciation()
    {
        return $this->hasOne(Depreciation::class);
    }

    public function maklon()
    {
        return $this->hasOne(MaklonTransaction::class);
    }

    public function rentIncomeSchedule()
    {
        return $this->hasOne(RentIncomeSchedule::class);
    }

    public function rentExpenseSchedule()
    {
        return $this->hasOne(RentExpenseSchedule::class);
    }

    public function scopePosted($query)
    {
        return $query->where('is_posted', true);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeByModule($query, $module)
    {
        return $query->where('source_module', $module);
    }

    public function isBalanced()
    {
        return $this->total_debit == $this->total_credit;
    }
}