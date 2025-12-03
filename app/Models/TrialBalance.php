<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrialBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'keterangan',
        'parent_id',
        'level',
        'sort_order',
        'tahun_2024',
        'is_kas_bank'
    ];

    protected $casts = [
        'is_kas_bank' => 'boolean'
    ];

    /** RELASI PARENT - CHILD */
    public function parent()
    {
        return $this->belongsTo(TrialBalance::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(TrialBalance::class, 'parent_id')->orderBy('sort_order');
    }

    /** RELASI KE CASHFLOW */
    public function cashflows()
    {
        return $this->hasMany(Cashflow::class);
    }

    /** RELASI YANG BENAR KE JOURNAL DETAILS */
    public function journalDetails()
    {
        return $this->hasMany(JournalDetail::class, 'trial_balance_id');
    }
}
