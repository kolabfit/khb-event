<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'location',
        'start_date',
        'end_date',
        'quota',
        'status',
        'thumbnail',
        'price',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function ticketTypes()
    {
        return $this->hasMany(TicketType::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function recalculateQuota(): void
    {
        $total = $this->ticketTypes()->sum('quota');
        $this->update(['quota' => $total]);
    }

    public function payments()
    {
        return $this->hasManyThrough(
            Payment::class,
            Ticket::class,
            'event_id',  // FK di tickets
            'ticket_id', // FK di payments
            'id',
        );
    }
    
    public function getThumbnailAttribute(): string
    {
        // Jika kamu menyimpan di storage/app/public/…
        // akan jadi: https://your-app.com/storage/namafile.jpg
        return Storage::url($this->attributes['thumbnail']);
        // atau, kalau di direktori public/images:
        // return asset($this->attributes['image']);
    }
}
