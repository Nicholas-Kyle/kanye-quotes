<?php

namespace App\Jobs;

use App\Enums\CacheKeysEnum;
use App\Services\QuoteServices\QuoteServiceManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class RefreshQuotesCacheJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
    }

    public function handle(): void
    {
        $initialQuotes = Cache::get(CacheKeysEnum::KANYE_INITIAL_QUOTES->value, []);
        $refreshQuotes = app(QuoteServiceManager::class)
            ->driver('kanye')
            ->fetchUniqueQuotes(desiredCount: 5, existingQuotes: $initialQuotes);

        Cache::put(CacheKeysEnum::KANYE_REFRESH_QUOTES->value, $refreshQuotes);
    }
}
