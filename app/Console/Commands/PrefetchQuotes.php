<?php

namespace App\Console\Commands;

use App\Enums\CacheKeysEnum;
use App\Services\QuoteServices\QuoteServiceManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class PrefetchQuotes extends Command
{
    protected $signature = 'cache:prefetch-quotes';
    protected $description = 'Fetches quotes from an external API and caches them.';

    public function handle()
    {
        $initialQuotes = Cache::rememberForever(CacheKeysEnum::KANYE_INITIAL_QUOTES->value, function () {
            return app(QuoteServiceManager::class)
                ->driver('kanye')
                ->fetchUniqueQuotes(desiredCount: 5, existingQuotes: []);
        });

        $refreshQuotes = app(QuoteServiceManager::class)
            ->driver('kanye')
            ->fetchUniqueQuotes(desiredCount: 5, existingQuotes: $initialQuotes);

        Cache::put(CacheKeysEnum::KANYE_REFRESH_QUOTES->value, $refreshQuotes);
    }
}
