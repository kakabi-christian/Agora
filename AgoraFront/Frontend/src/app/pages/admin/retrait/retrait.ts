import { Component, OnInit } from '@angular/core'; // Ajout de OnInit
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { DonService } from '../../../services/don.service';

@Component({
  selector: 'app-retrait',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './retrait.html',
  styleUrl: './retrait.css',
})
export class Retrait implements OnInit { // Implémentation de OnInit
  // Gestion des étapes
  step: number = 1; 

  // Données du formulaire
  amount: number | null = null;
  password: string = '';

  // --- NOUVELLES VARIABLES POUR LE REVENU ---
  revenuTotal: number = 0;
  devise: string = 'FCFA';
  loadingRevenu: boolean = true;
  // ------------------------------------------

  // État de l'interface
  isLoading = false;
  message = '';
  isError = false;

  constructor(private donService: DonService) {
    console.log('[RETRAIT-COMP] Composant initialisé');
  }

  // On récupère le solde dès l'initialisation du composant
  ngOnInit(): void {
    this.fetchRevenu();
  }

  /**
   * Récupère le revenu total disponible pour affichage
   */
  fetchRevenu(): void {
    this.loadingRevenu = true;
    this.donService.getTotalGeneral().subscribe({
      next: (res: any) => {
        if (res) {
          this.revenuTotal = res.total_general;
          this.devise = res.devise || 'FCFA';
        }
        this.loadingRevenu = false;
      },
      error: (err) => {
        console.error('[RETRAIT-COMP] Erreur récupération revenu:', err);
        this.loadingRevenu = false;
      }
    });
  }

  /**
   * Passage à l'étape du mot de passe
   */
  nextStep() {
    console.log('[RETRAIT-COMP] Tentative passage étape 2. Montant saisi:', this.amount);
    
    // Vérification : montant saisi ne doit pas dépasser le revenu total
    if (this.amount && this.amount > this.revenuTotal) {
      this.setNotify("Le montant saisi dépasse le solde disponible.", true);
      return;
    }

    if (this.amount && this.amount >= 5) {
      console.log('[RETRAIT-COMP] ✅ Montant valide. Passage au mot de passe.');
      this.step = 2;
      this.message = ''; 
    } else {
      console.warn('[RETRAIT-COMP] ❌ Montant invalide ou trop faible.');
      this.setNotify("Le montant minimum est de 10 XAF", true);
    }
  }

  /**
   * Retour à l'étape du montant avec nettoyage complet
   */
  prevStep() {
    console.group('[RETRAIT-NAVIGATION] Retour à l\'étape 1');
    this.step = 1;
    this.password = '';
    this.message = '';
    this.isError = false;
    console.groupEnd();
  }

  /**
   * Méthode finale pour confirmer le retrait
   */
  confirmWithdraw() {
    console.group('[RETRAIT-PROCESS] Lancement de la confirmation');
    
    if (!this.password) {
      this.setNotify("Veuillez saisir votre mot de passe pour confirmer", true);
      console.groupEnd();
      return;
    }

    this.isLoading = true;
    this.message = '';
    
    this.donService.retraitAdmin(this.amount!, this.password).subscribe({
      next: (res) => {
        this.isLoading = false;
        if (res.success) {
          this.setNotify(res.message || "Transfert effectué avec succès !", false);
          this.resetForm();
          this.fetchRevenu(); // RECHARGE le solde après un retrait réussi !
        } else {
          this.setNotify(res.message || "Échec du transfert", true);
        }
        console.groupEnd();
      },
      error: (err) => {
        this.isLoading = false;
        const errorMsg = err.error?.message || err.message || "Erreur de connexion au serveur.";
        this.setNotify(errorMsg, true);
        console.groupEnd();
      }
    });
  }

  private setNotify(msg: string, error: boolean) {
    this.message = msg;
    this.isError = error;
  }

  private resetForm() {
    this.amount = null;
    this.password = '';
    this.step = 1; 
  }
}