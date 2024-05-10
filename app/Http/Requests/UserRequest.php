<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;
use Illuminate\Support\Str;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
        ];
    }

    public function action(): string
    {
        $token = (string) Str::uuid();

        User::create([
            'name'      => $this->input('name'),
            'api_token' => $token,
        ]);

        return $token;
    }
}
