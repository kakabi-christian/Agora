{{-- views/emails/demande-adhesion-approuvee.blade.php --}}
@component('mail::message')
# Bienvenue à Agora Coopérative !

Bonjour {{ $demande->prenom }} {{ $demande->nom }},

Nous avons le plaisir de vous informer que votre demande d'adhésion a été **approuvée** !

Votre compte membre a été créé avec succès. Vous pouvez maintenant accéder à votre espace personnel.

## Vos identifiants de connexion

- **Code membre:** {{ $codeMembre }}
- **Email:** {{ $demande->email }}
@if($motDePasse)
- **Mot de passe temporaire:** `{{ $motDePasse }}`

@component('mail::panel')
⚠️ **Important:** Pour des raisons de sécurité, nous vous recommandons fortement de changer votre mot de passe lors de votre première connexion.
@endcomponent
@else
- **Mot de passe:** Utilisez votre mot de passe existant (votre compte était déjà créé)
@endif

@component('mail::button', ['url' => config('app.url') . '/api/auth/login'])
Se connecter
@endcomponent

## Prochaines étapes

1. Connectez-vous avec vos identifiants
@if($motDePasse)
2. Changez votre mot de passe temporaire
3. Complétez votre profil
4. Explorez les ressources et événements disponibles
@else
2. Complétez votre profil
3. Explorez les ressources et événements disponibles
@endif

@if($demande->commentaire_admin)
## Message de l'administrateur

{{ $demande->commentaire_admin }}
@endif

Nous sommes ravis de vous compter parmi nos membres !

Cordialement,  
L'équipe Agora Coopérative
@endcomponent
