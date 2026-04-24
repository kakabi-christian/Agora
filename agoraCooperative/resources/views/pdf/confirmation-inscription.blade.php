<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Confirmation d'inscription - {{ $evenement->titre }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #333; padding: 40px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #2c5282; padding-bottom: 20px; }
        .logo { font-size: 24px; font-weight: bold; color: #2c5282; }
        .subtitle { color: #666; margin-top: 5px; }
        .confirmation-badge { background: #38a169; color: white; padding: 15px 30px; display: inline-block; border-radius: 5px; margin: 20px 0; }
        .event-box { background: #ebf8ff; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #2c5282; }
        .event-title { font-size: 18px; font-weight: bold; color: #2c5282; margin-bottom: 15px; }
        .section { margin: 20px 0; }
        .section-title { font-weight: bold; color: #2c5282; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px; margin-bottom: 10px; }
        .info-row { padding: 8px 0; border-bottom: 1px dotted #e2e8f0; }
        .info-label { color: #666; display: inline-block; width: 150px; }
        .info-value { font-weight: bold; }
        .qr-placeholder { text-align: center; padding: 20px; background: #f7fafc; border: 2px dashed #cbd5e0; margin: 20px 0; }
        .payment-status { padding: 15px; border-radius: 5px; margin: 15px 0; }
        .payment-paid { background: #f0fff4; border: 1px solid #38a169; }
        .payment-pending { background: #fffaf0; border: 1px solid #dd6b20; }
        .footer { margin-top: 40px; text-align: center; font-size: 10px; color: #666; border-top: 1px solid #e2e8f0; padding-top: 20px; }
        .important-notice { background: #fff5f5; padding: 15px; border-left: 4px solid #e53e3e; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">AGORA COOPÉRATIVE</div>
        <div class="subtitle">Confirmation d'inscription</div>
    </div>

    <div style="text-align: center;">
        <div class="confirmation-badge">
            ✓ INSCRIPTION CONFIRMÉE
        </div>
    </div>

    <div class="event-box">
        <div class="event-title">{{ $evenement->titre }}</div>
        <div class="info-row">
            <span class="info-label">📅 Date :</span>
            <span class="info-value">{{ $evenement->date_debut->format('d/m/Y à H:i') }}</span>
        </div>
        @if($evenement->date_fin)
        <div class="info-row">
            <span class="info-label">🏁 Fin :</span>
            <span class="info-value">{{ $evenement->date_fin->format('d/m/Y à H:i') }}</span>
        </div>
        @endif
        <div class="info-row">
            <span class="info-label">📍 Lieu :</span>
            <span class="info-value">{{ $evenement->lieu ?? 'À confirmer' }}</span>
        </div>
    </div>

    <div class="section">
        <div class="section-title">INFORMATIONS DU PARTICIPANT</div>
        <div class="info-row">
            <span class="info-label">Nom complet :</span>
            <span class="info-value">{{ $membre->prenom }} {{ $membre->nom }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Code membre :</span>
            <span class="info-value">{{ $membre->code_membre }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Email :</span>
            <span class="info-value">{{ $membre->email }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">N° Inscription :</span>
            <span class="info-value">{{ $inscription->id }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Date inscription :</span>
            <span class="info-value">{{ $inscription->created_at->format('d/m/Y à H:i') }}</span>
        </div>
    </div>

    @if($evenement->frais_inscription > 0)
    <div class="section">
        <div class="section-title">PAIEMENT</div>
        @if($inscription->statut_paiement === 'paye')
        <div class="payment-status payment-paid">
            <strong>✓ Paiement effectué</strong><br>
            Montant : {{ number_format($inscription->montant_paye ?? $evenement->frais_inscription, 0, ',', ' ') }} XAF<br>
            Référence : {{ $inscription->reference_paiement }}
        </div>
        @else
        <div class="payment-status payment-pending">
            <strong>⏳ Paiement en attente</strong><br>
            Montant à payer : {{ number_format($evenement->frais_inscription, 0, ',', ' ') }} XAF
        </div>
        @endif
    </div>
    @endif

    <div class="qr-placeholder">
        <p style="font-size: 14px; color: #666; margin-bottom: 15px;">📱 Code QR de vérification</p>
        @if(isset($qrCode))
            @if(isset($qrCodeFormat) && $qrCodeFormat === 'svg')
                <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code" style="width: 200px; height: 200px;">
            @else
                <img src="data:image/png;base64,{{ $qrCode }}" alt="QR Code" style="width: 200px; height: 200px;">
            @endif
        @else
        <p style="font-size: 10px; margin-top: 10px;">Présentez ce document à l'entrée de l'événement</p>
        @endif
        <p style="font-size: 16px; font-weight: bold; margin-top: 10px; letter-spacing: 3px;">
            {{ $inscription->id }}-{{ strtoupper(substr(md5($inscription->id . $membre->code_membre), 0, 8)) }}
        </p>
    </div>

    @if($evenement->description)
    <div class="section">
        <div class="section-title">DESCRIPTION DE L'ÉVÉNEMENT</div>
        <p style="line-height: 1.6;">{{ $evenement->description }}</p>
    </div>
    @endif

    <div class="important-notice">
        <strong>⚠️ Important</strong><br>
        - Présentez ce document (imprimé ou sur mobile) à l'entrée<br>
        - Arrivez 15 minutes avant le début de l'événement<br>
        - En cas d'annulation, prévenez-nous au moins 48h à l'avance
    </div>

    <div class="footer">
        <p>Agora Coopérative - [Adresse complète] - [Téléphone] - [Email]</p>
        <p>Document généré le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>
</body>
</html>
