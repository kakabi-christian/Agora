@extends('layouts.app')

@section('title', 'Tableau de bord - Agora Coopérative')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>👋 Bienvenue, {{ auth()->user()->prenom }} !</h2>
        <p style="color: var(--gray); margin-top: 0.5rem;">
            Code membre : <strong>{{ auth()->user()->code_membre }}</strong>
        </p>
    </div>
    
    <!-- Statistiques -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 1.5rem; border-radius: 10px;">
            <div style="font-size: 14px; opacity: 0.9;">Participations</div>
            <div style="font-size: 32px; font-weight: 700; margin: 0.5rem 0;">
                {{ auth()->user()->profil->nombre_participations ?? 0 }}
            </div>
            <div style="font-size: 12px; opacity: 0.8;">Événements et projets</div>
        </div>
        
        <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 1.5rem; border-radius: 10px;">
            <div style="font-size: 14px; opacity: 0.9;">Statut</div>
            <div style="font-size: 24px; font-weight: 700; margin: 0.5rem 0;">
                {{ auth()->user()->est_actif ? 'Actif' : 'Inactif' }}
            </div>
            <div style="font-size: 12px; opacity: 0.8;">Compte membre</div>
        </div>
        
        <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 1.5rem; border-radius: 10px;">
            <div style="font-size: 14px; opacity: 0.9;">Membre depuis</div>
            <div style="font-size: 24px; font-weight: 700; margin: 0.5rem 0;">
                {{ auth()->user()->date_inscription->format('Y') }}
            </div>
            <div style="font-size: 12px; opacity: 0.8;">{{ auth()->user()->date_inscription->diffForHumans() }}</div>
        </div>
    </div>
</div>

<!-- Actions rapides -->
<div class="card">
    <div class="card-header">
        <h2>⚡ Actions rapides</h2>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
        <a href="{{ route('profil.show') }}" class="btn btn-outline" style="padding: 1.5rem; text-align: center;">
            <div style="font-size: 32px; margin-bottom: 0.5rem;">👤</div>
            <div>Mon Profil</div>
        </a>
        
        <a href="{{ route('ressources.index') }}" class="btn btn-outline" style="padding: 1.5rem; text-align: center;">
            <div style="font-size: 32px; margin-bottom: 0.5rem;">📚</div>
            <div>Ressources</div>
        </a>
        
        <a href="#" class="btn btn-outline" style="padding: 1.5rem; text-align: center;">
            <div style="font-size: 32px; margin-bottom: 0.5rem;">📅</div>
            <div>Événements</div>
        </a>
        
        <a href="#" class="btn btn-outline" style="padding: 1.5rem; text-align: center;">
            <div style="font-size: 32px; margin-bottom: 0.5rem;">🚀</div>
            <div>Projets</div>
        </a>
    </div>
</div>

<!-- Informations du profil -->
<div class="card">
    <div class="card-header">
        <h2>📋 Informations du profil</h2>
    </div>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
        <div>
            <h3 style="color: var(--primary); margin-bottom: 1rem;">Coordonnées</h3>
            <p><strong>Email :</strong> {{ auth()->user()->email }}</p>
            <p><strong>Téléphone :</strong> {{ auth()->user()->telephone ?? 'Non renseigné' }}</p>
            <p><strong>Ville :</strong> {{ auth()->user()->ville ?? 'Non renseignée' }}</p>
        </div>
        
        <div>
            <h3 style="color: var(--primary); margin-bottom: 1rem;">Compte</h3>
            <p><strong>Rôle :</strong> 
                <span class="badge {{ auth()->user()->role === 'administrateur' ? 'badge-danger' : 'badge-info' }}">
                    {{ ucfirst(auth()->user()->role) }}
                </span>
            </p>
            <p><strong>Inscription :</strong> {{ auth()->user()->date_inscription->format('d/m/Y') }}</p>
            <p><strong>Dernière connexion :</strong> 
                {{ auth()->user()->profil->date_derniere_connexion ? auth()->user()->profil->date_derniere_connexion->format('d/m/Y à H:i') : 'Première connexion' }}
            </p>
        </div>
    </div>
    
    <div style="margin-top: 2rem; text-align: center;">
        <a href="{{ route('profil.edit') }}" class="btn btn-primary">
            Modifier mon profil
        </a>
    </div>
</div>

@if(auth()->user()->role === 'administrateur')
<!-- Section Admin -->
<div class="card" style="border: 2px solid var(--danger);">
    <div class="card-header">
        <h2>👨‍💼 Administration</h2>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
        <a href="{{ route('admin.demandes.index') }}" class="btn btn-danger" style="padding: 1.5rem; text-align: center;">
            <div style="font-size: 32px; margin-bottom: 0.5rem;">📝</div>
            <div>Demandes d'adhésion</div>
        </a>
        
        <a href="#" class="btn btn-danger" style="padding: 1.5rem; text-align: center;">
            <div style="font-size: 32px; margin-bottom: 0.5rem;">👥</div>
            <div>Gestion membres</div>
        </a>
        
        <a href="#" class="btn btn-danger" style="padding: 1.5rem; text-align: center;">
            <div style="font-size: 32px; margin-bottom: 0.5rem;">📊</div>
            <div>Statistiques</div>
        </a>
    </div>
</div>
@endif
@endsection
