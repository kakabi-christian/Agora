import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule, AbstractControl } from '@angular/forms';
import { Router, ActivatedRoute, RouterModule } from '@angular/router';
import { AuthForgotPasswordService } from '../../../services/forgot-password.service';
@Component({
  selector: 'app-reset-password',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterModule],
  templateUrl: './reset-password.html',
  styleUrl: './reset-password.css',
})
export class ResetPassword implements OnInit {
  resetForm: FormGroup;
  isLoading = false;
  errorMessage: string | null = null;
  successMessage: string | null = null;
  email: string = '';

  constructor(
    private fb: FormBuilder,
    private authForgotService: AuthForgotPasswordService,
    private router: Router,
    private route: ActivatedRoute
  ) {
    // Initialisation du formulaire avec validation de longueur et de correspondance
    this.resetForm = this.fb.group({
      password: ['', [Validators.required, Validators.minLength(8)]],
      password_confirmation: ['', [Validators.required]]
    }, { 
      validators: this.passwordMatchValidator // Validateur personnalisé
    });
  }

  ngOnInit() {
    // On récupère l'email pour savoir quel compte mettre à jour
    this.route.queryParams.subscribe(params => {
      this.email = params['email'];
      if (!this.email) {
        this.router.navigate(['/auth/forgot-password']);
      }
    });
  }

  /**
   * Vérifie si le mot de passe et la confirmation sont identiques
   */
  passwordMatchValidator(control: AbstractControl) {
    const password = control.get('password')?.value;
    const confirm = control.get('password_confirmation')?.value;
    
    if (password !== confirm) {
      control.get('password_confirmation')?.setErrors({ mismatch: true });
      return { mismatch: true };
    }
    return null;
  }

  onResetPassword() {
    if (this.resetForm.invalid) return;

    this.isLoading = true;
    this.errorMessage = null;
    this.successMessage = null;

    const payload = {
      email: this.email,
      password: this.resetForm.value.password,
      password_confirmation: this.resetForm.value.password_confirmation
    };

    this.authForgotService.resetPassword(payload).subscribe({
      next: (response) => {
        this.isLoading = false;
        this.successMessage = "Mot de passe réinitialisé avec succès ! Redirection vers la connexion...";
        
        // Redirection après 3 secondes
        setTimeout(() => {
          this.router.navigate(['/login']);
        }, 3000);
      },
      error: (err) => {
        this.isLoading = false;
        this.errorMessage = err.error?.message || "Une erreur est survenue lors de la réinitialisation.";
      }
    });
  }
}