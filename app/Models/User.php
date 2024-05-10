<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = ['name', 'api_token'];

    protected function apiToken(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => hash('sha256', $value),
        );
    }

    public function scopeByToken(Builder $query, string $token): Builder
    {
        $hashedToken = hash('sha256', $token);
        return $query->where('api_token', $hashedToken);
    }
}
