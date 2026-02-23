<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AgentPerformanceOverview;
use App\Filament\Widgets\UsageOverviewStats;
use App\Filament\Widgets\TokenByAgentChart;
use App\Filament\Widgets\TokenTrendChart;
use App\Models\Agent;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

class Analytics extends Page
{
    use HasFiltersForm;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected string $view = 'filament.pages.analytics';

    protected static ?string $navigationLabel = 'Analytics';

    protected static string|\UnitEnum|null $navigationGroup = 'Monitoring';

    protected static ?int $navigationSort = 4;

    protected static ?string $title = 'Analytics & Reports';

    protected static ?string $description = 'Deep dive into your agent performance metrics';

    protected function getHeaderWidgets(): array
    {
        return [
            UsageOverviewStats::class,
            TokenTrendChart::class,
            TokenByAgentChart::class,
            AgentPerformanceOverview::class,
        ];
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('startDate')
                    ->label('Start Date')
                    ->default(now()->subDays(30))
                    ->maxDate(now()),

                DatePicker::make('endDate')
                    ->label('End Date')
                    ->default(now())
                    ->maxDate(now()),

                Select::make('agentId')
                    ->label('Agent Filter')
                    ->options(fn () => ['' => 'All Agents'] + Agent::pluck('name', 'id')->toArray()),
            ])
            ->columns(3);
    }
}
