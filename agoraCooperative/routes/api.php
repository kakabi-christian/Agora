<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContactMessageController;
use App\Http\Controllers\Api\DashboardStatsController;
use App\Http\Controllers\Api\DemandeAdhesionController;
use App\Http\Controllers\Api\DonController;
use App\Http\Controllers\Api\EvenementController;
use App\Http\Controllers\Api\EvenementPaiementController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\HistoriqueParticipationController;
use App\Http\Controllers\Api\InscriptionEvenementController;
use App\Http\Controllers\Api\MembreController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PartenaireController;
use App\Http\Controllers\Api\ParticipationProjetController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProjetController;
use App\Http\Controllers\Api\RessourceController;
use App\Http\Controllers\Api\StatsController;
use App\Http\Controllers\MetricsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Route pour Prometheus (sans authentification)
Route::get('/metrics', [MetricsController::class, 'index']);

// =======================
// Routes WEBHOOK (sans auth)
// =======================
Route::get('/evenements', [EvenementController::class, 'index']);

// =======================
// Routes PUBLIQUES
// =======================

Route::post('/demandes-adhesion', [DemandeAdhesionController::class, 'store']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/partenaires', [PartenaireController::class, 'index']);
Route::get('/partenaires/{partenaire}', [PartenaireController::class, 'show']);
Route::get('/faqs', [FaqController::class, 'index']);
Route::get('/faqs/{faq}', [FaqController::class, 'show']);
Route::post('/contacts', [ContactMessageController::class, 'store']);
// Stats Dashboard (page d'accueil)
Route::get('/dashboard/stats', [DashboardStatsController::class, 'getHomeStats']);
Route::get('/dashboard/partenaires', [DashboardStatsController::class, 'getPartenairesActifs']);

// Dons (routes publiques)
Route::post('/dons', [DonController::class, 'store']);
Route::post('/dons/{id}/payer', [DonController::class, 'payerDon']);

Route::post('/dons', [PaymentController::class, 'store']);
Route::post('/campay/webhook', [PaymentController::class, 'handleWebhook']);
// --- MOT DE PASSE OUBLIÉ (OTP) ---
Route::post('/password/forgot', [ForgotPasswordController::class, 'sendOtp']);
Route::post('/password/verify-otp', [ForgotPasswordController::class, 'verifyOtp']);
Route::post('/password/reset', [ForgotPasswordController::class, 'resetPassword']);
// =======================
// Routes AUTHENTIFIÉES (Membres & Admin)
// =======================
Route::middleware(['auth:sanctum', 'member.active', 'token.expiration'])->group(function () {

    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/change-password', [AuthController::class, 'changePassword']);
    // Membres
    Route::get('/membres/{code_membre}', [MembreController::class, 'show']);
    Route::put('/membres/{code_membre}', [MembreController::class, 'update']);
    Route::put('/membres/{code_membre}/password', [MembreController::class, 'updatePassword']);
    Route::post('/membres/{code_membre}/photo', [MembreController::class, 'uploadPhoto']);
    Route::put('/membres/{code_membre}/profil', [MembreController::class, 'updateProfil']);

    // Historique & Ressources
    Route::get('/historique', [HistoriqueParticipationController::class, 'index']);
    Route::get('/historique/export', [HistoriqueParticipationController::class, 'export']);
    Route::get('/ressources', [RessourceController::class, 'index']);
    Route::get('/ressources/{id}', [RessourceController::class, 'show']);
    Route::get('/ressources/{id}/download', [RessourceController::class, 'download'])->name('api.ressources.download');

    // PROJETS (Suivi & Liste)
    Route::get('/projets/deadlines', [ProjetController::class, 'getApprovedProjectsDeadlines']);
    Route::get('/projets', [ProjetController::class, 'index']);
    Route::get('/projets/{projet}', [ProjetController::class, 'show']);

    // ÉVÉNEMENTS (Suivi & Liste)
    Route::get('/evenements/upcoming', [EvenementController::class, 'getUpcomingEventsWithAutoStatus']);
    Route::get('/evenements/{code_evenement}', [EvenementController::class, 'show']);

    // Autres
    Route::post('/faqs/{faq}/vote', [FaqController::class, 'vote']);

    // Inscriptions événements
    Route::post('/evenements/{code_evenement}/inscription', [InscriptionEvenementController::class, 'inscrire']);
    Route::delete('/evenements/{code_evenement}/inscription', [InscriptionEvenementController::class, 'annuler']);
    Route::get('/evenements/{code_evenement}/inscription/statut', [InscriptionEvenementController::class, 'statut']);
    Route::get('/evenements/{code_evenement}/inscription/pdf', [InscriptionEvenementController::class, 'telechargerConfirmation']);
    Route::get('/mes-inscriptions', [InscriptionEvenementController::class, 'mesInscriptions']);

    // Paiements événements
    Route::post('/paiements/evenement/{code_evenement}', [EvenementPaiementController::class, 'payerEvenement']);
    Route::get('/paiements/{reference}', [EvenementPaiementController::class, 'verifierStatut']);
    Route::get('/mes-paiements', [EvenementPaiementController::class, 'mesPaiements']);

    // Participations projets (membre connecté)
    Route::post('/projets/{projet_id}/participer', [ParticipationProjetController::class, 'participer']);
    Route::delete('/projets/{projet_id}/participer', [ParticipationProjetController::class, 'quitter']);
    Route::get('/mes-participations-projets', [ParticipationProjetController::class, 'mesParticipations']);
    Route::post('/projets/{projet_id}/heures', [ParticipationProjetController::class, 'logHeures']);
});

// =======================
// Routes ADMIN UNIQUEMENT
// =======================
Route::middleware([
    'auth:sanctum',
    'member.active',
    'token.expiration',
    'admin.only',
])->prefix('admin')->group(function () {

    // Gestion Adhésions
    Route::get('/demandes-adhesion', [DemandeAdhesionController::class, 'index']);
    Route::get('/demandes-adhesion/{id}', [DemandeAdhesionController::class, 'show']);
    Route::post('/demandes-adhesion/{id}/reject', [DemandeAdhesionController::class, 'reject']);
    Route::post('/demandes-adhesion/{id}/approve', [DemandeAdhesionController::class, 'approve']);
    Route::get('/demandes-adhesion/stats/count', [DemandeAdhesionController::class, 'countPendingDemandes']);

    // Gestion Projets (Édition)
    Route::get('/projets', [ProjetController::class, 'index']); // Liste admin avec pagination
    Route::post('/projets', [ProjetController::class, 'store']);
    Route::put('/projets/{projet}', [ProjetController::class, 'update']);
    Route::delete('/projets/{projet}', [ProjetController::class, 'destroy']);

    // Gestion Événements (Création)
    Route::post('/evenements', [EvenementController::class, 'store']);
    Route::put('/evenements/{code_evenement}', [EvenementController::class, 'update']);
    Route::delete('/evenements/{code_evenement}', [EvenementController::class, 'destroy']);

    // Gestion Partenaires, Ressources, FAQ
    Route::post('/partenaires', [PartenaireController::class, 'store']);
    Route::put('/partenaires/{partenaire}', [PartenaireController::class, 'update']);
    Route::delete('/partenaires/{partenaire}', [PartenaireController::class, 'destroy']);
    Route::post('/ressources', [RessourceController::class, 'store']);
    Route::post('/faqs', [FaqController::class, 'store']);

    // Inscriptions événements (admin)
    Route::get('/evenements/{code_evenement}/inscriptions', [InscriptionEvenementController::class, 'listeParEvenement']);
    Route::put('/inscriptions/{id}/statut', [InscriptionEvenementController::class, 'updateStatut']);
    Route::post('/inscriptions/verifier-qr', [InscriptionEvenementController::class, 'verifierQrCode']);

    // Messagerie & Contacts
    Route::get('/contacts', [ContactMessageController::class, 'index']);
    Route::get('/contacts/unread-count', [ContactMessageController::class, 'unreadCount']);
    Route::post('/contacts/mark-as-read', [ContactMessageController::class, 'markAllAsRead']);
    Route::get('/contacts/{contactMessage}', [ContactMessageController::class, 'show']);
    Route::put('/contacts/{contactMessage}', [ContactMessageController::class, 'update']);
    Route::delete('/contacts/{contactMessage}', [ContactMessageController::class, 'destroy']);

    // Notifications Système
    Route::get('/', [NotificationController::class, 'index']);
    Route::post('/', [NotificationController::class, 'store']);
    Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
    Route::patch('/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/{notification}', [NotificationController::class, 'destroy']);
    Route::get('/membres-list', [NotificationController::class, 'getMembresList']);

    // Dons (admin)
    Route::get('/dons/total-general', [DonController::class, 'getTotalDons']);
    Route::get('/dons', [DonController::class, 'index']);
    Route::get('/dons/{id}', [DonController::class, 'show']);
    // Participations projets (admin)
    Route::get('/projets/{projet_id}/participants', [ParticipationProjetController::class, 'listeParProjet']);
    Route::post('/projets/{projet_id}/participants', [ParticipationProjetController::class, 'ajouterParticipant']);
    Route::put('/participations/{id}', [ParticipationProjetController::class, 'updateParticipation']);
    Route::delete('/participations/{id}', [ParticipationProjetController::class, 'retirerParticipant']);
    Route::post('/payout', [PaymentController::class, 'payoutToAdmin']);
    Route::get('/stats/dashboard', [StatsController::class, 'getDashboardStats']);

    // Gestion des membres (admin)
    Route::get('/membres', [MembreController::class, 'index']);
    Route::get('/membres/export/pdf', [MembreController::class, 'exportPdf']);
    Route::get('/membres/export/excel', [MembreController::class, 'exportExcel']);

});
