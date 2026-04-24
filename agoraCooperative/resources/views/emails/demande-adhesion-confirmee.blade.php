@component('mail::message')
# Confirmation de votre demande d'adhésion

Bonjour {{ $demande->prenom }} {{ $demande->nom }},

Nous avons bien reçu votre demande d'adhésion à **Agora Coopérative**.

## Informations de votre demande

- **Email:** {{ $demande->email }}
- **Téléphone:** {{ $demande->telephone }}
- **Date de demande:** {{ $demande->date_demande->format('d/m/Y à H:i') }}

Votre demande est actuellement **en attente de traitement** par notre équipe administrative.

Vous recevrez un email de confirmation dès que votre demande aura été examinée.

## Prochaines étapes

1. Notre équipe va examiner votre demande
2. Vous recevrez une réponse par email sous 2 à 3 jours ouvrables
3. Si votre demande est approuvée, vous recevrez vos identifiants de connexion

Si vous avez des questions, n'hésitez pas à nous contacter.

Cordialement,  
L'équipe Agora Coopérative
@endcomponent
