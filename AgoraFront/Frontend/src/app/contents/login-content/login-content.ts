import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { AuthService } from '../../services/auth.service';
import { Router } from '@angular/router';
import { RouterModule } from '@angular/router'; // Pour le routerLink


@Component({
  selector: 'app-login-content',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule,RouterModule],
  templateUrl: './login-content.html',
  styleUrl: './login-content.css',
})
export class LoginContent {
  loginForm: FormGroup;
  errorMessage: string = '';
  isLoading: boolean = false;

  constructor(
    private fb: FormBuilder,
    private authService: AuthService,
    private router: Router
  ) {
    this.loginForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
      mot_de_passe: ['', [Validators.required]]
    });
  }

  onSubmit(): void {
    if (this.loginForm.valid) {
      this.isLoading = true;
      this.errorMessage = '';

      // On appelle le service de connexion
      this.authService.login(this.loginForm.value).subscribe({
        next: (response: any) => {
          this.isLoading = false;

          /**
           * ANALYSE DE LA RÉPONSE (Postman) :
           * La structure est : response -> data -> membre -> role
           */
          const authData = response.data;

          if (authData && authData.membre) {
            const role = authData.membre.role;

            if (role === 'administrateur') {
              this.router.navigate(['/admin/dashboard']);
            } else {
              // Redirection vers le dashboard membre pour les membres approuvés
              this.router.navigate(['/membre/tableau-de-bord']);
            }
          } else {
            this.errorMessage = "Structure de données invalide reçue du serveur.";
            console.error("Détail de la réponse inattendue:", response);
          }
        },
        error: (err) => {
          this.isLoading = false;
          // Laravel renvoie souvent l'erreur dans err.error.message
          this.errorMessage = err.error?.message || 'Identifiants incorrects ou erreur serveur.';
        }
      });
    }
  }
}