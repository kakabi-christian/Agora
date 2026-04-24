<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartenairesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partenaires', function (Blueprint $table) {
            $table->string('code_partenaire', 50)->primary();
            $table->string('nom');
            $table->enum('type', ['partenaire', 'sponsor', 'institution', 'association']);
            $table->text('description')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('site_web')->nullable();
            $table->string('contact_nom')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_telephone')->nullable();
            $table->enum('niveau_partenariat', ['principal', 'secondaire', 'tertiaire'])->default('secondaire');
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->boolean('est_actif')->default(true);
            $table->integer('ordre_affichage')->default(0);
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
        Schema::dropIfExists('partenaires');
    }
}
