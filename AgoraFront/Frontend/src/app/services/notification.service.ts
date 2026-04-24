import { API_CONFIG } from './api'; // Config centralisée
import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Notification, UnreadCountResponse } from '../models/notification.model';

@Injectable({
  providedIn: 'root'
})
export class NotificationService {
  
  /**
   * L'URL est construite selon ton prefix('admin') dans api.php
   * Résultat : http://localhost:8000/api/admin
   */
  private readonly apiUrl = `${API_CONFIG.baseUrl}/admin`;

  constructor(private http: HttpClient) { }

  /**
   * Route: GET /api/admin
   * Liste toutes les notifications (paginées par 20 côté Laravel)
   */
  getNotifications(page: number = 1): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}?page=${page}`);
  }

  /**
   * Route: GET /api/admin/unread-count
   * Récupère le nombre de notifications non lues
   */
  getUnreadCount(codeMembre?: string): Observable<UnreadCountResponse> {
    let params = new HttpParams();
    if (codeMembre) {
      params = params.append('code_membre', codeMembre);
    }
    return this.http.get<UnreadCountResponse>(`${this.apiUrl}/unread-count`, { params });
  }

  /**
   * NOUVEAU - Route: GET /api/admin/membres-list
   * Récupère la liste simplifiée des membres (Nom, Prenom, Code, Email)
   * pour faciliter la sélection dans le formulaire d'envoi.
   */
  getMembresList(): Observable<any[]> {
    return this.http.get<any[]>(`${this.apiUrl}/membres-list`);
  }

  /**
   * Route: POST /api/admin
   * Créer une notification (Ciblée ou "pour_tous")
   */
  createNotification(data: any): Observable<any> {
    return this.http.post<any>(this.apiUrl, data);
  }

  /**
   * Route: PATCH /api/admin/{id}/read
   * Marquer une notification précise comme lue
   */
  markAsRead(id: number): Observable<any> {
    return this.http.patch<any>(`${this.apiUrl}/${id}/read`, {});
  }

  /**
   * Route: POST /api/admin/mark-all-read
   * Marquer tout comme lu pour un membre spécifique
   */
  markAllAsRead(codeMembre: string): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/mark-all-read`, { code_membre: codeMembre });
  }

  /**
   * Route: DELETE /api/admin/{notification}
   * Supprimer définitivement une notification
   */
  deleteNotification(id: number): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}/${id}`);
  }
}