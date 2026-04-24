<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('code_membre', 50)->nullable();
            $table->foreign('code_membre')->references('code_membre')->on('membres')->nullOnDelete();
            $table->string('nom_expediteur');
            $table->string('email_expediteur');
            $table->string('telephone')->nullable();
            $table->string('sujet');
            $table->text('message');
            $table->enum('type_demande', ['information', 'support', 'partenariat', 'autre']);
            $table->enum('statut', ['nouveau', 'en_traitement', 'traite', 'archive'])->default('nouveau');
            $table->string('code_admin_assignee', 50)->nullable();
            $table->foreign('code_admin_assignee')->references('code_membre')->on('membres')->nullOnDelete();
            $table->text('reponse')->nullable();
            $table->dateTime('date_reponse')->nullable();
            $table->boolean('lu')->default(false);
            $table->dateTime('date_lu')->nullable();
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
        Schema::dropIfExists('contact_messages');
    }
}
