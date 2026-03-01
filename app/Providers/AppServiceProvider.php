<?php

namespace App\Providers;

use App\Services\PegasusClient;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('pegasus_client', PegasusClient::class);
    }

    public function boot(): void
    {
        RateLimiter::for('chat-init', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('chat-message', function (Request $request) {
            $limit = config('pacman.chat.rate_limit', 20);

            return Limit::perMinute($limit)->by($request->user()?->id ?: $request->ip());
        });
    }
}
