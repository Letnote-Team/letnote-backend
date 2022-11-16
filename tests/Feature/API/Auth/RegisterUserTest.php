<?php

namespace Tests\Feature\API;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class RegisterUserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa o endpoint para registrar usuário
     *
     * @return void
     */
    public function test_user_register()
    {
        $user = User::factory(1)->makeOne();

        $response = $this->postJson(
            '/api/auth/register',
            array_merge($user->only(['email', 'name']), ['password' => 'password'])
        );

        $response->assertCreated();
        $response->assertJson(function (AssertableJson $json) use ($user) {
            $json->hasAll(['message', 'user', 'token']);

            $json->whereAllType([
                'message' => 'string',
                'user' => 'array',
                'token' => 'string',
            ]);

            $json->whereContains('user', $user->makeHidden('email_verified_at'));
        });
    }
}
