<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AlertsStatsWidget;
use App\Filament\Widgets\RecentErrorsTable;
use App\Models\ConversationMessage;
use Filament\Pages\Page;

class Alerts extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bell-alert';

    protected string $view = 'filament.pages.alerts';

    protected static ?string $navigationLabel = 'Alerts';

    protected static string|\UnitEnum|null $navigationGroup = 'Monitoring';

    protected static ?int $navigationSort = 5;

    protected static ?string $title = 'Performance Alerts';

    protected static ?string $description = 'Monitor errors and anomalies';

    public static function getNavigationBadge(): ?string
    {
        $errorCount = ConversationMessage::where('role', 'assistant')
            ->whereDate('created_at', today())
            ->where(function ($query) {
                $query->whereNull('content')
                    ->orWhere('content', '');
            })
            ->count();

        return $errorCount > 0 ? (string) $errorCount : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        $badge = static::getNavigationBadge();

        return $badge ? 'danger' : 'success';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AlertsStatsWidget::class,
            RecentErrorsTable::class,
        ];
    }
}
