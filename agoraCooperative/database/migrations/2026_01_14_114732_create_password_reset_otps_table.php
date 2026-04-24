<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePasswordResetOtpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up()
{
    Schema::create('password_reset_otps', function (Blueprint $table) {
        $table->id();
        $table->string('email')->index(); // L'email de l'utilisateur qui a oublié son pass
        $table->string('otp');            // Le code (sera hashé pour la sécurité)
        $table->timestamp('created_at')->nullable(); // Pour gérer l'expiration (ex: 15 min)
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('password_reset_otps');
    }
}
