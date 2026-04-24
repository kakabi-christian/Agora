import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { PartenaireService} from '../../services/partenaires';
import { Partenaire } from '../../models/partenaire.model';

@Component({
  selector: 'app-partenaire-content',
  standalone: true,
  imports: [CommonModule],
 templateUrl: './partenaire-content.html',
  styleUrl: './partenaire-content.css',
})
export class PartenaireContentComponent implements OnInit {
  partenaires: Partenaire[] = [];
  loading: boolean = true;

  constructor(private partenaireService: PartenaireService) {}

  ngOnInit(): void {
    this.fetchPartenaires();
  }

  fetchPartenaires(): void {
    this.partenaireService.getPartenaires().subscribe({
      next: (res) => {
        this.partenaires = res.partenaires.data;
        this.loading = false;
      },
      error: (err) => {
        console.error('Erreur de chargement', err);
        this.loading = false;
      }
    });
  }
}