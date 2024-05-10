<?php

namespace App\Http\Controllers;

use App\Enums\CacheKeysEnum;
use App\Jobs\RefreshQuotesCacheJob;
use App\Services\QuoteServices\QuoteServiceManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class KanyeQuoteController extends Controller
{
    public function index(): JsonResponse
    {
        $quotes = Cache::rememberForever(CacheKeysEnum::KANYE_INITIAL_QUOTES->value, function () {
            return app(QuoteServiceManager::class)
                ->driver('kanye')
                ->fetchUniqueQuotes(desiredCount: 5, existingQuotes: []);
        });

        return response()->json(['quotes' => $quotes]);
    }

    public function refresh(): JsonResponse
    {
        $quotes = Cache::pull(CacheKeysEnum::KANYE_REFRESH_QUOTES->value, function () {
            $initialQuotes = Cache::get(CacheKeysEnum::KANYE_INITIAL_QUOTES->value, []);
            return app(QuoteServiceManager::class)
                ->driver('kanye')
                ->fetchUniqueQuotes(desiredCount: 5, existingQuotes: $initialQuotes);
        });

        dispatch(new RefreshQuotesCacheJob());

        return response()->json(['quotes' => $quotes]);
    }
}
