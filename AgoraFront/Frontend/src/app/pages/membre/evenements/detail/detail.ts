import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, RouterModule } from '@angular/router';
import { EvenementService } from '../../../../services/evenement.service';

@Component({
  selector: 'app-membre-evenement-detail',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './detail.html',
  styleUrl: './detail.css'
})
export class EvenementDetailMembre implements OnInit {
  loading = true;
  errorMessage: string | null = null;

  code: string | null = null;
  evenement: any = null;

  statutInscription: any = null;
  actionLoading = false;

  isInscrit(): boolean {
    const s = this.statutInscription;
    if (!s) return false;

    // Cas courants (on reste permissif car la forme exacte dépend du backend)
    if (typeof s === 'boolean') return s;
    if (typeof s?.inscrit === 'boolean') return s.inscrit;
    if (typeof s?.is_inscrit === 'boolean') return s.is_inscrit;
    if (typeof s?.isInscrit === 'boolean') return s.isInscrit;

    const statut = (s?.statut ?? s?.status ?? s?.etat) as string | undefined;
    if (typeof statut === 'string') {
      return ['inscrit', 'inscrite', 'confirmed', 'confirme', 'confirmée', 'valide', 'validee', 'validée'].includes(
        statut.toLowerCase()
      );
    }

    // Fallback: présence d'un identifiant d'inscription
    if (s?.id || s?.inscription_id || s?.inscription?.id) return true;

    return false;
  }

  constructor(
    private route: ActivatedRoute,
    private evenementService: EvenementService
  ) {}

  ngOnInit(): void {
    this.code = this.route.snapshot.paramMap.get('code');
    if (!this.code) {
      this.errorMessage = 'Code événement manquant.';
      this.loading = false;
      return;
    }

    this.evenementService.getEvenementByCode(this.code).subscribe({
      next: (res: any) => {
        this.evenement = res?.evenement ?? res?.data ?? res;
      },
      error: () => {
        this.errorMessage = 'Impossible de charger le détail de l\'événement.';
      },
      complete: () => {
        this.loading = false;
        this.refreshStatut();
      }
    });
  }

  refreshStatut(): void {
    if (!this.code) return;
    this.evenementService.getStatutInscription(this.code).subscribe({
      next: (res: any) => {
        this.statutInscription = res;
      },
      error: () => {
        // Non bloquant
      }
    });
  }

  inscrire(): void {
    if (!this.code) return;
    this.actionLoading = true;
    this.evenementService.inscrire(this.code).subscribe({
      next: () => {
        this.refreshStatut();
      },
      error: () => {
        this.errorMessage = 'Inscription impossible.';
      },
      complete: () => {
        this.actionLoading = false;
      }
    });
  }

  annuler(): void {
    if (!this.code) return;
    this.actionLoading = true;
    this.evenementService.annulerInscription(this.code).subscribe({
      next: () => {
        this.refreshStatut();
      },
      error: () => {
        this.errorMessage = 'Annulation impossible.';
      },
      complete: () => {
        this.actionLoading = false;
      }
    });
  }

  telechargerPdf(): void {
    if (!this.code) return;
    this.actionLoading = true;
    this.evenementService.telechargerConfirmationPdf(this.code).subscribe({
      next: (blob) => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `confirmation-${this.code}.pdf`;
        a.click();
        window.URL.revokeObjectURL(url);
      },
      error: () => {
        this.errorMessage = 'Téléchargement du PDF impossible.';
      },
      complete: () => {
        this.actionLoading = false;
      }
    });
  }
}
