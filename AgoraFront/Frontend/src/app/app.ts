import { Component, signal } from '@angular/core';
import { RouterOutlet, Router } from '@angular/router'; // Ajoute Router
import { Header } from './header/header';
import { Footer } from './footer/footer';
import { FormsModule } from '@angular/forms';
import { HttpClientModule } from '@angular/common/http';
import { CommonModule } from '@angular/common'; // Ajoute CommonModule pour le *ngIf

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [Header, RouterOutlet, Footer, FormsModule, HttpClientModule, CommonModule],
  templateUrl: './app.html',
  styleUrl: './app.css',
})
export class App {
  protected readonly title = signal('agoraCooperativefrontend');

  constructor(private router: Router) {}

  // Cette fonction retourne 'true' si nous NE sommes PAS en mode admin
  showPublicLayout(): boolean {
    return !this.router.url.startsWith('/admin') && !this.router.url.startsWith('/membre');
  }
}