import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router, RouterModule } from '@angular/router';
import { AuthService } from '../../../services/auth.service';

@Component({
  selector: 'app-member-layout',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './member-layout.html',
  styleUrl: './member-layout.css'
})
export class MemberLayout implements OnInit, OnDestroy {
  showLogoutModal: boolean = false;
  isLoggingOut: boolean = false;

  constructor(
    private authService: AuthService,
    private router: Router
  ) {}

  ngOnInit(): void {
    // Initialisation si nécessaire
  }

  ngOnDestroy(): void {
    // Nettoyage si nécessaire
  }

  /**
   * Ouvre la modal de confirmation de déconnexion
   */
  openLogoutModal(): void {
    this.showLogoutModal = true;
  }

  /**
   * Ferme la modal de déconnexion
   */
  closeLogoutModal(): void {
    if (!this.isLoggingOut) {
      this.showLogoutModal = false;
    }
  }

  /**
   * Confirme et exécute la déconnexion
   */
  confirmLogout(): void {
    this.isLoggingOut = true;

    this.authService.logout().subscribe({
      next: () => {
        this.showLogoutModal = false;
        this.isLoggingOut = false;
        this.router.navigateByUrl('/login');
      },
      error: (err) => {
        console.error('Erreur lors de la déconnexion:', err);
        this.isLoggingOut = false;
        // Même en cas d'erreur, rediriger vers login
        this.router.navigateByUrl('/login');
      }
    });
  }

  /**
   * Ancienne méthode logout (conservée pour compatibilité)
   * @deprecated Utiliser openLogoutModal() à la place
   */
  logout(): void {
    this.openLogoutModal();
  }
}
