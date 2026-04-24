<?php

namespace Tests\Feature;

use App\Models\Membre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SimpleMemberTestWorking extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_list_members()
    {
        Membre::factory()->count(3)->create(['est_actif' => true]);

        $admin = $this->createAdminUser();
        $this->authenticateUser($admin);

        $response = $this->getJson('/api/admin/membres');

        $response->assertStatus(200);
    }

    public function test_member_cannot_list_members()
    {
        Membre::factory()->count(3)->create(['est_actif' => true]);

        $member = $this->createMemberUser();
        $this->authenticateUser($member);

        $response = $this->getJson('/api/admin/membres');

        $response->assertStatus(403);
    }

    public function test_member_can_show_own_profile()
    {
        $member = $this->createMemberUser();
        $this->authenticateUser($member);

        $response = $this->getJson("/api/membres/{$member->code_membre}");

        $response->assertStatus(200);
    }

    public function test_member_cannot_show_other_member_profile()
    {
        $otherMember = Membre::factory()->create(['est_actif' => true]);
        $member = $this->createMemberUser();
        $this->authenticateUser($member);

        $response = $this->getJson("/api/membres/{$otherMember->code_membre}");

        $response->assertStatus(403);
    }

    public function test_member_can_update_own_profile()
    {
        $member = $this->createMemberUser();
        $this->authenticateUser($member);

        $updateData = [
            'nom' => 'Updated Name',
            'prenom' => 'Updated Prenom',
            'telephone' => '0123456789',
            'biographie' => 'Updated biography',
        ];

        $response = $this->putJson("/api/membres/{$member->code_membre}", $updateData);

        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_cannot_access_member_routes()
    {
        $member = Membre::factory()->create(['est_actif' => true]);

        $response = $this->getJson("/api/membres/{$member->code_membre}");
        $response->assertStatus(401);

        $response = $this->putJson("/api/membres/{$member->code_membre}", ['nom' => 'Test']);
        $response->assertStatus(401);
    }
}
