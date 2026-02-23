<?php

namespace App\Filament\Widgets;

use App\Models\AgentMetric;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class TokenTrendChart extends ChartWidget
{
    protected ?string $heading = 'Token Usage Over Time';

    protected ?string $description = 'Daily input and output token consumption';

    protected static bool $isDiscovered = false;

    protected int|string|array $columnSpan = 1;

    public ?string $filter = '30';

    protected function getData(): array
    {
        $days = (int) $this->filter;
        $endDate = today();
        $startDate = today()->subDays($days - 1);

        $data = AgentMetric::query()
            ->selectRaw('DATE(date) as date, SUM(total_input_tokens) as input_tokens, SUM(total_output_tokens) as output_tokens')
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Input Tokens',
                    'data' => $data->pluck('input_tokens')->toArray(),
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'Output Tokens',
                    'data' => $data->pluck('output_tokens')->toArray(),
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $data->pluck('date')->map(fn ($date) => Carbon::parse($date)->format('M j'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getFilters(): ?array
    {
        return [
            '7' => 'Last 7 days',
            '30' => 'Last 30 days',
            '90' => 'Last 90 days',
        ];
    }
}
