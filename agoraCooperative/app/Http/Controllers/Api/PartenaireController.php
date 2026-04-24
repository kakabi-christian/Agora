<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Partenaire;
use Illuminate\Http\Request;
use App\Http\Requests\PartenaireRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PartenaireController extends Controller
{
    /**
     * Liste paginée des partenaires
     */
    public function index()
    {
        Log::info('Récupération de la liste des partenaires avec pagination.');

        $partenaires = Partenaire::orderBy('ordre_affichage', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(10); // 10 par page

        Log::info('Partenaires récupérés', ['count' => $partenaires->count()]);

        return response()->json([
            'message' => 'Liste des partenaires',
            'partenaires' => $partenaires
        ], 200);
    }

    /**
     * Création d’un partenaire
     */
    public function store(PartenaireRequest $request)
    {
        Log::info('Création d\'un nouveau partenaire.', ['request' => $request->all()]);

        $data = $request->validated();
        Log::info('Données validées', $data);

        // 🔹 Génération automatique du code partenaire
        $data['code_partenaire'] = $this->generateCodePartenaire();
        Log::info('Code partenaire généré', ['code' => $data['code_partenaire']]);

        // 🔹 Upload du logo
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $filename = Str::uuid() . '.' . $logo->getClientOriginalExtension();

            $logo->storeAs('public/partenaires', $filename);

            $data['logo_url'] = 'storage/partenaires/' . $filename;
            Log::info('Logo uploadé', ['logo_url' => $data['logo_url']]);
        } else {
            Log::info('Aucun logo uploadé.');
        }

        $partenaire = Partenaire::create($data);
        Log::info('Partenaire créé en base de données', ['id' => $partenaire->code_partenaire]);

        return response()->json([
            'message' => 'Partenaire créé avec succès',
            'partenaire' => $partenaire
        ], 201);
    }

    /**
     * Détails d’un partenaire
     */
    public function show(Partenaire $partenaire)
    {
        Log::info('Affichage d\'un partenaire', ['code_partenaire' => $partenaire->code_partenaire]);

        return response()->json([
            'message' => 'Détails du partenaire',
            'partenaire' => $partenaire
        ], 200);
    }

    /**
     * Mise à jour d’un partenaire
     */
    public function update(PartenaireRequest $request, Partenaire $partenaire)
    {
        Log::info('Mise à jour d\'un partenaire', ['code_partenaire' => $partenaire->code_partenaire, 'request' => $request->all()]);

        $data = $request->validated();

        // 🔹 Nouveau logo (optionnel)
        if ($request->hasFile('logo')) {

            // Supprimer l’ancien logo si existe
            if ($partenaire->logo_url) {
                $oldPath = str_replace('storage/', 'public/', $partenaire->logo_url);
                Storage::delete($oldPath);
                Log::info('Ancien logo supprimé', ['old_logo_url' => $partenaire->logo_url]);
            }

            $logo = $request->file('logo');
            $filename = Str::uuid() . '.' . $logo->getClientOriginalExtension();
            $logo->storeAs('public/partenaires', $filename);

            $data['logo_url'] = 'storage/partenaires/' . $filename;
            Log::info('Nouveau logo uploadé', ['logo_url' => $data['logo_url']]);
        }

        $partenaire->update($data);
        Log::info('Partenaire mis à jour', ['code_partenaire' => $partenaire->code_partenaire]);

        return response()->json([
            'message' => 'Partenaire mis à jour avec succès',
            'partenaire' => $partenaire
        ], 200);
    }

    /**
     * Suppression (soft delete) d’un partenaire
     */
    public function destroy(Partenaire $partenaire)
    {
        Log::info('Suppression d\'un partenaire', ['code_partenaire' => $partenaire->code_partenaire]);

        $partenaire->delete();

        return response()->json([
            'message' => 'Partenaire supprimé avec succès'
        ], 200);
    }

    /**
     * Génération automatique du code partenaire
     */
    private function generateCodePartenaire(): string
    {
        do {
            $code = 'PART-' . strtoupper(Str::random(6));
        } while (Partenaire::where('code_partenaire', $code)->exists());

        Log::info('Code partenaire unique généré', ['code' => $code]);

        return $code;
    }
}
