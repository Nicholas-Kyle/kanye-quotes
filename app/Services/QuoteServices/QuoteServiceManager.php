<?php

namespace App\Services\QuoteServices;

use App\Contracts\Services\QuoteServices\QuoteServiceInterface;
use Illuminate\Support\Manager;

class QuoteServiceManager extends Manager implements QuoteServiceInterface
{
    public function createKanyeDriver(): KanyeQuoteService
    {
        return new KanyeQuoteService();
    }

    public function fetchUniqueQuotes(int $desiredCount, array $existingQuotes): array
    {
        return $this->driver()->fetchUniqueQuotes($desiredCount, $existingQuotes);
    }

    public function getDefaultDriver(): string
    {
        return config('quote_service.driver', 'kanye');
    }
}
