<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

// Modèles
use App\Models\Membre;
use App\Models\Evenements;
use App\Models\Inscription_events;
use App\Models\Don;
use App\Models\Projets;
use App\Models\Participation_projets;
use App\Models\Ressource;
use App\Models\Partenaire;
use App\Models\DemandeAdhesion;
use App\Models\ContactMessage;

class StatsController extends Controller
{
    public function getDashboardStats(Request $request)
    {
        $startTime = microtime(true);
        $userId = auth()->id() ?? 'Admin-Session';
        
        // Récupération du filtre de période
        $period = $request->get('period', '1year'); 
        $startDate = $this->getStartDate($period);

        Log::info("📊 [STATS_GEN] Début - User: $userId - Période: $period");

        try {
            $now = Carbon::now();
            $totalMembres = Membre::count();

            // --- 1. ANALYSE DES MEMBRES ---
            Log::info("🔍 [STATS_MEMBRES] Calcul des ratios...");
            $villes = Membre::select('ville', DB::raw('count(*) as total'))
                ->whereNotNull('ville')
                ->groupBy('ville')
                ->orderByDesc('total')
                ->get()
                ->map(function ($v) use ($totalMembres) {
                    $v->pourcentage = $totalMembres > 0 ? round(($v->total / $totalMembres) * 100, 1) : 0;
                    return $v;
                });

            $membreStats = [
                'total' => $totalMembres,
                'actifs' => Membre::where('est_actif', true)->count(),
                'taux_activite' => $totalMembres > 0 ? round((Membre::where('est_actif', true)->count() / $totalMembres) * 100, 1) : 0,
                'nouveaux_periode' => Membre::where('created_at', '>=', $startDate)->count(),
                'villes_detaillees' => $villes
            ];

            // --- 2. FINANCES ---
            Log::info("💰 [STATS_FINANCE] Somme des revenus...");
            $dons = (float) Don::where('statut_paiement', 'paye')->where('created_at', '>=', $startDate)->sum('montant');
            $inscriptions = (float) Inscription_events::where('statut_paiement', 'paye')->where('created_at', '>=', $startDate)->sum('montant_paye');
            $revenuTotal = $dons + $inscriptions;

            $financeStats = [
                'total_revenus' => round($revenuTotal, 2),
                'part_dons' => $revenuTotal > 0 ? round(($dons / $revenuTotal) * 100, 1) : 0,
                'part_inscriptions' => $revenuTotal > 0 ? round(($inscriptions / $revenuTotal) * 100, 1) : 0,
                'top_donateurs' => Don::select('nom_donateur', DB::raw('SUM(montant) as total'))
                    ->where('statut_paiement', 'paye')
                    ->groupBy('nom_donateur')->orderByDesc('total')->take(5)->get(),
            ];

            // --- 3. PROJETS ---
            Log::info("🏗️ [STATS_PROJETS] Analyse d'impact...");
            $totalHeures = Participation_projets::where('created_at', '>=', $startDate)->sum('heures_contribuees') ?? 0;
            $nbPartcipantsUniques = Participation_projets::distinct('code_membre')->count();

            $projetStats = [
                'total' => Projets::count(),
                'en_cours' => Projets::where('statut', 'en_cours')->count(),
                'taux_engagement' => $totalMembres > 0 ? round(($nbPartcipantsUniques / $totalMembres) * 100, 1) : 0,
                'heures_benevolat' => $totalHeures,
                'valorisation_sociale' => $totalHeures * 3000,
            ];

            // --- 4. ÉVÉNEMENTS ---
            Log::info("📅 [STATS_EVENTS] Taux de conversion...");
            $eventStats = [
                'total' => Evenements::count(),
                'a_venir' => Evenements::where('date_debut', '>', $now)->count(),
                'taux_remplissage' => Inscription_events::count() > 0 ? round((Inscription_events::where('statut_paiement', 'paye')->count() / Inscription_events::count()) * 100, 1) : 0
            ];

            // --- 5. LOGISTIQUE & MESSAGES ---
            Log::info("✉️ [STATS_CONTACT] Ratios de réponse...");
            $contactStats = [
                'non_lus' => ContactMessage::where('lu', false)->count(),
                'taux_reponse' => ContactMessage::count() > 0 ? round((ContactMessage::where('lu', true)->count() / ContactMessage::count()) * 100, 1) : 0,
                'demandes_en_attente' => DemandeAdhesion::where('statut', 'en_attente')->count(),
            ];

            // --- 6. SYSTÈME & PARTENAIRES (Ajouté pour corriger l'erreur Angular) ---
            Log::info("⚙️ [STATS_SYSTEM] Partenaires et ressources...");
            $systemStats = [
                'partenaires' => Partenaire::count(),
                'telechargements' => Ressource::count(),
                'faq_actives' => DB::table('ressources')->where('type', 'document')->count(), // Exemple de logique
            ];

            // --- 7. ÉVOLUTION TEMPORELLE ---
            $evolution = DB::table('membres')
                ->select(DB::raw('count(*) as count'), DB::raw("DATE_FORMAT(created_at, '%b %Y') as label"))
                ->where('created_at', '>=', $startDate)
                ->groupBy('label')->get();

            $execTime = round(microtime(true) - $startTime, 3);
            Log::info("✅ [STATS_SUCCESS] Terminé en {$execTime}s");

            // RÉPONSE : Note que j'ai retiré le niveau 'data' pour correspondre à ton service Angular
            return response()->json([
                'status' => 'success',
                'meta' => [
                    'periode' => $period,
                    'generated_at' => $now->toDateTimeString(),
                    'execution_time' => $execTime
                ],
                'membres' => $membreStats,
                'finance' => $financeStats,
                'projets' => $projetStats,
                'evenements' => $eventStats,
                'contacts' => $contactStats,
                'systeme' => $systemStats, // <--- IMPORTANT : Correction de l'erreur frontend
                'evolution' => $evolution
            ]);

        } catch (\Exception $e) {
            Log::error("❌ [STATS_ERROR] Erreur Critique", ['msg' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Erreur de calcul'], 500);
        }
    }

    private function getStartDate($period) {
        switch($period) {
            case '30days':
                return Carbon::now()->subDays(30);
            case '6months':
                return Carbon::now()->subMonths(6);
            default:
                return Carbon::now()->subYear();
        }
    }
}