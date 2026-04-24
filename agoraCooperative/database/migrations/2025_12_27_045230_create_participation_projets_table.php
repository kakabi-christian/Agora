<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParticipationProjetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('participation_projets', function (Blueprint $table) {
            $table->id();
            $table->string('code_membre', 50);
            $table->foreign('code_membre')->references('code_membre')->on('membres')->onDelete('cascade');
            $table->foreignId('projet_id')->constrained('projets')->onDelete('cascade');
            $table->date('date_participation')->useCurrent();
            $table->enum('role', ['proposeur', 'coordinateur', 'participant', 'benevole', 'expert']);
            $table->enum('statut', ['actif', 'inactif', 'termine'])->default('actif');
            $table->integer('heures_contribuees')->default(0);
            $table->text('taches')->nullable();
            $table->text('competences_apportees')->nullable();
            $table->timestamps();
            $table->unique(['code_membre', 'projet_id'], 'idx_membre_projet');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('participation_projets');
    }
}
