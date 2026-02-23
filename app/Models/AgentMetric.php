<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentMetric extends Model
{
    protected $table = 'agent_metrics_daily';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'total_messages' => 'integer',
            'user_messages' => 'integer',
            'agent_messages' => 'integer',
            'total_conversations' => 'integer',
            'total_input_tokens' => 'integer',
            'total_output_tokens' => 'integer',
            'total_tokens' => 'integer',
            'avg_response_tokens' => 'integer',
            'unique_users' => 'integer',
            'error_count' => 'integer',
        ];
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function scopeForAgent($query, int $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    public function scopeForDateRange($query, string $startDate, string $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }
}
