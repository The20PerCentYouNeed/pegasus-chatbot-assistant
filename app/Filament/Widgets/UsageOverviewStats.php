<?php

namespace App\Filament\Widgets;

use App\Models\AgentMetric;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UsageOverviewStats extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static bool $isDiscovered = false;

    protected function getStats(): array
    {
        $startDate = $this->pageFilters['startDate'] ?? now()->subDays(30)->format('Y-m-d');
        $endDate = $this->pageFilters['endDate'] ?? now()->format('Y-m-d');
        $agentId = $this->pageFilters['agentId'] ?? null;

        $query = AgentMetric::query()
            ->whereBetween('date', [$startDate, $endDate]);

        if ($agentId) {
            $query->where('agent_id', $agentId);
        }

        $totalTokens = $query->sum('total_tokens');
        $totalMessages = $query->sum('total_messages');
        $tokensPerMessage = $totalMessages > 0 ? round($totalTokens / $totalMessages) : 0;
        $totalConversations = $query->sum('total_conversations');

        return [
            Stat::make('Total Tokens', number_format($totalTokens))
                ->description(number_format($totalMessages) . ' messages')
                ->icon('heroicon-o-calculator')
                ->color('primary'),

            Stat::make('Tokens per Message', number_format($tokensPerMessage))
                ->description('Average across all agents')
                ->icon('heroicon-o-chart-bar')
                ->color('success'),

            Stat::make('Total Conversations', number_format($totalConversations))
                ->description('In selected period')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color('warning'),
        ];
    }
}
