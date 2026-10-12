<?php

namespace Tests\Feature\Auth;

use App\Services\User\UserService;
use Mockery;
use Tests\TestCase;

class ApiLoginTest extends TestCase
{
    public function test_unknown_email_returns_authentication_error_instead_of_validation_error(): void
    {
        $this->app->detectEnvironment(fn () => 'testing');

        $service = Mockery::mock(UserService::class);
        $service->shouldReceive('login')
            ->once()
            ->andReturn(response()->json(['message' => 'Email ou senha incorretos'], 401));

        $this->app->instance(UserService::class, $service);

        $this->postJson('/api/auth/login', [
            'email' => 'nao-existe@example.com',
            'password' => 'password123',
        ])
            ->assertUnauthorized()
            ->assertExactJson(['message' => 'Email ou senha incorretos']);
    }
}
