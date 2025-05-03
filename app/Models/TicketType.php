<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketType extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'price',
        'quota',
    ];

    // Tambahkan remaining_quota ke appended attributes
    protected $appends = ['remaining_quota'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    // Accessor: berapa sisa quota
    public function getRemainingQuotaAttribute(): int
    {
        return $this->quota - $this->tickets()->count();
    }

    // Setelah simpan atau hapus, recalc total quota di Event
    protected static function booted(): void
    {
        static::saved(function (self $ticketType) {
            $ticketType->event->recalculateQuota();
        });
        static::deleted(function (self $ticketType) {
            $ticketType->event->recalculateQuota();
        });
    }
}