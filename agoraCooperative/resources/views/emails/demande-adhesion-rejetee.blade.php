@component('mail::message')
# Réponse à votre demande d'adhésion

Bonjour {{ $demande->prenom }} {{ $demande->nom }},

Nous vous remercions de l'intérêt que vous portez à **Agora Coopérative**.

Après examen de votre demande d'adhésion, nous sommes au regret de vous informer que nous ne pouvons pas y donner une suite favorable pour le moment.

## Motif du refus

{{ $demande->commentaire_admin }}

## Nouvelle demande

Vous pouvez soumettre une nouvelle demande d'adhésion ultérieurement si votre situation évolue.

@component('mail::button', ['url' => config('app.url') . '/api/demandes-adhesion'])
Soumettre une nouvelle demande
@endcomponent

Si vous avez des questions concernant cette décision, n'hésitez pas à nous contacter.

Cordialement,  
L'équipe Agora Coopérative
@endcomponent
