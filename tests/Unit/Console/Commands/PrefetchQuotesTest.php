<?php

namespace Tests\Unit\Console\Commands;

use App\Console\Commands\PrefetchQuotes;
use App\Enums\CacheKeysEnum;
use App\Services\QuoteServices\QuoteServiceManager;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Tests\TestCase;

class PrefetchQuotesTest extends TestCase
{
    public function test_handle_caches_quotes_correctly()
    {
        $initialQuotes = ['Quote 1', 'Quote 2', 'Quote 3', 'Quote 4', 'Quote 5'];
        $refreshQuotes = ['Quote 6', 'Quote 7', 'Quote 8', 'Quote 9', 'Quote 10'];

        $quoteServiceManagerMock = Mockery::mock(QuoteServiceManager::class);
        $quoteServiceManagerMock->shouldReceive('driver')
            ->with('kanye')
            ->andReturnSelf();
        $quoteServiceManagerMock->shouldReceive('fetchUniqueQuotes')
            ->with(5, [])
            ->andReturn($initialQuotes);
        $quoteServiceManagerMock->shouldReceive('fetchUniqueQuotes')
            ->with(5, $initialQuotes)
            ->andReturn($refreshQuotes);

        app()->instance(QuoteServiceManager::class, $quoteServiceManagerMock);

        Cache::shouldReceive('rememberForever')
            ->with(CacheKeysEnum::KANYE_INITIAL_QUOTES->value, Mockery::type('Closure'))
            ->once()
            ->andReturn($initialQuotes);
        Cache::shouldReceive('put')
            ->with(CacheKeysEnum::KANYE_REFRESH_QUOTES->value, $refreshQuotes)
            ->once();

        $command = new PrefetchQuotes();

        $command->handle();
    }
}
