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
        'level'
    ];

    public function parent()
    {
        return $this->belongsTo (TrialBalance::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(TrialBalance::class, 'parent_id');
    }

    public function cashflows()
    {
        return $this->hasMany(Cashflow::class);
    }
}
