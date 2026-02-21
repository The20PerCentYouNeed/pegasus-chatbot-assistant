<?php

namespace App\Filament\Widgets;

use App\Models\Conversation;
use App\Models\ConversationMessage;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ConversationStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalConversations = Conversation::count();
        $todayConversations = Conversation::whereDate('created_at', today())->count();
        $totalMessages = ConversationMessage::count();
        $avgMessages = $totalConversations > 0
            ? round($totalMessages / $totalConversations, 1)
            : 0;

        return [
            Stat::make('Total Conversations', $totalConversations),
            Stat::make("Today's Conversations", $todayConversations),
            Stat::make('Total Messages', $totalMessages),
            Stat::make('Avg Messages / Conversation', $avgMessages),
        ];
    }
}
