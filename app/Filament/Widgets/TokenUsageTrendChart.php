<?php

namespace App\Filament\Widgets;

use App\Models\AgentMetric;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class TokenUsageTrendChart extends ChartWidget
{
    protected ?string $heading = 'Token Usage Trends (Last 30 Days)';

    protected ?string $description = 'Daily input and output token consumption';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $endDate = today();
        $startDate = today()->subDays(29);

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
                ],
                [
                    'label' => 'Output Tokens',
                    'data' => $data->pluck('output_tokens')->toArray(),
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                ],
            ],
            'labels' => $data->pluck('date')->map(fn ($date) => Carbon::parse($date)->format('M j'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['display' => true],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Tokens',
                    ],
                ],
            ],
        ];
    }
}
