<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeMontantToTextInDonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dons', function (Blueprint $table) {
            // On change le type de la colonne montant en TEXT pour le chiffrement
            // On utilise change() pour modifier une colonne existante
            $table->text('montant')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dons', function (Blueprint $table) {
            // On revient au type decimal d'origine si on annule la migration
            $table->decimal('montant', 15, 2)->nullable()->change();
        });
    }
}