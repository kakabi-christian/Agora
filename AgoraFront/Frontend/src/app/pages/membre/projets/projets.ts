import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { ProjetService } from '../../../services/projet.service';

@Component({
  selector: 'app-membre-projets',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './projets.html',
  styleUrl: './projets.css'
})
export class ProjetsMembre implements OnInit {
  loading = true;
  errorMessage: string | null = null;

  page = 1;
  projets: any[] = [];
  mesParticipations: any[] = [];

  constructor(private projetService: ProjetService) { }

  ngOnInit(): void {
    this.load();
  }

  load(): void {
    this.loading = true;
    this.errorMessage = null;

    this.projetService.getProjets(this.page).subscribe({
      next: (res: any) => {
        this.projets = res?.projets?.data ?? res?.data ?? res?.projets ?? res ?? [];
      },
      error: () => {
        this.errorMessage = 'Impossible de charger les projets.';
      },
      complete: () => {
        this.loading = false;
      }
    });

    this.projetService.mesParticipations().subscribe({
      next: (res: any) => {
        this.mesParticipations = res?.data ?? res?.participations ?? res ?? [];
      },
      error: () => {
        // Non bloquant
      }
    });
  }

  nextPage(): void {
    this.page += 1;
    this.load();
  }

  prevPage(): void {
    if (this.page <= 1) return;
    this.page -= 1;
    this.load();
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
   * Tronque la description si elle est trop longue
   */
  truncateDescription(description: string, maxLength: number = 120): string {
    if (!description) return '';
    if (description.length <= maxLength) return description;
    return description.substring(0, maxLength) + '...';
  }
}
