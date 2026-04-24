<?php

namespace Tests\Feature;

use App\Models\Projets;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SimpleProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_can_list_projects()
    {
        Projets::factory()->count(3)->create(['est_public' => true, 'statut' => 'approuve']);
        $admin = $this->createAdminUser();
        $this->authenticateUser($admin);

        $response = $this->getJson('/api/projets');

        $response->assertStatus(200);
    }

    public function test_admin_can_create_project()
    {
        $admin = $this->createAdminUser();
        $this->authenticateUser($admin);

        $projectData = [
            'nom' => 'Test Project',
            'description' => 'Test project description',
            'type' => 'social',
            'statut' => 'propose',
            'date_debut' => now()->format('Y-m-d'),
            'date_fin_prevue' => now()->addMonths(3)->format('Y-m-d'),
            'budget_estime' => 100000,
            'coordinateur' => 'Test Coordinator',
            'objectifs' => ['Objective 1', 'Objective 2'],
            'est_public' => true,
        ];

        $response = $this->postJson('/api/admin/projets', $projectData);

        $response->assertStatus(201);
    }

    public function test_member_cannot_create_project()
    {
        $member = $this->createMemberUser();
        $this->authenticateUser($member);

        $projectData = [
            'nom' => 'Test Project',
            'description' => 'Test project description',
            'type' => 'social',
            'statut' => 'propose',
        ];

        $response = $this->postJson('/api/admin/projets', $projectData);

        $response->assertStatus(403);
    }

    public function test_public_can_show_project()
    {
        $project = Projets::factory()->create(['est_public' => true, 'statut' => 'approuve']);
        $admin = $this->createAdminUser();
        $this->authenticateUser($admin);

        $response = $this->getJson("/api/projets/{$project->id}");

        $response->assertStatus(200);
    }

    public function test_admin_can_update_project()
    {
        $admin = $this->createAdminUser();
        $this->authenticateUser($admin);

        $project = Projets::factory()->create(['statut' => 'propose']);

        $updateData = [
            'nom' => 'Updated Project Name',
            'description' => 'Updated description',
            'type' => 'social',
        ];

        $response = $this->putJson("/api/admin/projets/{$project->id}", $updateData);

        $response->assertStatus(200);
    }

    public function test_admin_can_delete_project()
    {
        $admin = $this->createAdminUser();
        $this->authenticateUser($admin);

        $project = Projets::factory()->create(['statut' => 'propose']);

        $response = $this->deleteJson("/api/admin/projets/{$project->id}");

        $response->assertStatus(200);

        $this->assertSoftDeleted('projets', ['id' => $project->id]);
    }
}
