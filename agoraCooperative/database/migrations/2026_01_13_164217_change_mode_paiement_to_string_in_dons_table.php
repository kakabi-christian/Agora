<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeModePaiementToStringInDonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('dons', function (Blueprint $table) {
        // On transforme l'ENUM en STRING pour accepter 'Campay'
        $table->string('mode_paiement')->default('Campay')->change();
        
        // Profitions-en pour faire pareil avec statut_paiement si besoin
        $table->string('statut_paiement')->default('en_attente')->change();
    });
}

public function down()
{
    Schema::table('dons', function (Blueprint $table) {
        // Optionnel : revenir à l'enum si nécessaire
    });
}
}
