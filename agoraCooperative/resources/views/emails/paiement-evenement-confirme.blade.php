@component('mail::message')
# Paiement Confirmé ✅

Bonjour {{ $membre->prenom }},

Nous avons le plaisir de vous confirmer que votre paiement pour l'événement suivant a été **validé avec succès** !

@component('mail::panel')
**{{ $evenement->titre }}**

📅 **Date :** {{ $evenement->date_debut->format('d/m/Y à H:i') }}
@if($evenement->date_fin)
🏁 **Fin :** {{ $evenement->date_fin->format('d/m/Y à H:i') }}
@endif
📍 **Lieu :** {{ $evenement->lieu ?? 'À confirmer' }}
@endcomponent

## Détails du Paiement

@component('mail::table')
| Information | Détail |
|:------------|:-------|
| **Montant payé** | {{ number_format($paiement->montant, 0, ',', ' ') }} XAF |
| **Référence** | {{ $paiement->reference }} |
| **Date de paiement** | {{ $paiement->date_paiement->format('d/m/Y à H:i') }} |
| **Mode de paiement** | {{ ucfirst($paiement->mode_paiement) }} |
@endcomponent

✅ **Votre inscription est maintenant confirmée !**

Vous trouverez votre reçu de paiement et votre confirmation d'inscription mis à jour en pièce jointe.

@component('mail::button', ['url' => config('app.url') . '/mes-inscriptions', 'color' => 'success'])
Voir mes inscriptions
@endcomponent

## Informations Importantes

- **Présentez votre confirmation** (imprimée ou sur mobile) à l'entrée de l'événement
- **Arrivez 15 minutes avant** le début pour faciliter l'accueil
- En cas d'empêchement, **annulez votre inscription au moins 48h à l'avance** pour bénéficier d'un remboursement

@if($evenement->description)
## À Propos de l'Événement

{{ Str::limit($evenement->description, 300) }}
@endif

Nous avons hâte de vous accueillir !

Cordialement,<br>
L'équipe **{{ config('app.name') }}**

---

<small style="color: #666;">
Ce reçu fait office de justificatif de paiement. Conservez-le précieusement.<br>
Pour toute question, contactez-nous à {{ config('mail.from.address') }}
</small>
@endcomponent
