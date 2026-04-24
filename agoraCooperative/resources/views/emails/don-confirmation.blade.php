@component('mail::message')
# Merci pour votre don ! 🙏

Cher(e) {{ $don->anonyme ? 'Donateur' : $don->nom_donateur }},

Nous avons bien reçu votre don et nous vous en remercions chaleureusement.

@component('mail::panel')
**Détails de votre don :**

- **Montant :** {{ number_format($don->montant, 0, ',', ' ') }} XAF
- **Type :** {{ ucfirst($don->type) }}
- **Date :** {{ $don->date_don->format('d/m/Y') }}
- **Référence :** {{ $don->reference_paiement }}
@if($don->numero_recu)
- **N° Reçu :** {{ $don->numero_recu }} 
@endif
@endcomponent

@if($don->deductible_impots)
Votre reçu fiscal est disponible en pièce jointe de cet email. Conservez-le précieusement pour votre déclaration d'impôts.
@endif

Grâce à votre générosité, nous pouvons continuer à développer nos projets et soutenir notre communauté.

@if($don->message_donateur)
---
*Votre message :*
> {{ $don->message_donateur }}
@endif

@component('mail::button', ['url' => config('app.url')])
Visiter notre site
@endcomponent

Avec toute notre gratitude,<br>
La Coopérative **{{ config('app.name') }}**
@endcomponent
