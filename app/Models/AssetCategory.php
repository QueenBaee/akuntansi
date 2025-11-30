<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetCategory extends Model
{
    protected $fillable = [
        'kode',
        'nama',
        'parent_kode',
        'level',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(AssetCategory::class, 'parent_kode', 'kode');
    }

    public function children()
    {
        return $this->hasMany(AssetCategory::class, 'parent_kode', 'kode');
    }

    public function fixedAssets()
    {
        return $this->hasMany(FixedAsset::class, 'category_kode', 'kode');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeParents($query)
    {
        return $query->where('level', 1);
    }

    public function scopeChildren($query)
    {
        return $query->where('level', 2);
    }
}