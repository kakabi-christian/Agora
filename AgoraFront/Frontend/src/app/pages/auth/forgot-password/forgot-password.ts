import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';
import { AuthForgotPasswordService } from '../../../services/forgot-password.service';
@Component({
  selector: 'app-forgot-password',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterModule],
  templateUrl: './forgot-password.html',
  styleUrl: './forgot-password.css',
})
export class ForgotPassword {
  forgotForm: FormGroup;
  isLoading = false;
  message: string | null = null;
  errorMessage: string | null = null;

  constructor(
    private fb: FormBuilder,
    private authForgotService: AuthForgotPasswordService,
    private router: Router
  ) {
    this.forgotForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]]
    });
  }

  onSendOtp() {
    if (this.forgotForm.invalid) return;

    this.isLoading = true;
    this.errorMessage = null;
    this.message = null;

    this.authForgotService.sendOtp(this.forgotForm.value).subscribe({
      next: (response) => {
        this.isLoading = false;
        this.message = response.message;
        
        // On attend 2 secondes pour que l'utilisateur voit le message de succès
        // puis on redirige vers l'étape de vérification de l'OTP
        setTimeout(() => {
          // On passe l'email en paramètre pour l'étape suivante
          this.router.navigate(['/verify-otp'], { 
            queryParams: { email: this.forgotForm.value.email } 
          });
        }, 2000);
      },
      error: (err) => {
        this.isLoading = false;
        this.errorMessage = err.error?.message || "Une erreur est survenue lors de l'envoi.";
      }
    });
  }
}