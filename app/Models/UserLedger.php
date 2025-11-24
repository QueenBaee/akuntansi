<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLedger extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ledger_id',
        'role',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ledger()
    {
        return $this->belongsTo(Ledger::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}