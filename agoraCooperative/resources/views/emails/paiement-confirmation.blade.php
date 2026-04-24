@component('mail::message')
# Paiement confirmé ! 💳✅

Bonjour {{ $membre->prenom ?? 'Cher client' }},

Nous confirmons la réception de votre paiement.

@component('mail::panel')
**Détails du paiement :**

- **Référence :** {{ $paiement->reference }}
- **Montant :** {{ number_format($paiement->montant, 0, ',', ' ') }} XAF
- **Date :** {{ $paiement->date_paiement->format('d/m/Y à H:i') }}
- **Type :** {{ ucfirst(str_replace('_', ' ', $paiement->type)) }}
- **Mode :** {{ ucfirst($paiement->mode_paiement) }}
@endcomponent

@if($paiement->type === 'inscription_evenement')
Votre inscription à l'événement est maintenant complète. Vous recevrez une confirmation séparée avec tous les détails.
@elseif($paiement->type === 'don')
Merci pour votre générosité ! Votre reçu fiscal vous sera envoyé séparément.
@endif

@component('mail::button', ['url' => config('app.url') . '/mes-paiements'])
Voir mes paiements
@endcomponent

Merci de votre confiance,<br>
L'équipe **{{ config('app.name') }}**
@endcomponent
