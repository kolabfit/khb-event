<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class PaymentStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $today = now()->startOfDay();
        $thisMonth = now()->startOfMonth();

        $todayStats = Payment::whereDate('created_at', $today)
            ->select(
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "paid" THEN 1 ELSE 0 END) as paid'),
                DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending'),
                DB::raw('SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed'),
                DB::raw('SUM(CASE WHEN status = "paid" THEN amount ELSE 0 END) as total_amount')
            )
            ->first();

        $monthStats = Payment::whereDate('created_at', '>=', $thisMonth)
            ->select(
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "paid" THEN 1 ELSE 0 END) as paid'),
                DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending'),
                DB::raw('SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed'),
                DB::raw('SUM(CASE WHEN status = "paid" THEN amount ELSE 0 END) as total_amount')
            )
            ->first();

        return [
            Stat::make('Pembayaran Hari Ini', $todayStats->total)
                ->description('Lunas: ' . $todayStats->paid . ' | Pending: ' . $todayStats->pending)
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color($todayStats->paid > 0 ? 'success' : 'gray'),

            Stat::make('Pembayaran Bulan Ini', $monthStats->total)
                ->description('Lunas: ' . $monthStats->paid . ' | Pending: ' . $monthStats->pending)
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color($monthStats->paid > 0 ? 'success' : 'gray'),

            Stat::make('Total Pendapatan Bulan Ini', 'Rp ' . number_format($monthStats->total_amount, 0, ',', '.'))
                ->description('Dari ' . $monthStats->paid . ' pembayaran')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
        ];
    }
} 