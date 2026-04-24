import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { ContactService } from '../../services/contact.service';

@Component({
  selector: 'app-contact',
  standalone: true, 
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './contact.html',
  styleUrl: './contact.css',
})
export class Contact implements OnInit {
  contactForm!: FormGroup;
  isSubmitting = false;
  
  // Variables pour la gestion des messages et de l'affichage
  showSuccessToast = false; 
  successMessage = '';
  errorMessage = '';

  constructor(
    private fb: FormBuilder,
    private contactService: ContactService
  ) {}

  ngOnInit(): void {
    this.initForm();
  }

  // Initialisation du formulaire
  initForm(): void {
    this.contactForm = this.fb.group({
      nom_expediteur: ['', [Validators.required, Validators.minLength(3)]],
      email_expediteur: ['', [Validators.required, Validators.email]],
      sujet: ['', [Validators.required]],
      message: ['', [Validators.required, Validators.minLength(10)]],
      type_demande: ['information', [Validators.required]],
      telephone: [''],
      code_membre: ['']
    });
  }

  onSubmit(): void {
    if (this.contactForm.valid) {
      this.isSubmitting = true;
      this.errorMessage = '';
      this.successMessage = '';

      this.contactService.sendContactMessage(this.contactForm.value).subscribe({
        next: (response) => {
          // 1. Afficher l'interface de succès
          this.successMessage = 'Votre message a été envoyé avec succès !';
          this.showSuccessToast = true;
          
          // 2. Réinitialiser le formulaire
          this.contactForm.reset({ type_demande: 'information' });
          this.isSubmitting = false;

          // 3. Faire disparaître l'interface après 3 secondes (3000 ms)
          setTimeout(() => {
            this.showSuccessToast = false;
          }, 3000);
        },
        error: (err) => {
          this.errorMessage = "Une erreur est survenue lors de l'envoi. Veuillez réessayer.";
          this.isSubmitting = false;
          console.error(err);
        }
      });
    } else {
      // Marquer tous les champs comme touchés pour afficher les erreurs visuelles
      this.contactForm.markAllAsTouched();
    }
  }
}