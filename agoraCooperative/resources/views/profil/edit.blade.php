@extends('layouts.app')

@section('title', 'Modifier mon profil - Agora Coopérative')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <h2>✏️ Modifier mon profil</h2>
            <p style="color: var(--gray); margin-top: 0.5rem;">
                Mettez à jour vos informations personnelles
            </p>
        </div>
        
        <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <!-- Photo de profil -->
            <div class="form-group" style="text-align: center;">
                @if(auth()->user()->photo_url)
                    <img src="{{ auth()->user()->photo_url }}" alt="Photo" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; margin-bottom: 1rem;">
                @else
                    <div style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); display: inline-flex; align-items: center; justify-content: center; color: white; font-size: 36px; font-weight: 700; margin-bottom: 1rem;">
                        {{ strtoupper(substr(auth()->user()->prenom, 0, 1)) }}{{ strtoupper(substr(auth()->user()->nom, 0, 1)) }}
                    </div>
                @endif
                
                <div>
                    <label for="photo" class="btn btn-outline" style="cursor: pointer;">
                        📷 Changer la photo
                    </label>
                    <input type="file" id="photo" name="photo" accept="image/*" style="display: none;">
                </div>
            </div>
            
            <h3 style="color: var(--primary); margin: 2rem 0 1rem;">Informations de base</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input 
                        type="text" 
                        id="nom" 
                        name="nom" 
                        class="form-control" 
                        value="{{ old('nom', auth()->user()->nom) }}"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input 
                        type="text" 
                        id="prenom" 
                        name="prenom" 
                        class="form-control" 
                        value="{{ old('prenom', auth()->user()->prenom) }}"
                        required
                    >
                </div>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-control" 
                    value="{{ old('email', auth()->user()->email) }}"
                    required
                >
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="telephone">Téléphone</label>
                    <input 
                        type="tel" 
                        id="telephone" 
                        name="telephone" 
                        class="form-control" 
                        value="{{ old('telephone', auth()->user()->telephone) }}"
                    >
                </div>
                
                <div class="form-group">
                    <label for="ville">Ville</label>
                    <input 
                        type="text" 
                        id="ville" 
                        name="ville" 
                        class="form-control" 
                        value="{{ old('ville', auth()->user()->ville) }}"
                    >
                </div>
            </div>
            
            <div class="form-group">
                <label for="adresse">Adresse</label>
                <input 
                    type="text" 
                    id="adresse" 
                    name="adresse" 
                    class="form-control" 
                    value="{{ old('adresse', auth()->user()->adresse) }}"
                >
            </div>
            
            <div class="form-group">
                <label for="code_postal">Code postal</label>
                <input 
                    type="text" 
                    id="code_postal" 
                    name="code_postal" 
                    class="form-control" 
                    value="{{ old('code_postal', auth()->user()->code_postal) }}"
                >
            </div>
            
            <div class="form-group">
                <label for="biographie">Biographie</label>
                <textarea 
                    id="biographie" 
                    name="biographie" 
                    class="form-control" 
                    placeholder="Parlez-nous de vous..."
                >{{ old('biographie', auth()->user()->biographie) }}</textarea>
            </div>
            
            <h3 style="color: var(--primary); margin: 2rem 0 1rem;">Compétences et intérêts</h3>
            
            <div class="form-group">
                <label for="competences">Compétences</label>
                <textarea 
                    id="competences" 
                    name="competences" 
                    class="form-control" 
                    placeholder="Ex: Agriculture biologique, Gestion de projet, Communication..."
                >{{ old('competences', auth()->user()->profil && auth()->user()->profil->competences ? implode(', ', auth()->user()->profil->competences) : '') }}</textarea>
                <small style="color: var(--gray);">Séparez les compétences par des virgules</small>
            </div>
            
            <div class="form-group">
                <label for="interets">Centres d'intérêt</label>
                <textarea 
                    id="interets" 
                    name="interets" 
                    class="form-control" 
                    placeholder="Ex: Permaculture, Développement durable, Circuits courts..."
                >{{ old('interets', auth()->user()->profil && auth()->user()->profil->interets ? implode(', ', auth()->user()->profil->interets) : '') }}</textarea>
                <small style="color: var(--gray);">Séparez les intérêts par des virgules</small>
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    💾 Enregistrer les modifications
                </button>
                <a href="{{ route('profil.show') }}" class="btn btn-outline">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
