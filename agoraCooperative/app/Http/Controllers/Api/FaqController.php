<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use App\Http\Requests\FaqRequest;

class FaqController extends Controller
{
    /**
     * Affiche toutes les FAQs actives avec pagination.
     */
    public function index(Request $request)
    {
        // Nombre d'éléments par page (par défaut 10)
        $perPage = $request->query('per_page', 10);

        $faqs = Faq::where('est_actif', true)
                    ->orderBy('ordre_affichage')
                    ->paginate($perPage);

        return response()->json($faqs);
    }

    /**
     * Crée une nouvelle FAQ.
     */
    public function store(FaqRequest $request)
    {
        $faq = Faq::create($request->validated());

        return response()->json([
            'message' => 'FAQ créée avec succès.',
            'faq' => $faq
        ], 201);
    }

    /**
     * Affiche une FAQ spécifique et incrémente le compteur de vues.
     */
    public function show(Faq $faq)
    {
        // Incrémenter le nombre de vues
        $faq->increment('nombre_vues');

        return response()->json($faq);
    }

    /**
     * Met à jour une FAQ existante.
     */
    public function update(FaqRequest $request, Faq $faq)
    {
        $faq->update($request->validated());

        return response()->json([
            'message' => 'FAQ mise à jour avec succès.',
            'faq' => $faq
        ]);
    }

    /**
     * Supprime une FAQ (soft delete).
     */
    public function destroy(Faq $faq)
    {
        $faq->delete();

        return response()->json([
            'message' => 'FAQ supprimée avec succès.'
        ]);
    }

    /**
     * Voter sur l'utilité d'une FAQ (utile ou inutile)
     */
    public function vote(Request $request, Faq $faq)
    {
        $request->validate([
            'vote' => 'required|in:utile,inutile'
        ]);

        if ($request->vote === 'utile') {
            $faq->increment('nombre_utile');
        } else {
            $faq->increment('nombre_inutile');
        }

        return response()->json([
            'message' => 'Vote enregistré.',
            'faq' => $faq
        ]);
    }
}