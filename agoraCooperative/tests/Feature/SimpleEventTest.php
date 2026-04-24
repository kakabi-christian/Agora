<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Evenements;
use App\Models\Membre;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SimpleEventTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_can_list_events()
    {
        Evenements::factory()->count(3)->create(['statut' => 'planifie']);

        $response = $this->getJson('/api/evenements');

        $response->assertStatus(200);
    }

    public function test_admin_can_create_event()
    {
        $admin = $this->createAdminUser();
        $this->authenticateUser($admin);

        $eventData = [
            'titre' => 'Test Event',
            'description' => 'Test event description',
            'date_debut' => now()->addDays(7)->format('Y-m-d H:i:s'),
            'lieu' => 'Test Location',
            'type' => 'formation',
            'frais_inscription' => 5000,
            'places_disponibles' => 50,
        ];

        $response = $this->postJson('/api/admin/evenements', $eventData);

        $response->assertStatus(201);
    }

    public function test_member_cannot_create_event()
    {
        $member = $this->createMemberUser();
        $this->authenticateUser($member);

        $eventData = [
            'titre' => 'Test Event',
            'description' => 'Test event description',
            'date_debut' => now()->addDays(7)->format('Y-m-d H:i:s'),
            'lieu' => 'Test Location',
            'type' => 'formation',
        ];

        $response = $this->postJson('/api/admin/evenements', $eventData);

        $response->assertStatus(403);
    }

    public function test_public_can_show_event()
    {
        $event = Evenements::factory()->create(['statut' => 'planifie']);
        $admin = $this->createAdminUser();
        $this->authenticateUser($admin);

        $response = $this->getJson("/api/evenements/{$event->code_evenement}");

        $response->assertStatus(200);
    }

    public function test_admin_can_update_event()
    {
        $admin = $this->createAdminUser();
        $this->authenticateUser($admin);

        $event = Evenements::factory()->create(['statut' => 'planifie']);

        $updateData = [
            'titre' => 'Updated Event Title',
            'description' => 'Updated description',
        ];

        $response = $this->putJson("/api/admin/evenements/{$event->code_evenement}", $updateData);

        $response->assertStatus(200);
    }

    public function test_admin_can_delete_event()
    {
        $admin = $this->createAdminUser();
        $this->authenticateUser($admin);

        $event = Evenements::factory()->create(['statut' => 'planifie']);

        $response = $this->deleteJson("/api/admin/evenements/{$event->code_evenement}");

        $response->assertStatus(200);
        
        $this->assertSoftDeleted('evenements', ['code_evenement' => $event->code_evenement]);
    }
}
