<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama',
        'penanggungjawab',
        'kontak',
        'alamat',
        'namakegiatan',
        'deskripsi',
        'tanggal',
        'status'
    ];

    protected $casts = [
        'tanggal' => 'date'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
} 