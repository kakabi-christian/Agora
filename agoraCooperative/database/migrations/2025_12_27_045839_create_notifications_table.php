<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('code_membre', 50);
            $table->foreign('code_membre')->references('code_membre')->on('membres')->onDelete('cascade');
            $table->string('titre');
            $table->text('contenu');
            $table->enum('type', ['email', 'alerte_site', 'notification_mobile', 'sms']);
            $table->enum('categorie', ['systeme', 'evenement', 'projet', 'administratif', 'urgence']);
            $table->enum('statut', ['envoye', 'lu', 'non_lu', 'erreur'])->default('non_lu');
            $table->string('objet_relie_type', 50)->nullable(); // Pour polymorphisme
            $table->string('objet_relie_code', 50)->nullable(); // Pour polymorphisme
            $table->dateTime('date_envoi')->useCurrent();
            $table->dateTime('date_lecture')->nullable();
            $table->string('lien_action')->nullable();
            $table->boolean('est_urgent')->default(false);
            $table->timestamps();
            $table->index(['objet_relie_type', 'objet_relie_code'], 'idx_notifications_objet_relie');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
