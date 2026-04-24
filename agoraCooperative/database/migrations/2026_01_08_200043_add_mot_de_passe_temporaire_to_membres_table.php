<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMotDePasseTemporaireToMembresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('membres', function (Blueprint $table) {
            $table->boolean('mot_de_passe_temporaire')->default(false)->after('mot_de_passe');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('membres', function (Blueprint $table) {
            $table->dropColumn('mot_de_passe_temporaire');
        });
    }
}
