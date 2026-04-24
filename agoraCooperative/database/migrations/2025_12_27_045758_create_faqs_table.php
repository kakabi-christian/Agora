<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFaqsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->text('question');
            $table->text('reponse');
            $table->enum('categorie', ['generale', 'membres', 'projets', 'dons', 'evenements', 'administratif']);
            $table->integer('ordre_affichage')->default(0);
            $table->boolean('est_actif')->default(true);
            $table->integer('nombre_vues')->default(0);
            $table->integer('nombre_utile')->default(0);
            $table->integer('nombre_inutile')->default(0);
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
        Schema::dropIfExists('faqs');
    }
}
