<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRappelFieldsToInscriptionEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inscription_events', function (Blueprint $table) {
            $table->boolean('rappel_envoye')->default(false)->after('commentaires');
            $table->timestamp('date_rappel_envoye')->nullable()->after('rappel_envoye');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inscription_events', function (Blueprint $table) {
            $table->dropColumn(['rappel_envoye', 'date_rappel_envoye']);
        });
    }
}
