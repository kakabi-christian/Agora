import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms'; // Ajouté pour la réponse
import { ContactService } from '../../../services/contact.service';

@Component({
  selector: 'app-admin-contact',
  standalone: true,
  imports: [CommonModule, FormsModule], // Ajout de FormsModule
  templateUrl: './contact.html',
  styleUrl: './contact.css',
})
export class AdminContactComponent implements OnInit {
  messages: any[] = [];
  isLoading = true;
  errorMessage = '';
  successMessage = '';
  showSuccessToast = false;

  // Gestion de la modal de réponse
  showReplyModal = false;
  selectedMessage: any = null;
  replyText: string = '';
  isSendingReply = false;

  constructor(private contactService: ContactService) {}

  ngOnInit(): void {
    this.loadMessages();
  }

  loadMessages(): void {
    this.isLoading = true;
    this.contactService.getMessages().subscribe({
      next: (response) => {
        // CORRECTION NG02200 : Laravel Paginate renvoie les données dans .data
        this.messages = response.data; 
        this.isLoading = false;
      },
      error: (err) => {
        this.errorMessage = "Impossible de charger les messages.";
        this.isLoading = false;
      }
    });
  }

  // --- RÉPONDRE AU MESSAGE ---
  openReplyModal(message: any): void {
    this.selectedMessage = message;
    this.replyText = '';
    this.showReplyModal = true;
  }

  closeReplyModal(): void {
    this.showReplyModal = false;
    this.selectedMessage = null;
  }

  sendReply(): void {
    if (!this.replyText.trim()) return;

    this.isSendingReply = true;
    this.contactService.replyToMessage(this.selectedMessage.id, this.replyText).subscribe({
      next: () => {
        this.triggerToast('Réponse envoyée par email avec succès !');
        this.selectedMessage.lu = true; // Marquer comme lu localement
        this.selectedMessage.statut = 'traité';
        this.isSendingReply = false;
        this.closeReplyModal();
      },
      error: (err) => {
        this.errorMessage = "Erreur lors de l'envoi de la réponse.";
        this.isSendingReply = false;
      }
    });
  }

  // --- SUPPRIMER ---
  onDeleteMessage(id: number): void {
    if (confirm('Voulez-vous vraiment supprimer ce message ?')) {
      this.contactService.deleteMessage(id).subscribe({
        next: () => {
          this.messages = this.messages.filter(m => m.id !== id);
          this.triggerToast('Message supprimé avec succès');
        }
      });
    }
  }

  // --- MARQUER LU/NON LU ---
  toggleStatus(message: any): void {
    const newStatus = !message.lu;
    this.contactService.updateMessage(message.id, { lu: newStatus }).subscribe({
      next: () => {
        message.lu = newStatus;
        this.triggerToast(newStatus ? 'Marqué comme lu' : 'Marqué comme non lu');
      }
    });
  }

  private triggerToast(msg: string): void {
    this.successMessage = msg;
    this.showSuccessToast = true;
    setTimeout(() => this.showSuccessToast = false, 3000);
  }
}

// src/app/pages/admin/contact/contact.ts