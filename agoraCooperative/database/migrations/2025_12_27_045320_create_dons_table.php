<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('dons', function (Blueprint $table) {
        $table->id();
        $table->string('code_membre', 50)->nullable();
        $table->foreign('code_membre')->references('code_membre')->on('membres')->nullOnDelete();
        
        $table->string('nom_donateur');
        $table->string('email_donateur');
        $table->string('telephone'); 
        
        $table->enum('type', ['don', 'parrainage', 'adhesion', 'sponsoring']);
        $table->decimal('montant', 15, 2); 
        
        // Ajout de 'Campay' dans les modes
        $table->string('mode_paiement')->default('Campay'); 
        
        $table->string('reference_paiement')->nullable();
        
        // Ajout de 'succes' et 'echec' pour matcher avec notre contrôleur
        $table->enum('statut_paiement', ['en_attente', 'succes', 'echec', 'annule'])->default('en_attente');
        
        $table->date('date_don')->useCurrent();
        $table->boolean('deductible_impots')->default(false);
        $table->string('numero_recu')->nullable();
        $table->text('message_donateur')->nullable();
        $table->boolean('anonyme')->default(false);
        $table->json('informations_donateur')->nullable();
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
        Schema::dropIfExists('dons');
    }
}
