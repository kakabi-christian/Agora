import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { DemandeAdhesion } from '../../../models/demande-adhesion.model';
import { DemandeAdhesionService } from '../../../services/demande-adhesion.service';

@Component({
  selector: 'app-demandes',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './demande.html',
  styleUrl: './demande.css',
})
export class Demandes implements OnInit {
  demandes: DemandeAdhesion[] = [];
  paginationMeta: any = null;
  loading: boolean = true;
  currentStatut: string | undefined = undefined;

  // --- Variables pour la Modal de Rejet ---
  afficherModal: boolean = false;
  demandeSelectionnee: DemandeAdhesion | null = null;
  motifRejet: string = '';
  loadingRejet: boolean = false;
  erreurModal: string = '';

  constructor(private demandeService: DemandeAdhesionService) {}

  ngOnInit(): void {
    this.chargerDemandes();
  }

  chargerDemandes(statut?: string, page: number = 1) {
    this.loading = true;
    this.currentStatut = statut;

    this.demandeService.getDemandes(statut, page).subscribe({
      next: (response: any) => {
        this.demandes = response.data || [];
        this.paginationMeta = response.meta || null;
        this.loading = false;
      },
      error: (err) => {
        console.error('Erreur chargement demandes', err);
        this.loading = false;
      }
    });
  }

  // --- Validation ---
  valider(id: number | undefined) {
    if (!id) return;

    if (confirm('Voulez-vous vraiment approuver cette adhésion ?')) {
      // Note: On passe un commentaire par défaut pour éviter une 422 côté ApproveDemandeRequest
      this.demandeService.approuverDemande(id, 'Approuvé par l\'administrateur').subscribe({
        next: () => {
          const currentPage = this.paginationMeta?.current_page || 1;
          this.chargerDemandes(this.currentStatut, currentPage);
        },
        error: (err) => {
          console.error('Erreur approbation', err);
          alert('Erreur lors de l\'approbation. Vérifiez les logs.');
        }
      });
    }
  }

  // --- Logique du Rejet ---
  ouvrirModalRejet(demande: DemandeAdhesion) {
    this.demandeSelectionnee = demande;
    this.motifRejet = '';
    this.erreurModal = '';
    this.afficherModal = true;
  }

  fermerModal() {
    this.afficherModal = false;
    this.demandeSelectionnee = null;
  }

  confirmerRejet() {
    // CORRECTION : On s'aligne sur les 20 caractères minimum de Laravel
    if (!this.motifRejet || this.motifRejet.trim().length < 20) {
      this.erreurModal = "Le motif doit contenir au moins 20 caractères (exigence de sécurité).";
      return;
    }

    if (!this.demandeSelectionnee?.id) return;

    this.loadingRejet = true;
    this.erreurModal = ''; // Reset de l'erreur

    this.demandeService.rejeterDemande(this.demandeSelectionnee.id, this.motifRejet).subscribe({
      next: () => {
        this.loadingRejet = false;
        this.fermerModal();
        const currentPage = this.paginationMeta?.current_page || 1;
        this.chargerDemandes(this.currentStatut, currentPage);
      },
      error: (err) => {
        this.loadingRejet = false;
        
        // CORRECTION : Capture de l'erreur 422 de Laravel
        if (err.status === 422 && err.error.errors) {
          // On récupère le premier message d'erreur renvoyé par Laravel
          const validationErrors = err.error.errors;
          this.erreurModal = validationErrors.commentaire_admin ? 
                             validationErrors.commentaire_admin[0] : 
                             "Erreur de validation des données.";
        } else {
          this.erreurModal = "Une erreur serveur est survenue (Status: " + err.status + ")";
        }
        console.error('Erreur rejet:', err);
      }
    });
  }

  allerAPage(page: number) {
    if (this.paginationMeta && page >= 1 && page <= this.paginationMeta.last_page) {
      this.chargerDemandes(this.currentStatut, page);
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
  }
}