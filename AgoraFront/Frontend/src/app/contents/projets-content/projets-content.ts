import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { 
  DashboardService, 
  DashboardStats, 
  Partenaire,
  ProjetRecent,
  EvenementProchain
} from '../../services/dashboard.service';
import { interval, Subscription } from 'rxjs';
@Component({
  selector: 'app-projets-content',
  imports: [CommonModule],
  templateUrl: './projets-content.html',
  styleUrl: './projets-content.css',
})
export class ProjetsContent {
  projetsTermines: ProjetRecent[] = [];

     // Statistiques
      stats: DashboardStats | null = null;
      partenaires: Partenaire[] = [];
      
      // États de chargement
      isLoadingStats = true;
      isLoadingPartenaires = true;
      
      // Gestion erreurs
      errorStats: string | null = null;
      errorPartenaires: string | null = null;
    
      // Carousel
      currentSlide = 0;
      totalSlides = 3;
      carouselInterval?: any;
    
      // Animation des compteurs
      private counterSubscription?: Subscription;
      animatedCounters = {
        membres: 0,
        annees: 0,
        projets: 0,
        engagement: 0,
        totalDons: 0,
        donateurs: 0,
        projetsFinalises: 0
      };
    
      constructor(private dashboardService: DashboardService) {}
    
      ngOnInit(): void {
        this.loadStats();
        this.loadPartenaires();
        this.initCarousel();
      }
    
      ngOnDestroy(): void {
        if (this.carouselInterval) {
          clearInterval(this.carouselInterval);
        }
        if (this.counterSubscription) {
          this.counterSubscription.unsubscribe();
        }
      }
    
      /**
       * Charge les statistiques depuis l'API
       */
      loadStats(): void {
  this.isLoadingStats = true;
  this.errorStats = null;

  this.dashboardService.getHomeStats().subscribe({
    next: (response) => {
      this.stats = response.stats;

      // ✅ Filtrer uniquement les projets terminés
      this.projetsTermines = this.stats.projets.recents.filter(
        projet => projet.statut === 'termine'
      );

      this.isLoadingStats = false;

      setTimeout(() => this.animateCounters(), 500);
    },
    error: (error) => {
      console.error('Erreur lors du chargement des stats:', error);
      this.errorStats = 'Impossible de charger les statistiques.';
      this.isLoadingStats = false;
    }
  });
}

    
      /**
       * Charge les partenaires actifs
       */
      loadPartenaires(): void {
        this.isLoadingPartenaires = true;
        this.errorPartenaires = null;
    
        this.dashboardService.getPartenairesActifs().subscribe({
          next: (response) => {
            this.partenaires = response.partenaires;
            this.isLoadingPartenaires = false;
          },
          error: (error) => {
            console.error('Erreur lors du chargement des partenaires:', error);
            this.errorPartenaires = 'Impossible de charger les partenaires.';
            this.isLoadingPartenaires = false;
          }
        });
      }
    
      /**
       * Animation progressive des compteurs
       */
      animateCounters(): void {
        if (!this.stats) return;
    
        const duration = 2000; // 2 secondes
        const steps = 60; // 60 frames (60fps)
        const stepDuration = duration / steps;
    
        const targets = {
          membres: this.stats.membres.total,
          annees: 18, // Valeur fixe
          projets: this.stats.projets.total,
          engagement: 100, // Valeur fixe
          totalDons: this.stats.dons.total_montant,
          donateurs: this.stats.dons.nombre_donateurs,
          projetsFinalises: this.stats.dons.projets_finances
        };
    
        let currentStep = 0;
    
        this.counterSubscription = interval(stepDuration).subscribe(() => {
          currentStep++;
          const progress = currentStep / steps;
    
          // Utiliser une fonction d'ease-out pour un effet plus naturel
          const easeProgress = 1 - Math.pow(1 - progress, 3);
    
          this.animatedCounters = {
            membres: Math.floor(targets.membres * easeProgress),
            annees: Math.floor(targets.annees * easeProgress),
            projets: Math.floor(targets.projets * easeProgress),
            engagement: Math.floor(targets.engagement * easeProgress),
            totalDons: Math.floor(targets.totalDons * easeProgress),
            donateurs: Math.floor(targets.donateurs * easeProgress),
            projetsFinalises: Math.floor(targets.projetsFinalises * easeProgress)
          };
    
          if (currentStep >= steps) {
            // S'assurer que les valeurs finales sont exactes
            this.animatedCounters = {
              membres: targets.membres,
              annees: targets.annees,
              projets: targets.projets,
              engagement: targets.engagement,
              totalDons: targets.totalDons,
              donateurs: targets.donateurs,
              projetsFinalises: targets.projetsFinalises
            };
            
            this.counterSubscription?.unsubscribe();
          }
        });
      }
    
      /**
       * Initialise le carousel des témoignages
       */
      initCarousel(): void {
        this.carouselInterval = setInterval(() => {
          this.nextSlide();
        }, 5000);
      }
    
      /**
       * Passe au slide suivant
       */
      nextSlide(): void {
        this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
      }
    
      /**
       * Va à un slide spécifique
       */
      moveToSlide(index: number): void {
        this.currentSlide = index;
      }
    
      /**
       * Retourne l'URL de l'image (gestion fallback)
       */
      getImageUrl(path: string | null): string {
        return this.dashboardService.getImageUrl(path);
      }
    
      /**
       * Retourne le badge de statut du projet
       */
      getProjetBadgeClass(statut: string): string {
        switch (statut) {
          case 'en_cours':
            return 'project-badge';
          case 'termine':
            return 'project-badge completed';
          default:
            return 'project-badge';
        }
      }
    
      /**
       * Retourne le texte du badge
       */
      getProjetBadgeText(statut: string): string {
        switch (statut) {
          case 'en_cours':
            return 'En cours';
          case 'termine':
            return 'Complété';
          case 'approuve':
            return 'Approuvé';
          default:
            return statut;
        }
      }
    
      /**
       * Formate une date en français
       */
      formatDate(dateString: string): { day: string; month: string } {
        const date = new Date(dateString);
        const months = ['JAN', 'FÉV', 'MAR', 'AVR', 'MAI', 'JUN', 
                       'JUL', 'AOÛ', 'SEP', 'OCT', 'NOV', 'DÉC'];
        
        return {
          day: date.getDate().toString().padStart(2, '0'),
          month: months[date.getMonth()]
        };
      }
    
      /**
       * Formatte un montant en XAF
       */
      formatMontant(montant: number): string {
        return montant.toLocaleString('fr-FR', { 
          minimumFractionDigits: 0,
          maximumFractionDigits: 0 
        });
      }
    
      /**
       * Retry en cas d'erreur
       */
      retry(): void {
        if (this.errorStats) {
          this.loadStats();
        }
        if (this.errorPartenaires) {
          this.loadPartenaires();
        }
      }
    
}
