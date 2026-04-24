<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Projets;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SimpleProjetTest extends TestCase
{
    use RefreshDatabase;

    public function test_projet_factory_creates_valid_projet()
    {
        $projet = Projets::factory()->create();

        $this->assertInstanceOf(Projets::class, $projet);
        $this->assertNotNull($projet->id);
        $this->assertNotNull($projet->nom);
        $this->assertNotNull($projet->description);
        $this->assertNotNull($projet->type);
        $this->assertNotNull($projet->statut);
    }

    public function test_projet_fillable_attributes()
    {
        $projet = new Projets();

        $fillable = $projet->getFillable();

        $expectedFillable = [
            'nom',
            'description',
            'type',
            'statut',
            'date_debut',
            'date_fin_prevue',
            'date_fin_reelle',
            'budget_estime',
            'budget_reel',
            'coordinateur',
            'objectifs',
            'resultats',
            'image_url',
            'notes',
            'est_public'
        ];

        foreach ($expectedFillable as $attribute) {
            $this->assertContains($attribute, $fillable);
        }
    }

    public function test_projet_type_is_valid()
    {
        $projet = Projets::factory()->create(['type' => 'social']);

        $this->assertEquals('social', $projet->type);
        $this->assertContains($projet->type, ['agricole', 'social', 'environnemental', 'educatif', 'autre']);
    }

    public function test_projet_statut_is_valid()
    {
        $projet = Projets::factory()->create(['statut' => 'propose']);

        $this->assertEquals('propose', $projet->statut);
        $this->assertContains($projet->statut, ['propose', 'en_etude', 'approuve', 'en_cours', 'termine', 'annule']);
    }

    public function test_projet_budget_can_be_null()
    {
        $projet = Projets::factory()->create(['budget_estime' => null]);

        $this->assertNull($projet->budget_estime);
    }

    public function test_projet_soft_delete()
    {
        $projet = Projets::factory()->create();
        
        $projet->delete();
        
        $this->assertSoftDeleted('projets', ['id' => $projet->id]);
        $this->assertNotNull($projet->deleted_at);
    }

    public function test_projet_factory_with_specific_type()
    {
        $social = Projets::factory()->social()->create();
        $agricole = Projets::factory()->agricole()->create();

        $this->assertEquals('social', $social->type);
        $this->assertEquals('agricole', $agricole->type);
    }

    public function test_projet_factory_with_specific_status()
    {
        $propose = Projets::factory()->propose()->create();
        $termine = Projets::factory()->termine()->create();

        $this->assertEquals('propose', $propose->statut);
        $this->assertEquals('termine', $termine->statut);
    }

    public function test_projet_public_scope()
    {
        Projets::factory()->count(3)->create(['est_public' => true, 'statut' => 'approuve']);
        Projets::factory()->count(2)->create(['est_public' => false]);

        $publicProjects = Projets::where('est_public', true)->get();

        $this->assertCount(3, $publicProjects);
    }

    public function test_projet_active_scope()
    {
        Projets::factory()->count(3)->create(['statut' => 'en_cours']);
        Projets::factory()->count(2)->create(['statut' => 'termine']);

        $activeProjects = Projets::where('statut', 'en_cours')->get();

        $this->assertCount(3, $activeProjects);
    }
}
