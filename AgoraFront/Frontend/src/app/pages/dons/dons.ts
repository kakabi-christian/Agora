import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { DonService } from '../../services/don.service';
import { Don } from '../../models/don.model';

@Component({
  selector: 'app-dons',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './dons.html',
  styleUrl: './dons.css',
})
export class Dons {
  // Le modèle utilisé pour le formulaire (Saisie de 9 chiffres)
  don: Don = {
    nom_donateur: '',
    email_donateur: '',
    telephone: '',
    type: 'don',
    montant: 0,
    anonyme: false,
    message_donateur: ''
  };

  isLoading = false;
  successMessage = '';
  errorMessage = '';

  constructor(private donService: DonService) { }

  onSubmit() {
    this.isLoading = true;
    this.successMessage = '';
    this.errorMessage = '';

    // Préparation des données : Ajout du préfixe 237 pour Campay
    const donFinal: Don = {
      ...this.don,
      telephone: '237' + this.don.telephone
    };

    console.log('[DonsComponent] Envoi du don avec préfixe:', donFinal.telephone);

    this.donService.initierDon(donFinal).subscribe({
      next: (response) => {
        this.isLoading = false;
        if (response.success) {
          this.successMessage = 'Demande de paiement envoyée ! Veuillez valider sur votre téléphone.';
          this.resetForm();
        }
      },
      error: (err) => {
        this.isLoading = false;
        this.errorMessage = err.message || 'Impossible d\'initier le paiement.';
        console.error('[DonsComponent] ❌ Erreur:', err);
      }
    });
  }

  resetForm() {
    this.don = {
      nom_donateur: '',
      email_donateur: '',
      telephone: '',
      type: 'don',
      montant: 0,
      anonyme: false,
      message_donateur: ''
    };
  }
}