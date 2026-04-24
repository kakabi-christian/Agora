import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, tap, map } from 'rxjs';
import { API_CONFIG } from './api';
import { Membre } from '../models/membre.model';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private readonly AUTH_KEY = 'auth_token';
  private readonly USER_KEY = 'user_data';

  constructor(private http: HttpClient) { }

  /**
   * Connexion au compte
   */
  login(credentials: { email: string; mot_de_passe: string }): Observable<any> {
    return this.http.post<any>(
      `${API_CONFIG.baseUrl}/auth/login`,
      credentials,
      {
        // headers/Accept gérés par l'interceptor
      }
    ).pipe(
      tap(response => {
        const authData = response.data;
        if (authData && authData.token && authData.membre) {
          this.saveSession(authData.token, authData.membre);
        }
      })
    );
  }

  /**
   * Changement de mot de passe
   */
  changePassword(payload: { ancien_mot_de_passe: string; nouveau_mot_de_passe: string; nouveau_mot_de_passe_confirmation: string }): Observable<any> {
    return this.http.post<any>(
      `${API_CONFIG.baseUrl}/auth/change-password`,
      payload,
      {}
    );
  }

  /**
   * Récupère l'utilisateur connecté
   */
  getCurrentUser(): Observable<Membre> {
    return this.http.get<any>(
      `${API_CONFIG.baseUrl}/auth/me`,
      {}
    ).pipe(
      map(response => response.data || response)
    );
  }

  /**
   * Déconnexion
   */
  logout(): Observable<void> {
    return this.http.post<void>(
      `${API_CONFIG.baseUrl}/auth/logout`,
      {},
      {}
    ).pipe(
      tap({
        next: () => this.clearSession(),
        error: () => this.clearSession()
      })
    );
  }

  /**
   * Récupère le rôle de l'utilisateur
   */
  getUserRole(): string {
    const user = this.getUserSync();
    return user ? user.role : 'membre';
  }

  isAdmin(): boolean {
    return this.getUserRole() === 'administrateur';
  }

  // --- LocalStorage ---
  private saveSession(token: string, membre: Membre): void {
    localStorage.setItem(this.AUTH_KEY, token);
    localStorage.setItem(this.USER_KEY, JSON.stringify(membre));
  }

  private clearSession(): void {
    localStorage.removeItem(this.AUTH_KEY);
    localStorage.removeItem(this.USER_KEY);
  }

  getToken(): string | null {
    return localStorage.getItem(this.AUTH_KEY);
  }

  getUserSync(): Membre | null {
    const data = localStorage.getItem(this.USER_KEY);
    return data ? JSON.parse(data) : null;
  }

  isLoggedIn(): boolean {
    return !!this.getToken();
  }
}
