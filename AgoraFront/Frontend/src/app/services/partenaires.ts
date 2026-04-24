import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { PartenaireResponse, Partenaire } from '../models/partenaire.model';
import { API_CONFIG } from './api';

@Injectable({
  providedIn: 'root'
})
export class PartenaireService {
  private apiUrl = `${API_CONFIG.baseUrl}/partenaires`;

  constructor(private http: HttpClient) { }

  /**
   * GET: Récupérer la liste paginée
   */
  getPartenaires(): Observable<PartenaireResponse> {
    return this.http.get<PartenaireResponse>(this.apiUrl);
  }

  /**
   * GET: Détails d'un partenaire par son code
   */
  getPartenaireByCode(code: string): Observable<{message: string, partenaire: Partenaire}> {
    return this.http.get<{message: string, partenaire: Partenaire}>(`${this.apiUrl}/${code}`);
  }

  /**
   * POST: Créer un nouveau partenaire
   * On utilise FormData car le contrôleur Laravel attend un fichier 'logo'
   */
  createPartenaire(formData: FormData): Observable<{message: string, partenaire: Partenaire}> {
    return this.http.post<{message: string, partenaire: Partenaire}>(this.apiUrl, formData);
  }

  /**
   * PUT: Mettre à jour un partenaire
   * Note: Laravel a parfois du mal avec PUT et les fichiers (Multipart). 
   * Si tu as des soucis, on utilisera POST avec le champ _method = 'PUT'.
   */
  updatePartenaire(code: string, formData: FormData): Observable<{message: string, partenaire: Partenaire}> {
    return this.http.post<{message: string, partenaire: Partenaire}>(`${this.apiUrl}/${code}`, formData);
  }

  /**
   * DELETE: Supprimer un partenaire
   */
  deletePartenaire(code: string): Observable<{message: string}> {
    return this.http.delete<{message: string}>(`${this.apiUrl}/${code}`);
  }
}