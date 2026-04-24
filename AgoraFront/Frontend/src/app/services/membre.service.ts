import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { API_CONFIG } from './api';
import { Membre } from '../models/membre.model';

export type UpdateMembrePayload = Partial<Pick<Membre, 'telephone' | 'adresse' | 'ville' | 'code_postal' | 'biographie'>>;

export interface PaginatedMembres {
  data: Membre[];
  links: {
    first: string;
    last: string;
    prev: string | null;
    next: string | null;
  };
  meta: {
    current_page: number;
    from: number;
    last_page: number;
    per_page: number;
    to: number;
    total: number;
  };
}

@Injectable({
  providedIn: 'root'
})
export class MembreService {
  private readonly apiUrl = `${API_CONFIG.baseUrl}/membres`;
  private readonly adminApiUrl = `${API_CONFIG.baseUrl}/admin/membres`;

  constructor(private http: HttpClient) {}

  getMembre(codeMembre: string): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/${codeMembre}`);
  }

  updateMembre(codeMembre: string, payload: UpdateMembrePayload): Observable<any> {
    return this.http.put<any>(`${this.apiUrl}/${codeMembre}`, payload);
  }

  /**
   * Récupérer la liste paginée des membres (Admin uniquement)
   */
  getMembres(params?: {
    statut?: 'actif' | 'inactif';
    role?: 'administrateur' | 'membre';
    search?: string;
    page?: number;
  }): Observable<PaginatedMembres> {
    let httpParams = new HttpParams();
    
    if (params?.statut) {
      httpParams = httpParams.set('statut', params.statut);
    }
    if (params?.role) {
      httpParams = httpParams.set('role', params.role);
    }
    if (params?.search) {
      httpParams = httpParams.set('search', params.search);
    }
    if (params?.page) {
      httpParams = httpParams.set('page', params.page.toString());
    }

    return this.http.get<PaginatedMembres>(this.adminApiUrl, { params: httpParams });
  }

  /**
   * Exporter la liste des membres en Excel
   */
  exportMembresExcel(): Observable<Blob> {
    return this.http.get(`${this.adminApiUrl}/export/excel`, {
      responseType: 'blob',
      observe: 'body'
    });
  }

  /**
   * Exporter la liste des membres en PDF
   */
  exportMembresPdf(): Observable<Blob> {
    return this.http.get(`${this.adminApiUrl}/export/pdf`, {
      responseType: 'blob',
      observe: 'body'
    });
  }

  /**
   * Télécharger un fichier blob
   */
  downloadFile(blob: Blob, filename: string): void {
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);
  }

  /**
   * Générer un nom de fichier avec timestamp
   */
  generateFilename(prefix: string, extension: string): string {
    const date = new Date();
    const timestamp = date.toISOString().split('T')[0];
    const time = date.toTimeString().split(' ')[0].replace(/:/g, '-');
    return `${prefix}-${timestamp}-${time}.${extension}`;
  }
}
