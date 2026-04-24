import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { API_CONFIG } from './api';
import { Projet, ProjetResponse } from '../models/projet.model';

@Injectable({
  providedIn: 'root'
})
export class ProjetService {
  private apiUrl = `${API_CONFIG.baseUrl}/projets`;
  private adminApiUrl = `${API_CONFIG.baseUrl}/admin/projets`;
  private participationUrl = `${API_CONFIG.baseUrl}`;

  constructor(private http: HttpClient) { }

  /**
   * Récupère la liste paginée des projets
   * @param page numéro de la page (défaut 1)
   * @returns Observable<ProjetResponse>
   */
  getProjets(page: number = 1): Observable<ProjetResponse> {
    const params = new HttpParams().set('page', page.toString());

    return this.http.get<ProjetResponse>(this.apiUrl, {
      params
    });
  }

  /**
   * Récupère un projet par son ID
   */
  getProjetById(id: number): Observable<{ projet: Projet }> {
    return this.http.get<{ projet: Projet }>(`${this.apiUrl}/${id}`);
  }

  /**
   * Crée un nouveau projet (FormData pour image) - ADMIN ONLY
   */
  createProjet(formData: FormData): Observable<any> {
    return this.http.post(this.adminApiUrl, formData);
  }

  /**
   * Met à jour un projet - ADMIN ONLY
   * Trick: Laravel supporte parfois mieux POST + _method = PUT pour FormData
   */
  updateProjet(id: number, formData: FormData): Observable<any> {
    formData.append('_method', 'PUT');

    return this.http.post(`${this.adminApiUrl}/${id}`, formData);
  }

  /**
   * Supprime un projet - ADMIN ONLY
   */
  deleteProjet(id: number): Observable<any> {
    return this.http.delete(`${this.adminApiUrl}/${id}`);
  }

  /**
   * Récupère les deadlines des projets (pour le dashboard)
   */
  getDeadlines(): Observable<any> {
    return this.http.get<any>(`${API_CONFIG.baseUrl}/projets/deadlines`);
  }

  participer(projetId: number): Observable<any> {
    return this.http.post(`${this.participationUrl}/projets/${projetId}/participer`, {});
  }

  quitter(projetId: number): Observable<any> {
    return this.http.delete(`${this.participationUrl}/projets/${projetId}/participer`);
  }

  mesParticipations(): Observable<any> {
    return this.http.get(`${this.participationUrl}/mes-participations-projets`);
  }

  logHeures(projetId: number, payload: { heures: number; date?: string; description?: string }): Observable<any> {
    return this.http.post(`${this.participationUrl}/projets/${projetId}/heures`, payload);
  }
}
