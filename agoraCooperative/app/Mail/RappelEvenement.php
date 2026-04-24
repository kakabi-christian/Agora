<?php

namespace App\Mail;

use App\Models\Evenements;
use App\Models\Inscription_events;
use App\Models\Membre;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RappelEvenement extends Mailable
{
    use Queueable, SerializesModels;

    public $inscription;
    public $evenement;
    public $membre;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Inscription_events $inscription, Evenements $evenement, Membre $membre)
    {
        $this->inscription = $inscription;
        $this->evenement = $evenement;
        $this->membre = $membre;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $heuresRestantes = now()->diffInHours($this->evenement->date_debut);
        
        return $this->subject('Rappel : ' . $this->evenement->titre . ' - Demain')
            ->markdown('emails.rappel-evenement')
            ->with([
                'inscription' => $this->inscription,
                'evenement' => $this->evenement,
                'membre' => $this->membre,
                'heures_restantes' => $heuresRestantes,
            ]);
    }
}
