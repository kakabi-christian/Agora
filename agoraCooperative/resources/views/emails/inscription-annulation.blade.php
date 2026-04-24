@component('mail::message')
# Annulation d'inscription

Bonjour {{ $membre->prenom }},

Nous confirmons l'annulation de votre inscription à l'événement suivant :

@component('mail::panel')
**{{ $evenement->titre }}**

📅 **Date :** {{ $evenement->date_debut->format('d/m/Y à H:i') }}
📍 **Lieu :** {{ $evenement->lieu ?? 'Non précisé' }}
@endcomponent

@if($inscription->statut_paiement === 'paye' && $inscription->montant_paye > 0)
## Remboursement

Votre paiement de **{{ number_format($inscription->montant_paye, 0, ',', ' ') }} XAF** sera traité selon nos conditions de remboursement.

Un membre de notre équipe vous contactera sous 48h pour finaliser le remboursement.
@endif

Si cette annulation est une erreur ou si vous souhaitez vous réinscrire, n'hésitez pas à nous contacter.

@component('mail::button', ['url' => config('app.url') . '/evenements'])
Voir les autres événements
@endcomponent

Cordialement,<br>
L'équipe **{{ config('app.name') }}**
@endcomponent
