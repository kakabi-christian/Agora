import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { DemandeAdhesionService } from '../../services/demande-adhesion.service';

@Component({
  selector: 'app-become-member-content',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './become-member-content.html',
  styleUrls: ['./become-member-content.css'],
})
export class BecomeMemberContent implements OnInit {
  adhesionForm!: FormGroup;
  isSubmitting = false;
  successMessage = '';
  errorMessage: string = '';

  emailExists = false;
  checkingEmail = false;

  constructor(
    private fb: FormBuilder,
    private adhesionService: DemandeAdhesionService
  ) {}

  ngOnInit(): void {
    this.initForm();

    // Vérification email en temps réel
    this.adhesionForm.get('email')?.valueChanges.subscribe(value => {
      if (this.adhesionForm.get('email')?.valid) {
        this.checkEmail(value);
      } else {
        this.emailExists = false;
      }
    });
  }

  private initForm(): void {
    this.adhesionForm = this.fb.group({
      nom: ['', [Validators.required, Validators.maxLength(255)]],
      prenom: ['', [Validators.required, Validators.maxLength(255)]],
      email: ['', [Validators.required, Validators.email]],
      telephone: ['', [Validators.required, Validators.pattern('^[0-9]{9}$')]],
      adresse: ['', [Validators.required]],
      ville: ['', [Validators.required]],
      code_postal: ['', [Validators.required, Validators.pattern('^[0-9]{5}$')]],
      date_naissance: ['', [Validators.required]],
      profession: ['', [Validators.required]],
      motivation: ['', [Validators.required, Validators.minLength(50), Validators.maxLength(1000)]],
      competences: [''],
    });
  }

  /**
   * Getter pour le compteur dynamique dans le template
   */
  get motivationLength(): number {
    return this.adhesionForm.get('motivation')?.value?.length || 0;
  }

  /**
   * Vérifie si un email a déjà une demande
   */
  checkEmail(email: string) {
    this.checkingEmail = true;
    this.adhesionService.checkEmail(email).subscribe({
      next: (res) => {
        this.emailExists = res.exists;
        if (this.emailExists) {
          this.adhesionForm.get('email')?.setErrors({ emailExists: true });
        }
        this.checkingEmail = false;
      },
      error: () => {
        this.checkingEmail = false;
      }
    });
  }

  onSubmit(): void {
    if (this.adhesionForm.invalid) {
      this.adhesionForm.markAllAsTouched();
      return;
    }

    if (this.emailExists) {
      this.errorMessage = "Cet email a déjà une demande en cours.";
      return;
    }

    this.isSubmitting = true;
    this.successMessage = '';
    this.errorMessage = '';

    // Transformation des compétences en tableau
    const formValue = { ...this.adhesionForm.value };
    if (formValue.competences && typeof formValue.competences === 'string') {
      formValue.competences = formValue.competences
        .split(',')
        .map((s: string) => s.trim())
        .filter((s: string) => s !== "");
    } else {
      formValue.competences = [];
    }

    this.adhesionService.envoyerDemande(formValue).subscribe({
      next: (response) => {
        this.successMessage = 'Votre demande a été envoyée avec succès !';
        this.adhesionForm.reset();
        this.isSubmitting = false;
      },
      error: (err) => {
        this.isSubmitting = false;

        if (err.status === 422) {
          const errors = err.error.errors || {};
          this.errorMessage = "Erreur de validation : " + Object.values(errors).flat()[0];
        } else if (err.status === 409) {
          this.errorMessage = String(err.error?.message || 'Cette demande existe déjà.');
        } else {
          this.errorMessage = String(err.error?.message || 'Une erreur est survenue lors de l\'envoi. Veuillez réessayer.');
        }

        console.error('Erreur adhésion detail:', err);
      }
    });
  }
}
