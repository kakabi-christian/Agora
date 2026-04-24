import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { LoginContent } from '../../contents/login-content/login-content';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, LoginContent],
  templateUrl: './login.html',
  styleUrl: './login.css',
})
export class Login {

}
