import { Component } from '@angular/core';
import { CommonModule } from '@angular/common'; // Pour le ngIf, ngClass
import { RouterLink, RouterModule } from '@angular/router'; // Pour le routerLink

@Component({
  selector: 'app-header',
  standalone: true, // Très important en Angular moderne
  imports: [CommonModule, RouterModule], // On ajoute les modules nécessaires ici
  templateUrl: './header.html',
  styleUrl: './header.css',
})
export class Header {
  // Cette variable servira plus tard à savoir si l'utilisateur est connecté
  isLoggedIn: boolean = false; 
  userRole: 'administrateur' | 'membre' | 'visiteur' = 'visiteur';

  constructor() {}
}