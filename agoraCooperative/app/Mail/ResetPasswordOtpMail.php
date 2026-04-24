<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    // Cette variable sera accessible directement dans ta vue Blade
    public $otp;

    /**
     * On reçoit l'OTP généré depuis le contrôleur
     */
    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    /**
     * Construction de l'email
     */
    public function build()
    {
        return $this->subject('Code de réinitialisation - AgoCooperative')
                    ->view('emails.otp'); // Assure-toi que le fichier resources/views/emails/otp.blade.php existe
    }
}