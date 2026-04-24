import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable, throwError, of } from 'rxjs';
import { map, catchError, shareReplay } from 'rxjs/operators';
import { API_CONFIG } from './api';

export interface DashboardStats {
  kpis: { total_revenus: number; membres_count: number; messages_attente: number; demandes_attente: number; };
  contacts: { non_lus: number; taux_reponse: number; demandes_en_attente: number; };
  membres: { total: number; actifs: number; taux_activite: number; nouveaux_periode: number; villes_detaillees: any[]; };
  finance: { total_revenus: number; total_dons: number; total_inscriptions: number; part_dons: number; part_inscriptions: number; top_donateurs: any[]; };
  projets: { total: number; en_cours: number; termines: number; taux_engagement: number; heures_benevolat: number; valorisation_sociale: number; };
  evenements: { total: number; a_venir: number; passes: number; taux_remplissage: number; };
  systeme: { partenaires: number; total_partenaires: number; telechargements: number; ressources_disponibles: number; faq_actives: number; };
  evolution: any[];
  meta?: { periode: string; generated_at: string; execution_time: number; };
}

@Injectable({
  providedIn: 'root'
})
export class StatsService {
  
  private readonly baseUrl = `${API_CONFIG.baseUrl}/admin/stats/dashboard`;
  private statsCache$: Observable<DashboardStats> | null = null;
  private lastPeriod: string = '';

  constructor(private http: HttpClient) { }

  getGlobalStats(period: string = '1year', forceRefresh: boolean = false): Observable<DashboardStats> {
    if (this.lastPeriod !== period) {
      forceRefresh = true;
      this.lastPeriod = period;
    }

    if (!this.statsCache$ || forceRefresh) {
      const params = new HttpParams().set('period', period);
      
      this.statsCache$ = this.http.get<any>(this.baseUrl, { params }).pipe(
        map(response => {
          // PROTECTION : On vérifie si les données sont dans 'data' ou à la racine
          const data = response.data ? response.data : response;
          
          // Vérification minimale que l'objet est valide
          if (data && data.membres && data.finance) {
            return data as DashboardStats;
          }
          throw new Error("Format de réponse invalide");
        }),
        shareReplay(1),
        catchError(err => {
          console.error('Erreur Service Stats:', err);
          this.statsCache$ = null;
          return throwError(() => err);
        })
      );
    }
    return this.statsCache$;
  }

  getFinancialHealth(): Observable<{status: string, message: string}> {
    return this.getGlobalStats().pipe(
      map(stats => {
        const rev = stats.finance?.total_revenus || 0;
        if (rev > 2000000) return { status: 'Excellent', message: 'La trésorerie est robuste.' };
        if (rev > 500000) return { status: 'Bon', message: 'Activité stable.' };
        return { status: 'Attention', message: 'Revenus en dessous des objectifs.' };
      }),
      catchError(() => of({ status: 'Indisponible', message: 'Erreur de calcul.' }))
    );
  }

  exportStatsToCSV(stats: DashboardStats): void {
    const rows = [
      ['Agora Cooperative - Rapport Statistique'],
      ['Date', stats.meta?.generated_at || new Date().toLocaleDateString()],
      ['Période', stats.meta?.periode || 'N/A'],
      [''],
      ['MODULE', 'INDICATEUR', 'VALEUR'],
      ['Finance', 'Revenus Totaux', `${stats.finance?.total_revenus || 0} FCFA`],
      ['Membres', 'Total', stats.membres?.total || 0],
      ['Système', 'Partenaires', stats.systeme?.partenaires || 0],
      ['Système', 'Téléchargements', stats.systeme?.telechargements || 0]
    ];

    const csvContent = "data:text/csv;charset=utf-8,\uFEFF" + rows.map(e => e.join(";")).join("\n");
    const link = document.createElement("a");
    link.href = encodeURI(csvContent);
    link.download = `Rapport_Agora_${new Date().getTime()}.csv`;
    link.click();
  }

  clearCache(): void {
    this.statsCache$ = null;
  }
}