<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Membre;
use App\Http\Requests\NotificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Liste toutes les notifications avec pagination
     */
    public function index()
    {
        $notifications = Notification::with('membre')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return response()->json($notifications);
    }

    /**
     * Compteur de notifications non lues (pour la Sidebar/Badge)
     * Si l'utilisateur est admin, il voit tout, sinon il voit les siennes
     */
    public function unreadCount(Request $request)
    {
        $query = Notification::where('statut', 'non_lu');

        // Si ce n'est pas un admin, on filtre par son code_membre
        if ($request->has('code_membre')) {
            $query->where('code_membre', $request->code_membre);
        }

        $count = $query->count();

        return response()->json([
            'unread_count' => $count
        ]);
    }
    /**
 * Récupère la liste des membres pour le sélecteur (UI)
 */
    public function getMembresList()
    {
        // On ne prend que les membres actifs et on sélectionne uniquement le nécessaire
        $membres = \App\Models\Membre::where('est_actif', true)
            ->select('code_membre', 'nom', 'prenom', 'email')
            ->orderBy('nom', 'asc')
            ->get();

        return response()->json($membres);
    }

    /**
     * Création et envoi (Ciblée ou de masse)
     */
    public function store(NotificationRequest $request)
{
    $data = $request->validated();
    
    // On récupère le code_membre de l'admin actuellement connecté
    // (Assure-toi que ton modèle User ou Membre lié à l'auth possède 'code_membre')
    $senderCode = auth()->user()->code_membre; 

    try {
        DB::beginTransaction();

        if ($request->pour_tous) {
            // Envoi de masse : On récupère tous les membres SAUF l'expéditeur
            $membres = Membre::where('code_membre', '!=', $senderCode)->get();
            
            foreach ($membres as $membre) {
                Notification::create([
                    'code_membre' => $membre->code_membre,
                    'titre'       => $data['titre'],
                    'contenu'     => $data['contenu'],
                    'type'        => $data['type'],
                    'categorie'   => $data['categorie'],
                    'est_urgent'  => $data['est_urgent'] ?? false,
                    'date_envoi'  => now(),
                    'statut'      => 'non_lu'
                ]);
            }
            $count = $membres->count();
            $message = "Notification envoyée à tous les membres ($count), vous avez été exclu de l'envoi.";
        } else {
            // Envoi ciblé : On vérifie si la cible est l'expéditeur lui-même
            if ($data['code_membre'] === $senderCode) {
                return response()->json([
                    'success' => false, 
                    'message' => "Opération annulée : Vous ne pouvez pas vous envoyer une notification à vous-même."
                ], 403);
            }

            Notification::create(array_merge($data, [
                'statut' => 'non_lu',
                'date_envoi' => now()
            ]));
            $message = "Notification envoyée au membre " . $data['code_membre'];
        }

        DB::commit();
        return response()->json(['success' => true, 'message' => $message], 201);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("Erreur Notification: " . $e->getMessage());
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}

    /**
     * Marquer une notification précise comme lue
     */
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update([
            'statut' => 'lu',
            'date_lecture' => now()
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Marquer TOUTES les notifications d'un membre comme lues
     */
    public function markAllAsRead(Request $request)
    {
        $request->validate(['code_membre' => 'required|exists:membres,code_membre']);

        Notification::where('code_membre', $request->code_membre)
            ->where('statut', 'non_lu')
            ->update([
                'statut' => 'lu',
                'date_lecture' => now()
            ]);

        return response()->json(['message' => 'Toutes les notifications marquées comme lues']);
    }

    /**
     * Supprimer une notification
     */
    public function destroy(Notification $notification)
    {
        $notification->delete();
        return response()->json(['message' => 'Notification supprimée']);
    }
}
//Controller/Api/NotificationController.php