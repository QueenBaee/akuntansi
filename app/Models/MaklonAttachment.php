<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaklonAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'maklon_id',
        'original_name',
        'file_path',
        'file_type',
        'file_size'
    ];

    public function maklon()
    {
        return $this->belongsTo(Maklon::class);
    }
}