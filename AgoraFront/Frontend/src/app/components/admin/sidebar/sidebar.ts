import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, Router } from '@angular/router';
import { DemandeAdhesionService } from '../../../services/demande-adhesion.service';
import { ContactService } from '../../../services/contact.service';
import { AuthService } from '../../../services/auth.service';
import { NotificationService } from '../../../services/notification.service'; // Import ajouté

@Component({
  selector: 'app-sidebar',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './sidebar.html',
  styleUrl: './sidebar.css',
})
export class Sidebar implements OnInit {
  pendingCount: number = 0;        // Compteur adhésions
  unreadContactCount: number = 0;  // Compteur messages contact (Badge rouge)
  unreadNotifCount: number = 0;    // Compteur notifications (Badge jaune/orange)

  showLogoutModal: boolean = false;
  isLoggingOut: boolean = false;

  constructor(
    private demandeService: DemandeAdhesionService,
    private contactService: ContactService,
    private authService: AuthService,
    private notificationService: NotificationService, // Injecté
    private router: Router
  ) {}

  ngOnInit(): void {
    this.refreshCounters();
  }

  /**
   * Rafraîchit tous les compteurs de la sidebar (Adhésions, Contacts, Notifications)
   */
  refreshCounters() {
    // 1. Compteur des demandes d'adhésion
    this.demandeService.getPendingCount().subscribe({
      next: (data) => this.pendingCount = data.pending_count,
      error: (err) => console.error('Erreur compteur adhésion:', err)
    });

    // 2. Compteur des messages de contact non lus
    this.contactService.getUnreadCount().subscribe({
      next: (data) => this.unreadContactCount = data.unread_count,
      error: (err) => console.error('Erreur compteur contact:', err)
    });

    // 3. Compteur des notifications non lues (Appel à ton nouveau service)
    this.notificationService.getUnreadCount().subscribe({
      next: (data) => this.unreadNotifCount = data.unread_count,
      error: (err) => console.error('Erreur compteur notification:', err)
    });
  }

  /**
   * Action pour marquer tous les messages comme lus
   */
  markAllContactsAsRead() {
    if (this.unreadContactCount > 0) {
      this.contactService.markAllAsRead().subscribe({
        next: () => {
          this.unreadContactCount = 0;
        },
        error: (err) => console.error('Erreur mark as read:', err)
      });
    }
  }

  // --- Fonctions de déconnexion ---

  ouvrirModalDeconnexion() {
    this.showLogoutModal = true;
  }

  fermerModal() {
    this.showLogoutModal = false;
  }

  confirmerDeconnexion() {
    this.isLoggingOut = true;
    this.authService.logout().subscribe({
      next: () => {
        this.showLogoutModal = false;
        this.router.navigate(['/login']);
      },
      error: (err) => {
        console.error('Erreur logout:', err);
        this.router.navigate(['/login']);
      }
    });
  }
}
