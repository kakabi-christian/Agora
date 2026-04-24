@extends('layouts.app')

@section('title', 'Demande d\'adhésion - Agora Coopérative')

@section('content')
<div style="max-width: 800px; margin: 2rem auto;">
    <div class="card">
        <div class="card-header">
            <h2>📝 Demande d'adhésion</h2>
            <p style="color: var(--gray); margin-top: 0.5rem;">
                Rejoignez notre coopérative et participez à nos projets de développement local
            </p>
        </div>
        
        <form action="{{ route('demandes.store') }}" method="POST">
            @csrf
            
            <h3 style="color: var(--primary); margin-bottom: 1rem;">Informations personnelles</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="nom">Nom *</label>
                    <input 
                        type="text" 
                        id="nom" 
                        name="nom" 
                        class="form-control" 
                        value="{{ old('nom') }}"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="prenom">Prénom *</label>
                    <input 
                        type="text" 
                        id="prenom" 
                        name="prenom" 
                        class="form-control" 
                        value="{{ old('prenom') }}"
                        required
                    >
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-control" 
                        value="{{ old('email') }}"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="telephone">Téléphone *</label>
                    <input 
                        type="tel" 
                        id="telephone" 
                        name="telephone" 
                        class="form-control" 
                        value="{{ old('telephone') }}"
                        placeholder="0612345678"
                        required
                    >
                </div>
            </div>
            
            <div class="form-group">
                <label for="date_naissance">Date de naissance</label>
                <input 
                    type="date" 
                    id="date_naissance" 
                    name="date_naissance" 
                    class="form-control" 
                    value="{{ old('date_naissance') }}"
                >
            </div>
            
            <div class="form-group">
                <label for="profession">Profession</label>
                <input 
                    type="text" 
                    id="profession" 
                    name="profession" 
                    class="form-control" 
                    value="{{ old('profession') }}"
                    placeholder="Ex: Ingénieur agronome"
                >
            </div>
            
            <h3 style="color: var(--primary); margin: 2rem 0 1rem;">Adresse</h3>
            
            <div class="form-group">
                <label for="adresse">Adresse *</label>
                <input 
                    type="text" 
                    id="adresse" 
                    name="adresse" 
                    class="form-control" 
                    value="{{ old('adresse') }}"
                    placeholder="123 Rue de la République"
                    required
                >
            </div>
            
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="ville">Ville *</label>
                    <input 
                        type="text" 
                        id="ville" 
                        name="ville" 
                        class="form-control" 
                        value="{{ old('ville') }}"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="code_postal">Code postal *</label>
                    <input 
                        type="text" 
                        id="code_postal" 
                        name="code_postal" 
                        class="form-control" 
                        value="{{ old('code_postal') }}"
                        placeholder="75001"
                        required
                    >
                </div>
            </div>
            
            <h3 style="color: var(--primary); margin: 2rem 0 1rem;">Motivation et compétences</h3>
            
            <div class="form-group">
                <label for="motivation">Motivation *</label>
                <textarea 
                    id="motivation" 
                    name="motivation" 
                    class="form-control" 
                    placeholder="Expliquez pourquoi vous souhaitez rejoindre la coopérative..."
                    required
                >{{ old('motivation') }}</textarea>
                <small style="color: var(--gray);">Minimum 50 caractères</small>
            </div>
            
            <div class="form-group">
                <label for="competences">Compétences</label>
                <textarea 
                    id="competences" 
                    name="competences" 
                    class="form-control" 
                    placeholder="Listez vos compétences pertinentes (agriculture, gestion, communication, etc.)"
                >{{ old('competences') }}</textarea>
            </div>
            
            <div class="alert alert-info" style="margin-top: 2rem;">
                <strong>ℹ️ Information :</strong> Votre demande sera examinée par notre équipe administrative. 
                Vous recevrez une réponse par email sous 3 à 5 jours ouvrables.
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    Soumettre ma demande
                </button>
                <a href="{{ url('/') }}" class="btn btn-outline">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
