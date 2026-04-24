<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HistoriqueParticipationResource;
use App\Models\HistoriqueParticipation;
use Illuminate\Http\Request;

class HistoriqueParticipationController extends Controller
{
    /**
     * Display a listing of participation history.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $codeMembre = $request->user()->code_membre;
        
        $query = HistoriqueParticipation::where('code_membre', $codeMembre);
        
        // Filtrer par type de participation
        if ($request->has('type_participation')) {
            $query->where('type_participation', $request->type_participation);
        }
        
        // Filtrer par plage de dates
        if ($request->has('date_debut')) {
            $query->where('date_participation', '>=', $request->date_debut);
        }
        
        if ($request->has('date_fin')) {
            $query->where('date_participation', '<=', $request->date_fin);
        }
        
        // Trier par date décroissante
        $historique = $query->orderBy('date_participation', 'desc')->paginate(20);
        
        return HistoriqueParticipationResource::collection($historique);
    }

    /**
     * Export participation history as PDF.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        $codeMembre = $request->user()->code_membre;
        
        $historique = HistoriqueParticipation::where('code_membre', $codeMembre)
            ->orderBy('date_participation', 'desc')
            ->get();
        
        // TODO: Générer PDF avec DomPDF ou similaire
        // Pour l'instant, retourner JSON
        
        return response()->json([
            'message' => 'Export PDF non implémenté. Utilisez une bibliothèque comme DomPDF.',
            'data' => HistoriqueParticipationResource::collection($historique),
        ]);
    }
}
