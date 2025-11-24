<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cashflow extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'keterangan',
        'level',
        'parent_id',
        'trial_balance_id'
    ];

    public function parent()
    {
        return $this->belongsTo(Cashflow::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Cashflow::class, 'parent_id');
    }

    public function trialBalance()
    {
        return $this->belongsTo(TrialBalance::class, 'trial_balance_id');
    }
}
