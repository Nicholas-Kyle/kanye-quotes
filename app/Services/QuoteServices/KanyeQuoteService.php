<?php

namespace App\Services\QuoteServices;

use App\Contracts\Services\QuoteServices\QuoteServiceInterface;
use Illuminate\Support\Facades\Http;

class KanyeQuoteService implements QuoteServiceInterface
{
    private const MAX_ATTEMPTS = 20;

    public function fetchUniqueQuotes(int $desiredCount, array $existingQuotes): array
    {
        $quotes = [];
        $attempts = 0;

        while ($this->shouldContinue($attempts, $desiredCount, $quotes)) {
            $quote = $this->attemptToFetchUniqueQuote($existingQuotes, $quotes);
            if ($quote) {
                $quotes[] = $quote;
            }
            $attempts++;
        }

        return $quotes;
    }

    private function shouldContinue(int $attempts, int $desiredCount, array $quotes): bool
    {
        return count($quotes) < $desiredCount && $attempts < self::MAX_ATTEMPTS;
    }

    private function attemptToFetchUniqueQuote(array $existingQuotes, array $quotes): ?string
    {
        $quote = $this->fetchQuote();
        if ($this->isUniqueQuote($quote, $existingQuotes, $quotes)) {
            return $quote;
        }
        return null;
    }

    private function fetchQuote(): string
    {
        $response = Http::get('https://api.kanye.rest/');

        if ($response->successful()) {
            return $response->json()['quote'] ?? 'Fallback quote in case of missing field';
        } else {
            return 'Fallback quote in case of failed API request';
        }
    }

    private function isUniqueQuote(string $quote, array $existingQuotes, array $quotes): bool
    {
        return !in_array($quote, $existingQuotes) && !in_array($quote, $quotes);
    }
}
