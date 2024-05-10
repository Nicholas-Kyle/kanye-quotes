<?php

namespace Tests\Feature\KanyeQuotes;

use App\Models\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class ListKanyeQuotesTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->token = (string) Str::uuid();
        User::factory()->create([
            'api_token' => $this->token
        ]);
    }

    public function test_the_quotes_endpoint_returns_a_successful_response()
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->get('/api/kanye-quotes');

        $response->assertStatus(200);
    }

    public function test_the_quotes_endpoint_returns_an_array_of_quotes()
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->get('/api/kanye-quotes');

        $response->assertJsonStructure(['quotes' => []]);
    }

    public function test_the_quotes_endpoint_returns_exactly_five_quotes()
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->get('/api/kanye-quotes');

        $response->assertJsonCount(5, 'quotes');
    }

    public function test_each_quote_in_the_response_is_a_string()
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->get('/api/kanye-quotes');

        $json = $response->json();
        foreach ($json['quotes'] as $quote) {
            $this->assertIsString($quote);
        }
    }
}
