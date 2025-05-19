<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Tables;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Columns\ImageColumn;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;

class ApprovalTicketsWidget extends TableWidget
{
    protected static ?string $heading = 'Ticket Approvals';
    protected string|int|array $columnSpan = 'full';
    public static bool $showOnDashboard = false;

    protected function getTableQuery(): Builder|Relation|null
    {
        return Payment::query()
            ->where('status', 'pending')
            ->with(['tickets', 'tickets.event', 'tickets.user'])
            ->latest();
    }       

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('id')->label('Payment ID')->sortable(),
            // Tables\Columns\TextColumn::make('transaction_id')->label('Transaction ID')->limit(15),
            Tables\Columns\TextColumn::make('tickets_count')->label('Tickets')->getStateUsing(fn($record) => $record?->tickets()->count() ?? 0),
            Tables\Columns\TextColumn::make('tickets.0.event.title')->label('Event')->getStateUsing(fn($record) => $record?->tickets()->first()?->event?->title ?? 'Unknown Event'),
            Tables\Columns\TextColumn::make('tickets.0.user.name')->label('Pemesan')->getStateUsing(fn($record) => $record?->tickets()->first()?->user?->name ?? 'Unknown User'),
            Tables\Columns\TextColumn::make('amount')->label('Jumlah')->formatStateUsing(fn($state) => 'Rp ' . number_format($state ?? 0, 0, ',', '.'))->sortable(),
            Tables\Columns\TextColumn::make('method')->label('Metode'),
            Tables\Columns\TextColumn::make('source')->label('Sumber')->getStateUsing(fn($record) => $record?->receipt_path ? 'User Upload' : 'Admin Created'),
            Tables\Columns\ImageColumn::make('receipt_path')->label('Receipt')->visible(fn($record) => $record?->receipt_path !== null)->disk('public')->url(fn($record) => $record?->receipt_path ? Storage::url($record->receipt_path) : null)->height(80)->width(80),
            Tables\Columns\TextColumn::make('created_at')->label('Dibuat')->dateTime('d M Y H:i')->sortable(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make('viewReceipt')
                ->label('Bukti Pembayaran')
                ->icon('heroicon-o-document')
                ->modalHeading('View Receipt')
                ->modalContent(fn ($record) => view('filament.widgets.payment-receipt', [
                    'receiptUrl' => $record->receipt_path ? Storage::url($record->receipt_path) : null,
                ]))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Tutup'),
            Tables\Actions\Action::make('approve')
                ->label('Terima')
                ->icon('heroicon-o-check')
                ->color('success')
                ->button()
                ->requiresConfirmation()
                ->action(function ($record, $action) {
                    if (!$record) {
                        $action->failureNotificationTitle('Error: Payment record not found.');
                        return;
                    }
                    $record->update(['status' => 'paid', 'paid_at' => now()]);
                    if ($record->tickets) {
                        $record->tickets()->update(['status' => 'paid']);
                    }
                    $source = $record->receipt_path ? 'user payment' : 'admin-created tickets';
                    $action->successNotificationTitle("Payment approved! ($source)");
                }),
            Tables\Actions\Action::make('reject')
                ->label('Tolak')
                ->icon('heroicon-o-x-mark')
                ->color('danger')
                ->button()
                ->requiresConfirmation()
                ->action(function ($record, $action) {
                    if (!$record) {
                        $action->failureNotificationTitle('Error: Payment record not found.');
                        return;
                    }
                    $record->update(['status' => 'failed']);
                    if ($record->tickets) {
                        $record->tickets()->update(['status' => 'cancelled']);
                    }
                    $ticketCount = $record->tickets()->count();
                    if ($ticketCount > 0) {
                        $firstTicket = $record->tickets()->first();
                        if ($firstTicket && $firstTicket->event) {
                            $firstTicket->event->increment('quota', $ticketCount);
                        }
                    }
                    $action->successNotificationTitle('Payment rejected and tickets cancelled.');
                }),
        ];
    }

    protected function getTableBulkActions(): array
    {
        return [
            Tables\Actions\BulkAction::make('bulk_approve')
                ->label('Approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Approve Selected Tickets')
                ->modalDescription('Are you sure you want to approve all selected tickets? This will mark all related payments as paid.')
                ->action(function (Collection $records) {
                    $approvedCount = 0;
                    $ticketCount = 0;
                    foreach ($records as $record) {
                        if (!$record) continue;
                        $record->update(['status' => 'paid', 'paid_at' => now()]);
                        $ticketsForPayment = $record->tickets()->count();
                        if ($ticketsForPayment > 0) {
                            $record->tickets()->update(['status' => 'paid']);
                            $ticketCount += $ticketsForPayment;
                        }
                        $approvedCount++;
                    }
                    Notification::make()->success()->title("Bulk Approval Completed")->body("Successfully approved $approvedCount payments with $ticketCount tickets.")->send();
                    $this->refreshTable();
                }),
            Tables\Actions\BulkAction::make('bulk_reject')
                ->label('Reject')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Reject Selected Tickets')
                ->modalDescription('Are you sure you want to reject all selected tickets? This will mark payments as failed, tickets as cancelled, and return quota to events.')
                ->action(function (Collection $records) {
                    $rejectedCount = 0;
                    $ticketCount = 0;
                    $returnedQuota = 0;
                    foreach ($records as $record) {
                        if (!$record) continue;
                        $record->update(['status' => 'failed']);
                        $ticketsForPayment = $record->tickets()->count();
                        if ($ticketsForPayment > 0) {
                            $record->tickets()->update(['status' => 'cancelled']);
                            $ticketCount += $ticketsForPayment;
                            $firstTicket = $record->tickets()->first();
                            if ($firstTicket && $firstTicket->event) {
                                $firstTicket->event->increment('quota', $ticketsForPayment);
                                $returnedQuota += $ticketsForPayment;
                            }
                        }
                        $rejectedCount++;
                    }
                    Notification::make()->success()->title("Bulk Rejection Completed")->body("Rejected $rejectedCount payments with $ticketCount tickets. Returned $returnedQuota quota to events.")->send();
                    $this->refreshTable();
                }),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            Tables\Filters\SelectFilter::make('payment_method')
                ->label('Payment Method')
                ->options([
                    'admin' => 'Admin Created',
                    'qris' => 'QRIS',
                    'transfer' => 'Bank Transfer',
                    'cash' => 'Cash',
                ]),
        ];
    }
} 