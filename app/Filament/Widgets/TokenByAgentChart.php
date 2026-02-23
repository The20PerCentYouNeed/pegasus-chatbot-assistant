<?php

namespace App\Filament\Widgets;

use App\Models\AgentMetric;
use Filament\Widgets\ChartWidget;

class TokenByAgentChart extends ChartWidget
{
    protected ?string $heading = 'Token Distribution by Agent';

    protected ?string $description = 'Total token usage per agent';

    protected static bool $isDiscovered = false;

    protected int|string|array $columnSpan = 1;

    public ?string $filter = '30';

    protected function getData(): array
    {
        $days = (int) $this->filter;
        $endDate = today();
        $startDate = today()->subDays($days - 1);

        $data = AgentMetric::query()
            ->whereBetween('date', [$startDate, $endDate])
            ->join('agents', 'agent_metrics_daily.agent_id', '=', 'agents.id')
            ->selectRaw('agents.name, SUM(total_tokens) as tokens')
            ->groupBy('agents.name')
            ->orderByDesc('tokens')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Total Tokens',
                    'data' => $data->pluck('tokens')->toArray(),
                    'backgroundColor' => [
                        'rgb(59, 130, 246)',
                        'rgb(34, 197, 94)',
                        'rgb(234, 179, 8)',
                        'rgb(239, 68, 68)',
                        'rgb(168, 85, 247)',
                        'rgb(236, 72, 153)',
                    ],
                ],
            ],
            'labels' => $data->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
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
