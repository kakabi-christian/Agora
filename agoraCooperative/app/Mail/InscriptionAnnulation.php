<?php

namespace App\Mail;

use App\Models\Evenements;
use App\Models\Inscription_events;
use App\Models\Membre;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InscriptionAnnulation extends Mailable
{
    use Queueable, SerializesModels;

    public $inscription;
    public $evenement;
    public $membre;

    public function __construct(Inscription_events $inscription, Evenements $evenement, Membre $membre)
    {
        $this->inscription = $inscription;
        $this->evenement = $evenement;
        $this->membre = $membre;
    }

    public function build()
    {
        return $this->subject('Annulation d\'inscription - ' . $this->evenement->titre)
            ->markdown('emails.inscription-annulation')
            ->with([
                'inscription' => $this->inscription,
                'evenement' => $this->evenement,
                'membre' => $this->membre,
            ]);
    }
}
