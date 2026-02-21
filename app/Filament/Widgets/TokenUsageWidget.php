<?php

namespace App\Filament\Widgets;

use App\Models\ConversationMessage;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class TokenUsageWidget extends ChartWidget
{
    protected ?string $heading = 'Token Usage (Last 30 Days)';

    protected function getData(): array
    {
        $days = collect(range(29, 0))->map(fn ($i) => Carbon::today()->subDays($i));

        $messages = ConversationMessage::where('created_at', '>=', now()->subDays(30))
            ->whereNotNull('usage')
            ->get()
            ->groupBy(fn ($msg) => $msg->created_at->format('Y-m-d'));

        $inputTokens = [];
        $outputTokens = [];
        $labels = [];

        foreach ($days as $day) {
            $key = $day->format('Y-m-d');
            $labels[] = $day->format('M d');

            $dayMessages = $messages->get($key, collect());

            $inputTokens[] = $dayMessages->sum(fn ($msg) => $msg->usage['input_tokens'] ?? 0);
            $outputTokens[] = $dayMessages->sum(fn ($msg) => $msg->usage['output_tokens'] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Input Tokens',
                    'data' => $inputTokens,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                ],
                [
                    'label' => 'Output Tokens',
                    'data' => $outputTokens,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
