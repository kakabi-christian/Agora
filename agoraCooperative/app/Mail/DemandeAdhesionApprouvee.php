<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\DemandeAdhesion;

class DemandeAdhesionApprouvee extends Mailable
{
    use Queueable, SerializesModels;

    public $demande;
    public $codeMembre;
    public $motDePasse;

    /**
     * Create a new message instance.
     */
    public function __construct(DemandeAdhesion $demande, string $codeMembre, ?string $motDePasse)
    {
        $this->demande = $demande;
        $this->codeMembre = $codeMembre;
        $this->motDePasse = $motDePasse; // null si membre existant
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Votre demande d\'adhésion a été approuvée !')
                    ->markdown('emails.demande-adhesion-approuvee')
                    ->with([
                        'demande' => $this->demande,
                        'codeMembre' => $this->codeMembre,
                        'motDePasse' => $this->motDePasse,
                    ]);
    }
}
// Mail/DemandeAdhesionApprouvee.php