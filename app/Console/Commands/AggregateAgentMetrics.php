<?php

namespace App\Console\Commands;

use App\Models\Agent;
use App\Models\AgentMetric;
use App\Models\ConversationMessage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AggregateAgentMetrics extends Command
{
    protected $signature = 'metrics:aggregate {--date= : Date to aggregate (Y-m-d), defaults to yesterday}';

    protected $description = 'Aggregate daily agent metrics from conversation messages';

    public function handle(): int
    {
        $date = $this->option('date') ?? now()->subDay()->format('Y-m-d');

        $this->info("Aggregating metrics for {$date}...");

        $agents = Agent::all();

        foreach ($agents as $agent) {
            $messages = ConversationMessage::query()
                ->join('agent_conversations', 'agent_conversation_messages.conversation_id', '=', 'agent_conversations.id')
                ->where('agent_conversation_messages.agent', $agent->agent_type)
                ->whereDate('agent_conversation_messages.created_at', $date);

            $totalMessages = (clone $messages)->count();

            if ($totalMessages === 0) {
                continue;
            }

            $userMessages = (clone $messages)->where('agent_conversation_messages.role', 'user')->count();
            $agentMessages = (clone $messages)->where('agent_conversation_messages.role', 'assistant')->count();

            $totalConversations = (clone $messages)
                ->distinct('agent_conversation_messages.conversation_id')
                ->count('agent_conversation_messages.conversation_id');

            $uniqueUsers = (clone $messages)
                ->distinct('agent_conversations.user_id')
                ->count('agent_conversations.user_id');

            $tokenData = DB::table('agent_conversation_messages')
                ->join('agent_conversations', 'agent_conversation_messages.conversation_id', '=', 'agent_conversations.id')
                ->where('agent_conversation_messages.agent', $agent->agent_type)
                ->whereDate('agent_conversation_messages.created_at', $date)
                ->where('agent_conversation_messages.role', 'assistant')
                ->whereNotNull('agent_conversation_messages.usage')
                ->get(['agent_conversation_messages.usage']);

            $totalInputTokens = 0;
            $totalOutputTokens = 0;

            foreach ($tokenData as $row) {
                $usage = json_decode($row->usage, true);
                $totalInputTokens += $usage['prompt_tokens'] ?? 0;
                $totalOutputTokens += $usage['completion_tokens'] ?? 0;
            }

            $totalTokens = $totalInputTokens + $totalOutputTokens;
            $avgResponseTokens = $agentMessages > 0 ? (int) round($totalOutputTokens / $agentMessages) : 0;

            AgentMetric::updateOrCreate(
                ['agent_id' => $agent->id, 'date' => $date],
                [
                    'total_messages' => $totalMessages,
                    'user_messages' => $userMessages,
                    'agent_messages' => $agentMessages,
                    'total_conversations' => $totalConversations,
                    'total_input_tokens' => $totalInputTokens,
                    'total_output_tokens' => $totalOutputTokens,
                    'total_tokens' => $totalTokens,
                    'avg_response_tokens' => $avgResponseTokens,
                    'unique_users' => $uniqueUsers,
                    'error_count' => 0,
                ]
            );

            $this->line("  {$agent->name}: {$totalMessages} messages, {$totalTokens} tokens");
        }

        $this->info('Aggregation complete.');

        return self::SUCCESS;
    }
}
