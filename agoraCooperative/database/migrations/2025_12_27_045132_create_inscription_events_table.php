<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInscriptionEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inscription_events', function (Blueprint $table) {
            $table->id();
            $table->string('code_membre', 50);
            $table->foreign('code_membre')->references('code_membre')->on('membres')->onDelete('cascade');
            $table->string('code_evenement', 50);
            $table->foreign('code_evenement')->references('code_evenement')->on('evenements')->onDelete('cascade');
            $table->dateTime('date_inscription')->useCurrent();
            $table->enum('statut_paiement', ['en_attente', 'paye', 'annule', 'rembourse'])->default('en_attente');
            $table->enum('statut_participation', ['inscrit', 'present', 'absent'])->default('inscrit');
            $table->decimal('montant_paye', 8, 2)->nullable();
            $table->string('mode_paiement')->nullable();
            $table->string('reference_paiement')->nullable();
            $table->text('commentaires')->nullable();
            $table->timestamps();
            $table->unique(['code_membre', 'code_evenement'], 'idx_membre_evenement');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inscription_events');
    }
}
