<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profils', function (Blueprint $table) {
            $table->id();
            $table->string('code_membre', 50)->unique();
            $table->foreign('code_membre')->references('code_membre')->on('membres')->onDelete('cascade');
            $table->json('informations_personnelles')->nullable();
            $table->json('competences')->nullable();
            $table->json('interets')->nullable();
            $table->date('date_derniere_connexion')->nullable();
            $table->integer('nombre_participations')->default(0);
            $table->json('preferences')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profils');
    }
}
