<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ConversationStatsWidget;
use App\Filament\Widgets\PopularTopicsWidget;
use App\Filament\Widgets\TokenUsageWidget;
use Filament\Pages\Page;

class AnalyticsDashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Analytics';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.analytics-dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            ConversationStatsWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            TokenUsageWidget::class,
            PopularTopicsWidget::class,
        ];
    }
}
