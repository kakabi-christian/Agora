@extends('layouts.app')

@section('title', 'Mon Profil - Agora Coopérative')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2>👤 Mon Profil</h2>
                <p style="color: var(--gray); margin-top: 0.5rem;">
                    {{ auth()->user()->prenom }} {{ auth()->user()->nom }}
                </p>
            </div>
            <a href="{{ route('profil.edit') }}" class="btn btn-primary">
                ✏️ Modifier
            </a>
        </div>
        
        <!-- Photo de profil -->
        <div style="text-align: center; margin-bottom: 2rem;">
            @if(auth()->user()->photo_url)
                <img src="{{ auth()->user()->photo_url }}" alt="Photo de profil" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 4px solid var(--primary);">
            @else
                <div style="width: 150px; height: 150px; border-radius: 50%; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); display: inline-flex; align-items: center; justify-content: center; color: white; font-size: 48px; font-weight: 700;">
                    {{ strtoupper(substr(auth()->user()->prenom, 0, 1)) }}{{ strtoupper(substr(auth()->user()->nom, 0, 1)) }}
                </div>
            @endif
        </div>
        
        <!-- Informations personnelles -->
        <h3 style="color: var(--primary); margin-bottom: 1rem;">📋 Informations personnelles</h3>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
            <div>
                <p style="color: var(--gray); font-size: 14px; margin-bottom: 0.25rem;">Code membre</p>
                <p style="font-weight: 600;">{{ auth()->user()->code_membre }}</p>
            </div>
            
            <div>
                <p style="color: var(--gray); font-size: 14px; margin-bottom: 0.25rem;">Email</p>
                <p style="font-weight: 600;">{{ auth()->user()->email }}</p>
            </div>
            
            <div>
                <p style="color: var(--gray); font-size: 14px; margin-bottom: 0.25rem;">Téléphone</p>
                <p style="font-weight: 600;">{{ auth()->user()->telephone ?? 'Non renseigné' }}</p>
            </div>
            
            <div>
                <p style="color: var(--gray); font-size: 14px; margin-bottom: 0.25rem;">Ville</p>
                <p style="font-weight: 600;">{{ auth()->user()->ville ?? 'Non renseignée' }}</p>
            </div>
            
            <div>
                <p style="color: var(--gray); font-size: 14px; margin-bottom: 0.25rem;">Rôle</p>
                <p>
                    <span class="badge {{ auth()->user()->role === 'administrateur' ? 'badge-danger' : 'badge-info' }}">
                        {{ ucfirst(auth()->user()->role) }}
                    </span>
                </p>
            </div>
            
            <div>
                <p style="color: var(--gray); font-size: 14px; margin-bottom: 0.25rem;">Statut</p>
                <p>
                    <span class="badge {{ auth()->user()->est_actif ? 'badge-success' : 'badge-warning' }}">
                        {{ auth()->user()->est_actif ? 'Actif' : 'Inactif' }}
                    </span>
                </p>
            </div>
        </div>
        
        @if(auth()->user()->biographie)
        <div style="margin-bottom: 2rem;">
            <h3 style="color: var(--primary); margin-bottom: 1rem;">✍️ Biographie</h3>
            <p style="line-height: 1.8;">{{ auth()->user()->biographie }}</p>
        </div>
        @endif
        
        <!-- Compétences -->
        @if(auth()->user()->profil && auth()->user()->profil->competences)
        <div style="margin-bottom: 2rem;">
            <h3 style="color: var(--primary); margin-bottom: 1rem;">💼 Compétences</h3>
            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                @foreach(auth()->user()->profil->competences as $competence)
                    <span class="badge badge-info">{{ $competence }}</span>
                @endforeach
            </div>
        </div>
        @endif
        
        <!-- Centres d'intérêt -->
        @if(auth()->user()->profil && auth()->user()->profil->interets)
        <div style="margin-bottom: 2rem;">
            <h3 style="color: var(--primary); margin-bottom: 1rem;">🎯 Centres d'intérêt</h3>
            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                @foreach(auth()->user()->profil->interets as $interet)
                    <span class="badge badge-success">{{ $interet }}</span>
                @endforeach
            </div>
        </div>
        @endif
        
        <!-- Statistiques -->
        <div style="border-top: 2px solid var(--light); padding-top: 2rem; margin-top: 2rem;">
            <h3 style="color: var(--primary); margin-bottom: 1rem;">📊 Statistiques</h3>
            
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                <div style="text-align: center; padding: 1rem; background-color: var(--light); border-radius: 8px;">
                    <div style="font-size: 32px; font-weight: 700; color: var(--primary);">
                        {{ auth()->user()->profil->nombre_participations ?? 0 }}
                    </div>
                    <div style="font-size: 14px; color: var(--gray);">Participations</div>
                </div>
                
                <div style="text-align: center; padding: 1rem; background-color: var(--light); border-radius: 8px;">
                    <div style="font-size: 32px; font-weight: 700; color: var(--primary);">
                        {{ auth()->user()->date_inscription->diffInDays(now()) }}
                    </div>
                    <div style="font-size: 14px; color: var(--gray);">Jours membre</div>
                </div>
                
                <div style="text-align: center; padding: 1rem; background-color: var(--light); border-radius: 8px;">
                    <div style="font-size: 32px; font-weight: 700; color: var(--primary);">
                        {{ auth()->user()->ressources->count() ?? 0 }}
                    </div>
                    <div style="font-size: 14px; color: var(--gray);">Ressources partagées</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Actions -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 2rem;">
        <a href="{{ route('profil.edit') }}" class="btn btn-primary">
            ✏️ Modifier mon profil
        </a>
        <a href="{{ route('profil.password') }}" class="btn btn-outline">
            🔒 Changer mon mot de passe
        </a>
    </div>
</div>
@endsection
