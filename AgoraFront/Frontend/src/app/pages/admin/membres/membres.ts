import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { MembreService, PaginatedMembres } from '../../../services/membre.service';
import { Membre } from '../../../models/membre.model';

@Component({
  selector: 'app-membres-admin',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './membres.html',
  styleUrl: './membres.css',
})
export class MembresAdmin implements OnInit {
  membres: Membre[] = [];
  loading = false;
  exportingPdf = false;
  exportingExcel = false;
  
  // Filtres
  filters = {
    statut: '',
    role: '',
    search: ''
  };
  
  // Pagination
  currentPage = 1;
  totalPages = 1;
  totalItems = 0;
  perPage = 20;

  // Messages
  successMessage = '';
  errorMessage = '';

  constructor(private membreService: MembreService) {}

  ngOnInit(): void {
    this.loadMembres();
  }

  /**
   * Charger la liste des membres
   */
  loadMembres(): void {
    this.loading = true;
    this.clearMessages();
    
    const params: any = {
      page: this.currentPage
    };
    
    if (this.filters.statut) {
      params.statut = this.filters.statut;
    }
    if (this.filters.role) {
      params.role = this.filters.role;
    }
    if (this.filters.search) {
      params.search = this.filters.search;
    }

    this.membreService.getMembres(params).subscribe({
      next: (response: PaginatedMembres) => {
        this.membres = response.data;
        this.totalPages = response.meta.last_page;
        this.totalItems = response.meta.total;
        this.perPage = response.meta.per_page;
        this.loading = false;
      },
      error: (error) => {
        console.error('Erreur lors du chargement des membres:', error);
        this.loading = false;
        this.showError('Erreur lors du chargement des membres');
      }
    });
  }

  /**
   * Appliquer les filtres
   */
  applyFilters(): void {
    this.currentPage = 1;
    this.loadMembres();
  }

  /**
   * Réinitialiser les filtres
   */
  resetFilters(): void {
    this.filters = {
      statut: '',
      role: '',
      search: ''
    };
    this.currentPage = 1;
    this.loadMembres();
  }

  /**
   * Changer de page
   */
  changePage(page: number): void {
    if (page >= 1 && page <= this.totalPages) {
      this.currentPage = page;
      this.loadMembres();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
  }

  /**
   * Obtenir les numéros de pages à afficher
   */
  getPageNumbers(): number[] {
    const pages: number[] = [];
    const maxPagesToShow = 5;
    
    let startPage = Math.max(1, this.currentPage - Math.floor(maxPagesToShow / 2));
    let endPage = Math.min(this.totalPages, startPage + maxPagesToShow - 1);
    
    if (endPage - startPage < maxPagesToShow - 1) {
      startPage = Math.max(1, endPage - maxPagesToShow + 1);
    }
    
    for (let i = startPage; i <= endPage; i++) {
      pages.push(i);
    }
    
    return pages;
  }

  /**
   * Exporter en Excel
   */
  exporterExcel(): void {
    this.exportingExcel = true;
    this.clearMessages();
    
    this.membreService.exportMembresExcel().subscribe({
      next: (blob: Blob) => {
        const filename = this.membreService.generateFilename('liste-membres', 'xlsx');
        this.membreService.downloadFile(blob, filename);
        this.exportingExcel = false;
        this.showSuccess('Export Excel réussi !');
      },
      error: (error) => {
        console.error('Erreur lors de l\'export Excel:', error);
        this.exportingExcel = false;
        this.showError('Erreur lors de l\'export Excel');
      }
    });
  }

  /**
   * Exporter en PDF
   */
  exporterPdf(): void {
    this.exportingPdf = true;
    this.clearMessages();
    
    this.membreService.exportMembresPdf().subscribe({
      next: (blob: Blob) => {
        const filename = this.membreService.generateFilename('liste-membres', 'pdf');
        this.membreService.downloadFile(blob, filename);
        this.exportingPdf = false;
        this.showSuccess('Export PDF réussi !');
      },
      error: (error) => {
        console.error('Erreur lors de l\'export PDF:', error);
        this.exportingPdf = false;
        this.showError('Erreur lors de l\'export PDF');
      }
    });
  }

  /**
   * Afficher un message de succès
   */
  private showSuccess(message: string): void {
    this.successMessage = message;
    setTimeout(() => {
      this.successMessage = '';
    }, 5000);
  }

  /**
   * Afficher un message d'erreur
   */
  private showError(message: string): void {
    this.errorMessage = message;
    setTimeout(() => {
      this.errorMessage = '';
    }, 5000);
  }

  /**
   * Effacer les messages
   */
  private clearMessages(): void {
    this.successMessage = '';
    this.errorMessage = '';
  }

  /**
   * Obtenir le badge de statut
   */
  getStatutBadgeClass(estActif: boolean): string {
    return estActif ? 'badge bg-success' : 'badge bg-danger';
  }

  /**
   * Obtenir le badge de rôle
   */
  getRoleBadgeClass(role: string): string {
    return role === 'administrateur' ? 'badge bg-warning text-dark' : 'badge bg-info';
  }

  /**
   * Formater la date
   */
  formatDate(date: string | Date | null | undefined): string {
    if (!date) return 'N/A';
    const d = new Date(date);
    return d.toLocaleDateString('fr-FR');
  }
}
