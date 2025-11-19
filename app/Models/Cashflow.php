<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cashflow extends Model
{
    use HasFactory;

    protected $fillable = ['kode', 'keterangan', 'trial_balance_id'];

    public function trialBalance()
    {
        return $this->belongsTo(TrialBalance::class);
    }
}
