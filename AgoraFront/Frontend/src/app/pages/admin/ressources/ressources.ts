import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RessourceService, Ressource, PaginatedRessources } from '../../../services/ressource.service';

@Component({
  selector: 'app-ressources-admin',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './ressources.html',
  styleUrl: './ressources.css',
})
export class RessourcesAdmin implements OnInit {
  ressources: Ressource[] = [];
  loading = false;
  uploading = false;
  
  // Modal
  showModal = false;
  
  // Formulaire
  form = {
    titre: '',
    type: 'document',
    categorie: 'administratif',
    description: '',
    date_expiration: '',
    est_public: false,
    necessite_authentification: true,
    fichier: null as File | null
  };
  
  // Filtres
  filters = {
    categorie: '',
    type: '',
    search: ''
  };
  
  // Pagination
  currentPage = 1;
  totalPages = 1;
  totalItems = 0;

  // Messages
  successMessage = '';
  errorMessage = '';
  
  // Options
  types = [
    { value: 'document', label: 'Document' },
    { value: 'formulaire', label: 'Formulaire' },
    { value: 'rapport', label: 'Rapport' },
    { value: 'reglement', label: 'Règlement' },
    { value: 'autre', label: 'Autre' }
  ];
  
  categories = [
    { value: 'administratif', label: 'Administratif' },
    { value: 'comptable', label: 'Comptable' },
    { value: 'juridique', label: 'Juridique' },
    { value: 'technique', label: 'Technique' },
    { value: 'pedagogique', label: 'Pédagogique' }
  ];

  constructor(private ressourceService: RessourceService) {}

  ngOnInit(): void {
    this.loadRessources();
  }

  /**
   * Charger la liste des ressources
   */
  loadRessources(): void {
    this.loading = true;
    this.clearMessages();
    
    const params: any = {};
    
    if (this.filters.categorie) {
      params.categorie = this.filters.categorie;
    }
    if (this.filters.type) {
      params.type = this.filters.type;
    }
    if (this.filters.search) {
      params.search = this.filters.search;
    }

    this.ressourceService.getRessources(params).subscribe({
      next: (response: PaginatedRessources) => {
        this.ressources = response.data;
        if (response.meta) {
          this.totalPages = response.meta.last_page || 1;
          this.totalItems = response.meta.total || 0;
        }
        this.loading = false;
      },
      error: (error) => {
        console.error('Erreur lors du chargement des ressources:', error);
        this.loading = false;
        this.showError('Erreur lors du chargement des ressources');
      }
    });
  }

  /**
   * Ouvrir la modal d'upload
   */
  openModal(): void {
    this.showModal = true;
    this.resetForm();
  }

  /**
   * Fermer la modal
   */
  closeModal(): void {
    this.showModal = false;
    this.resetForm();
  }

  /**
   * Gérer la sélection de fichier
   */
  onFileSelected(event: any): void {
    const file = event.target.files[0];
    if (file) {
      // Vérifier la taille (10MB max)
      if (file.size > 10 * 1024 * 1024) {
        this.showError('Le fichier ne doit pas dépasser 10 MB');
        event.target.value = '';
        return;
      }
      
      // Vérifier l'extension
      const allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'zip'];
      const extension = file.name.split('.').pop()?.toLowerCase();
      
      if (!extension || !allowedExtensions.includes(extension)) {
        this.showError('Format de fichier non autorisé. Formats acceptés : PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, ZIP');
        event.target.value = '';
        return;
      }
      
      this.form.fichier = file;
    }
  }

  /**
   * Soumettre le formulaire d'upload
   */
  onSubmit(): void {
    // Validation
    if (!this.form.titre.trim()) {
      this.showError('Le titre est obligatoire');
      return;
    }
    
    if (!this.form.fichier) {
      this.showError('Veuillez sélectionner un fichier');
      return;
    }
    
    this.uploading = true;
    this.clearMessages();
    
    // Créer le FormData
    const formData = new FormData();
    formData.append('titre', this.form.titre);
    formData.append('type', this.form.type);
    formData.append('categorie', this.form.categorie);
    formData.append('fichier', this.form.fichier);
    
    if (this.form.description) {
      formData.append('description', this.form.description);
    }
    
    if (this.form.date_expiration) {
      formData.append('date_expiration', this.form.date_expiration);
    }
    
    formData.append('est_public', this.form.est_public ? '1' : '0');
    formData.append('necessite_authentification', this.form.necessite_authentification ? '1' : '0');
    
    // Envoyer au serveur
    this.ressourceService.uploadRessource(formData).subscribe({
      next: (response) => {
        this.uploading = false;
        this.showSuccess('Ressource uploadée avec succès !');
        this.closeModal();
        this.loadRessources();
      },
      error: (error) => {
        console.error('Erreur lors de l\'upload:', error);
        this.uploading = false;
        
        if (error.status === 422 && error.error.errors) {
          const firstError = Object.values(error.error.errors)[0];
          this.showError(Array.isArray(firstError) ? firstError[0] : 'Erreur de validation');
        } else {
          this.showError('Erreur lors de l\'upload de la ressource');
        }
      }
    });
  }

  /**
   * Télécharger une ressource
   */
  downloadRessource(ressource: Ressource): void {
    this.ressourceService.downloadRessource(ressource.id).subscribe({
      next: (response) => {
        const blob = response.body;
        if (blob) {
          this.ressourceService.downloadFile(blob, ressource.nom_fichier);
        }
      },
      error: (error) => {
        console.error('Erreur lors du téléchargement:', error);
        this.showError('Erreur lors du téléchargement');
      }
    });
  }

  /**
   * Appliquer les filtres
   */
  applyFilters(): void {
    this.currentPage = 1;
    this.loadRessources();
  }

  /**
   * Réinitialiser les filtres
   */
  resetFilters(): void {
    this.filters = {
      categorie: '',
      type: '',
      search: ''
    };
    this.currentPage = 1;
    this.loadRessources();
  }

  /**
   * Réinitialiser le formulaire
   */
  resetForm(): void {
    this.form = {
      titre: '',
      type: 'document',
      categorie: 'administratif',
      description: '',
      date_expiration: '',
      est_public: false,
      necessite_authentification: true,
      fichier: null
    };
    this.clearMessages();
  }

  /**
   * Afficher un message de succès
   */
  private showSuccess(message: string): void {
    this.successMessage = message;
    setTimeout(() => {
      this.successMessage = '';
    }, 5000);
  }

  /**
   * Afficher un message d'erreur
   */
  private showError(message: string): void {
    this.errorMessage = message;
    setTimeout(() => {
      this.errorMessage = '';
    }, 5000);
  }

  /**
   * Effacer les messages
   */
  private clearMessages(): void {
    this.successMessage = '';
    this.errorMessage = '';
  }

  /**
   * Obtenir le label du type
   */
  getTypeLabel(type: string): string {
    const found = this.types.find(t => t.value === type);
    return found ? found.label : type;
  }

  /**
   * Obtenir le label de la catégorie
   */
  getCategorieLabel(categorie: string): string {
    const found = this.categories.find(c => c.value === categorie);
    return found ? found.label : categorie;
  }

  /**
   * Obtenir l'icône selon l'extension
   */
  getFileIcon(extension: string): string {
    const ext = extension.toLowerCase();
    if (ext === 'pdf') return 'bi-file-earmark-pdf-fill text-danger';
    if (['doc', 'docx'].includes(ext)) return 'bi-file-earmark-word-fill text-primary';
    if (['xls', 'xlsx'].includes(ext)) return 'bi-file-earmark-excel-fill text-success';
    if (['ppt', 'pptx'].includes(ext)) return 'bi-file-earmark-ppt-fill text-warning';
    if (ext === 'zip') return 'bi-file-earmark-zip-fill text-secondary';
    return 'bi-file-earmark-text-fill text-info';
  }

  /**
   * Formater la date
   */
  formatDate(date: string | null | undefined): string {
    if (!date) return 'N/A';
    const d = new Date(date);
    return d.toLocaleDateString('fr-FR');
  }

  /**
   * Obtenir la date de demain pour le min du date picker
   */
  getTomorrowDate(): string {
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    return tomorrow.toISOString().split('T')[0];
  }
}
