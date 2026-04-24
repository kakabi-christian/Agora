<?php

namespace App\Services;

use App\Models\Membre;
use App\Models\HistoriqueParticipation;

class HistoriqueParticipationAggregator
{
    /**
     * Agréger toutes les participations d'un membre
     * 
     * @param string $codeMembre
     * @return void
     */
    public function aggregate(string $codeMembre): void
    {
        $membre = Membre::where('code_membre', $codeMembre)->first();
        
        if (!$membre) {
            return;
        }
        
        // Supprimer l'historique existant pour recréer
        HistoriqueParticipation::where('code_membre', $codeMembre)->delete();
        
        // Agréger les inscriptions aux événements
        $this->aggregateInscriptionEvents($membre);
        
        // Agréger les participations aux projets
        $this->aggregateParticipationProjets($membre);
        
        // Agréger les dons
        $this->aggregateDons($membre);
        
        // Mettre à jour le nombre de participations dans le profil
        $this->updateProfilParticipationCount($membre);
    }
    
    /**
     * Agréger les inscriptions aux événements
     */
    protected function aggregateInscriptionEvents(Membre $membre): void
    {
        $inscriptions = $membre->inscriptionEvents()
            ->with('evenement')
            ->get();
        
        foreach ($inscriptions as $inscription) {
            if ($inscription->evenement) {
                HistoriqueParticipation::create([
                    'code_membre' => $membre->code_membre,
                    'type_participation' => 'evenement',
                    'titre' => $inscription->evenement->titre,
                    'description' => $inscription->evenement->description,
                    'date_participation' => $inscription->date_inscription,
                    'details' => [
                        'lieu' => $inscription->evenement->lieu,
                        'type_evenement' => $inscription->evenement->type,
                        'statut_participation' => $inscription->statut_participation,
                    ],
                    'montant_implique' => $inscription->montant_paye,
                    'role' => null,
                ]);
            }
        }
    }
    
    /**
     * Agréger les participations aux projets
     */
    protected function aggregateParticipationProjets(Membre $membre): void
    {
        $participations = $membre->participationProjets()
            ->with('projet')
            ->get();
        
        foreach ($participations as $participation) {
            if ($participation->projet) {
                HistoriqueParticipation::create([
                    'code_membre' => $membre->code_membre,
                    'type_participation' => 'projet',
                    'titre' => $participation->projet->nom,
                    'description' => $participation->projet->description,
                    'date_participation' => $participation->date_participation,
                    'details' => [
                        'type_projet' => $participation->projet->type,
                        'statut_projet' => $participation->projet->statut,
                        'taches' => $participation->taches,
                        'competences_apportees' => $participation->competences_apportees,
                    ],
                    'heures_contribuees' => $participation->heures_contribuees,
                    'role' => $participation->role,
                ]);
            }
        }
    }
    
    /**
     * Agréger les dons
     */
    protected function aggregateDons(Membre $membre): void
    {
        $dons = $membre->dons()->get();
        
        foreach ($dons as $don) {
            HistoriqueParticipation::create([
                'code_membre' => $membre->code_membre,
                'type_participation' => 'don',
                'titre' => 'Don ' . ($don->type_don ?? 'financier'),
                'description' => $don->description ?? 'Contribution financière à la coopérative',
                'date_participation' => $don->date_don,
                'details' => [
                    'type_don' => $don->type_don,
                    'mode_paiement' => $don->mode_paiement,
                    'recu_fiscal' => $don->recu_fiscal,
                ],
                'montant_implique' => $don->montant,
                'role' => 'donateur',
            ]);
        }
    }
    
    /**
     * Mettre à jour le nombre de participations dans le profil
     */
    protected function updateProfilParticipationCount(Membre $membre): void
    {
        $count = HistoriqueParticipation::where('code_membre', $membre->code_membre)->count();
        
        if ($membre->profil) {
            $membre->profil->update([
                'nombre_participations' => $count,
            ]);
        }
    }
}
