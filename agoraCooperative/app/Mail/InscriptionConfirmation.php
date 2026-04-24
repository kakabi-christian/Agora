<?php

namespace App\Mail;

use App\Models\Evenements;
use App\Models\Inscription_events;
use App\Models\Membre;
use App\Services\PdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InscriptionConfirmation extends Mailable
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
        $pdfService = app(PdfService::class);
        $pdfPath = $pdfService->genererConfirmationInscription($this->inscription);

        return $this->subject('Confirmation d\'inscription - ' . $this->evenement->titre)
            ->markdown('emails.inscription-confirmation')
            ->with([
                'inscription' => $this->inscription,
                'evenement' => $this->evenement,
                'membre' => $this->membre,
            ])
            ->attachFromStorage($pdfPath, "confirmation-inscription-{$this->inscription->id}.pdf", [
                'mime' => 'application/pdf',
            ]);
    }
}
