import { Component, OnInit, ViewChild, AfterViewInit, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { StatsService, DashboardStats } from '../../../services/stats.service';
import { StatsGraphe } from '../stats-graphe/stats-graphe';

@Component({
  selector: 'app-stats',
  standalone: true,
  imports: [CommonModule, StatsGraphe],
  templateUrl: './stats.html',
  styleUrl: './stats.css',
})
export class Stats implements OnInit, AfterViewInit {

  @ViewChild(StatsGraphe) childGrapheComponent!: StatsGraphe;

  statsData: DashboardStats | null = null;
  isLoading: boolean = true;
  renderCharts: boolean = false; // Flag pour forcer le cycle de vie de l'enfant
  errorMessage: string | null = null;
  currentPeriod: string = '1year';
  financialHealth: { status: string, message: string } = { status: 'Calcul...', message: '' };

  constructor(
    private statsService: StatsService,
    private cdr: ChangeDetectorRef
  ) {
    console.log('%c[Parent-Stats] Constructeur appelé', 'color: #3498db');
  }

  ngOnInit(): void {
    console.log('%c[Parent-Stats] ngOnInit déclenché', 'color: #3498db');
    this.loadStatistics();
  }

  ngAfterViewInit() {
    console.log('[Parent-Stats] ngAfterViewInit - Graphe présent ?:', !!this.childGrapheComponent);
  }

  loadStatistics(forceRefresh: boolean = false): void {
    this.isLoading = true;
    this.renderCharts = false; // On désactive le graphe pendant le chargement
    this.errorMessage = null;

    console.log(`%c[Parent-Stats] Chargement (Période: ${this.currentPeriod}, Force: ${forceRefresh})`, 'color: #f39c12');

    this.statsService.getGlobalStats(this.currentPeriod, forceRefresh).subscribe({
      next: (data) => {
        console.log('%c[Parent-Stats] Données reçues du service:', 'color: #2ecc71', data);

        if (!data || Object.keys(data).length === 0) {
          console.warn('[Parent-Stats] Les données reçues sont vides !');
          this.errorMessage = "Le serveur a renvoyé des données vides.";
          this.isLoading = false;
          return;
        }

        // --- CLONAGE PROFOND POUR DÉTECTION ---
        this.statsData = JSON.parse(JSON.stringify(data));
        this.runFinancialAnalysis();

        // --- LOGIQUE DE RENDU CRITIQUE ---
        // 1. On finit le chargement
        this.isLoading = false;

        // 2. On laisse un micro-délai pour que le loader disparaisse
        // 3. On active renderCharts qui va faire apparaître <app-stats-graphe> dans le HTML
        setTimeout(() => {
          this.renderCharts = true;
          this.cdr.detectChanges(); // Force Angular à voir que renderCharts est passé à true
          console.log('%c[Parent-Stats] Activation de renderCharts (Le graphe devrait apparaître)', 'color: #9b59b6; font-weight: bold');

          // Debugging final après injection
          setTimeout(() => {
            console.log('[Parent-Stats] Vérification finale de l\'enfant:', this.childGrapheComponent ? 'Injecté' : 'ABSENT DU DOM');
          }, 100);
        }, 300);
      },
      error: (err) => {
        console.error('%c[Parent-Stats] Erreur API:', 'color: #e74c3c', err);
        this.errorMessage = "Impossible de récupérer les statistiques. Vérifiez votre backend.";
        this.isLoading = false;
        this.renderCharts = false;
      }
    });
  }

  private runFinancialAnalysis(): void {
    if (!this.statsData?.finance) return;
    const rev = this.statsData.finance.total_revenus;
    console.log('[Parent-Stats] Analyse financière sur revenus:', rev);

    if (rev > 2000000) {
      this.financialHealth = { status: 'Excellent', message: 'Trésorerie robuste.' };
    } else if (rev > 500000) {
      this.financialHealth = { status: 'Bon', message: 'Activité stable.' };
    } else {
      this.financialHealth = { status: 'Attention', message: 'Revenus en dessous des objectifs.' };
    }
  }

  onPeriodChange(period: string): void {
    console.log('[Parent-Stats] Changement de période vers:', period);
    this.currentPeriod = period;
    this.loadStatistics(true);
  }

  refresh(): void {
    console.log('[Parent-Stats] Rafraîchissement manuel...');
    if (this.statsService.clearCache) this.statsService.clearCache();
    this.loadStatistics(true);
  }

  formatNumber(val: number | undefined): string {
    return new Intl.NumberFormat('fr-FR').format(val || 0);
  }

  getPercentage(part: number | undefined, total: number | undefined): number {
    if (!total || total === 0 || part === undefined) return 0;
    return Math.min(Math.round((part / total) * 100), 100);
  }

  exportData(): void {
    if (this.statsData) {
      console.log('[Parent-Stats] Export CSV lancé');
      this.statsService.exportStatsToCSV(this.statsData);
    }
  }
}