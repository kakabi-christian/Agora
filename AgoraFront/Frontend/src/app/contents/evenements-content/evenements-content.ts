import { CommonModule } from '@angular/common';
import { Component, OnInit, OnDestroy } from '@angular/core';
import { RouterModule } from '@angular/router';
import { EvenementService } from '../../services/evenement.service';
import { Evenement } from '../../models/evenement.model';

@Component({
  selector: 'app-evenements-content',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './evenements-content.html',
  styleUrls: ['./evenements-content.css'],
})
export class EvenementsContent implements OnInit, OnDestroy {
  evenements: Evenement[] = [];
  evenementsFiltres: Evenement[] = [];
  filtre: 'tous' | 'planifie' | 'termine' = 'tous';
  isLoading = true;
  error: string | null = null;

  readonly backendUrl = 'http://localhost:8000/';

  constructor(private evenementService: EvenementService) {}

  ngOnInit(): void {
    this.loadEvenements();
  }

  loadEvenements(): void {
    this.isLoading = true;
    this.evenementService.getEvenements().subscribe({
      next: (res) => {
        this.evenements = res.evenements.data;
        this.applyFiltre();
        this.isLoading = false;
      },
      error: (err) => {
        console.error(err);
        this.error = 'Impossible de charger les événements.';
        this.isLoading = false;
      }
    });
  }

  setFiltre(f: 'tous' | 'planifie' | 'termine') {
    this.filtre = f;
    this.applyFiltre();
  }

  applyFiltre() {
    if (this.filtre === 'tous') {
      this.evenementsFiltres = this.evenements;
    } else {
      this.evenementsFiltres = this.evenements.filter(e => e.statut === this.filtre);
    }
  }

  getEventImage(imageUrl: string | null | undefined): string {
  if (!imageUrl) return 'assets/images/default-event.jpg';
  return imageUrl.startsWith('http') ? imageUrl : `${this.backendUrl}${imageUrl}`;
}


  getStatutClass(statut: string): string {
    const map: any = {
      planifie: 'status-planifie',
      en_cours: 'status-en-cours',
      termine: 'status-termine'
    };
    return map[statut] || '';
  }

  formatDate(dateStr: string) {
    const date = new Date(dateStr);
    const months = ['JAN','FÉV','MAR','AVR','MAI','JUN','JUL','AOÛ','SEP','OCT','NOV','DÉC'];
    return { day: date.getDate().toString().padStart(2,'0'), month: months[date.getMonth()] };
  }

  ngOnDestroy(): void {}

}
