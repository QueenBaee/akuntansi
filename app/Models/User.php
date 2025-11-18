<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    public function createdJournals()
    {
        return $this->hasMany(Journal::class, 'created_by');
    }

    public function createdCashTransactions()
    {
        return $this->hasMany(CashTransaction::class, 'created_by');
    }

    public function createdBankTransactions()
    {
        return $this->hasMany(BankTransaction::class, 'created_by');
    }

    public function createdAssets()
    {
        return $this->hasMany(Asset::class, 'created_by');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function userAccounts()
    {
        return $this->hasMany(UserAccount::class);
    }

    public function accounts()
    {
        return $this->belongsToMany(Account::class, 'user_accounts')
                    ->withPivot('role', 'is_active')
                    ->withTimestamps();
    }
}