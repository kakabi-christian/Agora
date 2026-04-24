import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { EvenementService } from '../../../services/evenement.service';

@Component({
  selector: 'app-membre-evenements',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './evenements.html',
  styleUrl: './evenements.css'
})
export class EvenementsMembre implements OnInit {
  loading = true;
  errorMessage: string | null = null;

  page = 1;
  evenements: any[] = [];
  mesInscriptions: any[] = [];

  constructor(private evenementService: EvenementService) { }

  ngOnInit(): void {
    this.load();
  }

  /**
   * Charge les événements et les inscriptions
   */
  load(): void {
    this.loading = true;
    this.errorMessage = null;

    this.evenementService.getEvenements(this.page).subscribe({
      next: (res: any) => {
        this.evenements = res?.evenements?.data ?? res?.data ?? res?.evenements ?? res ?? [];
      },
      error: (err) => {
        this.errorMessage = err.error?.message || 'Impossible de charger les événements.';

        setTimeout(() => {
          this.errorMessage = null;
        }, 5000);
      },
      complete: () => {
        this.loading = false;
      }
    });

    this.evenementService.getMesInscriptions().subscribe({
      next: (res: any) => {
        this.mesInscriptions = res?.data ?? res?.inscriptions ?? res ?? [];
      },
      error: () => {
        // Non bloquant
      }
    });
  }

  /**
   * Page suivante
   */
  nextPage(): void {
    this.page += 1;
    this.load();
    this.scrollToTop();
  }

  /**
   * Page précédente
   */
  prevPage(): void {
    if (this.page <= 1) return;
    this.page -= 1;
    this.load();
    this.scrollToTop();
  }

  /**
   * Fait défiler vers le haut de la page
   */
  private scrollToTop(): void {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  /**
   * Obtient le jour d'un événement
   */
  getEventDay(date: string | Date | null | undefined): string {
    if (!date) return '--';

    try {
      const dateObj = typeof date === 'string' ? new Date(date) : date;
      if (isNaN(dateObj.getTime())) return '--';

      return dateObj.getDate().toString().padStart(2, '0');
    } catch {
      return '--';
    }
  }

  /**
   * Obtient le mois d'un événement
   */
  getEventMonth(date: string | Date | null | undefined): string {
    if (!date) return '---';

    try {
      const dateObj = typeof date === 'string' ? new Date(date) : date;
      if (isNaN(dateObj.getTime())) return '---';

      return dateObj.toLocaleDateString('fr-FR', { month: 'short' }).toUpperCase();
    } catch {
      return '---';
    }
  }

  /**
   * Formate une date complète en français
   */
  formatDate(date: string | Date | null | undefined): string {
    if (!date) return 'Date non spécifiée';

    try {
      const dateObj = typeof date === 'string' ? new Date(date) : date;
      if (isNaN(dateObj.getTime())) return 'Date invalide';

      return dateObj.toLocaleDateString('fr-FR', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    } catch {
      return 'Date invalide';
    }
  }

  /**
   * Obtient la date d'un événement depuis une inscription
   */
  getEventDate(inscription: any): string | null {
    return inscription?.date_debut ||
      inscription?.evenement?.date_debut ||
      inscription?.date ||
      null;
  }

  /**
   * Obtient le lieu d'un événement depuis une inscription
   */
  getEventLocation(inscription: any): string {
    return inscription?.lieu ||
      inscription?.evenement?.lieu ||
      'Lieu non spécifié';
  }

  /**
   * Tronque la description d'un événement
   */
  truncateDescription(description: string, maxLength: number = 120): string {
    if (!description) return '';
    if (description.length <= maxLength) return description;
    return description.substring(0, maxLength) + '...';
  }

  /**
   * Vérifie si l'utilisateur est inscrit à un événement
   */
  isInscrit(evenement: any): boolean {
    if (!evenement || !evenement.code_evenement) return false;

    return this.mesInscriptions.some((inscription: any) => {
      const codeInscription = inscription.code_evenement ||
        inscription.evenement?.code_evenement;
      return codeInscription === evenement.code_evenement;
    });
  }

  /**
   * Obtient le statut d'un événement
   */
  getEventStatus(evenement: any): string {
    if (!evenement.date_debut) return 'À venir';

    const now = new Date();
    const dateDebut = new Date(evenement.date_debut);
    const dateFin = evenement.date_fin ? new Date(evenement.date_fin) : null;

    if (dateFin && now > dateFin) {
      return 'Terminé';
    }

    if (now >= dateDebut && (!dateFin || now <= dateFin)) {
      return 'En cours';
    }

    return 'À venir';
  }

  /**
   * Obtient la classe CSS pour le statut d'un événement
   */
  getEventStatusClass(evenement: any): string {
    const status = this.getEventStatus(evenement);

    if (status === 'Terminé') return 'status-past';
    if (status === 'En cours') return 'status-ongoing';
    return 'status-upcoming';
  }

  /**
   * Obtient l'icône pour le type d'événement
   */
  getEventTypeIcon(type: string | null | undefined): string {
    if (!type) return 'bi-calendar-event-fill';

    const typeNormalized = type.toLowerCase();

    if (typeNormalized.includes('formation')) return 'bi-mortarboard-fill';
    if (typeNormalized.includes('atelier')) return 'bi-tools';
    if (typeNormalized.includes('conférence') || typeNormalized.includes('conference')) return 'bi-mic-fill';
    if (typeNormalized.includes('réunion') || typeNormalized.includes('reunion')) return 'bi-people-fill';
    if (typeNormalized.includes('webinaire') || typeNormalized.includes('webinar')) return 'bi-laptop-fill';
    if (typeNormalized.includes('social')) return 'bi-emoji-smile-fill';
    if (typeNormalized.includes('culturel')) return 'bi-palette-fill';
    if (typeNormalized.includes('sport')) return 'bi-trophy-fill';

    return 'bi-calendar-event-fill';
  }

  /**
   * Calcule le nombre de jours avant un événement
   */
  getDaysUntilEvent(date: string | Date | null | undefined): number {
    if (!date) return 0;

    try {
      const eventDate = typeof date === 'string' ? new Date(date) : date;
      const now = new Date();
      const diffTime = eventDate.getTime() - now.getTime();
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

      return diffDays;
    } catch {
      return 0;
    }
  }

  /**
   * Obtient le texte descriptif pour le compte à rebours
   */
  getCountdownText(date: string | Date | null | undefined): string {
    const days = this.getDaysUntilEvent(date);

    if (days < 0) return 'Événement passé';
    if (days === 0) return 'Aujourd\'hui';
    if (days === 1) return 'Demain';
    if (days < 7) return `Dans ${days} jours`;
    if (days < 30) return `Dans ${Math.floor(days / 7)} semaine(s)`;

    return `Dans ${Math.floor(days / 30)} mois`;
  }

  /**
   * Vérifie si un événement est bientôt
   */
  isEventSoon(date: string | Date | null | undefined): boolean {
    const days = this.getDaysUntilEvent(date);
    return days >= 0 && days <= 7;
  }

  /**
   * Vérifie si un événement est passé
   */
  isEventPast(date: string | Date | null | undefined): boolean {
    if (!date) return false;

    try {
      const eventDate = typeof date === 'string' ? new Date(date) : date;
      const now = new Date();
      return eventDate < now;
    } catch {
      return false;
    }
  }

  /**
   * Filtre les événements à venir
   */
  getUpcomingEvents(): any[] {
    return this.evenements.filter(e => !this.isEventPast(e.date_debut));
  }

  /**
   * Filtre les événements passés
   */
  getPastEvents(): any[] {
    return this.evenements.filter(e => this.isEventPast(e.date_debut));
  }

  /**
   * Trie les événements par date
   */
  sortEventsByDate(events: any[], ascending: boolean = true): any[] {
    return [...events].sort((a, b) => {
      const dateA = new Date(a.date_debut || 0).getTime();
      const dateB = new Date(b.date_debut || 0).getTime();
      return ascending ? dateA - dateB : dateB - dateA;
    });
  }

  /**
   * Recherche dans les événements
   */
  searchEvents(query: string): any[] {
    if (!query || query.trim() === '') return this.evenements;

    const searchTerm = query.toLowerCase().trim();

    return this.evenements.filter(e => {
      const titre = (e.titre || '').toLowerCase();
      const description = (e.description || '').toLowerCase();
      const lieu = (e.lieu || '').toLowerCase();
      const type = (e.type || '').toLowerCase();

      return titre.includes(searchTerm) ||
        description.includes(searchTerm) ||
        lieu.includes(searchTerm) ||
        type.includes(searchTerm);
    });
  }

  /**
   * Filtre les événements par type
   */
  filterEventsByType(type: string): any[] {
    if (!type || type === 'all') return this.evenements;

    return this.evenements.filter(e =>
      (e.type || '').toLowerCase() === type.toLowerCase()
    );
  }

  /**
   * Obtient tous les types d'événements uniques
   */
  getEventTypes(): string[] {
    const types = this.evenements
      .map(e => e.type)
      .filter(type => type && type.trim() !== '');

    return [...new Set(types)];
  }

  /**
   * Obtient les statistiques des événements
   */
  getEventsStats(): any {
    const upcoming = this.getUpcomingEvents().length;
    const past = this.getPastEvents().length;
    const inscriptions = this.mesInscriptions.length;
    const types = this.getEventTypes().length;

    return {
      total: this.evenements.length,
      upcoming,
      past,
      inscriptions,
      types,
      participationRate: this.evenements.length > 0
        ? Math.round((inscriptions / this.evenements.length) * 100)
        : 0
    };
  }

  /**
   * Vérifie si l'utilisateur peut s'inscrire à un événement
   */
  canRegister(evenement: any): boolean {
    if (this.isEventPast(evenement.date_debut)) return false;
    if (this.isInscrit(evenement)) return false;

    if (evenement.capacite_max && evenement.participants_count >= evenement.capacite_max) {
      return false;
    }

    return true;
  }

  /**
   * Obtient le message de disponibilité pour un événement
   */
  getAvailabilityMessage(evenement: any): string {
    if (this.isEventPast(evenement.date_debut)) {
      return 'Événement terminé';
    }

    if (this.isInscrit(evenement)) {
      return 'Vous êtes inscrit';
    }

    if (evenement.capacite_max) {
      const placesRestantes = evenement.capacite_max - (evenement.participants_count || 0);
      if (placesRestantes <= 0) {
        return 'Complet';
      }
      if (placesRestantes <= 5) {
        return `${placesRestantes} place(s) restante(s)`;
      }
      return 'Places disponibles';
    }

    return 'Inscription ouverte';
  }
}
