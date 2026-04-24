import { Component, OnInit } from '@angular/core';
import { CommonModule, DatePipe } from '@angular/common';
import { RouterModule } from '@angular/router';
import { AuthService } from '../../../services/auth.service';
import { Membre } from '../../../models/membre.model';
import { EvenementService } from '../../../services/evenement.service';
import { ProjetService } from '../../../services/projet.service';

@Component({
  selector: 'app-membre-tableau-de-bord',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './tableau-de-bord.html',
  styleUrl: './tableau-de-bord.css',
  providers: [DatePipe]
})
export class TableauDeBordMembre implements OnInit {
  
  membre: Membre | null = null;
  loading = true;
  errorMessage: string | null = null;

  // Propriété currentDate ajoutée
  currentDate = new Date();

  upcomingEvents: any[] = [];
  projectDeadlines: any[] = [];

  constructor(
    private authService: AuthService,
    private evenementService: EvenementService,
    private projetService: ProjetService,
    private datePipe: DatePipe
  ) {}

  ngOnInit(): void {
    this.membre = this.authService.getUserSync();

    this.authService.getCurrentUser().subscribe({
      next: (membre) => {
        this.membre = membre;
      },
      error: () => {
        // On laisse potentiellement le membre du localStorage si disponible
      }
    });

    this.evenementService.getUpcomingEvents().subscribe({
      next: (res: any) => {
        this.upcomingEvents = res?.data ?? res?.evenements ?? res ?? [];
      },
      error: () => {
        // Non bloquant
      }
    });

    this.projetService.getDeadlines().subscribe({
      next: (res: any) => {
        this.projectDeadlines = res?.data ?? res?.deadlines ?? res ?? [];
      },
      error: () => {
        // Non bloquant
      },
      complete: () => {
        this.loading = false;
      }
    });
  }

  // Méthodes d'aide pour le template

  getInitials(): string {
    if (!this.membre) return 'MM';
    return `${this.membre.prenom?.charAt(0) || 'M'}${this.membre.nom?.charAt(0) || 'M'}`;
  }

  getEventDay(event: any): string {
    if (event.date_debut) {
      try {
        const date = new Date(event.date_debut);
        return date.getDate().toString();
      } catch (e) {
        return '--';
      }
    }
    return '--';
  }

  getEventMonth(event: any): string {
    if (event.date_debut) {
      try {
        const date = new Date(event.date_debut);
        return this.datePipe.transform(date, 'MMM') || '---';
      } catch (e) {
        return '---';
      }
    }
    return '---';
  }

  getEventTime(event: any): string {
    if (event.heure_debut) {
      return event.heure_debut;
    }
    if (event.date_debut && typeof event.date_debut === 'string' && event.date_debut.includes('T')) {
      try {
        const timePart = event.date_debut.split('T')[1];
        return timePart.substring(0, 5);
      } catch (e) {
        return '--:--';
      }
    }
    return '--:--';
  }

  getDeadlineDate(deadline: any): string {
    const date = deadline.deadline || deadline.date;
    if (date) {
      try {
        return this.datePipe.transform(date, 'dd MMM yyyy') || String(date);
      } catch (e) {
        return String(date);
      }
    }
    return 'Date non définie';
  }

  getDeadlinePriority(deadline: any): string {
    if (!deadline.deadline) return 'low';

    try {
      const deadlineDate = new Date(deadline.deadline);
      const today = new Date();
      const diffTime = deadlineDate.getTime() - today.getTime();
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

      if (diffDays <= 3) return 'high';
      if (diffDays <= 7) return 'medium';
      return 'low';
    } catch (e) {
      return 'low';
    }
  }

  getDeadlineStatusClass(): string {
    if (!this.projectDeadlines.length) return 'trend-neutral';

    const urgentCount = this.projectDeadlines.filter(d => this.getDeadlinePriority(d) === 'high').length;
    if (urgentCount > 0) return 'trend-urgent';

    const mediumCount = this.projectDeadlines.filter(d => this.getDeadlinePriority(d) === 'medium').length;
    if (mediumCount > 0) return 'trend-warning';

    return 'trend-ok';
  }

  getDeadlineStatusText(): string {
    if (!this.projectDeadlines.length) return 'Aucune deadline';

    const urgentCount = this.projectDeadlines.filter(d => this.getDeadlinePriority(d) === 'high').length;
    if (urgentCount > 0) return `${urgentCount} urgent(s)`;

    const mediumCount = this.projectDeadlines.filter(d => this.getDeadlinePriority(d) === 'medium').length;
    if (mediumCount > 0) return `${mediumCount} à surveiller`;

    return 'Tout est en bonne voie';
  }
  // Ajouter après les autres méthodes

formatNom(nom: string | undefined | null): string {
  if (!nom) return '';

  // Capitaliser la première lettre
  return nom.charAt(0).toUpperCase() + nom.slice(1).toLowerCase();
}

// Optionnel: Pour formater complètement le nom (prénom + nom)
getNomComplet(): string {
  if (!this.membre) return '';

  const prenom = this.formatNom(this.membre.prenom);
  const nom = this.formatNom(this.membre.nom);

  return `${prenom} ${nom}`;
}
}
