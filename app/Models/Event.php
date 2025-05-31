<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Category;
use App\Models\TicketType;
use App\Models\Ticket;
use App\Models\Payment;
use App\Models\QrisSetting;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Log;
class Event extends Model
{
    use HasFactory;

    // protected $appends = ['formatted_price'];

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
        'is_paid',
        'qris_setting_id',
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

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function recalculateQuota(): void
    {
        $total = $this->ticketTypes()->sum('quota');
        $this->update(['quota' => $total]);
    }

    public function payments(): HasManyThrough
    {
        return $this->hasManyThrough(
            Payment::class,
            Ticket::class,
            'event_id',  // Foreign key on tickets table
            'id',        // Foreign key on payments table
            'id',        // Local key on events table
            'payment_id' // Local key on tickets table
        );
    }

    public function qrisSetting(): BelongsTo
    {
        return $this->belongsTo(QrisSetting::class);
    }

    public function getThumbnailAttribute(): string
    {
        if(request()->routeIs('filament.admin.*')) {
            return $this->attributes['thumbnail'];
        } else if(request()->routeIs('livewire.update')) {
            return $this->attributes['thumbnail'];
        }
        
        // Jika kamu menyimpan di storage/app/public/…
        // akan jadi: https://your-app.com/storage/namafile.jpg
        return Storage::url($this->attributes['thumbnail']);
        // atau, kalau di direktori public/images:
        // return asset($this->attributes['image']);
    }

    // public function getPriceAttribute($value)
    // {
    //     // setelah migration di‐atas, $value selalu int (bukan null)
    //     if ((int) $value === 0) {
    //         return 'Gratis';
    //     }

    //     // kalau mau format langsung di sini:
    //     return 'Rp ' . number_format($value, 0, ',', '.');
    //     // —atau— kalau butuh angka mentah, kembalikan $value saja
    // }

    public function getPriceLabelAttribute(): string
    {
        $price = $this->attributes['price'] ?? 0;

        if ((int) $price === 0) {
            return 'Gratis';
        }

        return 'Rp ' . number_format($price, 0, ',', '.');
    }
    
    public function getStatusLabelAttribute(): string
    {
        $now = now();
        $startDate = $this->start_date ? \Carbon\Carbon::parse($this->start_date) : null;
        $endDate = $this->end_date ? \Carbon\Carbon::parse($this->end_date) : null;
        
        // If event has no start date, consider it as draft
        if (!$startDate) {
            return 'draft';
        }
        
        // If event hasn't started yet
        if ($startDate->isFuture()) {
            return 'upcoming';
        }
        
        // If event has started but hasn't ended (or no end date)
        if ($startDate->isPast() && (!$endDate || $endDate->isFuture())) {
            return 'active';
        }
        
        // If event has ended
        return 'ended';
    }
    
    public function isEventActive(): bool
    {
        return in_array($this->status_label, ['upcoming', 'active']);
    }
}
