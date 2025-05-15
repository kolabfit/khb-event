<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
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

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
