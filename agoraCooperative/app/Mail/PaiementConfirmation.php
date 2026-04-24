<?php

namespace App\Mail;

use App\Models\Membre;
use App\Models\Paiement;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaiementConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $paiement;
    public $membre;

    public function __construct(Paiement $paiement, Membre $membre = null)
    {
        $this->paiement = $paiement;
        $this->membre = $membre;
    }

    public function build()
    {
        return $this->subject('Confirmation de paiement - ' . $this->paiement->reference)
            ->markdown('emails.paiement-confirmation')
            ->with([
                'paiement' => $this->paiement,
                'membre' => $this->membre,
            ]);
    }
}
