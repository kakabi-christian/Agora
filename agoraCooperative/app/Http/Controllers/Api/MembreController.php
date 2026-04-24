<?php

namespace App\Http\Controllers\Api;

use App\Exports\MembresExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateMembreRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfilRequest;
use App\Http\Requests\UploadPhotoRequest;
use App\Http\Resources\MembreResource;
use App\Http\Resources\ProfilResource;
use App\Models\Membre;
use App\Services\FileStorageService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class MembreController extends Controller
{
    protected $fileService;

    public function __construct(FileStorageService $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $codeMembre
     * @return Response
     */
    public function show($codeMembre)
    {
        $membre = Membre::where('code_membre', $codeMembre)
            ->with('profil')
            ->firstOrFail();

        // Vérifier permissions (propriétaire ou admin)
        $user = auth()->user();
        if ($user->code_membre !== $codeMembre && $user->role !== 'administrateur') {
            return response()->json([
                'message' => 'Accès refusé.',
            ], 403);
        }

        return new MembreResource($membre);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  string  $codeMembre
     * @return Response
     */
    public function update(UpdateMembreRequest $request, $codeMembre)
    {
        $membre = Membre::where('code_membre', $codeMembre)->firstOrFail();

        // Vérifier permissions (propriétaire uniquement)
        if (auth()->user()->code_membre !== $codeMembre) {
            return response()->json([
                'message' => 'Vous ne pouvez modifier que votre propre profil.',
            ], 403);
        }

        $membre->update($request->validated());

        return new MembreResource($membre->load('profil'));
    }

    /**
     * Update member password.
     *
     * @param  string  $codeMembre
     * @return Response
     */
    public function updatePassword(UpdatePasswordRequest $request, $codeMembre)
    {
        $membre = Membre::where('code_membre', $codeMembre)->firstOrFail();

        // Vérifier permissions (propriétaire uniquement)
        if (auth()->user()->code_membre !== $codeMembre) {
            return response()->json([
                'message' => 'Vous ne pouvez modifier que votre propre mot de passe.',
            ], 403);
        }

        // Vérifier l'ancien mot de passe
        if (! Hash::check($request->ancien_mot_de_passe, $membre->mot_de_passe)) {
            return response()->json([
                'message' => 'L\'ancien mot de passe est incorrect.',
            ], 400);
        }

        // Mettre à jour le mot de passe
        $membre->update([
            'mot_de_passe' => Hash::make($request->nouveau_mot_de_passe),
        ]);

        // Révoquer tous les tokens existants
        $membre->tokens()->delete();

        return response()->json([
            'message' => 'Mot de passe modifié avec succès. Veuillez vous reconnecter.',
        ], 200);
    }

    /**
     * Upload member photo.
     *
     * @param  string  $codeMembre
     * @return Response
     */
    public function uploadPhoto(UploadPhotoRequest $request, $codeMembre)
    {
        $membre = Membre::where('code_membre', $codeMembre)->firstOrFail();

        // Vérifier permissions (propriétaire uniquement)
        if (auth()->user()->code_membre !== $codeMembre) {
            return response()->json([
                'message' => 'Vous ne pouvez modifier que votre propre photo.',
            ], 403);
        }

        // Supprimer l'ancienne photo si elle existe
        if ($membre->photo_url) {
            $this->fileService->deletePhoto($membre->photo_url);
        }

        // Stocker la nouvelle photo
        $path = $this->fileService->uploadPhoto($request->file('photo'), $codeMembre);

        // Mettre à jour le membre
        $membre->update([
            'photo_url' => $path,
        ]);

        return new MembreResource($membre->load('profil'));
    }

    /**
     * Update extended profile.
     *
     * @param  string  $codeMembre
     * @return Response
     */
    public function updateProfil(UpdateProfilRequest $request, $codeMembre)
    {
        $membre = Membre::where('code_membre', $codeMembre)->firstOrFail();

        // Vérifier permissions (propriétaire uniquement)
        if (auth()->user()->code_membre !== $codeMembre) {
            return response()->json([
                'message' => 'Vous ne pouvez modifier que votre propre profil.',
            ], 403);
        }

        // Mettre à jour le profil
        if (! $membre->profil) {
            $membre->profil()->create($request->validated());
        } else {
            $membre->profil->update($request->validated());
        }

        return new ProfilResource($membre->profil);
    }

    /**
     * Liste tous les membres (Admin uniquement)
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $query = Membre::with('profil');

        // Filtres optionnels
        if ($request->has('statut')) {
            $query->where('est_actif', $request->statut === 'actif');
        }

        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                    ->orWhere('prenom', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('code_membre', 'like', "%{$search}%");
            });
        }

        $membres = $query->orderBy('date_inscription', 'desc')->paginate(20);

        return MembreResource::collection($membres);
    }

    /**
     * Exporter la liste des membres en PDF (Admin uniquement)
     *
     * @return Response
     */
    public function exportPdf()
    {
        $membres = Membre::with('profil')
            ->orderBy('date_inscription', 'desc')
            ->get();

        $pdf = Pdf::loadView('pdf.liste-membres', [
            'membres' => $membres,
        ]);

        // Configuration du PDF
        $pdf->setPaper('a4', 'landscape');

        $filename = 'liste-membres-'.now()->format('Y-m-d-His').'.pdf';

        return $pdf->download($filename);
    }

    /**
     * Exporter la liste des membres en Excel (Admin uniquement)
     *
     * @return Response
     */
    public function exportExcel()
    {
        $export = new MembresExport;

        return $export->download();
    }
}
