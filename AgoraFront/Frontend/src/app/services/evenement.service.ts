import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Evenement, EvenementResponse, UpcomingEventsResponse } from '../models/evenement.model';
import { API_CONFIG } from './api';

@Injectable({
  providedIn: 'root'
})
export class EvenementService {
  private apiUrl = `${API_CONFIG.baseUrl}`;

  constructor(private http: HttpClient) {}

  /**
   * Récupère les événements à venir (Dashboard)
   */
  getUpcomingEvents(): Observable<UpcomingEventsResponse> {
    return this.http.get<UpcomingEventsResponse>(`${this.apiUrl}/evenements/upcoming`);
  }

  /**
   * Récupérer la liste paginée des événements
   */
  getEvenements(page: number = 1): Observable<EvenementResponse> {
    return this.http.get<EvenementResponse>(`${this.apiUrl}/evenements?page=${page}`);
  }

  /**
   * Détails d'un événement par son code unique
   */
  getEvenementByCode(code: string): Observable<{message: string, evenement: Evenement}> {
    return this.http.get<{message: string, evenement: Evenement}>(`${this.apiUrl}/evenements/${code}`);
  }

  inscrire(code: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/evenements/${code}/inscription`, {});
  }

  annulerInscription(code: string): Observable<any> {
    return this.http.delete(`${this.apiUrl}/evenements/${code}/inscription`);
  }

  getStatutInscription(code: string): Observable<any> {
    return this.http.get(`${this.apiUrl}/evenements/${code}/inscription/statut`);
  }

  getMesInscriptions(): Observable<any> {
    return this.http.get(`${this.apiUrl}/mes-inscriptions`);
  }

  telechargerConfirmationPdf(code: string): Observable<Blob> {
    return this.http.get(`${this.apiUrl}/evenements/${code}/inscription/pdf`, {
      responseType: 'blob'
    });
  }

  /**
   * Créer un événement (Admin)
   */
  createEvenement(evenementData: any): Observable<any> {
    if (evenementData instanceof FormData) {
      return this.http.post(`${this.apiUrl}/admin/evenements`, evenementData);
    }
    const formData = this.convertToFormData(evenementData);
    return this.http.post(`${this.apiUrl}/admin/evenements`, formData);
  }

  /**
   * Mettre à jour un événement (Admin)
   * Note: On utilise POST avec un fallback pour le fichier image
   */
  updateEvenement(code: string, evenementData: any): Observable<any> {
    let body = evenementData;
    
    // Si on envoie une image, on doit utiliser FormData
    // Laravel traite mieux les fichiers en POST qu'en PUT avec FormData
    if (evenementData instanceof FormData) {
      // On simule le PUT pour Laravel tout en restant en POST (Multipart)
      evenementData.append('_method', 'PUT');
      return this.http.post(`${this.apiUrl}/admin/evenements/${code}`, evenementData);
    }
    
    return this.http.put(`${this.apiUrl}/admin/evenements/${code}`, body);
  }

  /**
   * Supprimer un événement (Admin)
   */
  deleteEvenement(code: string): Observable<any> {
    // Changé en /admin/evenements/ car la route est dans le groupe admin
    return this.http.delete(`${this.apiUrl}/admin/evenements/${code}`);
  }

  /**
   * Helper pour convertir un objet simple en FormData (Fallback)
   */
  private convertToFormData(data: any): FormData {
    const formData = new FormData();
    Object.keys(data).forEach(key => {
      const value = data[key];
      if (key === 'image' && value instanceof File) {
        formData.append('image', value);
      } else if (value !== null && value !== undefined) {
        formData.append(key, value);
      }
    });
    return formData;
  }
}