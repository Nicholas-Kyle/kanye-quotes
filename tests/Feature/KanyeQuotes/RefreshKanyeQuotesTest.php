<?php

namespace Tests\Feature\KanyeQuotes;

use App\Models\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class RefreshKanyeQuotesTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->token = (string) Str::uuid();
        User::factory()->create([
            'api_token' => $this->token
        ]);
    }

    public function test_the_refresh_quotes_endpoint_returns_a_successful_response()
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->post('/api/kanye-quotes/refresh');

        $response->assertStatus(200);
    }

    public function test_the_refresh_quotes_endpoint_returns_an_array_of_quotes()
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->post('/api/kanye-quotes/refresh');

        $response->assertJsonStructure(['quotes' => []]);
    }

    public function test_the_refresh_quotes_endpoint_returns_exactly_five_quotes()
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->post('/api/kanye-quotes/refresh');

        $response->assertJsonCount(5, 'quotes');
    }

    public function test_each_quote_from_the_refresh_endpoint_is_a_string()
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->post('/api/kanye-quotes/refresh');

        $json = $response->json();
        foreach ($json['quotes'] as $quote) {
            $this->assertIsString($quote);
        }
    }

    public function test_refresh_quotes_do_not_contain_any_initial_quotes()
    {
        $initialResponse = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->get('/api/kanye-quotes');
        $initialQuotes = $initialResponse->json()['quotes'];

        $refreshResponse = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->post('/api/kanye-quotes/refresh');
        $refreshQuotes = $refreshResponse->json()['quotes'];

        $allQuotes = array_merge($initialQuotes, $refreshQuotes);
        $uniqueQuotes = array_unique($allQuotes);

        $this->assertSameSize($allQuotes, $uniqueQuotes, "Quotes across multiple calls should be unique");
    }
}
