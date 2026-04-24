import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HomeContent } from '../../contents/home-content/home-content';
@Component({
  selector: 'app-home',
  standalone: true,
  imports: [CommonModule, HomeContent],
  templateUrl: './home.html',
  styleUrl: './home.css',
})
export class Home {

}
