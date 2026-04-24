<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvenementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evenements', function (Blueprint $table) {
            $table->string('code_evenement', 50)->primary();
            $table->string('titre');
            $table->text('description');
            $table->dateTime('date_debut');
            $table->dateTime('date_fin')->nullable();
            $table->string('lieu');
            $table->string('adresse')->nullable();
            $table->string('ville')->nullable();
            $table->decimal('frais_inscription', 8, 2)->default(0);
            $table->integer('places_disponibles')->nullable();
            $table->enum('type', ['assemblee', 'atelier', 'reunion', 'formation', 'autre']);
            $table->enum('statut', ['planifie', 'en_cours', 'termine', 'annule'])->default('planifie');
            $table->string('image_url')->nullable();
            $table->text('instructions')->nullable();
            $table->boolean('paiement_obligatoire')->default(false);
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
        Schema::dropIfExists('evenements');
    }
}
