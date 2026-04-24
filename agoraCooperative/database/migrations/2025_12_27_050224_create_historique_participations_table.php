<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoriqueParticipationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historique_participations', function (Blueprint $table) {
            $table->id();
            $table->string('code_membre', 50);
            $table->foreign('code_membre')->references('code_membre')->on('membres')->onDelete('cascade');
            $table->string('type_participation'); // 'evenement', 'projet', 'don', 'autre'
            $table->string('titre');
            $table->text('description');
            $table->date('date_participation');
            $table->json('details')->nullable();
            $table->decimal('montant_implique', 10, 2)->nullable();
            $table->integer('heures_contribuees')->nullable();
            $table->string('role')->nullable();
            $table->timestamps();
            $table->index(['code_membre', 'type_participation', 'date_participation'],'idx_hist_participations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('historique_participations');
    }
}
