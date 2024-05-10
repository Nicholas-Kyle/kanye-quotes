<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function store(UserRequest $userRequest): JsonResponse
    {
        $token = $userRequest->action();

        return response()->json(['api_token' => $token], 201);
    }
}
