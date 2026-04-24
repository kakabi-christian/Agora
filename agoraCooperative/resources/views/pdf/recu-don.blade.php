<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu de Don - {{ $don->numero_recu }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #333; padding: 40px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #2c5282; padding-bottom: 20px; }
        .logo { font-size: 24px; font-weight: bold; color: #2c5282; }
        .subtitle { color: #666; margin-top: 5px; }
        .recu-number { background: #f7fafc; padding: 15px; text-align: center; margin: 20px 0; border-radius: 5px; }
        .recu-number h2 { color: #2c5282; font-size: 18px; }
        .section { margin: 20px 0; }
        .section-title { font-weight: bold; color: #2c5282; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px; margin-bottom: 10px; }
        .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dotted #e2e8f0; }
        .info-label { color: #666; }
        .info-value { font-weight: bold; }
        .montant-box { background: #ebf8ff; padding: 20px; text-align: center; margin: 25px 0; border-radius: 8px; border: 2px solid #2c5282; }
        .montant { font-size: 28px; font-weight: bold; color: #2c5282; }
        .montant-lettres { font-style: italic; color: #666; margin-top: 5px; }
        .fiscal-notice { background: #f0fff4; padding: 15px; border-left: 4px solid #38a169; margin: 20px 0; }
        .footer { margin-top: 40px; text-align: center; font-size: 10px; color: #666; border-top: 1px solid #e2e8f0; padding-top: 20px; }
        .signature { margin-top: 30px; text-align: right; }
        .signature-line { border-top: 1px solid #333; width: 200px; margin-left: auto; margin-top: 50px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">AGORA COOPÉRATIVE</div>
        <div class="subtitle">Association à but non lucratif</div>
        <div class="subtitle">Siège social : [Adresse de la coopérative]</div>
    </div>

    <div class="recu-number">
        <h2>REÇU DE DON N° {{ $don->numero_recu }}</h2>
        <p>Date d'émission : {{ now()->format('d/m/Y') }}</p>
    </div>

    <div class="section">
        <div class="section-title">INFORMATIONS DU DONATEUR</div>
        <div class="info-row">
            <span class="info-label">Nom :</span>
            <span class="info-value">{{ $don->anonyme ? 'Donateur anonyme' : $don->nom_donateur }}</span>
        </div>
        @if(!$don->anonyme)
        <div class="info-row">
            <span class="info-label">Email :</span>
            <span class="info-value">{{ $don->email_donateur }}</span>
        </div>
        @endif
        @if($don->code_membre)
        <div class="info-row">
            <span class="info-label">Code membre :</span>
            <span class="info-value">{{ $don->code_membre }}</span>
        </div>
        @endif
    </div>

    <div class="section">
        <div class="section-title">DÉTAILS DU DON</div>
        <div class="info-row">
            <span class="info-label">Type de don :</span>
            <span class="info-value">{{ ucfirst($don->type) }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Date du don :</span>
            <span class="info-value">{{ $don->date_don->format('d/m/Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Mode de paiement :</span>
            <span class="info-value">{{ ucfirst($don->mode_paiement) }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Référence :</span>
            <span class="info-value">{{ $don->reference_paiement }}</span>
        </div>
    </div>

    <div class="montant-box">
        <div class="montant">{{ number_format($don->montant, 0, ',', ' ') }} XAF</div>
        <div class="montant-lettres">({{ $montantEnLettres ?? '' }})</div>
    </div>

    @if($don->deductible_impots)
    <div class="fiscal-notice">
        <strong>📋 Attestation fiscale</strong><br>
        Ce reçu peut être utilisé pour justifier d'un don ouvrant droit à réduction d'impôt, 
        conformément aux dispositions fiscales en vigueur.
    </div>
    @endif

    @if($don->message_donateur)
    <div class="section">
        <div class="section-title">MESSAGE DU DONATEUR</div>
        <p style="font-style: italic; padding: 10px; background: #f7fafc; border-radius: 5px;">
            "{{ $don->message_donateur }}"
        </p>
    </div>
    @endif

    <div class="signature">
        <p>Le Président de la Coopérative</p>
        <div class="signature-line"></div>
        <p style="margin-top: 5px;">[Nom du Président]</p>
    </div>

    <div class="footer">
        <p>Agora Coopérative - [Adresse complète] - [Téléphone] - [Email]</p>
        <p>N° SIRET : [Numéro SIRET] - Code APE : [Code APE]</p>
        <p>Document généré automatiquement le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>
</body>
</html>
