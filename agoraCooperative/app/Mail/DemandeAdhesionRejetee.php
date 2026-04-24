<?php

namespace App\Mail;

use App\Models\DemandeAdhesion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DemandeAdhesionRejetee extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $demande;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(DemandeAdhesion $demande)
    {
        $this->demande = $demande;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Réponse à votre demande d\'adhésion - Agora Coopérative')
                    ->markdown('emails.demande-adhesion-rejetee');
    }
}
