<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projets', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->text('description');
            $table->enum('type', ['agricole', 'social', 'environnemental', 'educatif', 'autre']);
            $table->enum('statut', ['propose', 'en_etude', 'approuve', 'en_cours', 'termine', 'annule'])->default('propose');
            $table->date('date_debut')->nullable();
            $table->date('date_fin_prevue')->nullable();
            $table->date('date_fin_reelle')->nullable();
            $table->decimal('budget_estime', 10, 2)->nullable();
            $table->decimal('budget_reel', 10, 2)->nullable();
            $table->string('coordinateur')->nullable();
            $table->json('objectifs')->nullable();
            $table->json('resultats')->nullable();
            $table->string('image_url')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('est_public')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projets');
    }
}
