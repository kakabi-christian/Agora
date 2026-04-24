import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { AuthService } from '../../../services/auth.service';
import { Membre } from '../../../models/membre.model';

@Component({
  selector: 'app-profil',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './profil.html',
  styleUrl: './profil.css',
})
export class Profil implements OnInit {
  user: Membre | null = null;
  loading: boolean = true;

  constructor(private authService: AuthService) {}

  ngOnInit(): void {
    this.chargerInfosProfil();
  }

  chargerInfosProfil(): void {
    this.loading = true;
    // On appelle l'API /auth/me via ton service
    this.authService.getCurrentUser().subscribe({
      next: (data) => {
        this.user = data;
        this.loading = false;
      },
      error: (err) => {
        console.error('Erreur lors du chargement du profil :', err);
        this.loading = false;
        // Optionnel : rediriger vers login si le token est expiré ou invalide
      }
    });
  }
}
// src/app/pages/admin/profil/profil.ts