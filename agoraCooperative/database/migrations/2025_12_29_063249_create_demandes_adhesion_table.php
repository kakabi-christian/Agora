<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDemandesAdhesionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('demandes_adhesion', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('email')->unique();
            $table->string('telephone');
            $table->string('adresse')->nullable();
            $table->string('ville')->nullable();
            $table->string('code_postal')->nullable();
            $table->date('date_naissance')->nullable();
            $table->string('profession')->nullable();
            $table->text('motivation')->nullable();
            $table->json('competences')->nullable();
            $table->enum('statut', ['en_attente', 'en_examen', 'approuvee', 'rejetee'])->default('en_attente');
            $table->dateTime('date_demande')->useCurrent();
            $table->dateTime('date_traitement')->nullable();
            $table->string('code_admin_traitant', 50)->nullable();
            $table->foreign('code_admin_traitant')->references('code_membre')->on('membres')->nullOnDelete();
            $table->text('commentaire_admin')->nullable();
            $table->json('documents_joints')->nullable();
            $table->string('code_membre_cree', 50)->nullable();
            $table->foreign('code_membre_cree')->references('code_membre')->on('membres')->nullOnDelete();
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
        Schema::dropIfExists('demandes_adhesion');
    }
}
