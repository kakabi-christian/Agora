@extends('layouts.app')

@section('title', 'Connexion - Agora Coopérative')

@section('content')
<div style="max-width: 500px; margin: 4rem auto;">
    <div class="card">
        <div class="card-header">
            <h2>🔐 Connexion</h2>
            <p style="color: var(--gray); margin-top: 0.5rem;">Accédez à votre espace membre</p>
        </div>
        
        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="email">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-control" 
                    value="{{ old('email') }}"
                    placeholder="votre.email@example.com"
                    required
                    autofocus
                >
            </div>
            
            <div class="form-group">
                <label for="mot_de_passe">Mot de passe</label>
                <input 
                    type="password" 
                    id="mot_de_passe" 
                    name="mot_de_passe" 
                    class="form-control" 
                    placeholder="••••••••"
                    required
                >
            </div>
            
            <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem;">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember" style="margin: 0; font-weight: normal;">Se souvenir de moi</label>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                Se connecter
            </button>
        </form>
        
        <div style="text-align: center; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--light);">
            <p style="color: var(--gray);">
                Pas encore membre ? 
                <a href="{{ route('demandes.create') }}" style="color: var(--primary); font-weight: 600;">
                    Faire une demande d'adhésion
                </a>
            </p>
        </div>
    </div>
</div>
@endsection
