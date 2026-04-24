<?php

namespace App\Mail;

use App\Models\Evenements;
use App\Models\Inscription_events;
use App\Models\Membre;
use App\Models\Paiement;
use App\Services\PdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaiementEvenementConfirme extends Mailable
{
    use Queueable, SerializesModels;

    public $inscription;
    public $evenement;
    public $membre;
    public $paiement;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Inscription_events $inscription, Paiement $paiement)
    {
        $this->inscription = $inscription;
        $this->paiement = $paiement;
        $this->evenement = Evenements::where('code_evenement', $inscription->code_evenement)->first();
        $this->membre = Membre::where('code_membre', $inscription->code_membre)->first();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // Régénérer le PDF avec le statut de paiement mis à jour
        $pdfService = app(PdfService::class);
        $pdfPath = $pdfService->genererConfirmationInscription($this->inscription);

        return $this->subject('Paiement confirmé - ' . $this->evenement->titre)
            ->markdown('emails.paiement-evenement-confirme')
            ->with([
                'inscription' => $this->inscription,
                'evenement' => $this->evenement,
                'membre' => $this->membre,
                'paiement' => $this->paiement,
            ])
            ->attachFromStorage($pdfPath, "recu-paiement-{$this->inscription->id}.pdf", [
                'mime' => 'application/pdf',
            ]);
    }
}
