import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { PartenaireService } from '../../../services/partenaire.service';
import { AuthService } from '../../../services/auth.service'; // Importation indispensable
import { Partenaire } from '../../../models/partenaire.model';

@Component({
  selector: 'app-partenaire',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './partenaire.html',
  styleUrl: './partenaire.css',
})
export class Partners implements OnInit {
  partenaires: Partenaire[] = [];
  paginationMeta: any = null;
  loading: boolean = true;
  userRole: string = 'membre';

  // États UI
  showModal: boolean = false;
  isEditMode: boolean = false;
  selectedFile: File | null = null;
  storageUrl = 'http://127.0.0.1:8000/'; // Base URL pour les images
  
  // Variable pour le filtrage
  currentStatut: string = ''; 

  // Objet de formulaire
  currentPartenaire: any = this.initPartenaire();

  constructor(
    private partenaireService: PartenaireService,
    private authService: AuthService // Injection du service Auth
  ) {}

  ngOnInit(): void {
    this.extractUserRole();
    this.chargerPartenaires();
  }

  /**
   * Correction : Récupère le rôle via le AuthService (user_data)
   * et non plus en décodant le token Sanctum
   */
  extractUserRole() {
    this.userRole = this.authService.getUserRole();
    console.log('Rôle détecté dans le composant Partenaire :', this.userRole);
  }

  /**
   * Utilisé dans le HTML pour afficher/cacher les boutons
   */
  isAdmin(): boolean {
    return this.authService.isAdmin();
  }

  /**
   * Initialise un objet partenaire vide
   */
  initPartenaire() {
    return {
      nom: '',
      type: 'sponsor',
      description: '',
      site_web: '',
      contact_nom: '',
      contact_email: '',
      contact_telephone: '',
      niveau_partenariat: 'principal',
      est_actif: true,
      ordre_affichage: 1
    };
  }

  /**
   * Charge les partenaires depuis l'API
   */
  chargerPartenaires(page: number = 1) {
    this.loading = true;
    this.partenaireService.getPartenaires(page).subscribe({
      next: (res) => {
        // Filtrage local si un niveau (principal/secondaire) est sélectionné
        if (this.currentStatut) {
          this.partenaires = res.partenaires.data.filter(
            (p: any) => p.niveau_partenariat === this.currentStatut
          );
        } else {
          this.partenaires = res.partenaires.data;
        }
        
        this.paginationMeta = res.partenaires;
        this.loading = false;
      },
      error: (err) => {
        console.error('Erreur chargement partenaires:', err);
        this.loading = false;
      }
    });
  }

  /**
   * Gère l'ouverture de la modal (Ajout ou Modification)
   */
  ouvrirModal(partenaire?: Partenaire) {
    this.selectedFile = null;
    if (partenaire) {
      this.isEditMode = true;
      this.currentPartenaire = { ...partenaire };
    } else {
      this.isEditMode = false;
      this.currentPartenaire = this.initPartenaire();
    }
    this.showModal = true;
  }

  /**
   * Capture du fichier lors de l'upload
   */
  onFileSelected(event: any) {
    const file = event.target.files[0];
    if (file) {
      this.selectedFile = file;
    }
  }

  /**
   * Enregistre les données via FormData (pour gérer le fichier image)
   */
  enregistrer() {
    const formData = new FormData();
    
    Object.keys(this.currentPartenaire).forEach(key => {
      const value = this.currentPartenaire[key];
      
      if (key === 'est_actif') {
        formData.append('est_actif', value ? '1' : '0');
      } else if (value !== null && value !== undefined && key !== 'logo_url' && key !== 'code_partenaire') {
        formData.append(key, value);
      }
    });

    if (this.selectedFile) {
      formData.append('logo', this.selectedFile);
    }

    const action = this.isEditMode 
      ? this.partenaireService.updatePartenaire(this.currentPartenaire.code_partenaire, formData)
      : this.partenaireService.createPartenaire(formData);

    action.subscribe({
      next: () => {
        this.showModal = false;
        this.chargerPartenaires(this.paginationMeta?.current_page || 1);
      },
      error: (err) => {
        console.error('Erreur lors de l\'enregistrement', err);
        alert('Une erreur est survenue lors de l\'enregistrement.');
      }
    });
  }

  /**
   * Supprime un partenaire après confirmation
   */
  supprimer(code: string) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce partenaire ?')) {
      this.partenaireService.deletePartenaire(code).subscribe({
        next: () => {
          this.chargerPartenaires(this.paginationMeta?.current_page || 1);
        },
        error: (err) => alert('Erreur lors de la suppression')
      });
    }
  }

  /**
   * Navigation de pagination
   */
  allerAPage(page: number) {
    if (page >= 1 && page <= this.paginationMeta.last_page) {
      this.chargerPartenaires(page);
    }
  }
}