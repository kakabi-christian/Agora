import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, RouterModule } from '@angular/router';
import { ProjetService } from '../../../../services/projet.service';

@Component({
  selector: 'app-membre-projet-detail',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './detail.html',
  styleUrl: './detail.css'
})
export class ProjetDetailMembre implements OnInit {
  loading = true;
  actionLoading = false;
  errorMessage: string | null = null;

  id: number | null = null;
  projet: any = null;

  // MVP: on déduit l'état via la liste mes participations (pas parfait mais suffisant)
  isParticipant = false;

  constructor(
    private route: ActivatedRoute,
    private projetService: ProjetService
  ) { }

  ngOnInit(): void {
    const idParam = this.route.snapshot.paramMap.get('id');
    this.id = idParam ? Number(idParam) : null;

    if (!this.id || Number.isNaN(this.id)) {
      this.errorMessage = 'Identifiant projet invalide.';
      this.loading = false;
      return;
    }

    this.projetService.getProjetById(this.id).subscribe({
      next: (res: any) => {
        this.projet = res?.projet ?? res?.data ?? res;
      },
      error: () => {
        this.errorMessage = 'Impossible de charger le projet.';
      },
      complete: () => {
        this.loading = false;
        this.refreshParticipation();
      }
    });
  }

  /**
   * Rafraîchit le statut de participation de l'utilisateur
   */
  refreshParticipation(): void {
    this.projetService.mesParticipations().subscribe({
      next: (res: any) => {
        const list = res?.data ?? res?.participations ?? res ?? [];
        this.isParticipant = Array.isArray(list) && list.some((p: any) => {
          const pid = p?.projet_id ?? p?.projet?.id ?? p?.id;
          return pid === this.id;
        });
      },
      error: () => {
        // Non bloquant
      }
    });
  }

  /**
   * Permet à l'utilisateur de participer au projet
   */
  participer(): void {
    if (!this.id) return;
    this.actionLoading = true;
    this.errorMessage = null;

    this.projetService.participer(this.id).subscribe({
      next: () => {
        this.isParticipant = true;
        // Message de succès (optionnel - peut être géré avec un toast)
        console.log('Participation enregistrée avec succès');
      },
      error: (err) => {
        this.errorMessage = err.error?.message || 'Participation impossible. Veuillez réessayer.';

        // Auto-masquer le message d'erreur après 5 secondes
        setTimeout(() => {
          this.errorMessage = null;
        }, 5000);
      },
      complete: () => {
        this.actionLoading = false;
      }
    });
  }

  /**
   * Permet à l'utilisateur de quitter le projet
   */
  quitter(): void {
    if (!this.id) return;

    // Confirmation avant de quitter
    if (!confirm('Êtes-vous sûr de vouloir quitter ce projet ?')) {
      return;
    }

    this.actionLoading = true;
    this.errorMessage = null;

    this.projetService.quitter(this.id).subscribe({
      next: () => {
        this.isParticipant = false;
        console.log('Vous avez quitté le projet');
      },
      error: (err) => {
        this.errorMessage = err.error?.message || 'Impossible de quitter le projet. Veuillez réessayer.';

        // Auto-masquer le message d'erreur après 5 secondes
        setTimeout(() => {
          this.errorMessage = null;
        }, 5000);
      },
      complete: () => {
        this.actionLoading = false;
      }
    });
  }

  /**
   * Obtient la classe CSS pour le type de projet
   */
  getProjectTypeClass(type: string): string {
    if (!type) return 'type-default';
    const typeNormalized = type.toLowerCase();

    if (typeNormalized.includes('innovation')) return 'type-innovation';
    if (typeNormalized.includes('agricole') || typeNormalized.includes('agriculture')) return 'type-agriculture';
    if (typeNormalized.includes('social')) return 'type-social';

    return 'type-default';
  }

  /**
   * Obtient l'icône pour le type de projet
   */
  getProjectTypeIcon(type: string): string {
    if (!type) return 'bi-clipboard-check-fill';
    const typeNormalized = type.toLowerCase();

    if (typeNormalized.includes('innovation')) return 'bi-lightbulb-fill';
    if (typeNormalized.includes('agricole') || typeNormalized.includes('agriculture')) return 'bi-tree-fill';
    if (typeNormalized.includes('social')) return 'bi-people-fill';

    return 'bi-clipboard-check-fill';
  }

  /**
   * Obtient la classe CSS pour le statut du projet
   */
  getStatusClass(statut: string): string {
    if (!statut) return 'status-en-cours';
    const statutNormalized = statut.toLowerCase();

    if (statutNormalized.includes('actif') || statutNormalized.includes('en cours')) return 'status-actif';
    if (statutNormalized.includes('terminé') || statutNormalized.includes('termine')) return 'status-termine';
    if (statutNormalized.includes('attente')) return 'status-en-attente';

    return 'status-en-cours';
  }

  /**
   * Formate une date en français
   */
  formatDate(date: string | Date): string {
    if (!date) return '';

    try {
      const dateObj = typeof date === 'string' ? new Date(date) : date;

      if (isNaN(dateObj.getTime())) return '';

      return dateObj.toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    } catch {
      return '';
    }
  }

  /**
   * Formate un montant budgétaire
   */
  formatBudget(budget: number | string): string {
    if (!budget) return '';

    try {
      const amount = typeof budget === 'string' ? parseFloat(budget) : budget;

      if (isNaN(amount)) return '';

      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'XAF', // Franc CFA
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
      }).format(amount);
    } catch {
      return budget.toString();
    }
  }

  /**
   * Calcule le nombre de jours restants jusqu'à la fin du projet
   */
  getDaysRemaining(endDate: string | Date): number {
    if (!endDate) return 0;

    try {
      const end = typeof endDate === 'string' ? new Date(endDate) : endDate;
      const now = new Date();
      const diffTime = end.getTime() - now.getTime();
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

      return diffDays > 0 ? diffDays : 0;
    } catch {
      return 0;
    }
  }

  /**
   * Obtient le texte descriptif pour les jours restants
   */
  getDaysRemainingText(endDate: string | Date): string {
    const days = this.getDaysRemaining(endDate);

    if (days === 0) return 'Projet terminé';
    if (days === 1) return '1 jour restant';
    if (days < 7) return `${days} jours restants`;
    if (days < 30) return `${Math.floor(days / 7)} semaine(s) restante(s)`;

    return `${Math.floor(days / 30)} mois restant(s)`;
  }

  /**
   * Vérifie si le projet est en retard
   */
  isProjectOverdue(endDate: string | Date): boolean {
    if (!endDate) return false;

    try {
      const end = typeof endDate === 'string' ? new Date(endDate) : endDate;
      const now = new Date();
      return end < now;
    } catch {
      return false;
    }
  }

  /**
   * Calcule le pourcentage de temps écoulé du projet
   */
  getTimeProgress(startDate: string | Date, endDate: string | Date): number {
    if (!startDate || !endDate) return 0;

    try {
      const start = typeof startDate === 'string' ? new Date(startDate) : startDate;
      const end = typeof endDate === 'string' ? new Date(endDate) : endDate;
      const now = new Date();

      const totalDuration = end.getTime() - start.getTime();
      const elapsedDuration = now.getTime() - start.getTime();

      if (totalDuration <= 0) return 0;
      if (elapsedDuration < 0) return 0;
      if (elapsedDuration > totalDuration) return 100;

      return Math.round((elapsedDuration / totalDuration) * 100);
    } catch {
      return 0;
    }
  }

  /**
   * Obtient la couleur de la barre de progression selon le pourcentage
   */
  getProgressColor(progress: number): string {
    if (progress < 33) return '#4caf50'; // Vert
    if (progress < 66) return '#ff9800'; // Orange
    return '#f44336'; // Rouge
  }

  /**
   * Partage le projet sur les réseaux sociaux
   */
  shareProject(platform: 'facebook' | 'twitter' | 'linkedin' | 'whatsapp'): void {
    if (!this.projet) return;

    const title = encodeURIComponent(this.projet.nom || 'Projet');
    const url = encodeURIComponent(window.location.href);
    const description = encodeURIComponent(this.projet.description || '');

    let shareUrl = '';

    switch (platform) {
      case 'facebook':
        shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
        break;
      case 'twitter':
        shareUrl = `https://twitter.com/intent/tweet?text=${title}&url=${url}`;
        break;
      case 'linkedin':
        shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${url}`;
        break;
      case 'whatsapp':
        shareUrl = `https://wa.me/?text=${title}%20${url}`;
        break;
    }

    if (shareUrl) {
      window.open(shareUrl, '_blank', 'width=600,height=400');
    }
  }

  /**
   * Copie le lien du projet dans le presse-papier
   */
  copyProjectLink(): void {
    const url = window.location.href;

    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(url).then(() => {
        alert('Lien copié dans le presse-papier !');
      }).catch(() => {
        this.fallbackCopyToClipboard(url);
      });
    } else {
      this.fallbackCopyToClipboard(url);
    }
  }

  /**
   * Méthode de secours pour copier dans le presse-papier
   */
  private fallbackCopyToClipboard(text: string): void {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    document.body.appendChild(textArea);
    textArea.select();

    try {
      document.execCommand('copy');
      alert('Lien copié dans le presse-papier !');
    } catch (err) {
      alert('Impossible de copier le lien. Veuillez le copier manuellement.');
    }

    document.body.removeChild(textArea);
  }

  /**
   * Imprime la page du projet
   */
  printProject(): void {
    window.print();
  }

  /**
   * Télécharge les informations du projet en PDF (à implémenter avec une bibliothèque)
   */
  downloadProjectPDF(): void {
    // TODO: Implémenter la génération de PDF avec jsPDF ou similaire
    alert('Fonctionnalité de téléchargement PDF à venir');
  }

  /**
   * Signale un problème avec le projet
   */
  reportProject(): void {
    const reason = prompt('Veuillez indiquer la raison du signalement :');

    if (reason && reason.trim()) {
      // TODO: Appeler une API pour enregistrer le signalement
      console.log('Projet signalé:', this.id, 'Raison:', reason);
      alert('Merci pour votre signalement. Notre équipe va examiner ce projet.');
    }
  }

  /**
   * Marque le projet comme favori (à implémenter)
   */
  toggleFavorite(): void {
    // TODO: Implémenter la fonctionnalité de favoris
    alert('Fonctionnalité de favoris à venir');
  }

  /**
   * Obtient les statistiques du projet
   */
  getProjectStats(): any {
    if (!this.projet) return null;

    return {
      participants: this.projet.participants_count || 0,
      progression: this.projet.progression || 0,
      budget: this.projet.budget || 0,
      daysRemaining: this.getDaysRemaining(this.projet.date_fin),
      timeProgress: this.getTimeProgress(this.projet.date_debut, this.projet.date_fin),
      isOverdue: this.isProjectOverdue(this.projet.date_fin)
    };
  }
}
