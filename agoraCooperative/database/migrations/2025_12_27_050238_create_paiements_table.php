<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaiementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paiements', function (Blueprint $table) {
            $table->string('code_paiement', 50)->primary();
            $table->string('code_membre', 50)->nullable();
            $table->foreign('code_membre')->references('code_membre')->on('membres')->nullOnDelete();
            $table->string('reference')->unique();
            $table->decimal('montant', 10, 2);
            $table->enum('type', ['inscription_evenement', 'don', 'adhesion', 'autre']);
            $table->enum('statut', ['initie', 'en_attente', 'paye', 'annule', 'erreur'])->default('initie');
            $table->string('mode_paiement');
            $table->json('details_paiement')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('objet_relie_type', 50)->nullable(); // 'Evenement', 'Don', etc.
            $table->string('objet_relie_code', 50)->nullable();
            $table->dateTime('date_paiement')->nullable();
            $table->timestamps();
            $table->index(['objet_relie_type', 'objet_relie_code'], 'idx_paiements_objet_relie');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('paiements');
    }
}
