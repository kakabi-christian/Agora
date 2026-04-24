<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DonResource;
use App\Models\Don;
use App\Services\PdfService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DonController extends Controller
{
    private PdfService $pdfService;

    public function __construct(PdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    /**
     * Liste des dons (admin)
     */
    public function index(Request $request): JsonResponse
    {
        $query = Don::with('membre');

        // Filtres
        if ($request->has('statut')) {
            $query->where('statut_paiement', $request->statut);
        }
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        if ($request->has('date_debut')) {
            $query->whereDate('date_don', '>=', $request->date_debut);
        }
        if ($request->has('date_fin')) {
            $query->whereDate('date_don', '<=', $request->date_fin);
        }

        $dons = $query->orderBy('created_at', 'desc')->paginate(15);

        // Statistiques
        $stats = [
            'total_dons' => Don::where('statut_paiement', 'paye')->sum('montant'),
            'nombre_dons' => Don::where('statut_paiement', 'paye')->count(),
            'dons_en_attente' => Don::where('statut_paiement', 'en_attente')->count(),
        ];

        return response()->json([
            'message' => 'Liste des dons',
            'dons' => DonResource::collection($dons),
            'stats' => $stats,
            'pagination' => [
                'total' => $dons->total(),
                'per_page' => $dons->perPage(),
                'current_page' => $dons->currentPage(),
                'last_page' => $dons->lastPage(),
            ],
        ]);
    }

    /**
     * Détail d'un don (admin)
     */
    public function show(int $id): JsonResponse
    {
        $don = Don::with('membre')->find($id);

        if (!$don) {
            return response()->json(['message' => 'Don non trouvé.'], 404);
        }

        return response()->json([
            'don' => new DonResource($don),
        ]);
    }

    /**
     * Télécharger le reçu d'un don
     */
    public function telechargerRecu(int $id)
    {
        $don = Don::find($id);

        if (!$don) {
            return response()->json(['message' => 'Don non trouvé.'], 404);
        }

        if ($don->statut_paiement !== 'paye' || !$don->numero_recu) {
            return response()->json(['message' => 'Reçu non disponible. Le don doit être payé.'], 422);
        }

        return $this->pdfService->telechargerRecuDon($don);
    }

/**
 * Retourne le montant total de TOUS les dons cumulés.
 * Gère le déchiffrement automatique via Eloquent Casts.
 */
public function getTotalDons(): JsonResponse
{
    $startTime = microtime(true);
    $userId = auth()->id() ?? 'Invité/Admin';

    Log::info("💰 [DON_TOTAL] Début du calcul du revenu global.", [
        'user_id' => $userId,
        'ip' => request()->ip()
    ]);

    try {
        // On récupère uniquement les colonnes nécessaires pour économiser la mémoire
        // Le cast 'encrypted' déchiffre automatiquement le montant à l'accès
        $dons = Don::select('id', 'montant', 'statut_paiement')->get();
        
        $count = $dons->count();
        $totalGlobal = 0;
        $erreursDechiffrement = 0;

        Log::debug("🔍 [DON_TOTAL] Analyse de {$count} enregistrements en base de données.");

        foreach ($dons as $don) {
            try {
                // L'accès à $don->montant déclenche le déchiffrement
                $totalGlobal += (float) ($don->montant ?? 0);
            } catch (\Exception $decryptEx) {
                $erreursDechiffrement++;
                Log::error("⚠️ [DON_TOTAL] Erreur de déchiffrement sur le Don ID: {$don->id}", [
                    'message' => $decryptEx->getMessage()
                ]);
            }
        }

        $execTime = round(microtime(true) - $startTime, 3);

        // Log de succès avec résumé
        Log::notice("✅ [DON_TOTAL] Calcul terminé avec succès.", [
            'montant_total' => $totalGlobal,
            'nombre_dons' => $count,
            'erreurs_dechiffrement' => $erreursDechiffrement,
            'temps_execution' => "{$execTime}s"
        ]);

        return response()->json([
            'status' => 'success',
            'total_general' => (float) $totalGlobal,
            'devise' => 'FCFA',
            'meta' => [
                'count' => $count,
                'errors' => $erreursDechiffrement,
                'execution_time' => $execTime
            ]
        ]);

    } catch (\Exception $e) {
        Log::critical("❌ [DON_TOTAL] Erreur fatale lors du calcul !", [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'status' => 'error',
            'message' => 'Une erreur technique est survenue lors de l\'agrégation des dons.'
        ], 500);
    }
}
}
