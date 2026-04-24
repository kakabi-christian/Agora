import { Component } from '@angular/core';
import { PartenaireContentComponent } from '../../contents/partenaire-content/partenaire-content';

@Component({
  selector: 'app-partenaires',
  standalone: true,
  imports: [PartenaireContentComponent], // On importe le contenu ici
  templateUrl: './partenaires.html',
  styleUrl: './partenaires.css',
})
export class Partenaires {
  // Cette page sert de structure pour accueillir le contenu
}