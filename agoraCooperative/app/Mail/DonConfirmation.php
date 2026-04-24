<?php

namespace App\Mail;

use App\Models\Don;
use App\Services\PdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DonConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $don;

    public function __construct(Don $don)
    {
        $this->don = $don;
    }

    public function build()
    {
        $mail = $this->subject('Merci pour votre don - Reçu ' . ($this->don->numero_recu ?? 'en cours'))
            ->markdown('emails.don-confirmation')
            ->with(['don' => $this->don]);

        // Attacher le reçu PDF si le don est payé
        if ($this->don->numero_recu && $this->don->statut_paiement === 'paye') {
            $pdfService = app(PdfService::class);
            $pdfPath = $pdfService->genererRecuDon($this->don);
            
            $mail->attachFromStorage($pdfPath, "recu-don-{$this->don->numero_recu}.pdf", [
                'mime' => 'application/pdf',
            ]);
        }

        return $mail;
    }
}
