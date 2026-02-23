<?php

namespace App\Filament\Widgets;

use App\Models\Agent;
use App\Models\AgentMetric;
use App\Models\Conversation;
use App\Models\ConversationMessage;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AgentStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $today = today();
        $yesterday = today()->subDay();

        $todayMessages = ConversationMessage::whereDate('created_at', $today)->count();
        $yesterdayMessages = ConversationMessage::whereDate('created_at', $yesterday)->count();
        $messagesChange = $yesterdayMessages > 0
            ? round((($todayMessages - $yesterdayMessages) / $yesterdayMessages) * 100, 1)
            : 0;

        $todayConversations = Conversation::whereDate('created_at', $today)->count();

        $activeAgents = Agent::active()->count();

        $todayTokens = ConversationMessage::whereDate('created_at', $today)
            ->where('role', 'assistant')
            ->whereNotNull('usage')
            ->get()
            ->sum(function ($msg) {
                $usage = $msg->usage;

                return ($usage['prompt_tokens'] ?? 0) + ($usage['completion_tokens'] ?? 0);
            });

        return [
            Stat::make('Messages Today', number_format($todayMessages))
                ->description(
                    $messagesChange >= 0
                        ? "+{$messagesChange}% from yesterday"
                        : "{$messagesChange}% from yesterday"
                )
                ->descriptionIcon(
                    $messagesChange >= 0
                        ? 'heroicon-m-arrow-trending-up'
                        : 'heroicon-m-arrow-trending-down'
                )
                ->color($messagesChange >= 0 ? 'success' : 'danger')
                ->chart($this->getWeeklyMessagesChart()),

            Stat::make('Conversations Today', number_format($todayConversations))
                ->description('New conversations started')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('primary'),

            Stat::make('Active Agents', $activeAgents)
                ->description('Currently enabled')
                ->descriptionIcon('heroicon-m-cpu-chip')
                ->color('success'),

            Stat::make('Tokens Today', number_format($todayTokens))
                ->description('Total input + output')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('info'),
        ];
    }

    private function getWeeklyMessagesChart(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $data[] = ConversationMessage::whereDate('created_at', today()->subDays($i))->count();
        }

        return $data;
    }
}
