<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ledger extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_ledger',
        'kode_ledger',
        'tipe_ledger',
        'deskripsi',
        'is_active',
        'trial_balance_id'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function getCurrentBalance()
    {
        return 0;
    }

    public function journals()
    {
        return $this->hasMany(Journal::class);
    }

    public function userLedgers()
    {
        return $this->hasMany(UserLedger::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_ledgers')
                    ->withPivot('role', 'is_active')
                    ->withTimestamps();
    }

    public function activeUsers()
    {
        return $this->belongsToMany(User::class, 'user_ledgers')
                    ->wherePivot('is_active', true)
                    ->where('users.is_active', true)
                    ->withPivot('role', 'is_active')
                    ->withTimestamps();
    }

    public function trialBalance()
    {
        return $this->belongsTo(TrialBalance::class);
    }
}