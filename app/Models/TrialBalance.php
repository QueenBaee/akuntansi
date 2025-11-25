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
        'tahun_2024',
        'is_kas_bank'
    ];

    /** RELASI PARENT - CHILD */
    public function parent()
    {
        return $this->belongsTo(TrialBalance::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(TrialBalance::class, 'parent_id');
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
