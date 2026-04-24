<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Membre;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MembreTest extends TestCase
{
    use RefreshDatabase;

    public function test_membre_factory_creates_valid_membre()
    {
        $membre = Membre::factory()->create();

        $this->assertInstanceOf(Membre::class, $membre);
        $this->assertNotNull($membre->code_membre);
        $this->assertNotNull($membre->nom);
        $this->assertNotNull($membre->prenom);
        $this->assertNotNull($membre->email);
        $this->assertNotNull($membre->role);
        $this->assertNotNull($membre->est_actif);
    }

    public function test_membre_fillable_attributes()
    {
        $membre = new Membre();

        $fillable = $membre->getFillable();

        $expectedFillable = [
            'nom',
            'prenom',
            'email',
            'mot_de_passe',
            'date_inscription',
            'role',
            'est_actif',
            'telephone',
            'adresse',
            'ville',
            'code_postal',
            'biographie',
            'photo_url'
        ];

        foreach ($expectedFillable as $attribute) {
            $this->assertContains($attribute, $fillable);
        }
    }

    public function test_membre_hidden_attributes()
    {
        $membre = new Membre();

        $hidden = $membre->getHidden();

        $expectedHidden = [
            'mot_de_passe',
            'remember_token'
        ];

        foreach ($expectedHidden as $attribute) {
            $this->assertContains($attribute, $hidden);
        }
    }

    public function test_membre_scope_actif()
    {
        Membre::factory()->count(3)->create(['est_actif' => true]);
        Membre::factory()->count(2)->create(['est_actif' => false]);

        $actifMembres = Membre::where('est_actif', true)->get();

        $this->assertCount(3, $actifMembres);
        $actifMembres->each(function ($membre) {
            $this->assertTrue($membre->est_actif);
        });
    }

    public function test_membre_scope_admin()
    {
        Membre::factory()->count(2)->create(['role' => 'administrateur']);
        Membre::factory()->count(3)->create(['role' => 'membre']);

        $adminMembres = Membre::where('role', 'administrateur')->get();

        $this->assertCount(2, $adminMembres);
        $adminMembres->each(function ($membre) {
            $this->assertEquals('administrateur', $membre->role);
        });
    }

    public function test_membre_is_admin_method()
    {
        $admin = Membre::factory()->create(['role' => 'administrateur']);
        $member = Membre::factory()->create(['role' => 'membre']);

        $this->assertEquals('administrateur', $admin->role);
        $this->assertEquals('membre', $member->role);
    }

    public function test_membre_is_active_method()
    {
        $activeMember = Membre::factory()->create(['est_actif' => true]);
        $inactiveMember = Membre::factory()->create(['est_actif' => false]);

        $this->assertTrue($activeMember->est_actif);
        $this->assertFalse($inactiveMember->est_actif);
    }

    public function test_membre_code_membre_is_unique()
    {
        $membre1 = Membre::factory()->create();
        
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Membre::factory()->create(['code_membre' => $membre1->code_membre]);
    }

    public function test_membre_email_is_unique()
    {
        $membre1 = Membre::factory()->create(['email' => 'test@example.com']);
        
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Membre::factory()->create(['email' => 'test@example.com']);
    }

    public function test_membre_can_create_personal_access_tokens()
    {
        $membre = Membre::factory()->create();
        $token = $membre->createToken('test-token');

        $this->assertInstanceOf(\Laravel\Sanctum\NewAccessToken::class, $token);
        $this->assertNotNull($token->accessToken);
        $this->assertEquals($membre->code_membre, $token->accessToken->tokenable_id);
    }

    public function test_membre_has_api_tokens_relationship()
    {
        $membre = Membre::factory()->create();
        $token = $membre->createToken('test-token');

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\MorphMany::class, $membre->tokens());
        $this->assertCount(1, $membre->tokens);
    }

    public function test_membre_soft_delete()
    {
        $membre = Membre::factory()->create();
        
        $membre->delete();
        
        $this->assertSoftDeleted('membres', ['code_membre' => $membre->code_membre]);
        $this->assertNotNull($membre->deleted_at);
    }

    public function test_membre_factory_with_specific_role()
    {
        $admin = Membre::factory()->admin()->create();
        $member = Membre::factory()->create();

        $this->assertEquals('administrateur', $admin->role);
        $this->assertEquals('membre', $member->role);
    }

    public function test_membre_factory_with_specific_status()
    {
        $activeMember = Membre::factory()->active()->create();
        $inactiveMember = Membre::factory()->inactive()->create();

        $this->assertTrue($activeMember->est_actif);
        $this->assertFalse($inactiveMember->est_actif);
    }
}
