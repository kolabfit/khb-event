<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'ticket_type_id',
        'user_id',
        'purchased_at',
        'price_paid',
        'status',
        'qr_code_path',
        'payment_id',
        'participant_name',
        'participant_email',
        'participant_phone',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function ticketType()
    {
        return $this->belongsTo(TicketType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id', 'id');
    }

    public function payments()
    {
        return $this->hasMany(\App\Models\Payment::class, 'ticket_id');
    }

    public function getCustomTicketQrAttribute()
    {
        return [
            'ticket' => $this,
        ];
    }
}
