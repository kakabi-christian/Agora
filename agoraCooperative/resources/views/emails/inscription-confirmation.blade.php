@component('mail::message')
# Inscription confirmée ! ✅

Bonjour {{ $membre->prenom }},

Votre inscription à l'événement suivant a bien été enregistrée :

@component('mail::panel')
**{{ $evenement->titre }}**

📅 **Date :** {{ $evenement->date_debut->format('d/m/Y à H:i') }}
@if($evenement->date_fin)
🏁 **Fin :** {{ $evenement->date_fin->format('d/m/Y à H:i') }}
@endif
📍 **Lieu :** {{ $evenement->lieu ?? 'À confirmer' }}
@endcomponent

@if($evenement->frais_inscription > 0)
## Paiement

@if($inscription->statut_paiement === 'paye')
✅ **Paiement effectué**
- Montant : {{ number_format($inscription->montant_paye ?? $evenement->frais_inscription, 0, ',', ' ') }} XAF
- Référence : {{ $inscription->reference_paiement }}
@else
⏳ **Paiement en attente**
- Montant à payer : {{ number_format($evenement->frais_inscription, 0, ',', ' ') }} XAF

@component('mail::button', ['url' => config('app.url') . '/paiement/evenement/' . $evenement->code_evenement, 'color' => 'primary'])
Procéder au paiement
@endcomponent
@endif
@endif

## Informations importantes

- Présentez votre confirmation (imprimée ou sur mobile) à l'entrée
- Arrivez 15 minutes avant le début de l'événement
- En cas d'empêchement, annulez votre inscription au moins 48h à l'avance

@if($evenement->description)
## À propos de l'événement

{{ Str::limit($evenement->description, 300) }}
@endif

Votre confirmation d'inscription est disponible en pièce jointe.

À très bientôt !<br>
L'équipe **{{ config('app.name') }}**
@endcomponent
