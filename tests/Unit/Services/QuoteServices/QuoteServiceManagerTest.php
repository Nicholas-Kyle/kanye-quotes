<?php

namespace Tests\Unit\Services\QuoteServices;

use App\Contracts\Services\QuoteServices\QuoteServiceInterface;
use App\Services\QuoteServices\KanyeQuoteService;
use App\Services\QuoteServices\QuoteServiceManager;
use Mockery;
use Tests\TestCase;

class QuoteServiceManagerTest extends TestCase
{
    public function test_fetch_unique_quotes_delegates_to_quote_service_driver()
    {
        $manager = Mockery::mock(QuoteServiceManager::class)->makePartial();
        $quoteService = Mockery::mock(QuoteServiceInterface::class);

        $manager->shouldReceive('driver')->andReturn($quoteService);
        $quoteService->shouldReceive('fetchUniqueQuotes')->once()->with(3, ['existing'])->andReturn(['quote1', 'quote2', 'quote3']);

        $quotes = $manager->fetchUniqueQuotes(3, ['existing']);

        $this->assertEquals(['quote1', 'quote2', 'quote3'], $quotes);
    }

    public function test_create_kanye_driver_creates_kanye_quote_service()
    {
        $service = app(QuoteServiceManager::class)
            ->driver('kanye');

        $this->assertInstanceOf(KanyeQuoteService::class, $service);
    }

    public function test_get_default_driver_returns_kanye()
    {
        config(['quote_service.driver' => 'kanye']);

        $manager = app(QuoteServiceManager::class);

        $this->assertEquals('kanye', $manager->getDefaultDriver());
    }
}
