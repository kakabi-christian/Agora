import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { AuthService } from '../../../services/auth.service';
import { Membre } from '../../../models/membre.model';
import { MembreService, UpdateMembrePayload } from '../../../services/membre.service';

@Component({
  selector: 'app-membre-profil',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './profil.html',
  styleUrl: './profil.css'
})
export class ProfilMembre implements OnInit {
  loading = true;
  saving = false;
  errorMessage: string | null = null;
  successMessage: string | null = null;

  membre: Membre | null = null;

  form: UpdateMembrePayload = {
    telephone: '',
    adresse: '',
    ville: '',
    code_postal: '',
    biographie: ''
  };

  // Formulaire de changement de mot de passe
  passwordForm = {
    ancien_mot_de_passe: '',
    nouveau_mot_de_passe: '',
    nouveau_mot_de_passe_confirmation: ''
  };

  changingPassword = false;
  passwordError: string | null = null;
  passwordSuccess: string | null = null;

  constructor(
    private authService: AuthService,
    private membreService: MembreService
  ) { }

  ngOnInit(): void {
    this.authService.getCurrentUser().subscribe({
      next: (membre) => {
        this.membre = membre;

        this.form = {
          telephone: membre.telephone ?? '',
          adresse: membre.adresse ?? '',
          ville: membre.ville ?? '',
          code_postal: membre.code_postal ?? '',
          biographie: membre.biographie ?? ''
        };
      },
      error: () => {
        this.errorMessage = "Impossible de charger le profil.";
      },
      complete: () => {
        this.loading = false;
      }
    });
  }

  /**
   * Obtient les initiales du membre (prénom + nom)
   */
  getInitials(): string {
    if (!this.membre) return '?';
    const firstInitial = this.membre.prenom?.charAt(0)?.toUpperCase() || '';
    const lastInitial = this.membre.nom?.charAt(0)?.toUpperCase() || '';
    return `${firstInitial}${lastInitial}` || '?';
  }

  /**
   * Formate un nom avec la première lettre en majuscule
   */
  formatNom(nom: string | undefined): string {
    if (!nom) return '';
    return nom.charAt(0).toUpperCase() + nom.slice(1).toLowerCase();
  }

  /**
   * Sauvegarde les informations personnelles du membre
   */
  save(): void {
    if (!this.membre) return;

    this.saving = true;
    this.errorMessage = null;
    this.successMessage = null;

    this.membreService.updateMembre(this.membre.code_membre, this.form).subscribe({
      next: () => {
        this.successMessage = 'Profil mis à jour avec succès.';

        // Rafraîchir l'utilisateur local
        this.authService.getCurrentUser().subscribe({
          next: (m) => {
            this.membre = m;
          }
        });

        // Auto-masquer le message de succès après 5 secondes
        setTimeout(() => {
          this.successMessage = null;
        }, 5000);
      },
      error: (err) => {
        this.errorMessage = err.error?.message || 'Échec de la mise à jour du profil.';

        // Auto-masquer le message d'erreur après 5 secondes
        setTimeout(() => {
          this.errorMessage = null;
        }, 5000);
      },
      complete: () => {
        this.saving = false;
      }
    });
  }

  /**
   * Change le mot de passe du membre
   */
  changePassword(): void {
    this.changingPassword = true;
    this.passwordError = null;
    this.passwordSuccess = null;

    // Validation côté client
    if (this.passwordForm.nouveau_mot_de_passe !== this.passwordForm.nouveau_mot_de_passe_confirmation) {
      this.passwordError = 'Les mots de passe ne correspondent pas.';
      this.changingPassword = false;
      return;
    }

    if (this.passwordForm.nouveau_mot_de_passe.length < 8) {
      this.passwordError = 'Le mot de passe doit contenir au moins 8 caractères.';
      this.changingPassword = false;
      return;
    }

    // Validation supplémentaire : mot de passe fort
    if (!this.isPasswordStrong(this.passwordForm.nouveau_mot_de_passe)) {
      this.passwordError = 'Le mot de passe doit contenir au moins une lettre majuscule, une lettre minuscule et un chiffre.';
      this.changingPassword = false;
      return;
    }

    this.authService.changePassword(this.passwordForm).subscribe({
      next: () => {
        this.passwordSuccess = 'Mot de passe changé avec succès.';

        // Réinitialiser le formulaire
        this.passwordForm = {
          ancien_mot_de_passe: '',
          nouveau_mot_de_passe: '',
          nouveau_mot_de_passe_confirmation: ''
        };

        // Mettre à jour le statut du mot de passe temporaire
        if (this.membre) {
          this.membre.mot_de_passe_temporaire = false;
        }

        // Auto-masquer le message de succès après 5 secondes
        setTimeout(() => {
          this.passwordSuccess = null;
        }, 5000);
      },
      error: (err) => {
        this.passwordError = err.error?.message || 'Échec du changement de mot de passe.';

        // Auto-masquer le message d'erreur après 5 secondes
        setTimeout(() => {
          this.passwordError = null;
        }, 5000);
      },
      complete: () => {
        this.changingPassword = false;
      }
    });
  }

  /**
   * Vérifie si le mot de passe est suffisamment fort
   * Doit contenir au moins une majuscule, une minuscule et un chiffre
   */
  private isPasswordStrong(password: string): boolean {
    const hasUpperCase = /[A-Z]/.test(password);
    const hasLowerCase = /[a-z]/.test(password);
    const hasNumber = /[0-9]/.test(password);

    return hasUpperCase && hasLowerCase && hasNumber;
  }
}
