import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms'; 
import { Notification as NotificationModel } from '../../../models/notification.model';
import { NotificationService } from '../../../services/notification.service';
import { AuthService } from '../../../services/auth.service';

@Component({
  selector: 'app-notification',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './notification.html',
  styleUrl: './notification.css',
})
export class Notification implements OnInit {
  notifications: NotificationModel[] = [];
  membres: any[] = []; // Liste des membres pour le sélecteur
  loading: boolean = false;

  // Objet pour le formulaire de création
  newNotification = {
    pour_tous: true,
    code_membre: '',
    titre: '',
    contenu: '',
    type: 'alerte_site',
    categorie: 'systeme',
    est_urgent: false
  };

  constructor(private notificationService: NotificationService,
    private authService: AuthService
  ) {}

  ngOnInit(): void {
    this.loadNotifications();
    this.loadMembres(); // On charge les membres dès l'initialisation
  }

  /**
   * Charge la liste des membres (Route: GET /api/admin/membres-list)
   */
loadMembres(): void {
  this.notificationService.getMembresList().subscribe({
    next: (data) => {
      // 1. On récupère les infos de l'admin connecté de façon synchrone
      const currentUser = this.authService.getUserSync();
      
      // 2. On filtre la liste pour exclure l'admin actuel via son code_membre
      if (currentUser && currentUser.code_membre) {
        this.membres = data.filter(m => m.code_membre !== currentUser.code_membre);
      } else {
        this.membres = data;
      }
    },
    error: (err) => console.error('Erreur lors du chargement des membres:', err)
  });
}

  /**
   * Charge la liste des notifications (Route: GET /api/admin)
   */
  loadNotifications(): void {
    this.loading = true;
    this.notificationService.getNotifications().subscribe({
      next: (res: any) => {
        // Laravel paginate(20) renvoie les données dans 'data'
        this.notifications = res.data;
        this.loading = false;
      },
      error: (err) => {
        console.error('Erreur de chargement:', err);
        this.loading = false;
      }
    });
  }

  /**
   * Envoi d'une nouvelle notification (Route: POST /api/admin)
   */
  envoyerNotification(): void {
    // Petite validation de sécurité si ce n'est pas "pour tous"
    if (!this.newNotification.pour_tous && !this.newNotification.code_membre) {
      alert('Veuillez sélectionner un membre destinataire.');
      return;
    }

    this.notificationService.createNotification(this.newNotification).subscribe({
      next: () => {
        alert('Notification envoyée avec succès !');
        this.loadNotifications(); 
        this.resetForm();
      },
      error: (err) => console.error('Erreur lors de l\'envoi:', err)
    });
  }

  /**
   * Marquer comme lu (Route: PATCH /api/admin/{id}/read)
   */
  marquerCommeLue(id: number | undefined): void {
    if (!id) return;
    this.notificationService.markAsRead(id).subscribe(() => {
      const notif = this.notifications.find(n => n.id === id);
      if (notif) {
        notif.statut = 'lu';
        notif.date_lecture = new Date().toISOString();
      }
    });
  }

  /**
   * Supprimer (Route: DELETE /api/admin/{id})
   */
  supprimer(id: number | undefined): void {
    if (!id || !confirm('Voulez-vous vraiment supprimer cette notification ?')) return;
    this.notificationService.deleteNotification(id).subscribe(() => {
      this.notifications = this.notifications.filter(n => n.id !== id);
    });
  }

  private resetForm() {
    this.newNotification = {
      pour_tous: true,
      code_membre: '',
      titre: '',
      contenu: '',
      type: 'alerte_site',
      categorie: 'systeme',
      est_urgent: false
    };
  }

  getCategorieClass(categorie: string): string {
    const classes: any = {
      'urgence': 'bg-danger',
      'systeme': 'bg-info',
      'projet': 'bg-success',
      'evenement': 'bg-primary',
      'administratif': 'bg-secondary'
    };
    return classes[categorie] || 'bg-dark';
  }
}