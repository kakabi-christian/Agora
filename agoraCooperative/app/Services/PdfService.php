<?php

namespace App\Services;

use App\Models\Don;
use App\Models\Inscription_events;
use App\Models\Evenements;
use App\Models\Membre;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PdfService
{
    /**
     * Générer un reçu de don en PDF
     */
    public function genererRecuDon(Don $don): string
    {
        $montantEnLettres = $this->nombreEnLettres($don->montant);

        $pdf = Pdf::loadView('pdf.recu-don', [
            'don' => $don,
            'montantEnLettres' => $montantEnLettres,
        ]);

        $filename = "recu-don-{$don->numero_recu}.pdf";
        $path = "recus/dons/{$filename}";
        
        Storage::disk('local')->put($path, $pdf->output());

        return $path;
    }

    /**
     * Générer une confirmation d'inscription en PDF
     */
    public function genererConfirmationInscription(Inscription_events $inscription): string
    {
        $evenement = Evenements::where('code_evenement', $inscription->code_evenement)->first();
        $membre = Membre::where('code_membre', $inscription->code_membre)->first();

        // Générer le QR code avec les données de l'inscription
        $qrData = json_encode([
            'inscription_id' => $inscription->id,
            'code_membre' => $inscription->code_membre,
            'code_evenement' => $inscription->code_evenement,
            'statut_paiement' => $inscription->statut_paiement,
            'hash' => hash('sha256', $inscription->id . $inscription->code_membre . config('app.key'))
        ]);
        
        // Utiliser le format SVG qui ne nécessite pas d'extension d'image
        $qrCodeSvg = QrCode::format('svg')
            ->size(200)
            ->margin(1)
            ->errorCorrection('H')
            ->generate($qrData);
        
        // Convertir le SVG en base64 pour l'intégrer dans le PDF
        $qrCode = base64_encode($qrCodeSvg);

        $pdf = Pdf::loadView('pdf.confirmation-inscription', [
            'inscription' => $inscription,
            'evenement' => $evenement,
            'membre' => $membre,
            'qrCode' => $qrCode,
            'qrCodeFormat' => 'svg',
        ]);

        $filename = "inscription-{$inscription->id}-{$evenement->code_evenement}.pdf";
        $path = "confirmations/inscriptions/{$filename}";
        
        Storage::disk('local')->put($path, $pdf->output());

        return $path;
    }

    /**
     * Télécharger un reçu de don
     */
    public function telechargerRecuDon(Don $don)
    {
        $montantEnLettres = $this->nombreEnLettres($don->montant);

        $pdf = Pdf::loadView('pdf.recu-don', [
            'don' => $don,
            'montantEnLettres' => $montantEnLettres,
        ]);

        return $pdf->download("recu-don-{$don->numero_recu}.pdf");
    }

    /**
     * Télécharger une confirmation d'inscription
     */
    public function telechargerConfirmationInscription(Inscription_events $inscription)
    {
        $evenement = Evenements::where('code_evenement', $inscription->code_evenement)->first();
        $membre = Membre::where('code_membre', $inscription->code_membre)->first();

        // Générer le QR code avec SVG (ne nécessite ni Imagick ni GD)
        $qrData = json_encode([
            'inscription_id' => $inscription->id,
            'code_membre' => $inscription->code_membre,
            'code_evenement' => $inscription->code_evenement,
            'statut_paiement' => $inscription->statut_paiement,
            'hash' => hash('sha256', $inscription->id . $inscription->code_membre . config('app.key'))
        ]);
        
        // Utiliser le format SVG qui ne nécessite pas d'extension d'image
        $qrCodeSvg = QrCode::format('svg')
            ->size(200)
            ->margin(1)
            ->errorCorrection('H')
            ->generate($qrData);
        
        // Convertir le SVG en base64 pour l'intégrer dans le PDF
        $qrCode = base64_encode($qrCodeSvg);

        $pdf = Pdf::loadView('pdf.confirmation-inscription', [
            'inscription' => $inscription,
            'evenement' => $evenement,
            'membre' => $membre,
            'qrCode' => $qrCode,
            'qrCodeFormat' => 'svg', // Indiquer que c'est du SVG
        ]);

        return $pdf->download("confirmation-inscription-{$inscription->id}.pdf");
    }

    /**
     * Convertir un nombre en lettres (français)
     */
    private function nombreEnLettres(float $nombre): string
    {
        $unites = ['', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf', 'dix', 'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize', 'dix-sept', 'dix-huit', 'dix-neuf'];
        $dizaines = ['', '', 'vingt', 'trente', 'quarante', 'cinquante', 'soixante', 'soixante', 'quatre-vingt', 'quatre-vingt'];

        $nombre = (int) $nombre;
        
        if ($nombre == 0) return 'zéro';
        if ($nombre < 0) return 'moins ' . $this->nombreEnLettres(-$nombre);
        if ($nombre < 20) return $unites[$nombre];
        
        if ($nombre < 100) {
            $dizaine = (int)($nombre / 10);
            $unite = $nombre % 10;
            
            if ($dizaine == 7 || $dizaine == 9) {
                return $dizaines[$dizaine] . '-' . $unites[10 + $unite];
            }
            if ($unite == 0) {
                return $dizaines[$dizaine] . ($dizaine == 8 ? 's' : '');
            }
            if ($unite == 1 && $dizaine != 8) {
                return $dizaines[$dizaine] . '-et-un';
            }
            return $dizaines[$dizaine] . '-' . $unites[$unite];
        }
        
        if ($nombre < 1000) {
            $centaine = (int)($nombre / 100);
            $reste = $nombre % 100;
            $result = ($centaine == 1 ? 'cent' : $unites[$centaine] . ' cent');
            if ($reste == 0 && $centaine > 1) $result .= 's';
            if ($reste > 0) $result .= ' ' . $this->nombreEnLettres($reste);
            return $result;
        }
        
        if ($nombre < 1000000) {
            $milliers = (int)($nombre / 1000);
            $reste = $nombre % 1000;
            $result = ($milliers == 1 ? 'mille' : $this->nombreEnLettres($milliers) . ' mille');
            if ($reste > 0) $result .= ' ' . $this->nombreEnLettres($reste);
            return $result;
        }

        return number_format($nombre, 0, ',', ' ');
    }
}
