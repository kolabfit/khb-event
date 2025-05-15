<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Tables;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Columns\ImageColumn;


class PendingApprovalTicketsWidget extends TableWidget
{
    /**
     * Judul widget di dashboard
     */
    protected static ?string $heading = 'Pending Ticket Approvals';

    protected string|int|array $columnSpan = [
        'sm' => 2,
        'lg' => 'full',
    ];

    /**
     * Query untuk mengisi tabel: hanya pembayaran yang di-upload bukti dan masih pending
     *
     * @return Builder|Relation|null
     */
    protected function getTableQuery(): Builder|Relation|null
    {
        return Payment::query()
            ->where('status', 'pending');

    }

    /**
     * Kolom-kolom yang ditampilkan di tabel
     *
     * @return array<int, Tables\Columns\Column>
     */
    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('ticket.id')
                ->label('Ticket ID')
                ->sortable(),

            Tables\Columns\TextColumn::make('ticket.event.title')
                ->label('Event')
                ->sortable(),

            Tables\Columns\TextColumn::make('ticket.user.name')
                ->label('User')
                ->sortable(),

            Tables\Columns\TextColumn::make('amount')
                ->label('Amount')
                ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                ->sortable(),

            Tables\Columns\TextColumn::make('method')
                ->label('Method'),

            Tables\Columns\ImageColumn::make('receipt_path')
                ->label('Receipt')
                ->getStateUsing(fn (Payment $record) => $record->receipt_path)
                ->disk('public')
                ->url(fn(Payment $record): string => Storage::url($record->receipt_path))
                ->height(80)
                ->width(80),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Uploaded At')
                ->dateTime('d M Y H:i')
                ->sortable(),
        ];
    }

    /**
     * Action di setiap baris: Approve payment
     *
     * @return array<int, Tables\Actions\Action>
     */
    protected function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-check')
                ->button()
                ->requiresConfirmation()
                ->action(function (Payment $record, Tables\Actions\Action $action) {
                    // Update payment dan ticket status
                    $record->update(['status' => 'paid', 'paid_at' => now()]);
                    $record->ticket->update(['status' => 'paid']);

                    // tandai tiket juga sudah paid
                    $record->ticket->update([
                        'status' => 'paid',
                    ]);

                    $action->successNotificationTitle('Payment approved!');
                }),
        ];
    }
}
