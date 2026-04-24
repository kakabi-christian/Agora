import { Component, Input, OnInit, OnChanges, SimpleChanges, ViewChild, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { BaseChartDirective } from 'ng2-charts'; 
import { ChartConfiguration, ChartData } from 'chart.js';
import { DashboardStats } from '../../../services/stats.service';

@Component({
  selector: 'app-stats-graphe',
  standalone: true,
  imports: [CommonModule, BaseChartDirective], 
  templateUrl: './stats-graphe.html',
  styleUrl: './stats-graphe.css',
})
export class StatsGraphe implements OnInit, OnChanges {
  @ViewChild(BaseChartDirective) chart: BaseChartDirective | undefined;
  
  @Input() statsData: DashboardStats | null = null;

  // --- 1. Graphique d'Évolution (Membres au lieu de Revenus) ---
  public lineChartData: ChartData<'line'> = {
    labels: ['Jan', 'Avr', 'Juil', 'Aujourd\'hui'],
    datasets: [{ 
      data: [0, 0, 0, 0], 
      label: 'Nombre total de membres', 
      borderColor: '#0d6efd', 
      backgroundColor: 'rgba(13, 110, 253, 0.1)',
      fill: true, tension: 0.4 
    }]
  };

  public lineChartOptions: ChartConfiguration<'line'>['options'] = {
    responsive: true,
    maintainAspectRatio: false,
    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
  };

  // --- 2. Graphique de Répartition (Membres vs Partenaires) ---
  public doughnutChartData: ChartData<'doughnut'> = {
    labels: ['Membres Actifs', 'Partenaires'],
    datasets: [{ data: [0, 0], backgroundColor: ['#0d6efd', '#ffc107'] }]
  };

  public doughnutChartOptions: ChartConfiguration<'doughnut'>['options'] = {
    responsive: true,
    maintainAspectRatio: false,
    cutout: '70%'
  };

  // --- 3. Graphique des Villes (Histogramme) ---
  public barChartData: ChartData<'bar'> = {
    labels: [],
    datasets: [{ data: [], label: 'Membres par ville', backgroundColor: '#198754' }]
  };

  public barChartOptions: ChartConfiguration<'bar'>['options'] = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } }
  };

  constructor(private cdr: ChangeDetectorRef) {}

  ngOnInit(): void {
    if (this.statsData) this.updateCharts();
  }

  ngOnChanges(changes: SimpleChanges): void {
    if (changes['statsData'] && this.statsData) {
      this.updateCharts();
    }
  }

  private updateCharts() {
    if (!this.statsData) return;

    try {
      // 1. Évolution des membres (On simule une courbe basée sur le total actuel)
      const totalMembres = this.statsData.membres?.total || 0;
      this.lineChartData = {
        ...this.lineChartData,
        datasets: [{
          ...this.lineChartData.datasets[0],
          data: [
            Math.floor(totalMembres * 0.4), 
            Math.floor(totalMembres * 0.7), 
            Math.floor(totalMembres * 0.9), 
            totalMembres
          ]
        }]
      };

      // 2. Répartition Membres vs Partenaires
      const actifs = this.statsData.membres?.actifs || 0;
      const partenaires = this.statsData.systeme?.partenaires || 0;
      
      this.doughnutChartData = {
        labels: ['Membres Actifs', 'Partenaires'],
        datasets: [{
          data: (actifs === 0 && partenaires === 0) ? [1, 0.1] : [actifs, partenaires],
          backgroundColor: ['#0d6efd', '#ffc107']
        }]
      };

      // 3. Top Villes
      const villes = this.statsData.membres?.villes_detaillees || [];
      this.barChartData = {
        labels: villes.length > 0 ? villes.slice(0, 6).map(v => v.ville) : ['Villes'],
        datasets: [{
          label: 'Membres',
          data: villes.length > 0 ? villes.slice(0, 6).map(v => v.total) : [0],
          backgroundColor: '#198754',
          borderRadius: 5
        }]
      };

      this.cdr.detectChanges(); 
      setTimeout(() => this.chart?.chart?.update(), 150);

    } catch (error) {
      console.error('[StatsGraphe] Erreur:', error);
    }
  }
}