<?php

namespace Tests\Unit\Http\Controllers;

use App\Enums\CacheKeysEnum;
use App\Http\Controllers\KanyeQuoteController;
use App\Jobs\RefreshQuotesCacheJob;
use App\Services\QuoteServices\QuoteServiceManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use Mockery;

class KanyeQuoteControllerTest extends TestCase
{
    public function testIndex()
    {
        $initialQuotes = ['quote1', 'quote2', 'quote3', 'quote4', 'quote5'];

        Cache::shouldReceive('rememberForever')
            ->once()
            ->with(CacheKeysEnum::KANYE_INITIAL_QUOTES->value, Mockery::type('Closure'))
            ->andReturn($initialQuotes);

        $controller = new KanyeQuoteController();
        $response = $controller->index();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(['quotes' => $initialQuotes], $response->getData(true));
    }

    public function testRefresh()
    {
        $initialQuotes = ['Quote 1', 'Quote 2', 'Quote 3', 'Quote 4', 'Quote 5'];
        $refreshQuotes = ['Quote 6', 'Quote 7', 'Quote 8', 'Quote 9', 'Quote 10'];

        Cache::shouldReceive('pull')
            ->once()
            ->with(CacheKeysEnum::KANYE_REFRESH_QUOTES->value, Mockery::type('Closure'))
            ->andReturnUsing(function ($key, $closure) use ($refreshQuotes) {
                return $closure();
            });

        Cache::shouldReceive('get')
            ->once()
            ->with(CacheKeysEnum::KANYE_INITIAL_QUOTES->value, [])
            ->andReturn($initialQuotes);

        $quoteServiceManagerMock = Mockery::mock(QuoteServiceManager::class);
        $quoteServiceManagerMock->shouldReceive('driver')
            ->with('kanye')
            ->andReturnSelf();
        $quoteServiceManagerMock->shouldReceive('fetchUniqueQuotes')
            ->with(5, $initialQuotes)
            ->andReturn($refreshQuotes);

        app()->instance(QuoteServiceManager::class, $quoteServiceManagerMock);

        Queue::fake();

        $controller = new KanyeQuoteController();
        $response = $controller->refresh();

        Queue::assertPushed(RefreshQuotesCacheJob::class);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(['quotes' => $refreshQuotes], $response->getData(true));
    }
}
