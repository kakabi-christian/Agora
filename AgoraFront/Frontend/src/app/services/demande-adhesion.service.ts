import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { API_CONFIG } from './api';
import {
  DemandeAdhesion,
  DemandeAdhesionResponse,
  DemandeAdhesionListResponse
} from '../models/demande-adhesion.model';

@Injectable({
  providedIn: 'root'
})
export class DemandeAdhesionService {

  private readonly publicEndpoint = `${API_CONFIG.baseUrl}/demandes-adhesion`;
  private readonly adminEndpoint = `${API_CONFIG.baseUrl}/admin/demandes-adhesion`;

  constructor(private http: HttpClient) { }

  // =========================
  // ROUTES PUBLIQUES
  // =========================

  /**
 * Vérifie si un email a déjà une demande d'adhésion
 */
checkEmail(email: string): Observable<{ exists: boolean }> {
  return this.http.get<{ exists: boolean }>(
    `${this.publicEndpoint}/check-email?email=${encodeURIComponent(email)}`
  );
}


  /**
   * Envoyer une nouvelle demande d'adhésion
   */
  envoyerDemande(data: DemandeAdhesion): Observable<DemandeAdhesionResponse> {
    return this.http.post<DemandeAdhesionResponse>(this.publicEndpoint, data);
  }

  // =========================
  // ROUTES ADMIN
  // =========================

  /**
   * Récupérer toutes les demandes avec pagination et filtrage
   */
  getDemandes(statut?: string, page: number = 1): Observable<DemandeAdhesionListResponse> {
    let params = new HttpParams().set('page', page.toString());
    if (statut) params = params.set('statut', statut);

    return this.http.get<DemandeAdhesionListResponse>(this.adminEndpoint, {
      params

    });
  }

  /**
   * Voir les détails d'une demande spécifique
   */
  getDemandeById(id: number): Observable<DemandeAdhesionResponse> {
    return this.http.get<DemandeAdhesionResponse>(`${this.adminEndpoint}/${id}`);
  }

  /**
   * Approuver une demande
   */
  approuverDemande(id: number, commentaire?: string): Observable<DemandeAdhesionResponse> {
    const body = { commentaire_admin: commentaire };
    return this.http.post<DemandeAdhesionResponse>(`${this.adminEndpoint}/${id}/approve`, body);
  }

  /**
   * Rejeter une demande
   */
  rejeterDemande(id: number, commentaire: string): Observable<DemandeAdhesionResponse> {
    const body = { commentaire_admin: commentaire };
    return this.http.post<DemandeAdhesionResponse>(`${this.adminEndpoint}/${id}/reject`, body);
  }

  /**
   * Récupérer le compteur des demandes en attente
   */
  getPendingCount(): Observable<{pending_count: number}> {
    return this.http.get<{pending_count: number}>(`${this.adminEndpoint}/stats/count`);
  }
}
