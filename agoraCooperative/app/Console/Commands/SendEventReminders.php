<?php

namespace App\Console\Commands;

use App\Mail\RappelEvenement;
use App\Models\Evenements;
use App\Models\Inscription_events;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoyer des rappels par email pour les événements à venir (24h avant)';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🔍 Recherche des événements nécessitant un rappel...');

        // Récupérer les événements dans les prochaines 24h (entre 23h et 25h pour avoir une marge)
        $evenements = Evenements::whereBetween('date_debut', [
            now()->addHours(23),
            now()->addHours(25)
        ])
        ->whereIn('statut', ['planifie', 'en_cours'])
        ->get();

        if ($evenements->isEmpty()) {
            $this->info('✅ Aucun événement dans les prochaines 24h.');
            Log::info('Rappels événements: Aucun événement trouvé');
            return 0;
        }

        $this->info("📅 {$evenements->count()} événement(s) trouvé(s)");

        $totalEmailsEnvoyes = 0;
        $totalErreurs = 0;

        foreach ($evenements as $evenement) {
            $this->line("📌 Traitement: {$evenement->titre}");

            // Récupérer les inscriptions éligibles pour le rappel
            $inscriptions = Inscription_events::where('code_evenement', $evenement->code_evenement)
                ->where('statut_participation', 'inscrit')
                ->where('rappel_envoye', false)
                ->with('membre')
                ->get();

            // Filtrer selon le paiement obligatoire
            if ($evenement->paiement_obligatoire && $evenement->frais_inscription > 0) {
                $inscriptions = $inscriptions->where('statut_paiement', 'paye');
                $this->line("   💰 Événement payant obligatoire - Filtrage sur paiement confirmé");
            }

            if ($inscriptions->isEmpty()) {
                $this->line("   ℹ️  Aucune inscription éligible pour cet événement");
                continue;
            }

            $this->line("   👥 {$inscriptions->count()} inscription(s) éligible(s)");

            foreach ($inscriptions as $inscription) {
                try {
                    // Envoyer l'email de rappel
                    Mail::to($inscription->membre->email)
                        ->send(new RappelEvenement($inscription, $evenement, $inscription->membre));

                    // Marquer le rappel comme envoyé
                    $inscription->update([
                        'rappel_envoye' => true,
                        'date_rappel_envoye' => now(),
                    ]);

                    $totalEmailsEnvoyes++;
                    $this->line("   ✅ Rappel envoyé à {$inscription->membre->prenom} {$inscription->membre->nom}");

                    Log::info('Rappel événement envoyé', [
                        'evenement_code' => $evenement->code_evenement,
                        'evenement_titre' => $evenement->titre,
                        'membre_code' => $inscription->code_membre,
                        'membre_email' => $inscription->membre->email,
                        'date_evenement' => $evenement->date_debut,
                    ]);

                } catch (\Exception $e) {
                    $totalErreurs++;
                    $this->error("   ❌ Erreur pour {$inscription->membre->email}: {$e->getMessage()}");

                    Log::error('Erreur envoi rappel événement', [
                        'evenement_code' => $evenement->code_evenement,
                        'membre_code' => $inscription->code_membre,
                        'membre_email' => $inscription->membre->email,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        $this->newLine();
        $this->info("📊 Résumé:");
        $this->info("   ✅ Emails envoyés: {$totalEmailsEnvoyes}");
        if ($totalErreurs > 0) {
            $this->error("   ❌ Erreurs: {$totalErreurs}");
        }

        Log::info('Rappels événements terminés', [
            'emails_envoyes' => $totalEmailsEnvoyes,
            'erreurs' => $totalErreurs,
        ]);

        return 0;
    }
}
