import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RessourceService } from '../../../services/ressource.service';

@Component({
  selector: 'app-membre-ressources',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './ressources.html',
  styleUrl: './ressources.css'
})
export class RessourcesMembre implements OnInit {
  loading = true;
  errorMessage: string | null = null;

  items: any[] = [];
  filteredItems: any[] = [];
  downloadingId: number | null = null;

  // Filtres
  selectedCategory: string = 'all';
  selectedType: string = 'all';
  searchQuery: string = '';

  constructor(private ressourceService: RessourceService) { }

  ngOnInit(): void {
    this.ressourceService.getRessources().subscribe({
      next: (res: any) => {
        this.items = res?.data ?? res?.ressources ?? res ?? [];
        this.applyFilters();
      },
      error: (err) => {
        this.errorMessage = err.error?.message || 'Impossible de charger les ressources.';

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
   * Télécharge une ressource
   */
  download(id: number): void {
    this.downloadingId = id;
    this.errorMessage = null;

    this.ressourceService.downloadRessource(id).subscribe({
      next: (resp) => {
        const blob = resp.body;
        if (!blob) {
          this.errorMessage = 'Fichier vide ou introuvable.';
          return;
        }

        const disposition = resp.headers.get('content-disposition') || '';
        const match = disposition.match(/filename\*?=(?:UTF-8''|\")?([^;\"]+)/i);
        const filename = match?.[1] ? decodeURIComponent(match[1]) : `ressource-${id}`;

        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        a.click();
        window.URL.revokeObjectURL(url);
      },
      error: (err) => {
        this.errorMessage = err.error?.message || 'Téléchargement impossible.';

        // Auto-masquer le message d'erreur après 5 secondes
        setTimeout(() => {
          this.errorMessage = null;
        }, 5000);
      },
      complete: () => {
        this.downloadingId = null;
      }
    });
  }

  /**
   * Applique les filtres sur les ressources
   */
  applyFilters(): void {
    let filtered = [...this.items];

    // Filtre par catégorie
    if (this.selectedCategory !== 'all') {
      filtered = filtered.filter(item =>
        (item.categorie || '').toLowerCase() === this.selectedCategory.toLowerCase()
      );
    }

    // Filtre par type
    if (this.selectedType !== 'all') {
      filtered = filtered.filter(item =>
        (item.type || '').toLowerCase() === this.selectedType.toLowerCase()
      );
    }

    // Filtre par recherche
    if (this.searchQuery && this.searchQuery.trim() !== '') {
      const searchTerm = this.searchQuery.toLowerCase().trim();
      filtered = filtered.filter(item => {
        const titre = (item.titre || item.nom || '').toLowerCase();
        const description = (item.description || '').toLowerCase();
        const categorie = (item.categorie || '').toLowerCase();

        return titre.includes(searchTerm) ||
          description.includes(searchTerm) ||
          categorie.includes(searchTerm);
      });
    }

    this.filteredItems = filtered;
  }

  /**
   * Réinitialise tous les filtres
   */
  resetFilters(): void {
    this.selectedCategory = 'all';
    this.selectedType = 'all';
    this.searchQuery = '';
    this.applyFilters();
  }

  /**
   * Vérifie si des filtres sont actifs
   */
  hasActiveFilters(): boolean {
    return this.selectedCategory !== 'all' ||
      this.selectedType !== 'all' ||
      (this.searchQuery !== null && this.searchQuery !== undefined && this.searchQuery.trim() !== '');
  }

  /**
   * Filtre par catégorie spécifique
   */
  filterByCategory(category: string): void {
    this.selectedCategory = this.selectedCategory === category ? 'all' : category;
    this.applyFilters();
  }

  /**
   * Obtient toutes les catégories uniques
   */
  getUniqueCategories(): string[] {
    const categories = this.items
      .map(item => item.categorie)
      .filter(cat => cat && cat.trim() !== '');

    return [...new Set(categories)];
  }

  /**
   * Obtient tous les types uniques
   */
  getUniqueTypes(): string[] {
    const types = this.items
      .map(item => item.type)
      .filter(type => type && type.trim() !== '');

    return [...new Set(types)];
  }

  /**
   * Compte les ressources par catégorie
   */
  countByCategory(category: string): number {
    return this.items.filter(item =>
      (item.categorie || '').toLowerCase() === category.toLowerCase()
    ).length;
  }

  /**
   * Obtient la classe CSS pour le type de fichier
   */
  getFileTypeClass(type: string | null | undefined): string {
    if (!type) return 'type-default';

    const typeNormalized = type.toLowerCase();

    if (typeNormalized.includes('pdf')) return 'type-pdf';
    if (typeNormalized.includes('word') || typeNormalized.includes('doc')) return 'type-word';
    if (typeNormalized.includes('excel') || typeNormalized.includes('xls')) return 'type-excel';
    if (typeNormalized.includes('image') || typeNormalized.includes('png') ||
      typeNormalized.includes('jpg') || typeNormalized.includes('jpeg')) return 'type-image';
    if (typeNormalized.includes('video') || typeNormalized.includes('mp4')) return 'type-video';

    return 'type-default';
  }

  /**
   * Obtient l'icône pour le type de fichier
   */
  getFileTypeIcon(type: string | null | undefined): string {
    if (!type) return 'bi-file-earmark-text-fill';

    const typeNormalized = type.toLowerCase();

    if (typeNormalized.includes('pdf')) return 'bi-file-earmark-pdf-fill';
    if (typeNormalized.includes('word') || typeNormalized.includes('doc')) return 'bi-file-earmark-word-fill';
    if (typeNormalized.includes('excel') || typeNormalized.includes('xls')) return 'bi-file-earmark-excel-fill';
    if (typeNormalized.includes('image') || typeNormalized.includes('png') ||
      typeNormalized.includes('jpg') || typeNormalized.includes('jpeg')) return 'bi-file-earmark-image-fill';
    if (typeNormalized.includes('video') || typeNormalized.includes('mp4')) return 'bi-file-earmark-play-fill';
    if (typeNormalized.includes('audio') || typeNormalized.includes('mp3')) return 'bi-file-earmark-music-fill';
    if (typeNormalized.includes('zip') || typeNormalized.includes('rar')) return 'bi-file-earmark-zip-fill';
    if (typeNormalized.includes('powerpoint') || typeNormalized.includes('ppt')) return 'bi-file-earmark-ppt-fill';

    return 'bi-file-earmark-text-fill';
  }

  /**
   * Obtient l'icône pour une catégorie
   */
  getCategoryIcon(category: string | null | undefined): string {
    if (!category) return 'bi-folder-fill';

    const catNormalized = category.toLowerCase();

    if (catNormalized.includes('formation')) return 'bi-mortarboard-fill';
    if (catNormalized.includes('document') || catNormalized.includes('admin')) return 'bi-file-earmark-text-fill';
    if (catNormalized.includes('guide') || catNormalized.includes('manuel')) return 'bi-book-fill';
    if (catNormalized.includes('rapport')) return 'bi-bar-chart-fill';
    if (catNormalized.includes('formulaire')) return 'bi-pencil-square';
    if (catNormalized.includes('image') || catNormalized.includes('photo')) return 'bi-camera-fill';
    if (catNormalized.includes('video')) return 'bi-camera-video-fill';
    if (catNormalized.includes('présentation') || catNormalized.includes('presentation')) return 'bi-easel-fill';

    return 'bi-folder-fill';
  }

  /**
   * Formate la taille du fichier
   */
  formatFileSize(bytes: number | string | null | undefined): string {
    if (!bytes) return 'Taille inconnue';

    const numBytes = typeof bytes === 'string' ? parseInt(bytes) : bytes;
    if (isNaN(numBytes)) return 'Taille inconnue';

    const sizes = ['o', 'Ko', 'Mo', 'Go', 'To'];
    if (numBytes === 0) return '0 o';

    const i = Math.floor(Math.log(numBytes) / Math.log(1024));
    const size = (numBytes / Math.pow(1024, i)).toFixed(2);

    return `${size} ${sizes[i]}`;
  }

  /**
   * Formate une date
   */
  formatDate(date: string | Date | null | undefined): string {
    if (!date) return 'Date non spécifiée';

    try {
      const dateObj = typeof date === 'string' ? new Date(date) : date;
      if (isNaN(dateObj.getTime())) return 'Date invalide';

      return dateObj.toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      });
    } catch {
      return 'Date invalide';
    }
  }

  /**
   * Tronque la description
   */
  truncateDescription(description: string | null | undefined, maxLength: number = 120): string {
    if (!description) return '';
    if (description.length <= maxLength) return description;
    return description.substring(0, maxLength) + '...';
  }

  /**
   * Vérifie si un fichier peut être prévisualisé
   */
  canPreview(type: string | null | undefined): boolean {
    if (!type) return false;

    const typeNormalized = type.toLowerCase();

    return typeNormalized.includes('pdf') ||
      typeNormalized.includes('image') ||
      typeNormalized.includes('png') ||
      typeNormalized.includes('jpg') ||
      typeNormalized.includes('jpeg');
  }

  /**
   * Prévisualise un fichier
   */
  preview(ressource: any): void {
    // TODO: Implémenter la prévisualisation dans une modal
    console.log('Prévisualisation de:', ressource);
    alert('Fonctionnalité de prévisualisation à venir');
  }

  /**
   * Calcule la taille totale de toutes les ressources
   */
  getTotalSize(): string {
    const totalBytes = this.items.reduce((total, item) => {
      const size = typeof item.taille === 'string' ? parseInt(item.taille) : (item.taille || 0);
      return total + (isNaN(size) ? 0 : size);
    }, 0);

    return this.formatFileSize(totalBytes);
  }

  /**
   * Calcule le nombre total de téléchargements
   */
  getTotalDownloads(): number {
    return this.items.reduce((total, item) => {
      const downloads = parseInt(item.telechargements || 0);
      return total + (isNaN(downloads) ? 0 : downloads);
    }, 0);
  }

  /**
   * Obtient les ressources les plus téléchargées
   */
  getMostDownloaded(limit: number = 5): any[] {
    return [...this.items]
      .filter(item => item.telechargements)
      .sort((a, b) => {
        const downloadsA = parseInt(a.telechargements || 0);
        const downloadsB = parseInt(b.telechargements || 0);
        return downloadsB - downloadsA;
      })
      .slice(0, limit);
  }

  /**
   * Obtient les ressources récentes (30 derniers jours)
   */
  getRecentResources(days: number = 30): any[] {
    const cutoffDate = new Date();
    cutoffDate.setDate(cutoffDate.getDate() - days);

    return this.items.filter(item => {
      if (!item.date_creation) return false;

      const itemDate = new Date(item.date_creation);
      return itemDate >= cutoffDate;
    });
  }

  /**
   * Exporte la liste des ressources en CSV
   */
  exportToCSV(): void {
    if (!this.items || this.items.length === 0) {
      alert('Aucune ressource à exporter');
      return;
    }

    const headers = ['Titre', 'Catégorie', 'Type', 'Taille', 'Date', 'Téléchargements'];
    const csvContent = [
      headers.join(','),
      ...this.items.map(item => [
        this.escapeCSV(item.titre || item.nom || ''),
        this.escapeCSV(item.categorie || ''),
        this.escapeCSV(item.type || ''),
        this.escapeCSV(this.formatFileSize(item.taille)),
        this.escapeCSV(item.date_creation || ''),
        this.escapeCSV(item.telechargements?.toString() || '0')
      ].join(','))
    ].join('\n');

    this.downloadFile(csvContent, 'ressources.csv', 'text/csv');
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
   * Trie les ressources
   */
  sortResources(sortBy: 'name' | 'date' | 'size' | 'downloads', ascending: boolean = true): any[] {
    const sorted = [...this.filteredItems];

    switch (sortBy) {
      case 'name':
        return sorted.sort((a, b) => {
          const nameA = (a.titre || a.nom || '').toLowerCase();
          const nameB = (b.titre || b.nom || '').toLowerCase();
          return ascending ? nameA.localeCompare(nameB) : nameB.localeCompare(nameA);
        });

      case 'date':
        return sorted.sort((a, b) => {
          const dateA = new Date(a.date_creation || 0).getTime();
          const dateB = new Date(b.date_creation || 0).getTime();
          return ascending ? dateA - dateB : dateB - dateA;
        });

      case 'size':
        return sorted.sort((a, b) => {
          const sizeA = parseInt(a.taille || 0);
          const sizeB = parseInt(b.taille || 0);
          return ascending ? sizeA - sizeB : sizeB - sizeA;
        });

      case 'downloads':
        return sorted.sort((a, b) => {
          const downloadsA = parseInt(a.telechargements || 0);
          const downloadsB = parseInt(b.telechargements || 0);
          return ascending ? downloadsA - downloadsB : downloadsB - downloadsA;
        });

      default:
        return sorted;
    }
  }

  /**
   * Obtient des statistiques détaillées
   */
  getDetailedStats(): any {
    const mostDownloadedList = this.getMostDownloaded(1);
    const stats = {
      total: this.items.length,
      categories: this.getUniqueCategories().length,
      types: this.getUniqueTypes().length,
      totalSize: this.getTotalSize(),
      totalDownloads: this.getTotalDownloads(),
      recentResources: this.getRecentResources().length,
      mostDownloaded: mostDownloadedList.length > 0 ? mostDownloadedList[0].titre : 'N/A'
    };

    return stats;
  }

  /**
   * Recherche avancée dans les ressources
   */
  advancedSearch(query: string, fields: string[] = ['titre', 'description', 'categorie']): any[] {
    if (!query || query.trim() === '') return this.items;

    const searchTerm = query.toLowerCase().trim();

    return this.items.filter(item => {
      return fields.some(field => {
        const value = (item[field] || '').toLowerCase();
        return value.includes(searchTerm);
      });
    });
  }

  /**
   * Partage une ressource (copie le lien)
   */
  shareResource(ressource: any): void {
    // Construction d'un lien de partage (à adapter selon votre application)
    const shareUrl = `${window.location.origin}/ressources/${ressource.id}`;

    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(shareUrl).then(() => {
        alert('Lien copié dans le presse-papier !');
      }).catch(() => {
        this.fallbackCopyToClipboard(shareUrl);
      });
    } else {
      this.fallbackCopyToClipboard(shareUrl);
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
   * Marque une ressource comme favorite (à implémenter avec backend)
   */
  toggleFavorite(ressource: any): void {
    // TODO: Implémenter avec le backend
    console.log('Toggle favorite:', ressource);
    alert('Fonctionnalité de favoris à venir');
  }

  /**
   * Signale un problème avec une ressource
   */
  reportResource(ressource: any): void {
    const reason = prompt('Veuillez indiquer la raison du signalement :');

    if (reason && reason.trim()) {
      // TODO: Appeler une API pour enregistrer le signalement
      console.log('Ressource signalée:', ressource.id, 'Raison:', reason);
      alert('Merci pour votre signalement. Notre équipe va examiner cette ressource.');
    }
  }
}
