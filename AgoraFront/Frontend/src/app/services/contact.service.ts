import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { API_CONFIG } from './api';
import { Contact } from '../models/contact.model';

@Injectable({
  providedIn: 'root'
})
export class ContactService {

  private publicUrl = `${API_CONFIG.baseUrl}/contacts`;
  private adminUrl = `${API_CONFIG.baseUrl}/admin/contacts`;

  constructor(private http: HttpClient) { }

  // --- PUBLIC ---
  sendContactMessage(data: Contact): Observable<any> {
    return this.http.post<any>(this.publicUrl, data);
  }

  // --- ADMIN ---
  getMessages(page: number = 1): Observable<any> {
    const params = new HttpParams().set('page', page.toString());
    return this.http.get<any>(this.adminUrl, { params });
  }

  getMessageById(id: number): Observable<Contact> {
    return this.http.get<Contact>(`${this.adminUrl}/${id}`);
  }

  replyToMessage(id: number, reponse: string): Observable<any> {
    return this.http.put<any>(`${this.adminUrl}/${id}`, { reponse });
  }

  getUnreadCount(): Observable<{ unread_count: number }> {
    return this.http.get<{ unread_count: number }>(`${this.adminUrl}/unread-count`);
  }

  markAllAsRead(): Observable<any> {
    return this.http.post<any>(`${this.adminUrl}/mark-as-read`, {});
  }

  // Cette méthode est générique
  updateMessage(id: number, data: Partial<Contact>): Observable<Contact> {
    return this.http.put<Contact>(`${this.adminUrl}/${id}`, data);
  }

  /**
   * AJOUT : Méthode spécifique pour le statut (Lu/Non lu)
   * Cela résout l'erreur .ts(updateStatus)
   */
  updateStatus(id: number, data: { is_read: boolean }): Observable<any> {
    return this.http.put<any>(`${this.adminUrl}/${id}`, data);
  }

  deleteMessage(id: number): Observable<any> {
    return this.http.delete<any>(`${this.adminUrl}/${id}`);
  }
}