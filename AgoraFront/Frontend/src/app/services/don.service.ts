import { Injectable } from '@angular/core';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { Observable, throwError } from 'rxjs';
import { catchError, tap } from 'rxjs/operators';
import { Don, CampayResponse } from '../models/don.model';
import { API_CONFIG } from './api';

@Injectable({
  providedIn: 'root'
})
export class DonService {

  private readonly apiUrl = `${API_CONFIG.baseUrl}`;

  constructor(private http: HttpClient) { }

  /**
   * Envoyer un nouveau don
   */
  initierDon(don: Don): Observable<CampayResponse> {
    console.log('[DonService] Tentative d\'initiation de don:', don);

    return this.http.post<CampayResponse>(`${this.apiUrl}/dons`, don).pipe(
      tap(response => console.log('[DonService] ✅ Réponse reçue:', response)),
      catchError(this.handleError)
    );
  }

  /**
   * Effectuer un retrait vers l'admin (Route protégée Admin)
   * MISE À JOUR : Ajout du paramètre password
   */
  retraitAdmin(amount: number, password: string): Observable<any> {
    // On envoie le montant ET le password dans le corps de la requête
    return this.http.post(`${this.apiUrl}/admin/payout`, { amount, password }).pipe(
      tap(res => console.log('[DonService] Retrait admin initié:', res)),
      catchError(this.handleError)
    );
  }

  /**
   * Récupérer la liste des dons (Route Admin)
   */
  getTousLesDons(): Observable<Don[]> {
    return this.http.get<Don[]>(`${this.apiUrl}/admin/dons`).pipe(
      catchError(this.handleError)
    );
  }

  /**
   * Gestion centralisée des erreurs HTTP
   */
  private handleError(error: HttpErrorResponse) {
    let errorMessage = 'Une erreur inconnue est survenue.';

    if (error.error instanceof ErrorEvent) {
      errorMessage = `Erreur : ${error.error.message}`;
    } else {
      // Important : on récupère le message renvoyé par Laravel (ex: "Mot de passe incorrect")
      errorMessage = error.error?.message || `Code erreur : ${error.status}`;
      console.error('[DonService] Erreur Serveur:', error.error);
    }

    return throwError(() => new Error(errorMessage));
  }

  /**
   * Récupérer le montant total de tous les dons cumulés (Déchiffrés)
   * Route: GET /dons/total-general
   */
  getTotalGeneral(): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/admin/dons/total-general`).pipe(
      tap(res => {
        if (res.status === 'success') {
          console.log(`[DonService] ✅ Revenu total: ${res.total_general} ${res.devise}`);
          console.log(`[DonService] ⏱️ Temps calcul backend: ${res.meta.execution_time}s`);
        }
      }),
      catchError(this.handleError)
    );
  }
}