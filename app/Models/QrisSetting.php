<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrisSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchant_name',
        'merchant_city',
        'qris_image_path',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
} 