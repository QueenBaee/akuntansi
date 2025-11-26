<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class FixedAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'parent_id',
        'acquisition_date',
        'acquisition_price',
        'residual_value',
        'useful_life_months',
        'depreciation_rate',
        'asset_account_id',
        'accumulated_account_id',
        'expense_account_id',
        'accumulated_depreciation',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'acquisition_date' => 'date',
        'acquisition_price' => 'decimal:2',
        'residual_value' => 'decimal:2',
        'accumulated_depreciation' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function parent()
    {
        return $this->belongsTo(FixedAsset::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(FixedAsset::class, 'parent_id');
    }

    public function assetAccount()
    {
        return $this->belongsTo(TrialBalance::class, 'asset_account_id');
    }

    public function accumulatedAccount()
    {
        return $this->belongsTo(TrialBalance::class, 'accumulated_account_id');
    }

    public function expenseAccount()
    {
        return $this->belongsTo(TrialBalance::class, 'expense_account_id');
    }

    public function mutations()
    {
        return $this->hasMany(AssetMutation::class, 'asset_id');
    }

    public function depreciations()
    {
        return $this->hasMany(AssetDepreciation::class, 'asset_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessors & Helpers
    public function getBaseValueAttribute()
    {
        return $this->acquisition_price - $this->residual_value;
    }

    public function getCurrentBookValueAttribute()
    {
        return $this->acquisition_price - $this->accumulated_depreciation;
    }

    public function getMonthlyDepreciationAttribute()
    {
        if ($this->useful_life_months <= 0) {
            return 0;
        }
        return $this->base_value / $this->useful_life_months;
    }

    public function getTotalMutationsAttribute()
    {
        return $this->mutations()
            ->where('type', 'addition')
            ->sum('amount') - 
            $this->mutations()
            ->where('type', 'disposal')
            ->sum('amount');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}