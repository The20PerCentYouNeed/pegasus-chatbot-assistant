<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agent extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'agent_type',
        'system_prompt',
        'model',
        'provider',
        'temperature',
        'max_tokens',
        'max_steps',
        'vector_store_id',
        'max_conversation_messages',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'temperature' => 'decimal:1',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Cached instances per request to avoid repeated queries.
     *
     * @var array<string, static>
     */
    protected static array $resolved = [];

    /**
     * Resolve the agent configuration for a given agent class.
     */
    public static function for(string $agentType): static
    {
        return static::$resolved[$agentType] ??= static::where('agent_type', $agentType)->firstOrFail();
    }

    /**
     * Clear the per-request cache (useful in tests).
     */
    public static function flushResolved(): void
    {
        static::$resolved = [];
    }

    public function knowledgeDocuments(): HasMany
    {
        return $this->hasMany(KnowledgeDocument::class);
    }

    public function metrics(): HasMany
    {
        return $this->hasMany(AgentMetric::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
