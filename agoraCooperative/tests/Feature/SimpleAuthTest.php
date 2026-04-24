<?php

namespace Tests\Feature;

use App\Models\Membre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SimpleAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_with_valid_credentials()
    {
        $user = Membre::factory()->create([
            'email' => 'test@example.com',
            'mot_de_passe' => bcrypt('password123'),
            'est_actif' => true,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'mot_de_passe' => 'password123',
        ]);

        $response->assertStatus(200);
    }

    public function test_login_with_invalid_credentials()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'invalid@example.com',
            'mot_de_passe' => 'wrongpassword',
        ]);

        $response->assertStatus(401);
    }

    public function test_logout_successfully()
    {
        $user = $this->createMemberUser();
        $this->authenticateUser($user);

        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(204);
    }

    public function test_get_authenticated_user()
    {
        $user = $this->createMemberUser();
        $this->authenticateUser($user);

        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'code_membre' => $user->code_membre,
                    'email' => $user->email,
                ],
            ]);
    }

    public function test_unauthenticated_user_cannot_access_protected_routes()
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(401);
    }
}
