import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ProjetService } from '../../../services/projet.service';
import { Projet } from '../../../models/projet.model';

@Component({
  selector: 'app-project',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './project.html',
  styleUrl: './project.css',
})
export class Project implements OnInit {
  projets: Projet[] = [];
  paginationMeta: any = null;
  loading: boolean = true;

  // Filtre de statut pour l'interface
  currentStatut: string = '';

  // États pour la Modal
  showModal: boolean = false;
  isEditMode: boolean = false;
  selectedFile: File | null = null;

  // Champ temporaire pour ajouter un objectif à la fois
  nouvelObjectif: string = '';

  // Objet Projet initialisé
  currentProjet: any = this.initProjet();

  constructor(private projetService: ProjetService) {}

  ngOnInit(): void {
    this.chargerProjets();
  }

  initProjet() {
    return {
      nom: '',
      description: '',
      type: 'agricole',
      statut: 'propose',
      date_debut: '',
      date_fin_prevue: '',
      budget_estime: null, // Changé à null pour éviter d'envoyer 0 si vide
      coordinateur: '',
      objectifs: [],
      est_public: true
    };
  }

  chargerProjets(page: number = 1) {
    this.loading = true;
    this.projetService.getProjets(page).subscribe({
      next: (res) => {
        let data = res.projets.data;
        if (this.currentStatut) {
          data = data.filter((p: Projet) => p.statut === this.currentStatut);
        }
        this.projets = data;
        this.paginationMeta = res.projets;
        this.loading = false;
      },
      error: (err) => {
        console.error('Erreur chargement projets:', err);
        this.loading = false;
      }
    });
  }

  filtrerProjets(statut: string) {
    this.currentStatut = statut;
    this.chargerProjets(1);
  }

  ouvrirModal(projet?: Projet) {
    this.selectedFile = null;
    this.nouvelObjectif = '';

    if (projet) {
      this.isEditMode = true;
      this.currentProjet = {
        ...projet,
        objectifs: Array.isArray(projet.objectifs) ? [...projet.objectifs] : []
      };
    } else {
      this.isEditMode = false;
      this.currentProjet = this.initProjet();
    }
    this.showModal = true;
  }

  ajouterObjectif() {
    if (this.nouvelObjectif.trim()) {
      this.currentProjet.objectifs.push(this.nouvelObjectif.trim());
      this.nouvelObjectif = '';
    }
  }

  retirerObjectif(index: number) {
    this.currentProjet.objectifs.splice(index, 1);
  }

  onFileSelected(event: any) {
    const file = event.target.files[0];
    if (file) {
      this.selectedFile = file;
    }
  }

  enregistrerProjet() {
    const formData = new FormData();

    // Nettoyage et préparation des données pour Laravel (Évite l'erreur 422)
    Object.keys(this.currentProjet).forEach(key => {
      const value = this.currentProjet[key];

      // 1. Tableaux d'objectifs
      if (key === 'objectifs' && Array.isArray(value)) {
        value.forEach((item: string) => formData.append('objectifs[]', item));
      }
      // 2. Conversion du booléen pour le FormData (true -> "1")
      else if (key === 'est_public') {
        formData.append('est_public', value ? '1' : '0');
      }
      // 3. Gestion des champs numériques et dates (ne pas envoyer si vide)
      else if (value === '' || value === null || value === undefined) {
        // On n'ajoute rien pour laisser Laravel utiliser le mode "nullable"
      }
      // 4. On ignore l'URL de l'image (le fichier sera ajouté séparément)
      else if (key !== 'image_url') {
        formData.append(key, value);
      }
    });

    // Ajout du fichier image s'il existe
    if (this.selectedFile) {
      formData.append('image_url', this.selectedFile);
    }

    // Gestion de la méthode pour Laravel (PUT/PATCH via FormData)
    const action = this.isEditMode
      ? this.projetService.updateProjet(this.currentProjet.id, formData)
      : this.projetService.createProjet(formData);

    action.subscribe({
      next: () => {
        this.showModal = false;
        this.chargerProjets(this.paginationMeta?.current_page || 1);
        this.selectedFile = null;
      },
      error: (err) => {
        if (err.status === 422) {
          console.error('Erreurs de validation Laravel:', err.error.errors);
          alert('Erreur : ' + Object.values(err.error.errors).flat().join('\n'));
        } else {
          console.error('Erreur lors de l\'enregistrement:', err);
        }
      }
    });
  }

  supprimer(id: number) {
    if (confirm('Voulez-vous vraiment supprimer ce projet ?')) {
      this.projetService.deleteProjet(id).subscribe({
        next: () => this.chargerProjets(this.paginationMeta?.current_page),
        error: (err) => console.error('Erreur suppression:', err)
      });
    }
  }

  allerAPage(page: number) {
    if (this.paginationMeta && page >= 1 && page <= this.paginationMeta.last_page) {
      this.chargerProjets(page);
    }
  }
}
