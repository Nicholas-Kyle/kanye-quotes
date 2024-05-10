<?php

namespace App\Contracts\Services\QuoteServices;

interface QuoteServiceInterface
{
    public function fetchUniqueQuotes(int $desiredCount, array $existingQuotes): array;
}
