<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Membre;
use App\Models\Evenements;
use App\Models\Projets;
use App\Models\Partenaire;
use App\Models\Don;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardStatsController extends Controller
{
    /**
     * Récupère toutes les statistiques pour la page d'accueil
     */
    public function getHomeStats(): JsonResponse
    {
        try {
            Log::info('=== Récupération des stats dashboard ===');

            $stats = [
                'membres' => $this->getMembresStats(),
                'projets' => $this->getProjetsStats(),
                'evenements' => $this->getEvenementsStats(),
                'dons' => $this->getDonsStats(),
            ];

            return response()->json([
                'message' => 'Statistiques récupérées avec succès',
                'stats' => $stats
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur récupération stats dashboard: ' . $e->getMessage());
            return response()->json([
                'message' => 'Erreur lors de la récupération des statistiques',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Statistiques des membres
     */
    private function getMembresStats(): array
    {
        return [
            'total' => Membre::where('est_actif', true)->count(),
            'nouveaux_mois' => Membre::where('est_actif', true)
                ->whereMonth('date_inscription', now()->month)
                ->whereYear('date_inscription', now()->year)
                ->count(),
        ];
    }

    /**
     * Statistiques et liste des projets récents
     */
    private function getProjetsStats(): array
    {
        $projetsRecents = Projets::where('est_public', true)
            ->whereIn('statut', ['termine'])
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get()
            ->map(function ($projet) {
                return [
                    'id' => $projet->id,
                    'nom' => $projet->nom,
                    'description' => $projet->description,
                    'type' => $projet->type,
                    'statut' => $projet->statut,
                    'image_url' => $projet->image_url,
                    'participants_count' => $projet->participations()->count(),
                ];
            });

        return [
            'total' => Projets::whereIn('statut', ['termine'])->count(),
            //'en_cours' => Projets::where('statut', 'en_cours')->count(),
            'recents' => $projetsRecents,
        ];
    }

    /**
     * Statistiques et liste des événements à venir
     */
    private function getEvenementsStats(): array
    {
        $now = now();
        
        // Auto-mise à jour des événements terminés
        Evenements::where('date_fin', '<', $now)
            ->whereNotIn('statut', ['termine', 'annule'])
            ->update(['statut' => 'termine']);

        $evenementsAvenir = Evenements::whereIn('statut', ['planifie', 'en_cours'])
            ->where('date_debut', '>=', $now)
            ->orderBy('date_debut', 'asc')
            ->take(3)
            ->get()
            ->map(function ($event) {
                return [
                    'code_evenement' => $event->code_evenement,
                    'titre' => $event->titre,
                    'description' => $event->description,
                    'date_debut' => $event->date_debut->format('Y-m-d'),
                    'date_fin' => $event->date_fin->format('Y-m-d'),
                    'lieu' => $event->lieu,
                    'ville' => $event->ville,
                    'type' => $event->type,
                    'image_url' => $event->image_url,
                    'places_disponibles' => $event->places_disponibles,
                    'inscrits_count' => $event->inscriptions()
                        ->whereIn('statut_participation', ['inscrit', 'present'])
                        ->count(),
                ];
            });

        return [
            'total_annee' => Evenements::whereYear('date_debut', now()->year)->count(),
            'a_venir' => Evenements::whereIn('statut', ['planifie', 'en_cours'])
                ->where('date_debut', '>=', $now)
                ->count(),
            'prochains' => $evenementsAvenir,
        ];
    }

    /**
     * Statistiques des dons
     */
    private function getDonsStats(): array
    {
        $anneeActuelle = now()->year;

        $totalDons = Don::where('statut_paiement', 'paye')
            ->whereYear('date_don', $anneeActuelle)
            ->sum('montant');

        $nombreDonateurs = Don::where('statut_paiement', 'paye')
            ->whereYear('date_don', $anneeActuelle)
            ->distinct('email_donateur')
            ->count('email_donateur');

        $projetsFinalises = Projets::where('statut', 'termine')
            ->whereYear('created_at', $anneeActuelle)
            ->count();

        return [
            'total_montant' => round($totalDons, 2),
            'nombre_donateurs' => $nombreDonateurs,
            'projets_finances' => $projetsFinalises,
            'annee' => $anneeActuelle,
        ];
    }

    /**
     * Liste des partenaires actifs pour la page d'accueil
     */
    public function getPartenairesActifs(): JsonResponse
    {
        try {
            $now = now();
            
            $partenaires = Partenaire::where('est_actif', true)
                ->where(function ($query) use ($now) {
                    $query->whereNull('date_fin')
                        ->orWhere('date_fin', '>=', $now);
                })
                ->orderBy('ordre_affichage', 'asc')
                ->orderBy('niveau_partenariat', 'desc')
                ->take(8) // Limite à 8 partenaires pour l'affichage
                ->get()
                ->map(function ($partenaire) {
                    return [
                        'code_partenaire' => $partenaire->code_partenaire,
                        'nom' => $partenaire->nom,
                        'logo_url' => $partenaire->logo_url,
                        'site_web' => $partenaire->site_web,
                        'type' => $partenaire->type,
                        'niveau_partenariat' => $partenaire->niveau_partenariat,
                    ];
                });

            return response()->json([
                'message' => 'Partenaires actifs récupérés',
                'partenaires' => $partenaires
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur récupération partenaires: ' . $e->getMessage());
            return response()->json([
                'message' => 'Erreur lors de la récupération des partenaires',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}