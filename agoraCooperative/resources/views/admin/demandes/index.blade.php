@extends('layouts.app')

@section('title', 'Gestion des demandes - Administration')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>📝 Gestion des demandes d'adhésion</h2>
        <p style="color: var(--gray); margin-top: 0.5rem;">
            Examinez et traitez les demandes d'adhésion
        </p>
    </div>
    
    <!-- Filtres -->
    <div style="display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap;">
        <a href="{{ route('admin.demandes.index') }}" class="btn {{ !request('statut') ? 'btn-primary' : 'btn-outline' }}">
            Toutes
        </a>
        <a href="{{ route('admin.demandes.index', ['statut' => 'en_attente']) }}" class="btn {{ request('statut') === 'en_attente' ? 'btn-primary' : 'btn-outline' }}">
            En attente
        </a>
        <a href="{{ route('admin.demandes.index', ['statut' => 'approuvee']) }}" class="btn {{ request('statut') === 'approuvee' ? 'btn-primary' : 'btn-outline' }}">
            Approuvées
        </a>
        <a href="{{ route('admin.demandes.index', ['statut' => 'rejetee']) }}" class="btn {{ request('statut') === 'rejetee' ? 'btn-primary' : 'btn-outline' }}">
            Rejetées
        </a>
    </div>
    
    <!-- Liste des demandes -->
    @if($demandes->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: var(--light); text-align: left;">
                        <th style="padding: 1rem; border-bottom: 2px solid var(--primary);">Candidat</th>
                        <th style="padding: 1rem; border-bottom: 2px solid var(--primary);">Email</th>
                        <th style="padding: 1rem; border-bottom: 2px solid var(--primary);">Ville</th>
                        <th style="padding: 1rem; border-bottom: 2px solid var(--primary);">Date</th>
                        <th style="padding: 1rem; border-bottom: 2px solid var(--primary);">Statut</th>
                        <th style="padding: 1rem; border-bottom: 2px solid var(--primary);">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($demandes as $demande)
                    <tr style="border-bottom: 1px solid var(--light);">
                        <td style="padding: 1rem;">
                            <strong>{{ $demande->prenom }} {{ $demande->nom }}</strong>
                            @if($demande->profession)
                                <br><small style="color: var(--gray);">{{ $demande->profession }}</small>
                            @endif
                        </td>
                        <td style="padding: 1rem;">{{ $demande->email }}</td>
                        <td style="padding: 1rem;">{{ $demande->ville }}</td>
                        <td style="padding: 1rem;">{{ $demande->date_demande->format('d/m/Y') }}</td>
                        <td style="padding: 1rem;">
                            @if($demande->statut === 'en_attente')
                                <span class="badge badge-warning">En attente</span>
                            @elseif($demande->statut === 'approuvee')
                                <span class="badge badge-success">Approuvée</span>
                            @elseif($demande->statut === 'rejetee')
                                <span class="badge badge-danger">Rejetée</span>
                            @endif
                        </td>
                        <td style="padding: 1rem;">
                            <a href="{{ route('admin.demandes.show', $demande->id) }}" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 14px;">
                                👁️ Voir
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div style="margin-top: 2rem;">
            {{ $demandes->links() }}
        </div>
    @else
        <div class="alert alert-info">
            ℹ️ Aucune demande trouvée pour ce filtre.
        </div>
    @endif
</div>
@endsection
