<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Journal extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'date',
        'number',
        'reference',
        'description',
        'pic',
        'proof_number',
        'cash_in',
        'cash_out',
        'debit_account_id',
        'credit_account_id',
        'cashflow_id',
        'balance',
        'source_module',
        'source_id',
        'fixed_asset_id',
        'total_debit',
        'total_credit',
        'total_amount',
        'is_posted',
        'created_by',
        'ledger_id',
        'file_path',
    ];

    protected $casts = [
        'date' => 'date',
        'cash_in' => 'decimal:2',
        'cash_out' => 'decimal:2',
        'balance' => 'decimal:2',
        'total_debit' => 'decimal:2',
        'total_credit' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'is_posted' => 'boolean',
    ];



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

    public function debitAccount()
    {
        return $this->belongsTo(TrialBalance::class, 'debit_account_id');
    }

    public function creditAccount()
    {
        return $this->belongsTo(TrialBalance::class, 'credit_account_id');
    }

    public function cashflow()
    {
        return $this->belongsTo(Cashflow::class, 'cashflow_id');
    }

    public function attachments()
    {
        return $this->hasMany(JournalAttachment::class);
    }

    public function ledger()
    {
        return $this->belongsTo(Ledger::class);
    }

    public function fixedAsset()
    {
        return $this->belongsTo(FixedAsset::class);
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

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($journal) {
            if ($journal->isForceDeleting()) {
                return;
            }
            
            // Soft delete related records
            $journal->attachments()->delete();
        });
    }

    // Helper method to restore journal with its related records
    public function restoreWithRelated()
    {
        $this->restore();
        $this->attachments()->withTrashed()->restore();
    }
}