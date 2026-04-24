import { Injectable } from '@angular/core';
import { HttpClient, HttpParams, HttpResponse } from '@angular/common/http';
import { Observable } from 'rxjs';
import { API_CONFIG } from './api';

export interface Ressource {
  id: number;
  titre: string;
  type: string;
  categorie: string;
  description?: string;
  nom_fichier: string;
  extension_fichier: string;
  date_publication: string;
  date_expiration?: string;
  est_public: boolean;
  necessite_authentification: boolean;
  nombre_telechargements: number;
  uploader?: {
    code_membre: string;
    nom: string;
    prenom: string;
  };
}

export interface PaginatedRessources {
  data: Ressource[];
  links: any;
  meta: any;
}

@Injectable({
  providedIn: 'root'
})
export class RessourceService {
  private readonly apiUrl = `${API_CONFIG.baseUrl}/ressources`;
  private readonly adminApiUrl = `${API_CONFIG.baseUrl}/admin/ressources`;

  constructor(private http: HttpClient) {}

  getRessources(filters?: { categorie?: string; type?: string; search?: string }): Observable<PaginatedRessources> {
    let params = new HttpParams();

    if (filters?.categorie) params = params.set('categorie', filters.categorie);
    if (filters?.type) params = params.set('type', filters.type);
    if (filters?.search) params = params.set('search', filters.search);

    return this.http.get<PaginatedRessources>(this.apiUrl, { params });
  }

  downloadRessource(id: number): Observable<HttpResponse<Blob>> {
    return this.http.get(`${this.apiUrl}/${id}/download`, {
      observe: 'response',
      responseType: 'blob'
    });
  }

  /**
   * Upload une nouvelle ressource (Admin uniquement)
   */
  uploadRessource(formData: FormData): Observable<any> {
    return this.http.post(this.adminApiUrl, formData);
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
}
