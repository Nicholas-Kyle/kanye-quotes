<?php

namespace Tests\Unit\Services\QuoteServices;

use App\Services\QuoteServices\KanyeQuoteService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class KanyeQuoteServiceTest extends TestCase
{
    public function test_fetch_unique_quotes_returns_correct_number_of_unique_quotes()
    {
        Http::fake([
            'https://api.kanye.rest/' => Http::sequence()
                ->push(['quote' => 'I am a Creative'], 200)
                ->push(['quote' => 'I am a God'], 200)
                ->push(['quote' => 'I am a Genius'], 200),
        ]);

        $service = new KanyeQuoteService();
        $existingQuotes = [];
        $desiredCount = 3;

        $quotes = $service->fetchUniqueQuotes($desiredCount, $existingQuotes);

        $this->assertCount($desiredCount, $quotes);
        $this->assertEquals(['I am a Creative', 'I am a God', 'I am a Genius'], $quotes);
    }

    public function test_fetch_unique_quotes_does_not_exceed_max_attempts()
    {
        Http::fake([
            'https://api.kanye.rest/' => Http::response(['quote' => 'I am a God'], 200),
        ]);

        $service = new KanyeQuoteService();
        $existingQuotes = ['I am a God'];
        $desiredCount = 5;

        $quotes = $service->fetchUniqueQuotes($desiredCount, $existingQuotes);

        $this->assertLessThanOrEqual(20, count($quotes));
    }

    public function test_fetch_unique_quotes_handles_api_failure_gracefully()
    {
        Http::fake([
            'https://api.kanye.rest/' => Http::response(null, 500),
        ]);

        $service = new KanyeQuoteService();
        $existingQuotes = [];
        $desiredCount = 1;

        $quotes = $service->fetchUniqueQuotes($desiredCount, $existingQuotes);

        $this->assertCount($desiredCount, $quotes);
        $this->assertEquals(['Fallback quote in case of failed API request'], $quotes);
    }

    public function test_fetch_unique_quotes_handles_duplicate_quotes_correctly()
    {
        Http::fake([
            'https://api.kanye.rest/' => Http::sequence()
                ->push(['quote' => 'I am a God'], 200)
                ->push(['quote' => 'I am a Genius'], 200)
                ->push(['quote' => 'I am a God'], 200)
                ->push(['quote' => 'I am a Creative'], 200),
        ]);

        $service = new KanyeQuoteService();
        $existingQuotes = [];
        $desiredCount = 3;

        $quotes = $service->fetchUniqueQuotes($desiredCount, $existingQuotes);

        $this->assertCount($desiredCount, $quotes);
        $this->assertEquals(['I am a God', 'I am a Genius', 'I am a Creative'], $quotes);
    }
}
