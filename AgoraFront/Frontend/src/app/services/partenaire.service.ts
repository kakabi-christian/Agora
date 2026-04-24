import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { PartenaireResponse, Partenaire } from '../models/partenaire.model';
import { API_CONFIG } from './api'; // Config centralisée

@Injectable({
  providedIn: 'root'
})
export class PartenaireService {
  // Base URL de l'API
  private apiUrl = API_CONFIG.baseUrl; 

  constructor(private http: HttpClient) {}

  /**
   * =========================
   * ROUTES PUBLIQUES
   * =========================
   */

  /**
   * Récupère la liste paginée des partenaires
   * @param page numéro de la page
   */
  getPartenaires(page: number = 1): Observable<PartenaireResponse> {
    const params = new HttpParams().set('page', page.toString());

    return this.http.get<PartenaireResponse>(`${this.apiUrl}/partenaires`, {
      params
    });
  }

  /**
   * Récupère un partenaire par son code
   */
  getPartenaireByCode(code: string): Observable<{ partenaire: Partenaire }> {
    return this.http.get<{ partenaire: Partenaire }>(`${this.apiUrl}/partenaires/${code}`);
  }

  /**
   * =========================
   * ROUTES ADMIN
   * =========================
   */

  /**
   * Crée un partenaire avec FormData (ex. logo)
   */
  createPartenaire(formData: FormData): Observable<any> {
    return this.http.post(`${this.apiUrl}/admin/partenaires`, formData);
  }

  /**
   * Met à jour un partenaire
   * Note: on utilise POST + _method=PUT pour Laravel quand FormData est utilisé
   */
  updatePartenaire(code: string, formData: FormData): Observable<any> {
    formData.append('_method', 'PUT'); // Trick Laravel
    return this.http.post(`${this.apiUrl}/admin/partenaires/${code}`, formData);
  }

  /**
   * Supprime un partenaire
   */
  deletePartenaire(code: string): Observable<any> {
    return this.http.delete(`${this.apiUrl}/admin/partenaires/${code}`);
  }
}
