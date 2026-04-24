import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ProjetService } from '../../../services/projet.service'; 
import { EvenementService } from '../../../services/evenement.service';
import { DonService } from '../../../services/don.service'; // Import du service de dons
import { Evenement } from '../../../models/evenement.model';
import { interval, Subscription, forkJoin } from 'rxjs';

interface CountdownItem {
  remaining: { d: number; h: number; m: number; s: number };
  isExpired: boolean;
}

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './home.html',
  styleUrl: './home.css',
})
export class HomeAdmin implements OnInit, OnDestroy {
  projetsCountdown: (any & CountdownItem)[] = [];
  evenementsCountdown: (Evenement & CountdownItem)[] = [];
  
  // Nouvelles variables pour les dons
  revenuTotal: number = 0;
  devise: string = 'FCFA';
  totalDonsCount: number = 0;

  loading: boolean = true;
  private timerSubscription?: Subscription;

  constructor(
    private projetService: ProjetService,
    private evenementService: EvenementService,
    private donService: DonService // Injection du service
  ) {}

  ngOnInit(): void {
    this.fetchAllData();
    
    this.timerSubscription = interval(1000).subscribe(() => {
      this.updateAllTimers();
    });
  }

  fetchAllData(): void {
    this.loading = true;

    // Ajout des dons dans le forkJoin pour un chargement parallélisé
    forkJoin({
      projets: this.projetService.getDeadlines(),
      evenements: this.evenementService.getUpcomingEvents(),
      statsDons: this.donService.getTotalGeneral() // Appel à ta nouvelle route
    }).subscribe({
      next: (res: any) => {
        // 1. Traitement des Projets
        this.projetsCountdown = (res.projets?.projets || []).map((p: any) => ({
          ...p,
          remaining: { d: 0, h: 0, m: 0, s: 0 },
          isExpired: false
        }));

        // 2. Traitement des Événements
        this.evenementsCountdown = (res.evenements?.evenements || []).map((e: Evenement) => ({
          ...e,
          remaining: { d: 0, h: 0, m: 0, s: 0 },
          isExpired: false
        }));

        // 3. Mise à jour des revenus déchiffrés
        if (res.statsDons) {
          this.revenuTotal = res.statsDons.total_general;
          this.devise = res.statsDons.devise || 'FCFA';
          this.totalDonsCount = res.statsDons.count;
          console.log(`[HomeAdmin] 💰 Revenu total récupéré : ${this.revenuTotal} ${this.devise}`);
        }

        this.updateAllTimers(); 
        this.loading = false;
      },
      error: (err) => {
        console.error('Erreur lors du chargement des données globales:', err);
        this.loading = false;
      }
    });
  }

  updateAllTimers(): void {
    const now = new Date().getTime();

    this.projetsCountdown.forEach(p => {
      this.calculateTime(p, p.date_fin_prevue, now);
    });

    this.evenementsCountdown.forEach(e => {
      this.calculateTime(e, e.date_debut, now);
    });
  }

  private calculateTime(item: CountdownItem, targetDate: string, now: number): void {
    const target = new Date(targetDate).getTime();
    const distance = target - now;

    if (distance > 0) {
      item.remaining = {
        d: Math.floor(distance / (1000 * 60 * 60 * 24)),
        h: Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)),
        m: Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60)),
        s: Math.floor((distance % (1000 * 60)) / 1000)
      };
      item.isExpired = false;
    } else {
      item.isExpired = true;
    }
  }

  ngOnDestroy(): void {
    if (this.timerSubscription) {
      this.timerSubscription.unsubscribe();
    }
  }
}