import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { API_CONFIG } from './api';

@Injectable({
  providedIn: 'root'
})
export class HistoriqueService {
  private readonly apiUrl = `${API_CONFIG.baseUrl}/historique`;

  constructor(private http: HttpClient) {}

  getHistorique(filters?: { type_participation?: string; date_debut?: string; date_fin?: string }): Observable<any> {
    let params = new HttpParams();

    if (filters?.type_participation) params = params.set('type_participation', filters.type_participation);
    if (filters?.date_debut) params = params.set('date_debut', filters.date_debut);
    if (filters?.date_fin) params = params.set('date_fin', filters.date_fin);

    return this.http.get<any>(this.apiUrl, { params });
  }
}
