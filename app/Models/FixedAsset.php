<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class FixedAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_number',
        'asset_name',
        'code',
        'name',
        'quantity',
        'location',
        'group',
        'condition',
        'status',
        'category_kode',
        'parent_id',
        'acquisition_date',
        'acquisition_price',
        'residual_value',
        'depreciation_method',
        'useful_life_years',
        'useful_life_months',
        'depreciation_rate',
        'depreciation_start_date',
        'account_acquisition',
        'account_accumulated',
        'account_expense',
        'asset_account_id',
        'accumulated_account_id',
        'expense_account_id',
        'accumulated_depreciation',
        'is_active',
        'created_by',
        'merged_from',
        'is_merged',
        'is_converted',
        'parent_asset_id',
        'converted_at',
        'converted_by',
    ];

    protected $casts = [
        'acquisition_date' => 'date',
        'depreciation_start_date' => 'date',
        'acquisition_price' => 'decimal:2',
        'residual_value' => 'decimal:2',
        'depreciation_rate' => 'decimal:2',
        'accumulated_depreciation' => 'decimal:2',
        'is_active' => 'boolean',
        'is_merged' => 'boolean',
        'merged_from' => 'array',
        'quantity' => 'integer',
        'useful_life_years' => 'integer',
        'useful_life_months' => 'integer',
        'is_converted' => 'boolean',
        'converted_at' => 'datetime',
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

    public function parentAsset()
    {
        return $this->belongsTo(FixedAsset::class, 'parent_asset_id');
    }

    public function convertedAssets()
    {
        return $this->hasMany(FixedAsset::class, 'parent_asset_id');
    }

    public function converter()
    {
        return $this->belongsTo(User::class, 'converted_by');
    }

    public function category()
    {
        return $this->belongsTo(AssetCategory::class, 'category_kode', 'kode');
    }

    public function journals()
    {
        return $this->hasMany(Journal::class);
    }

    public function sourceJournals()
    {
        return $this->journals()->whereIn('source_module', ['manual', 'memorial']);
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

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    public function scopeInProgress($query)
    {
        return $query->where('group', 'Aset Dalam Penyelesaian')
                    ->where('is_converted', false);
    }

    public function scopeRegularAssets($query)
    {
        return $query->where('group', '!=', 'Aset Dalam Penyelesaian');
    }

    public function scopeConverted($query)
    {
        return $query->where('is_converted', true);
    }

    // Auto-generate asset number
    public static function generateAssetNumber()
    {
        $lastAsset = self::orderBy('id', 'desc')->first();
        $nextNumber = $lastAsset ? $lastAsset->id + 1 : 1;
        return 'AST-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    // Auto-calculate useful life in months from years
    public function setUsefulLifeYearsAttribute($value)
    {
        $this->attributes['useful_life_years'] = $value;
        if ($value) {
            $this->attributes['useful_life_months'] = $value * 12;
        }
    }

    // Auto-calculate depreciation rate based on method and group
    public function calculateDepreciationRate()
    {
        if ($this->depreciation_method === 'Straight Line') {
            if ($this->useful_life_years > 0) {
                return round(100 / $this->useful_life_years, 2);
            }
        } elseif ($this->depreciation_method === 'Declining Balance') {
            // Double declining balance method
            if ($this->useful_life_years > 0) {
                return round((2 / $this->useful_life_years) * 100, 2);
            }
        }
        return 0;
    }

    // Auto-suggest accounts based on group
    public function suggestAccounts()
    {
        $accounts = [];
        
        switch ($this->group) {
            case 'Permanent':
                $accounts = [
                    'acquisition' => 'A23-01',
                    'accumulated' => 'A24-01',
                    'expense' => 'E22-96'
                ];
                break;
            case 'Non-permanent':
                $accounts = [
                    'acquisition' => 'A23-02',
                    'accumulated' => 'A24-02',
                    'expense' => 'E22-97'
                ];
                break;
            case 'Group 1':
                $accounts = [
                    'acquisition' => 'A23-03',
                    'accumulated' => 'A24-03',
                    'expense' => 'E22-98'
                ];
                break;
            case 'Group 2':
                $accounts = [
                    'acquisition' => 'A23-04',
                    'accumulated' => 'A24-04',
                    'expense' => 'E22-99'
                ];
                break;
        }
        
        return $accounts;
    }
}