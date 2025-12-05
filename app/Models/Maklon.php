<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maklon extends Model
{
    use HasFactory;

    protected $table = 'maklon';

    protected $fillable = [
        'date',
        'description',
        'pic',
        'proof_number',
        'batang',
        'dpp',
        'ppn',
        'pph23',
        'is_posted',
        'created_by'
    ];

    protected $casts = [
        'date' => 'date',
        'batang' => 'decimal:2',
        'dpp' => 'decimal:2',
        'ppn' => 'decimal:2',
        'pph23' => 'decimal:2',
        'is_posted' => 'boolean'
    ];

    public function attachments()
    {
        return $this->hasMany(MaklonAttachment::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}