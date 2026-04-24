// src/app/services/dashboard.service.ts
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { API_CONFIG } from './api';

export interface MembreStats {
  total: number;
  nouveaux_mois: number;
}

export interface ProjetRecent {
  id: number;
  nom: string;
  description: string;
  type: string;
  statut: string;
  image_url: string | null;
  participants_count: number;
}

export interface ProjetStats {
  total: number;
  en_cours: number;
  recents: ProjetRecent[];
}

export interface EvenementProchain {
  code_evenement: string;
  titre: string;
  description: string;
  date_debut: string;
  date_fin: string;
  lieu: string;
  ville: string;
  type: string;
  image_url: string | null;
  places_disponibles: number | null;
  inscrits_count: number;
}

export interface EvenementStats {
  total_annee: number;
  a_venir: number;
  prochains: EvenementProchain[];
}

export interface DonStats {
  total_montant: number;
  nombre_donateurs: number;
  projets_finances: number;
  annee: number;
}

export interface DashboardStats {
  membres: MembreStats;
  projets: ProjetStats;
  evenements: EvenementStats;
  dons: DonStats;
}

export interface Partenaire {
  code_partenaire: string;
  nom: string;
  logo_url: string | null;
  site_web: string | null;
  type: string;
  niveau_partenariat: string;
}

@Injectable({
  providedIn: 'root'
})
export class DashboardService {
  private apiUrl = API_CONFIG.baseUrl;

  constructor(private http: HttpClient) {}

  /**
   * Récupère toutes les statistiques du dashboard
   */
  getHomeStats(): Observable<{ message: string; stats: DashboardStats }> {
    return this.http.get<{ message: string; stats: DashboardStats }>(
      `${this.apiUrl}/dashboard/stats`
    );
  }

  /**
   * Récupère les partenaires actifs
   */
  getPartenairesActifs(): Observable<{ message: string; partenaires: Partenaire[] }> {
    return this.http.get<{ message: string; partenaires: Partenaire[] }>(
      `${this.apiUrl}/dashboard/partenaires`
    );
  }

  /**
   * Retourne l'URL complète d'une image
   */
  getImageUrl(path: string | null): string {
    if (!path) {
      return 'https://images.unsplash.com/photo-1557683316-973673baf926?w=800&h=600&fit=crop';
    }
    
    // Si c'est déjà une URL complète
    if (path.startsWith('http')) {
      return path;
    }
    
    // Construire l'URL complète
    return `${API_CONFIG.storageUrl.replace('/storage', '')}/${path}`;
  }
}