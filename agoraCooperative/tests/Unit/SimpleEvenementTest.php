<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Evenements;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SimpleEvenementTest extends TestCase
{
    use RefreshDatabase;

    public function test_evenement_factory_creates_valid_evenement()
    {
        $evenement = Evenements::factory()->create();

        $this->assertInstanceOf(Evenements::class, $evenement);
        $this->assertNotNull($evenement->code_evenement);
        $this->assertNotNull($evenement->titre);
        $this->assertNotNull($evenement->description);
        $this->assertNotNull($evenement->date_debut);
        $this->assertNotNull($evenement->type);
        $this->assertNotNull($evenement->statut);
    }

    public function test_evenement_fillable_attributes()
    {
        $evenement = new Evenements();

        $fillable = $evenement->getFillable();

        $expectedFillable = [
            'titre',
            'description',
            'date_debut',
            'date_fin',
            'lieu',
            'adresse',
            'ville',
            'frais_inscription',
            'places_disponibles',
            'type',
            'statut',
            'image_url',
            'instructions',
            'paiement_obligatoire'
        ];

        foreach ($expectedFillable as $attribute) {
            $this->assertContains($attribute, $fillable);
        }
    }

    public function test_evenement_code_evenement_is_unique()
    {
        $evenement1 = Evenements::factory()->create();
        
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Evenements::factory()->create(['code_evenement' => $evenement1->code_evenement]);
    }

    public function test_evenement_type_is_valid()
    {
        $evenement = Evenements::factory()->create(['type' => 'formation']);

        $this->assertEquals('formation', $evenement->type);
        $this->assertContains($evenement->type, ['assemblee', 'atelier', 'reunion', 'formation', 'autre']);
    }

    public function test_evenement_statut_is_valid()
    {
        $evenement = Evenements::factory()->create(['statut' => 'planifie']);

        $this->assertEquals('planifie', $evenement->statut);
        $this->assertContains($evenement->statut, ['planifie', 'en_cours', 'termine', 'annule']);
    }

    public function test_evenement_frais_inscription_can_be_zero()
    {
        $evenement = Evenements::factory()->create(['frais_inscription' => 0]);

        $this->assertEquals(0, $evenement->frais_inscription);
    }

    public function test_evenement_soft_delete()
    {
        $evenement = Evenements::factory()->create();
        
        $evenement->delete();
        
        $this->assertSoftDeleted('evenements', ['code_evenement' => $evenement->code_evenement]);
        $this->assertNotNull($evenement->deleted_at);
    }

    public function test_evenement_factory_with_specific_type()
    {
        $formation = Evenements::factory()->formation()->create();
        $reunion = Evenements::factory()->reunion()->create();

        $this->assertEquals('formation', $formation->type);
        $this->assertEquals('reunion', $reunion->type);
    }

    public function test_evenement_factory_with_specific_status()
    {
        $planifie = Evenements::factory()->planifie()->create();
        $termine = Evenements::factory()->termine()->create();

        $this->assertEquals('planifie', $planifie->statut);
        $this->assertEquals('termine', $termine->statut);
    }

    public function test_evenement_upcoming_scope()
    {
        Evenements::factory()->count(3)->create([
            'date_debut' => now()->addDays(7),
            'statut' => 'planifie'
        ]);
        Evenements::factory()->count(2)->create([
            'date_debut' => now()->subDays(7),
            'statut' => 'termine'
        ]);

        $upcomingEvents = Evenements::where('date_debut', '>', now())->get();

        $this->assertCount(3, $upcomingEvents);
    }
}
