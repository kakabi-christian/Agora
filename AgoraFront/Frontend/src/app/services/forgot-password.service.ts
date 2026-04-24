import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { API_CONFIG } from './api';
import { OtpRequest, OtpVerify, PasswordReset, ApiResponse } from '../models/otp.model';

@Injectable({
  providedIn: 'root'
})
export class AuthForgotPasswordService {
  // Utilisation de ton baseUrl défini dans api.ts
  private apiUrl = API_CONFIG.baseUrl;

  constructor(private http: HttpClient) {}

  /**
   * ÉTAPE 1 : Envoyer l'email pour générer et recevoir l'OTP
   * Route Backend: POST /api/password/forgot
   */
  sendOtp(data: OtpRequest): Observable<ApiResponse> {
    return this.http.post<ApiResponse>(`${this.apiUrl}/password/forgot`, data);
  }

  /**
   * ÉTAPE 2 : Vérifier la validité du code OTP saisi par l'utilisateur
   * Route Backend: POST /api/password/verify-otp
   */
  verifyOtp(data: OtpVerify): Observable<ApiResponse> {
    return this.http.post<ApiResponse>(`${this.apiUrl}/password/verify-otp`, data);
  }

  /**
   * ÉTAPE 3 : Définir le nouveau mot de passe
   * Route Backend: POST /api/password/reset
   */
  resetPassword(data: PasswordReset): Observable<ApiResponse> {
    return this.http.post<ApiResponse>(`${this.apiUrl}/password/reset`, data);
  }
}