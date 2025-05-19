<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use App\Models\Payment;
use App\Models\Ticket;
use App\Models\Event;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use App\Filament\Resources\TicketResource\Pages\PendingApprovalTickets;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;

    protected function formatToRupiah($number): string
    {
        return 'Rp ' . number_format($number, 0, ',', '.');
    }

    protected function handleRecordCreation(array $data): Model
    {
        // Validate that participants are provided
        if (!isset($data['participants']) || empty($data['participants'])) {
            Notification::make()
                ->title('No participants added')
                ->body('You must add at least one participant to create tickets.')
                ->danger()
                ->send();
            
            $this->halt();
        }
        
        return DB::transaction(function () use ($data) {
            // Get event and calculate prices
            $event = Event::findOrFail($data['event_id']);
            $participants = $data['participants'];
            $quantity = count($participants);
            $pricePerTicket = $event->price ?? 0;
            $totalAmount = $pricePerTicket * $quantity;

            // Check if event has enough quota
            if ($event->quota < $quantity) {
                Notification::make()
                    ->title('Not enough quota')
                    ->body("The event only has {$event->quota} tickets available, but you're trying to create {$quantity} tickets.")
                    ->danger()
                    ->send();
                
                $this->halt();
            }

            // Show price summary notification
            Notification::make()
                ->title('Price Summary')
                ->body("Price per ticket: {$this->formatToRupiah($pricePerTicket)}\nTotal amount: {$this->formatToRupiah($totalAmount)}")
                ->info()
                ->send();
            
            // Create payment record
            $payment = Payment::create([
                'amount' => $totalAmount,
                'method' => $data['payment_method'],
                'transaction_id' => $data['transaction_id'],
                'status' => 'pending',
                'paid_at' => null,
                'buyer_name' => auth()->user()->name,
                'buyer_email' => auth()->user()->email,
                'buyer_phone' => auth()->user()->phone ?? '',
            ]);
            
            // Create first ticket - this will be returned by this method
            $firstTicket = null;
            
            // Create tickets for each participant
            foreach ($participants as $index => $participant) {
                $ticket = Ticket::create([
                    'event_id' => $data['event_id'],
                    'user_id' => $data['user_id'],
                    'payment_id' => $payment->id,
                    'price_paid' => $pricePerTicket,
                    'status' => 'pending',
                    'participant_name' => $participant['fullname'],
                    'participant_email' => $participant['email'],
                    'participant_phone' => $participant['phone'],
                ]);
                
                // Store the first ticket to return
                if ($index === 0) {
                    $firstTicket = $ticket;
                }
            }
            
            // Update quota
            $event->decrement('quota', $quantity);
            
            return $firstTicket;
        });
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Tickets created')
            ->body('The tickets have been created successfully and are now waiting for approval.')
            ->actions([
                \Filament\Notifications\Actions\Action::make('view_pending')
                    ->label('View Pending Tickets')
                    ->url(route('filament.admin.resources.tickets.index'))
            ]);
    }
}
