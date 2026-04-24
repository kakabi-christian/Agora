import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { Router, ActivatedRoute, RouterModule } from '@angular/router';
import { AuthForgotPasswordService } from '../../../services/forgot-password.service';
@Component({
  selector: 'app-verify-otp',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterModule],
  templateUrl: './verify-otp.html',
  styleUrl: './verify-otp.css',
})
export class VerifyOtp implements OnInit {
  verifyForm: FormGroup;
  isLoading = false;
  errorMessage: string | null = null;
  email: string = '';

  constructor(
    private fb: FormBuilder,
    private authForgotService: AuthForgotPasswordService,
    private router: Router,
    private route: ActivatedRoute
  ) {
    // Validation : 6 chiffres exactement
    this.verifyForm = this.fb.group({
      otp: ['', [Validators.required, Validators.pattern('^[0-9]{6}$')]]
    });
  }

  ngOnInit() {
    // Récupération de l'email passé par le composant précédent
    this.route.queryParams.subscribe(params => {
      this.email = params['email'];
      
      // Sécurité : si on arrive ici sans email, on repart au début
      if (!this.email) {
        this.router.navigate(['/auth/forgot-password']);
      }
    });
  }

  onVerifyOtp() {
    if (this.verifyForm.invalid) return;

    this.isLoading = true;
    this.errorMessage = null;

    const payload = {
      email: this.email,
      otp: this.verifyForm.value.otp
    };

    this.authForgotService.verifyOtp(payload).subscribe({
      next: (response) => {
        this.isLoading = false;
        // Succès : on passe à la création du nouveau mot de passe
        this.router.navigate(['/reset-password'], { 
          queryParams: { email: this.email } 
        });
      },
      error: (err) => {
        this.isLoading = false;
        // Affiche l'erreur du backend (ex: "Code expiré" ou "Code invalide")
        this.errorMessage = err.error?.message || "Le code est incorrect ou expiré.";
      }
    });
  }
}