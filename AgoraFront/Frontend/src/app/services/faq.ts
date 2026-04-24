import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Faq, FaqPagination } from '../models/faq.model';
import { API_CONFIG } from './api';

@Injectable({
  providedIn: 'root'
})
export class FaqService {
  private apiUrl = API_CONFIG.baseUrl;

  constructor(private http: HttpClient) {}

  // =========================
  // ROUTES PUBLIQUES
  // =========================

  /**
   * Récupérer les FAQs paginées (Public)
   */
  getFaqs(page: number = 1, perPage: number = 10): Observable<FaqPagination> {
    const params = new HttpParams()
      .set('page', page.toString())
      .set('per_page', perPage.toString());

    return this.http.get<FaqPagination>(`${this.apiUrl}/faqs`, {
      params
    });
  }

  /**
   * Voir une FAQ spécifique (Public)
   */
  getFaqById(id: number): Observable<Faq> {
    return this.http.get<Faq>(`${this.apiUrl}/faqs/${id}`);
  }

  /**
   * Voter pour une FAQ (Authentifié)
   */
  voteFaq(id: number, type: 'utile' | 'inutile'): Observable<any> {
    return this.http.post(`${this.apiUrl}/faqs/${id}/vote`, { vote: type });
  }

  // =========================
  // ROUTES ADMIN
  // =========================

  /**
   * Créer une FAQ (Admin)
   */
  createFaq(faq: Faq): Observable<any> {
    return this.http.post(`${this.apiUrl}/admin/faqs`, faq);
  }

  /**
   * Mettre à jour une FAQ (Admin)
   */
  updateFaq(id: number, faq: Faq): Observable<any> {
    return this.http.put(`${this.apiUrl}/admin/faqs/${id}`, faq);
  }

  /**
   * Supprimer une FAQ (Admin)
   */
  deleteFaq(id: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}/admin/faqs/${id}`);
  }
}
