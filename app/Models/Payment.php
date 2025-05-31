<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

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

    protected static function booted()
    {
        static::created(function ($payment) {
            // Notifikasi untuk admin ketika ada pembayaran baru
            if ($payment->status === 'pending') {
                Notification::make()
                    ->title('Pembayaran Baru')
                    ->icon('heroicon-o-currency-dollar')
                    ->body("Pembayaran baru sebesar Rp " . number_format($payment->amount, 0, ',', '.') . " dari " . ($payment->buyer_name ?? 'Pembeli'))
                    ->actions([
                        \Filament\Notifications\Actions\Action::make('view')
                            ->label('Lihat Detail')
                            ->url(route('filament.admin.resources.payments.view', ['record' => $payment]))
                            ->button(),
                    ])
                    ->sendToDatabase(
                        \App\Models\User::role('admin')->get()
                    );
            }
        });

        static::updated(function ($payment) {
            // Notifikasi ketika status pembayaran berubah
            if ($payment->isDirty('status')) {
                $oldStatus = $payment->getOriginal('status');
                $newStatus = $payment->status;

                if ($newStatus === 'paid') {
                    Notification::make()
                        ->title('Pembayaran Dikonfirmasi')
                        ->icon('heroicon-o-check-circle')
                        ->body("Pembayaran sebesar Rp " . number_format($payment->amount, 0, ',', '.') . " telah dikonfirmasi")
                        ->actions([
                            \Filament\Notifications\Actions\Action::make('view')
                                ->label('Lihat Detail')
                                ->url(route('filament.admin.resources.payments.view', ['record' => $payment]))
                                ->button(),
                        ])
                        ->sendToDatabase(
                            \App\Models\User::role('admin')->get()
                        );
                } elseif ($newStatus === 'refunded') {
                    Notification::make()
                        ->title('Pembayaran Di-refund')
                        ->icon('heroicon-o-x-circle')
                        ->body("Pembayaran sebesar Rp " . number_format($payment->amount, 0, ',', '.') . " telah di-refund")
                        ->actions([
                            \Filament\Notifications\Actions\Action::make('view')
                                ->label('Lihat Detail')
                                ->url(route('filament.admin.resources.payments.view', ['record' => $payment]))
                                ->button(),
                        ])
                        ->sendToDatabase(
                            \App\Models\User::role('admin')->get()
                        );
                }
            }
        });
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'payment_id', 'id');
    }
    
    public function ticket(): HasOne
    {
        return $this->hasOne(Ticket::class, 'payment_id', 'id');
    }
}
