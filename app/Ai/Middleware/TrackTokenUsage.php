<?php

namespace App\Ai\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Prompts\AgentPrompt;
use Laravel\Ai\Responses\AgentResponse;

class TrackTokenUsage
{
    public function handle(AgentPrompt $prompt, Closure $next)
    {
        return $next($prompt)->then(function (AgentResponse $response) use ($prompt) {
            if ($response->usage) {
                Log::channel('daily')->info('Agent token usage', [
                    'agent' => $prompt->agent::class,
                    'input_tokens' => $response->usage->promptTokens,
                    'output_tokens' => $response->usage->completionTokens,
                ]);
            }
        });
    }
}
