import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';
import { HistoriqueService } from '../../../services/historique.service';

@Component({
  selector: 'app-membre-historique',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule],
  templateUrl: './historique.html',
  styleUrl: './historique.css'
})
export class HistoriqueMembre implements OnInit {
  loading = true;
  errorMessage: string | null = null;

  items: any[] = [];
  filteredItems: any[] = [];
  groupedByMonth: any[] = [];

  // Filtres
  selectedType: string = 'all';
  selectedPeriod: string = 'all';
  sortBy: string = 'date-desc';

  constructor(private historiqueService: HistoriqueService) { }

  ngOnInit(): void {
    this.historiqueService.getHistorique().subscribe({
      next: (res: any) => {
        this.items = res?.data ?? res?.historique ?? res ?? [];
        this.applyFilters();
      },
      error: (err) => {
        this.errorMessage = err.error?.message || 'Impossible de charger l\'historique.';

        // Auto-masquer le message d'erreur après 5 secondes
        setTimeout(() => {
          this.errorMessage = null;
        }, 5000);
      },
      complete: () => {
        this.loading = false;
      }
    });
  }

  /**
   * Applique les filtres et le tri sur les items
   */
  applyFilters(): void {
    let filtered = [...this.items];

    // Filtre par type
    if (this.selectedType !== 'all') {
      filtered = filtered.filter(item =>
        (item.type_participation || '').toLowerCase() === this.selectedType.toLowerCase()
      );
    }

    // Filtre par période
    if (this.selectedPeriod !== 'all') {
      filtered = this.filterByPeriod(filtered, this.selectedPeriod);
    }

    // Tri
    filtered = this.sortItems(filtered, this.sortBy);

    this.filteredItems = filtered;
    this.groupedByMonth = this.groupItemsByMonth(filtered);
  }

  /**
   * Filtre les items par période
   */
  private filterByPeriod(items: any[], period: string): any[] {
    const now = new Date();
    const currentYear = now.getFullYear();
    const currentMonth = now.getMonth();

    return items.filter(item => {
      if (!item.date_participation) return false;

      const itemDate = new Date(item.date_participation);
      const itemYear = itemDate.getFullYear();
      const itemMonth = itemDate.getMonth();

      switch (period) {
        case 'month':
          return itemYear === currentYear && itemMonth === currentMonth;

        case 'quarter':
          const currentQuarter = Math.floor(currentMonth / 3);
          const itemQuarter = Math.floor(itemMonth / 3);
          return itemYear === currentYear && itemQuarter === currentQuarter;

        case 'year':
          return itemYear === currentYear;

        default:
          return true;
      }
    });
  }

  /**
   * Trie les items selon le critère
   */
  private sortItems(items: any[], sortBy: string): any[] {
    const sorted = [...items];

    switch (sortBy) {
      case 'date-desc':
        return sorted.sort((a, b) => {
          const dateA = new Date(a.date_participation || 0).getTime();
          const dateB = new Date(b.date_participation || 0).getTime();
          return dateB - dateA;
        });

      case 'date-asc':
        return sorted.sort((a, b) => {
          const dateA = new Date(a.date_participation || 0).getTime();
          const dateB = new Date(b.date_participation || 0).getTime();
          return dateA - dateB;
        });

      case 'amount-desc':
        return sorted.sort((a, b) => {
          const amountA = parseFloat(a.montant_implique || 0);
          const amountB = parseFloat(b.montant_implique || 0);
          return amountB - amountA;
        });

      case 'amount-asc':
        return sorted.sort((a, b) => {
          const amountA = parseFloat(a.montant_implique || 0);
          const amountB = parseFloat(b.montant_implique || 0);
          return amountA - amountB;
        });

      default:
        return sorted;
    }
  }

  /**
   * Groupe les items par mois
   */
  private groupItemsByMonth(items: any[]): any[] {
    const groups: { [key: string]: any[] } = {};

    items.forEach(item => {
      if (!item.date_participation) return;

      const date = new Date(item.date_participation);
      const monthKey = this.getMonthKey(date);

      if (!groups[monthKey]) {
        groups[monthKey] = [];
      }

      groups[monthKey].push(item);
    });

    // Convertir en tableau et trier par date décroissante
    return Object.keys(groups)
      .sort((a, b) => b.localeCompare(a))
      .map(key => ({
        month: this.formatMonthYear(key),
        items: groups[key]
      }));
  }

  /**
   * Obtient la clé du mois (YYYY-MM)
   */
  private getMonthKey(date: Date): string {
    const year = date.getFullYear();
    const month = (date.getMonth() + 1).toString().padStart(2, '0');
    return `${year}-${month}`;
  }

  /**
   * Formate le mois et l'année
   */
  private formatMonthYear(key: string): string {
    const [year, month] = key.split('-');
    const date = new Date(parseInt(year), parseInt(month) - 1, 1);

    return date.toLocaleDateString('fr-FR', {
      month: 'long',
      year: 'numeric'
    });
  }

  /**
   * Formate une date en français
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
   * Formate un montant en FCFA
   */
  formatAmount(amount: number | string | null | undefined): string {
    if (!amount) return '0 FCFA';

    try {
      const numAmount = typeof amount === 'string' ? parseFloat(amount) : amount;
      if (isNaN(numAmount)) return '0 FCFA';

      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'XAF',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
      }).format(numAmount);
    } catch {
      return `${amount} FCFA`;
    }
  }

  /**
   * Obtient tous les types uniques de participations
   */
  getUniqueTypes(): string[] {
    const types = this.items
      .map(item => item.type_participation)
      .filter(type => type && type.trim() !== '');

    return [...new Set(types)];
  }

  /**
   * Obtient la classe CSS pour le type
   */
  getTypeClass(type: string | null | undefined): string {
    if (!type) return 'type-default';

    const typeNormalized = type.toLowerCase();

    if (typeNormalized.includes('projet')) return 'type-projet';
    if (typeNormalized.includes('événement') || typeNormalized.includes('evenement')) return 'type-evenement';
    if (typeNormalized.includes('cotisation')) return 'type-cotisation';

    return 'type-default';
  }

  /**
   * Obtient l'icône pour le type
   */
  getTypeIcon(type: string | null | undefined): string {
    if (!type) return 'bi-clipboard-check-fill';

    const typeNormalized = type.toLowerCase();

    if (typeNormalized.includes('projet')) return 'bi-bullseye';
    if (typeNormalized.includes('événement') || typeNormalized.includes('evenement')) return 'bi-calendar-event-fill';
    if (typeNormalized.includes('cotisation')) return 'bi-cash-coin';
    if (typeNormalized.includes('formation')) return 'bi-mortarboard-fill';
    if (typeNormalized.includes('bénévolat') || typeNormalized.includes('benevolat')) return 'bi-people-fill';

    return 'bi-clipboard-check-fill';
  }

  /**
   * Vérifie si l'item a des informations additionnelles
   */
  hasAdditionalInfo(item: any): boolean {
    return !!(item.projet_nom || item.evenement_nom || item.statut);
  }

  /**
   * Calcule le montant total
   */
  getTotalAmount(): number {
    return this.items.reduce((total, item) => {
      const amount = parseFloat(item.montant_implique || 0);
      return total + (isNaN(amount) ? 0 : amount);
    }, 0);
  }

  /**
   * Compte les participations du mois en cours
   */
  getThisMonthCount(): number {
    const now = new Date();
    const currentYear = now.getFullYear();
    const currentMonth = now.getMonth();

    return this.items.filter(item => {
      if (!item.date_participation) return false;

      const itemDate = new Date(item.date_participation);
      return itemDate.getFullYear() === currentYear &&
        itemDate.getMonth() === currentMonth;
    }).length;
  }

  /**
   * Exporte l'historique en CSV
   */
  exportToCSV(): void {
    if (!this.items || this.items.length === 0) {
      alert('Aucune donnée à exporter');
      return;
    }

    const headers = ['Type', 'Titre', 'Date', 'Montant', 'Description', 'Statut'];
    const csvContent = [
      headers.join(','),
      ...this.items.map(item => [
        this.escapeCSV(item.type_participation || ''),
        this.escapeCSV(item.titre || ''),
        this.escapeCSV(item.date_participation || ''),
        this.escapeCSV(item.montant_implique?.toString() || '0'),
        this.escapeCSV(item.description || ''),
        this.escapeCSV(item.statut || '')
      ].join(','))
    ].join('\n');

    this.downloadFile(csvContent, 'historique.csv', 'text/csv');
  }

  /**
   * Échappe les valeurs CSV
   */
  private escapeCSV(value: string): string {
    if (value.includes(',') || value.includes('"') || value.includes('\n')) {
      return `"${value.replace(/"/g, '""')}"`;
    }
    return value;
  }

  /**
   * Télécharge un fichier
   */
  private downloadFile(content: string, filename: string, type: string): void {
    const blob = new Blob([content], { type });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = filename;
    link.click();
    window.URL.revokeObjectURL(url);
  }

  /**
   * Imprime l'historique
   */
  printHistory(): void {
    window.print();
  }

  /**
   * Réinitialise les filtres
   */
  resetFilters(): void {
    this.selectedType = 'all';
    this.selectedPeriod = 'all';
    this.sortBy = 'date-desc';
    this.applyFilters();
  }

  /**
   * Obtient les statistiques par type
   */
  getStatsByType(): any[] {
    const stats: { [key: string]: { count: number; total: number } } = {};

    this.items.forEach(item => {
      const type = item.type_participation || 'Non spécifié';

      if (!stats[type]) {
        stats[type] = { count: 0, total: 0 };
      }

      stats[type].count++;
      const amount = parseFloat(item.montant_implique || 0);
      if (!isNaN(amount)) {
        stats[type].total += amount;
      }
    });

    return Object.keys(stats).map(type => ({
      type,
      count: stats[type].count,
      total: stats[type].total
    }));
  }

  /**
   * Obtient les participations récentes (30 derniers jours)
   */
  getRecentParticipations(): any[] {
    const thirtyDaysAgo = new Date();
    thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);

    return this.items.filter(item => {
      if (!item.date_participation) return false;

      const itemDate = new Date(item.date_participation);
      return itemDate >= thirtyDaysAgo;
    });
  }

  /**
   * Calcule le taux d'activité (participations par mois)
   */
  getActivityRate(): number {
    if (this.items.length === 0) return 0;

    const dates = this.items
      .filter(item => item.date_participation)
      .map(item => new Date(item.date_participation));

    if (dates.length === 0) return 0;

    const oldestDate = new Date(Math.min(...dates.map(d => d.getTime())));
    const now = new Date();

    const monthsDiff = (now.getFullYear() - oldestDate.getFullYear()) * 12 +
      (now.getMonth() - oldestDate.getMonth()) + 1;

    return Math.round(this.items.length / monthsDiff);
  }

  /**
   * Recherche dans l'historique
   */
  searchInHistory(query: string): any[] {
    if (!query || query.trim() === '') return this.items;

    const searchTerm = query.toLowerCase().trim();

    return this.items.filter(item => {
      const titre = (item.titre || '').toLowerCase();
      const description = (item.description || '').toLowerCase();
      const type = (item.type_participation || '').toLowerCase();

      return titre.includes(searchTerm) ||
        description.includes(searchTerm) ||
        type.includes(searchTerm);
    });
  }

  /**
   * Obtient le nombre de jours depuis la dernière participation
   */
  getDaysSinceLastParticipation(): number {
    if (this.items.length === 0) return 0;

    const dates = this.items
      .filter(item => item.date_participation)
      .map(item => new Date(item.date_participation).getTime());

    if (dates.length === 0) return 0;

    const mostRecentDate = new Date(Math.max(...dates));
    const now = new Date();
    const diffTime = now.getTime() - mostRecentDate.getTime();
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    return diffDays;
  }

  /**
   * Obtient la participation la plus récente
   */
  getMostRecentParticipation(): any | null {
    if (this.items.length === 0) return null;

    const itemsWithDate = this.items.filter(item => item.date_participation);
    if (itemsWithDate.length === 0) return null;

    return itemsWithDate.reduce((latest, item) => {
      const latestDate = new Date(latest.date_participation).getTime();
      const itemDate = new Date(item.date_participation).getTime();
      return itemDate > latestDate ? item : latest;
    });
  }

  /**
   * Obtient des statistiques globales
   */
  getGlobalStats(): any {
    return {
      total: this.items.length,
      thisMonth: this.getThisMonthCount(),
      totalAmount: this.getTotalAmount(),
      types: this.getUniqueTypes().length,
      activityRate: this.getActivityRate(),
      daysSinceLast: this.getDaysSinceLastParticipation(),
      recent: this.getRecentParticipations().length
    };
  }
}
