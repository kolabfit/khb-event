<?php

namespace App\Filament\TicketWidgets;

use Filament\Widgets\Widget;
use App\Models\Payment;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Livewire\Attributes\On;
use Livewire\WithPagination;

class TicketApprovalsWidget extends Widget
{
    use WithPagination;

    protected static string $view = 'filament.widgets.ticket-approvals-widget';
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?string $heading = 'Ticket Approvals';

    public $selected = [];
    public $selectAll = false;

    public function getViewData(): array
    {
        return [
            'pendingPayments' => Payment::query()
                ->where('status', 'pending')
                ->with(['tickets', 'tickets.event', 'tickets.user'])
                ->latest()
                ->paginate(10)
        ];
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected = Payment::query()
                ->where('status', 'pending')
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function approveSelected()
    {
        if (empty($this->selected)) {
            Notification::make()
                ->warning()
                ->title('No payments selected.')
                ->send();
            return;
        }

        $payments = Payment::whereIn('id', $this->selected)->get();
        
        foreach ($payments as $payment) {
            $payment->update(['status' => 'paid', 'paid_at' => now()]);
            
            if ($payment->tickets) {
                $payment->tickets()->update(['status' => 'paid']);
            }
        }

        Notification::make()
            ->success()
            ->title(count($this->selected) . ' payments approved successfully.')
            ->send();

        $this->selected = [];
        $this->selectAll = false;
        $this->dispatch('payment-approved');
    }

    public function rejectSelected()
    {
        if (empty($this->selected)) {
            Notification::make()
                ->warning()
                ->title('No payments selected.')
                ->send();
            return;
        }

        $payments = Payment::whereIn('id', $this->selected)->get();
        
        foreach ($payments as $payment) {
            $payment->update(['status' => 'failed']);
            
            if ($payment->tickets) {
                $payment->tickets()->update(['status' => 'cancelled']);
                
                $ticketCount = $payment->tickets()->count();
                if ($ticketCount > 0) {
                    $firstTicket = $payment->tickets()->first();
                    if ($firstTicket && $firstTicket->event) {
                        $firstTicket->event->increment('quota', $ticketCount);
                    }
                }
            }
        }

        Notification::make()
            ->success()
            ->title(count($this->selected) . ' payments rejected successfully.')
            ->send();

        $this->selected = [];
        $this->selectAll = false;
        $this->dispatch('payment-rejected');
    }

    #[On('payment-approved')]
    #[On('payment-rejected')]
    public function refresh()
    {
        // This method will be called after approve/reject to refresh the widget
    }

    public function approve($paymentId)
    {
        $payment = Payment::find($paymentId);
        
        if (!$payment) {
            Notification::make()
                ->danger()
                ->title('Error: Payment record not found.')
                ->send();
            return;
        }

        $payment->update(['status' => 'paid', 'paid_at' => now()]);
        
        if ($payment->tickets) {
            $payment->tickets()->update(['status' => 'paid']);
        }

        $source = $payment->receipt_path ? 'user payment' : 'admin-created tickets';
        
        Notification::make()
            ->success()
            ->title("Payment approved! ($source)")
            ->send();

        $this->dispatch('payment-approved');
    }

    public function reject($paymentId)
    {
        $payment = Payment::find($paymentId);
        
        if (!$payment) {
            Notification::make()
                ->danger()
                ->title('Error: Payment record not found.')
                ->send();
            return;
        }

        $payment->update(['status' => 'failed']);
        
        if ($payment->tickets) {
            $payment->tickets()->update(['status' => 'cancelled']);
            
            $ticketCount = $payment->tickets()->count();
            if ($ticketCount > 0) {
                $firstTicket = $payment->tickets()->first();
                if ($firstTicket && $firstTicket->event) {
                    $firstTicket->event->increment('quota', $ticketCount);
                }
            }
        }

        Notification::make()
            ->success()
            ->title('Payment rejected and tickets cancelled.')
            ->send();

        $this->dispatch('payment-rejected');
    }
} 