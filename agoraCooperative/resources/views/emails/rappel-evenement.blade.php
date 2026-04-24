@component('mail::message')
# 📅 Rappel d'Événement

Bonjour {{ $membre->prenom }},

Nous vous rappelons que l'événement auquel vous êtes inscrit(e) aura lieu **demain** !

@component('mail::panel')
**{{ $evenement->titre }}**

📅 **Date :** {{ $evenement->date_debut->format('d/m/Y à H:i') }}
@if($evenement->date_fin)
🏁 **Fin :** {{ $evenement->date_fin->format('d/m/Y à H:i') }}
@endif
📍 **Lieu :** {{ $evenement->lieu ?? 'À confirmer' }}
@if($evenement->adresse)
📮 **Adresse :** {{ $evenement->adresse }}
@if($evenement->ville)
, {{ $evenement->ville }}
@endif
@endif
@endcomponent

@if($evenement->instructions)
## Instructions Importantes

{{ $evenement->instructions }}
@endif

## Informations Pratiques

- ⏰ **Arrivez 15 minutes avant** le début pour faciliter l'accueil
- 📱 **Présentez votre confirmation** (imprimée ou sur mobile) à l'entrée
@if($inscription->statut_paiement === 'paye')
- ✅ **Votre paiement est confirmé**
@endif

@component('mail::button', ['url' => config('app.url') . '/evenements/' . $evenement->code_evenement . '/inscription/pdf', 'color' => 'success'])
Voir ma confirmation
@endcomponent

@if(now()->diffInHours($evenement->date_debut) > 48)
## Empêchement ?

Si vous ne pouvez plus participer, merci d'annuler votre inscription au moins 48h à l'avance.

@component('mail::button', ['url' => config('app.url') . '/mes-inscriptions', 'color' => 'secondary'])
Gérer mes inscriptions
@endcomponent
@endif

@if($evenement->description)
## À Propos de l'Événement

{{ Str::limit($evenement->description, 300) }}
@endif

Nous avons hâte de vous accueillir !

À très bientôt,<br>
L'équipe **{{ config('app.name') }}**

---

<small style="color: #666;">
Vous recevez cet email car vous êtes inscrit(e) à cet événement.<br>
Pour toute question, contactez-nous à {{ config('mail.from.address') }}
</small>
@endcomponent
