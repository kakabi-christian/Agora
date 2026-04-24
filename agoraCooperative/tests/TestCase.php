<?php

namespace Tests;

use App\Models\Membre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Configuration pour les tests
        $this->artisan('config:clear');
    }

    /**
     * Créer un utilisateur admin pour les tests
     */
    protected function createAdminUser()
    {
        return Membre::factory()->create([
            'role' => 'administrateur',
            'est_actif' => true,
        ]);
    }

    protected function createMemberUser()
    {
        return Membre::factory()->create([
            'role' => 'membre',
            'est_actif' => true,
        ]);
    }

    protected function createInactiveMemberUser()
    {
        return Membre::factory()->create([
            'role' => 'membre',
            'est_actif' => false,
        ]);
    }

    /**
     * Authentifier un utilisateur et retourner le token
     */
    protected function authenticateUser($user)
    {
        $token = $user->createToken('test-token')->plainTextToken;
        $this->withHeader('Authorization', 'Bearer '.$token);

        return $token;
    }

    /**
     * Créer des données de test pour les événements
     */
    protected function createEventData()
    {
        return [
            'titre' => 'Test Event',
            'description' => 'Test event description',
            'date_debut' => now()->addDays(7)->format('Y-m-d H:i:s'),
            'date_fin' => now()->addDays(7)->addHours(2)->format('Y-m-d H:i:s'),
            'lieu' => 'Test Location',
            'frais_inscription' => 5000,
            'places_disponibles' => 50,
            'type' => 'formation',
            'statut' => 'planifie',
        ];
    }

    /**
     * Créer des données de test pour les projets
     */
    protected function createProjectData()
    {
        return [
            'nom' => 'Test Project',
            'description' => 'Test project description',
            'type' => 'social',
            'statut' => 'propose',
            'date_debut' => now()->format('Y-m-d'),
            'date_fin_prevue' => now()->addMonths(3)->format('Y-m-d'),
            'budget_estime' => 100000,
            'coordinateur' => 'Test Coordinator',
            'objectifs' => json_encode(['Objective 1', 'Objective 2']),
            'est_public' => true,
        ];
    }
}
