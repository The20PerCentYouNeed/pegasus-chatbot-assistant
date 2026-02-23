<?php

namespace App\Filament\Widgets;

use App\Models\ConversationMessage;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AlertsStatsWidget extends StatsOverviewWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static bool $isDiscovered = false;

    protected function getStats(): array
    {
        $emptyResponseQuery = fn () => ConversationMessage::where('role', 'assistant')
            ->where(function ($query) {
                $query->whereNull('content')
                    ->orWhere('content', '');
            });

        $todayErrors = (clone $emptyResponseQuery)()
            ->whereDate('created_at', today())
            ->count();

        $yesterdayErrors = (clone $emptyResponseQuery)()
            ->whereDate('created_at', today()->subDay())
            ->count();

        $trend = $yesterdayErrors > 0
            ? round((($todayErrors - $yesterdayErrors) / $yesterdayErrors) * 100, 1)
            : 0;

        $last24Hours = (clone $emptyResponseQuery)()
            ->where('created_at', '>=', now()->subDay())
            ->count();

        $last7Days = (clone $emptyResponseQuery)()
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        $totalToday = ConversationMessage::where('role', 'assistant')
            ->whereDate('created_at', today())
            ->count();
        $errorRate = $totalToday > 0 ? round(($todayErrors / $totalToday) * 100, 2) : 0;

        return [
            Stat::make('Empty Responses Today', $todayErrors)
                ->description(
                    $trend >= 0 ? "{$trend}% increase" : "{$trend}% decrease"
                )
                ->descriptionIcon($trend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($todayErrors > 0 ? 'danger' : 'success'),

            Stat::make('Last 24 Hours', $last24Hours)
                ->description('Rolling 24-hour window')
                ->descriptionIcon('heroicon-m-clock')
                ->color($last24Hours > 10 ? 'warning' : 'success'),

            Stat::make('Last 7 Days', $last7Days)
                ->description('Total issues this week')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color(
                    $last7Days > 50 ? 'danger' : (
                        $last7Days > 20 ? 'warning' : 'success'
                    )
                ),

            Stat::make('Error Rate', $errorRate . '%')
                ->description('Today\'s error percentage')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color(
                    $errorRate > 5 ? 'danger' : (
                        $errorRate > 2 ? 'warning' : 'success'
                    )
                ),
        ];
    }
}
