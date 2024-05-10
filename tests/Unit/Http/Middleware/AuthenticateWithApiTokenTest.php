<?php

namespace Tests\Unit\Http\Middleware;

use App\Http\Middleware\AuthenticateWithApiToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthenticateWithApiTokenTest extends TestCase
{
    use RefreshDatabase;

    public function test_handle_no_token_provided()
    {
        $middleware = new AuthenticateWithApiToken();

        $request = Request::create('/api/test', 'GET');

        $response = $middleware->handle($request, function () {
            return response()->json(['success' => 'Next middleware']);
        });

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals(['error' => 'Unauthorized'], $response->getData(true));
    }

    public function test_handle_invalid_token()
    {
        $middleware = new AuthenticateWithApiToken();

        $request = Request::create('/api/test', 'GET');
        $request->headers->set('Authorization', 'Bearer invalid_token');

        $response = $middleware->handle($request, function () {
            return response()->json(['success' => 'Next middleware']);
        });

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals(['error' => 'Unauthorized'], $response->getData(true));
    }

    public function test_handle_valid_token()
    {
        $token = (string) Str::uuid();
        $user = User::factory()->create([
            'api_token' => $token
        ]);

        $middleware = new AuthenticateWithApiToken();

        $request = Request::create('/api/test', 'GET');
        $request->headers->set('Authorization', 'Bearer ' . $token);

        $response = $middleware->handle($request, function () {
            return response()->json(['success' => 'Next middleware']);
        });

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['success' => 'Next middleware'], $response->getData(true));
        $this->assertInstanceOf(User::class, auth()->user());
        $this->assertEquals($user->id, auth()->id());
    }
}
