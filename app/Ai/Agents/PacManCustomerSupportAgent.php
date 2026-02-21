<?php

namespace App\Ai\Agents;

use App\Ai\Middleware\TrackTokenUsage;
use App\Ai\Tools\VoucherLookup;
use App\Models\Agent;
use Laravel\Ai\Concerns\RemembersConversations;
use Laravel\Ai\Contracts\Agent as AgentContract;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasMiddleware;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Promptable;
use Laravel\Ai\Providers\Tools\FileSearch;
use Stringable;

class PacManCustomerSupportAgent implements AgentContract, Conversational, HasTools, HasMiddleware
{
    use Promptable, RemembersConversations;

    public Agent $config;

    public function __construct()
    {
        $this->config = Agent::for(static::class);
    }

    public function instructions(): Stringable|string
    {
        return $this->config->system_prompt;
    }

    public function provider(): string
    {
        return $this->config->provider;
    }

    public function model(): string
    {
        return $this->config->model;
    }

    /**
     * @return \Laravel\Ai\Contracts\Tool[]
     */
    public function tools(): iterable
    {
        $tools = [
            new VoucherLookup,
        ];

        if ($this->config->vector_store_id) {
            $tools[] = new FileSearch(stores: [$this->config->vector_store_id]);
        }

        return $tools;
    }

    public function middleware(): array
    {
        return [
            new TrackTokenUsage,
        ];
    }

    protected function maxConversationMessages(): int
    {
        return $this->config->max_conversation_messages;
    }
}
