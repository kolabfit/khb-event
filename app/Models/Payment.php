<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;


class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'method',
        'transaction_id',
        'status',
        'paid_at',
        'buyer_name',
        'buyer_email',
        'buyer_phone',
        'receipt_path',
    ];

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'payment_id', 'id');
    }
    
    public function ticket(): HasOne
    {
        return $this->hasOne(Ticket::class, 'payment_id', 'id');
    }
}
