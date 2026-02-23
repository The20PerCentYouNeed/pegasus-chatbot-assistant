<?php

namespace App\Filament\Widgets;

use App\Models\AgentMetric;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class MessagesTrendChart extends ChartWidget
{
    protected ?string $heading = 'Messages Over Time (Last 30 Days)';

    protected ?string $description = 'Total, user, and agent message counts by day';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $endDate = today();
        $startDate = today()->subDays(29);

        $data = AgentMetric::query()
            ->selectRaw('DATE(date) as date, SUM(total_messages) as total, SUM(user_messages) as user_msgs, SUM(agent_messages) as agent_msgs')
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Total Messages',
                    'data' => $data->pluck('total')->toArray(),
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'User Messages',
                    'data' => $data->pluck('user_msgs')->toArray(),
                    'borderColor' => 'rgb(34, 197, 94)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                ],
                [
                    'label' => 'Agent Messages',
                    'data' => $data->pluck('agent_msgs')->toArray(),
                    'borderColor' => 'rgb(168, 85, 247)',
                    'backgroundColor' => 'rgba(168, 85, 247, 0.1)',
                    'pointStyle' => 'triangle',
                    'pointRadius' => 6,
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
                'y' => ['beginAtZero' => true],
            ],
        ];
    }
}
