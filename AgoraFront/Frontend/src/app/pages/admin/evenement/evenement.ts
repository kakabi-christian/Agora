import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { EvenementService } from '../../../services/evenement.service';
import { Evenement as EvenementModel, EvenementResponse } from '../../../models/evenement.model';

@Component({
  selector: 'app-evenement',
  standalone: true,
  imports: [CommonModule, FormsModule, ReactiveFormsModule],
  templateUrl: './evenement.html',
  styleUrl: './evenement.css',
})
export class Evenement implements OnInit {
  evenements: EvenementModel[] = [];
  eventForm: FormGroup;
  
  // États de l'interface
  loading: boolean = false;
  showModal: boolean = false;
  isEditMode: boolean = false;
  currentEventCode: string | null = null;
  
  // États pour la suppression
  showDeleteModal: boolean = false;
  eventToDelete: EvenementModel | null = null;
  
  // Gestion de l'image
  selectedFile: File | null = null;
  imagePreview: string | null = null;

  constructor(
    private eventService: EvenementService,
    private fb: FormBuilder
  ) {
    this.eventForm = this.fb.group({
      titre: ['', [Validators.required, Validators.minLength(3)]],
      description: ['', Validators.required],
      date_debut: ['', Validators.required],
      date_fin: [''],
      lieu: ['', Validators.required],
      adresse: [''],
      ville: [''],
      type: ['assemblee', Validators.required],
      frais_inscription: [0, [Validators.min(0)]],
      places_disponibles: [null],
      paiement_obligatoire: [false],
      instructions: [''],
      statut: ['planifie']
    });
  }

  ngOnInit(): void {
    this.loadEvenements();
  }

  loadEvenements(): void {
    this.loading = true;
    this.eventService.getEvenements().subscribe({
      next: (res: EvenementResponse) => {
        this.evenements = res.evenements.data;
        this.loading = false;
      },
      error: (err) => {
        console.error('Erreur chargement:', err);
        this.loading = false;
      }
    });
  }

  onFileSelected(event: any): void {
    const file = event.target.files[0];
    if (file) {
      this.selectedFile = file;
      const reader = new FileReader();
      reader.onload = () => this.imagePreview = reader.result as string;
      reader.readAsDataURL(file);
    }
  }

  /**
   * OUVERTURE DU MODAL DE SUPPRESSION
   */
  deleteEvent(event: EvenementModel): void {
    this.eventToDelete = event;
    this.showDeleteModal = true;
  }

  closeDeleteModal(): void {
    this.showDeleteModal = false;
    this.eventToDelete = null;
  }

  /**
   * CONFIRMATION DE LA SUPPRESSION
   */
  confirmDelete(): void {
    if (!this.eventToDelete || !this.eventToDelete.code_evenement) return;

    this.loading = true;
    this.eventService.deleteEvenement(this.eventToDelete.code_evenement).subscribe({
      next: () => {
        this.handleSuccess('Événement supprimé avec succès');
        this.closeDeleteModal();
      },
      error: (err) => {
        console.error('Erreur suppression:', err);
        this.loading = false;
        alert('Impossible de supprimer cet événement.');
      }
    });
  }

  /**
   * OUVRE LE MODAL POUR ÉDITION
   */
  editEvent(event: EvenementModel): void {
    this.isEditMode = true;
    this.showModal = true;
    this.currentEventCode = event.code_evenement || null;

    this.eventForm.patchValue({
      titre: event.titre,
      description: event.description,
      date_debut: event.date_debut,
      date_fin: event.date_fin,
      lieu: event.lieu,
      adresse: event.adresse,
      ville: event.ville,
      type: event.type,
      frais_inscription: event.frais_inscription,
      places_disponibles: event.places_disponibles,
      paiement_obligatoire: !!event.paiement_obligatoire,
      instructions: event.instructions,
      statut: event.statut
    });

    if (event.image_url) {
      this.imagePreview = `http://localhost:8000/${event.image_url}`;
    }
  }

  /**
   * SOUMISSION UNIQUE (CREATE OU UPDATE)
   */
  onSubmit(): void {
    if (this.eventForm.valid) {
      this.loading = true;
      const formData = new FormData();
      
      Object.keys(this.eventForm.value).forEach(key => {
        const value = this.eventForm.value[key];
        if (value !== null && value !== undefined) {
          if (key === 'paiement_obligatoire') {
            formData.append(key, value ? '1' : '0');
          } else {
            formData.append(key, value);
          }
        }
      });

      if (this.selectedFile) {
        formData.append('image', this.selectedFile);
      }

      if (this.isEditMode && this.currentEventCode) {
        this.eventService.updateEvenement(this.currentEventCode, formData).subscribe({
          next: () => this.handleSuccess('Mis à jour avec succès'),
          error: (err) => this.handleError(err)
        });
      } else {
        this.eventService.createEvenement(formData).subscribe({
          next: () => this.handleSuccess('Créé avec succès'),
          error: (err) => this.handleError(err)
        });
      }
    }
  }

  private handleSuccess(msg: string): void {
    this.closeModal();
    this.loadEvenements();
    this.loading = false;
  }

  private handleError(err: any): void {
    console.error('Erreur:', err);
    this.loading = false;
    if (err.status === 422) {
      alert('Erreur de validation. Vérifiez les champs.');
    }
  }

  openModal(): void {
    this.isEditMode = false;
    this.showModal = true;
  }

  closeModal(): void {
    this.showModal = false;
    this.isEditMode = false;
    this.currentEventCode = null;
    this.eventForm.reset({ 
      type: 'assemblee', 
      statut: 'planifie', 
      frais_inscription: 0,
      paiement_obligatoire: false 
    });
    this.selectedFile = null;
    this.imagePreview = null;
  }
}