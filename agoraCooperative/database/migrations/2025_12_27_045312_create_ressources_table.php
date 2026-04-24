<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRessourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ressources', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->enum('type', ['document', 'formulaire', 'rapport', 'reglement', 'autre']);
            $table->enum('categorie', ['administratif', 'comptable', 'juridique', 'technique', 'pedagogique']);
            $table->string('chemin_fichier');
            $table->string('nom_fichier');
            $table->string('extension_fichier');
            $table->text('description')->nullable();
            $table->date('date_publication')->nullable();
            $table->date('date_expiration')->nullable();
            $table->boolean('est_public')->default(false);
            $table->boolean('necessite_authentification')->default(true);
            $table->integer('nombre_telechargements')->default(0);
            $table->string('code_membre', 50)->nullable();
            $table->foreign('code_membre')->references('code_membre')->on('membres')->nullOnDelete(); // Uploadé par
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
        Schema::dropIfExists('ressources');
    }
}
