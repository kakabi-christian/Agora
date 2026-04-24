import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Faq, FaqPagination } from '../../../models/faq.model';
import { AuthService } from '../../../services/auth.service';
import { FaqService } from '../../../services/faq';

@Component({
  selector: 'app-faq',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './faq.html',
  styleUrl: './faq.css',
})
export class FaqComponent implements OnInit {
  faqs: Faq[] = [];
  paginationMeta?: FaqPagination;
  loading = false;
  showModal = false;
  isEditMode = false;

  // Modèle pour le formulaire
  currentFaq: Faq = this.getEmptyFaq();

  // Filtres
  categories = ['generale', 'membres', 'projets', 'dons', 'evenements', 'administratif'];
  selectedCategorie = '';

  constructor(
    private faqService: FaqService,
    private authService: AuthService
  ) {}

  ngOnInit(): void {
    this.chargerFaqs();
  }

  chargerFaqs(page: number = 1): void {
    this.loading = true;
    this.faqService.getFaqs(page, 10).subscribe({
      next: (response) => {
        this.faqs = response.data;
        this.paginationMeta = response;
        this.loading = false;
      },
      error: (err) => {
        console.error('Erreur chargement FAQ', err);
        this.loading = false;
      }
    });
  }

  isAdmin(): boolean {
    return this.authService.isAdmin(); // Assure-toi que cette méthode existe dans ton AuthService
  }

  ouvrirModal(faq?: Faq): void {
    this.isEditMode = !!faq;
    this.currentFaq = faq ? { ...faq } : this.getEmptyFaq();
    this.showModal = true;
  }

  enregistrer(): void {
    if (this.isEditMode && this.currentFaq.id) {
      this.faqService.updateFaq(this.currentFaq.id, this.currentFaq).subscribe({
        next: () => this.finaliserAction('FAQ mise à jour'),
        error: (err) => console.error(err)
      });
    } else {
      this.faqService.createFaq(this.currentFaq).subscribe({
        next: () => this.finaliserAction('FAQ créée'),
        error: (err) => console.error(err)
      });
    }
  }

  supprimer(id: number): void {
    if (confirm('Voulez-vous vraiment supprimer cette FAQ ?')) {
      this.faqService.deleteFaq(id).subscribe({
        next: () => this.chargerFaqs(),
        error: (err) => console.error(err)
      });
    }
  }

  private finaliserAction(message: string): void {
    this.showModal = false;
    this.chargerFaqs();
    this.currentFaq = this.getEmptyFaq();
  }

  private getEmptyFaq(): Faq {
    return {
      question: '',
      reponse: '',
      categorie: 'generale',
      ordre_affichage: 0,
      est_actif: true
    };
  }

  allerAPage(page: number): void {
    if (page >= 1 && page <= (this.paginationMeta?.last_page || 1)) {
      this.chargerFaqs(page);
    }
  }
}