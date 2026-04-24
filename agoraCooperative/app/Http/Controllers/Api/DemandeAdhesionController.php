<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApproveDemandeRequest;
use App\Http\Requests\DemandeAdhesionRequest;
use App\Http\Requests\RejectDemandeRequest;
use App\Http\Resources\DemandeAdhesionResource;
use App\Mail\DemandeAdhesionApprouvee;
use App\Mail\DemandeAdhesionConfirmee;
use App\Mail\DemandeAdhesionRejetee;
use App\Models\DemandeAdhesion;
use App\Models\Membre;
use App\Models\Profils;
use App\Notifications\NouvelleDemandeAdmin;
use App\Services\CodeMembreGenerator;
use App\Services\PasswordGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class DemandeAdhesionController extends Controller
{
    public function index(Request $request)
    {
        $query = DemandeAdhesion::with(['adminTraitant', 'membreCree']);

        if ($request->has('statut')) {
            $query->where('statut', $request->statut);
        }

        $demandes = $query->orderBy('date_demande', 'desc')->paginate(15);

        return DemandeAdhesionResource::collection($demandes);
    }

    public function store(DemandeAdhesionRequest $request)
    {
        $demande = DemandeAdhesion::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'adresse' => $request->adresse,
            'ville' => $request->ville,
            'code_postal' => $request->code_postal,
            'date_naissance' => $request->date_naissance,
            'profession' => $request->profession,
            'motivation' => $request->motivation,
            'competences' => $request->competences,
            'statut' => 'en_attente',
            'date_demande' => now(),
        ]);

        try {
            Mail::to($demande->email)->send(new DemandeAdhesionConfirmee($demande));
        } catch (\Exception $e) {
            \Log::error('Erreur envoi email confirmation demande: '.$e->getMessage());
        }

        // Notification aux admins
        $admins = Membre::where('role', 'administrateur')->where('est_actif', true)->get();
        foreach ($admins as $admin) {
            try {
                $admin->notify(new NouvelleDemandeAdmin($demande));
            } catch (\Exception $e) {
                \Log::error('Erreur notification admin: '.$e->getMessage());
            }
        }

        return new DemandeAdhesionResource($demande);
    }

    public function show($id)
    {
        $demande = DemandeAdhesion::with(['adminTraitant', 'membreCree'])->findOrFail($id);
        $user = auth()->user();

        if ($user && $user->role !== 'administrateur' && $user->email !== $demande->email) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        return new DemandeAdhesionResource($demande);
    }

    public function approve($id, ApproveDemandeRequest $request)
    {
        $demande = DemandeAdhesion::findOrFail($id);

        if ($demande->statut !== 'en_attente') {
            return response()->json([
                'message' => 'Cette demande a déjà été traitée.',
            ], 409);
        }

        DB::beginTransaction();

        try {
            // 1️⃣ Vérifier si le membre existe déjà
            $membre = Membre::where('email', $demande->email)->first();

            if (! $membre) {
                // ➕ Nouveau membre
                $codeMembre = CodeMembreGenerator::generate();
                $motDePasseTemporaire = PasswordGenerator::generate();

                \Log::info('Génération nouveau membre', [
                    'code_membre' => $codeMembre,
                    'email' => $demande->email,
                    'mot_de_passe_genere' => $motDePasseTemporaire,
                ]);

                $membre = Membre::create([
                    'code_membre' => $codeMembre,
                    'nom' => $demande->nom,
                    'prenom' => $demande->prenom,
                    'email' => $demande->email,
                    'mot_de_passe' => Hash::make($motDePasseTemporaire),
                    'mot_de_passe_temporaire' => true,
                    'date_inscription' => now(),
                    'role' => 'membre',
                    'est_actif' => true,
                    'telephone' => $demande->telephone,
                    'adresse' => $demande->adresse,
                    'ville' => $demande->ville,
                    'code_postal' => $demande->code_postal,
                ]);
            } else {
                // 🔁 Membre existant
                $codeMembre = $membre->code_membre;
                $motDePasseTemporaire = null;

                \Log::info('Membre existant trouvé', [
                    'code_membre' => $codeMembre,
                    'email' => $demande->email,
                ]);
            }

            // 2️⃣ Création du profil si absent
            Profils::firstOrCreate(
                ['code_membre' => $codeMembre],
                [
                    'informations_personnelles' => [
                        'date_naissance' => $demande->date_naissance,
                        'profession' => $demande->profession,
                    ],
                    'competences' => $demande->competences,
                    'nombre_participations' => 0,
                ]
            );

            // 3️⃣ Sécurisation de l’admin traitant
            $admin = auth()->user();
            $codeAdmin = ($admin && isset($admin->code_membre))
                ? $admin->code_membre
                : null;

            // 4️⃣ Mise à jour de la demande
            $demande->update([
                'statut' => 'approuvee',
                'date_traitement' => now(),
                'code_admin_traitant' => $codeAdmin,
                'commentaire_admin' => $request->commentaire_admin,
                'code_membre_cree' => $codeMembre,
            ]);

            DB::commit();

            // 5️⃣ ⚠️ MAIL TOUJOURS ENVOYÉ
            try {
                \Log::info('Envoi email approbation', [
                    'demande_id' => $demande->id,
                    'email' => $demande->email,
                    'code_membre' => $codeMembre,
                    'mot_de_passe_present' => ! is_null($motDePasseTemporaire),
                    'mot_de_passe' => $motDePasseTemporaire,
                ]);

                Mail::to($demande->email)->send(
                    new DemandeAdhesionApprouvee(
                        $demande,
                        $codeMembre,
                        $motDePasseTemporaire // null si membre existant
                    )
                );

                \Log::info('Email approbation envoyé avec succès', [
                    'demande_id' => $demande->id,
                ]);
            } catch (\Exception $e) {
                \Log::error('Erreur envoi mail approbation', [
                    'demande_id' => $demande->id,
                    'error' => $e->getMessage(),
                ]);
            }

            return new DemandeAdhesionResource(
                $demande->load(['adminTraitant', 'membreCree'])
            );

        } catch (\Throwable $e) {
            DB::rollBack();

            \Log::error('Erreur approbation demande', [
                'demande_id' => $demande->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Erreur lors de la validation de la demande.',
            ], 500);
        }
    }

    public function reject($id, RejectDemandeRequest $request)
    {
        $demande = DemandeAdhesion::findOrFail($id);

        if (! in_array($demande->statut, ['en_attente', 'en_examen'])) {
            return response()->json(['message' => 'Cette demande ne peut pas être rejetée.'], 400);
        }

        $demande->update([
            'statut' => 'rejetee',
            'date_traitement' => now(),
            'code_admin_traitant' => auth()->user()->code_membre,
            'commentaire_admin' => $request->commentaire_admin,
        ]);

        try {
            Mail::to($demande->email)->send(new DemandeAdhesionRejetee($demande));
        } catch (\Exception $e) {
            \Log::error('Erreur envoi mail rejet', ['demande_id' => $demande->id, 'error' => $e->getMessage()]);
        }

        return new DemandeAdhesionResource($demande->load('adminTraitant'));
    }

    public function countPendingDemandes()
    {
        // On compte les demandes dont le statut est 'en_attente'
        // Adapte 'en_attente' selon la valeur exacte dans ta base de données
        // Utilisation de 254 lignes
        $count = DemandeAdhesion::where('statut', 'en_attente')->count();

        return response()->json([
            'pending_count' => $count,
        ]);
    }
}
