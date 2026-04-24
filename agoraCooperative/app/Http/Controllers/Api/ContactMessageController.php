<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Http\Requests\ContactRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Mail\ContactReplyMail;
use Illuminate\Support\Facades\Mail; // <--- AJOUTE ÇA
use Illuminate\Support\Facades\Log; // Import indispensable pour les logs

class ContactMessageController extends Controller
{
    /**
     * Liste des messages avec pagination (pour l'admin)
     */
    public function index()
    {
        // On récupère 15 messages par page, triés du plus récent au plus ancien
        $messages = ContactMessage::orderBy('created_at', 'desc')->paginate(15);
        
        return response()->json($messages);
    }

    /**
     * Envoi d'un message par l'internaute
     */
    public function store(ContactRequest $request)
    {
        $contact = ContactMessage::create($request->validated());

        return response()->json([
            'message' => 'Message envoyé avec succès',
            'data' => $contact
        ], 201);
    }

    /**
     * Voir un message précis et le marquer comme lu
     */
    public function show(ContactMessage $contactMessage)
    {
        if (!$contactMessage->lu) {
            $contactMessage->update([
                'lu' => true,
                'date_lu' => Carbon::now()
            ]);
        }

        return response()->json($contactMessage);
    }

    /**
     * Récupérer le nombre de messages non lus (Badge Sidebar)
     */
    public function unreadCount()
    {
        $count = ContactMessage::where('lu', false)->count();
        return response()->json(['unread_count' => $count]);
    }

    /**
     * Marquer TOUS les messages comme lus
     */
    public function markAllAsRead()
    {
        ContactMessage::where('lu', false)->update([
            'lu' => true,
            'date_lu' => Carbon::now()
        ]);

        return response()->json(['message' => 'Tous les messages ont été marqués comme lus.']);
    }

    /**
     * Mettre à jour (ex: pour la réponse de l'admin)
     */
  public function update(Request $request, $id)
    {
        Log::info("--- DÉBUT PROCESSUS RÉPONSE CONTACT ID: $id ---");

        try {
            // 1. Recherche du message
            Log::info("Recherche du message en base de données...");
            $contactMessage = ContactMessage::findOrFail($id);
            Log::info("Message trouvé. Email destinataire: " . $contactMessage->email_expediteur);

            // 2. Validation
            Log::info("Validation des données entrantes...");
            $request->validate([
                'reponse' => 'required|string',
            ]);
            Log::info("Validation réussie.");

            // 3. Mise à jour DB
            Log::info("Tentative de mise à jour de la table contact_messages...");
            $contactMessage->update([
                'reponse' => $request->reponse,
                'date_reponse' => Carbon::now(),
                'statut' => 'traité',
                'lu' => true,
                'code_admin_assignee' => auth()->user()->code_membre ?? null 
            ]);
            Log::info("Mise à jour DB réussie.");

            // 4. Instanciation du Mailable (C'est souvent ici que ça plante)
            Log::info("Instanciation de la classe ContactReplyMail...");
            $mailable = new ContactReplyMail($contactMessage, $request->reponse);
            Log::info("Objet Mailable créé avec succès.");

            // 5. Envoi de l'email
            Log::info("Tentative d'envoi de l'email via Mail::to()...");
            Mail::to($contactMessage->email_expediteur)->send($mailable);
            Log::info("Email envoyé sans erreur apparente.");

            return response()->json([
                'success' => true,
                'message' => 'Réponse enregistrée et email envoyé avec succès !',
                'data' => $contactMessage
            ]);

        } catch (\Throwable $e) {
            // Log de l'erreur complète pour le développeur
            Log::error("ERREUR CRITIQUE DANS UPDATE CONTACT:");
            Log::error("Message: " . $e->getMessage());
            Log::error("Fichier: " . $e->getFile());
            Log::error("Ligne: " . $e->getLine());
            Log::error("Trace: " . $e->getTraceAsString());

            // Retour JSON détaillé pour Angular (visible dans l'onglet Network)
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du traitement de la réponse.',
                'debug_error' => $e->getMessage(),
                'debug_file' => $e->getFile(),
                'debug_line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Supprimer un message
     */
    public function destroy(ContactMessage $contactMessage)
    {
        $contactMessage->delete();
        return response()->json(['message' => 'Message supprimé']);
    }
}

