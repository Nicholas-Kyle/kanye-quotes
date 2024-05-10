<?php

namespace Tests\Unit\Jobs;

use App\Enums\CacheKeysEnum;
use App\Jobs\RefreshQuotesCacheJob;
use App\Services\QuoteServices\QuoteServiceManager;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Tests\TestCase;

class RefreshQuotesCacheJobTest extends TestCase
{
    public function test_handle_caches_refreshed_quotes()
    {
        $initialQuotes = ['Quote 1', 'Quote 2', 'Quote 3', 'Quote 4', 'Quote 5'];
        $refreshQuotes = ['Quote 6', 'Quote 7', 'Quote 8', 'Quote 9', 'Quote 10'];

        Cache::shouldReceive('get')
            ->with(CacheKeysEnum::KANYE_INITIAL_QUOTES->value, [])
            ->once()
            ->andReturn($initialQuotes);

        Cache::shouldReceive('put')
            ->with(CacheKeysEnum::KANYE_REFRESH_QUOTES->value, $refreshQuotes)
            ->once();

        $quoteServiceManagerMock = Mockery::mock(QuoteServiceManager::class);
        $quoteServiceManagerMock->shouldReceive('driver')
            ->with('kanye')
            ->andReturnSelf();
        $quoteServiceManagerMock->shouldReceive('fetchUniqueQuotes')
            ->with(5, $initialQuotes)
            ->andReturn($refreshQuotes);

        app()->instance(QuoteServiceManager::class, $quoteServiceManagerMock);

        $job = new RefreshQuotesCacheJob();
        $job->handle();
    }
}

