import { Component, OnInit, OnDestroy, AfterViewInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { AuthService } from '../../../services/auth.service';
import { Subscription } from 'rxjs';

// Déclaration de la variable globale Jitsi chargée via index.html
declare var JitsiMeetExternalAPI: any;

@Component({
  selector: 'app-meeting',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './meeting.html',
  styleUrl: './meeting.css'
})
export class MeetingComponent implements OnInit, OnDestroy, AfterViewInit {

  api: any; // Instance de la réunion
  user: any = null; // Contiendra les données réelles après souscription
  private userSubscription?: Subscription;
  containerId: string = 'jitsi-container';

  constructor(private authService: AuthService) {
    console.log('[Meeting] 1. Initialisation du composant...');
  }

  ngOnInit(): void {
    console.log('[Meeting] 2. ngOnInit : Tentative de récupération de l\'utilisateur...');

    // CORRECTION : On souscrit à l'Observable pour extraire les données
    this.userSubscription = this.authService.getCurrentUser().subscribe({
      next: (userData) => {
        this.user = userData;
        console.log('[Meeting] 3. Données utilisateur extraites avec succès :', this.user);
      },
      error: (err) => {
        console.error('[Meeting] ERREUR : Impossible de récupérer l\'utilisateur', err);
      }
    });
  }

  ngAfterViewInit(): void {
    console.log('[Meeting] 4. La vue est prête. Attente du chargement du script Jitsi...');

    // Attendre que le script Jitsi soit chargé avec vérification récursive
    this.attendreJitsiEtLancer();
  }

  attendreJitsiEtLancer(tentatives: number = 0) {
    const maxTentatives = 20; // 20 tentatives = 10 secondes max

    console.log(`[Meeting] 5. Tentative ${tentatives + 1}/${maxTentatives} de chargement Jitsi...`);

    const element = document.getElementById(this.containerId);

    if (!element) {
      console.error(`[Meeting] ERREUR : L'élément #${this.containerId} est introuvable.`);
      return;
    }

    if (typeof JitsiMeetExternalAPI === 'undefined') {
      if (tentatives < maxTentatives) {
        // Réessayer après 500ms
        setTimeout(() => {
          this.attendreJitsiEtLancer(tentatives + 1);
        }, 500);
      } else {
        console.error("[Meeting] ERREUR : Le script Jitsi n'a pas pu être chargé après 10 secondes.");
      }
      return;
    }

    console.log('[Meeting] 6. Script Jitsi chargé avec succès. Lancement de la réunion...');
    this.lancerReunion();
  }

  lancerReunion() {
    try {
      const domain = 'meet.jit.si';

      // On prépare le nom à afficher (on vérifie plusieurs champs possibles)
      const displayUserName = this.user?.nom || this.user?.name || this.user?.prenom || 'Membre Agora';

      const options = {
        roomName: 'AgoraCooperative_Vip_Room_2024',
        width: '100%',
        height: 600,
        parentNode: document.getElementById(this.containerId),
        userInfo: {
          displayName: displayUserName
        },
        configOverwrite: {
          startWithAudioMuted: true,
          disableInviteFunctions: true,
          prejoinPageEnabled: false
        },
        interfaceConfigOverwrite: {
          TOOLBAR_BUTTONS: [
            'microphone', 'camera', 'closedcaptions', 'desktop', 'fullscreen',
            'fadedirectory', 'hangup', 'profile', 'chat', 'recording',
            'livestreaming', 'etherpad', 'sharedvideo', 'settings', 'raisehand',
            'videoquality', 'filmstrip', 'invite', 'feedback', 'stats', 'shortcuts',
            'tileview', 'videobackgroundblur', 'download', 'help', 'mute-everyone'
          ],
        }
      };

      console.log(`[Meeting] 7. Création de l'iframe Jitsi pour : ${displayUserName}`);
      this.api = new JitsiMeetExternalAPI(domain, options);

      // Debug des événements
      this.api.addEventListeners({
        videoConferenceJoined: () => console.log('[Meeting] OK : Conférence rejointe.'),
        videoConferenceLeft: () => console.log('[Meeting] OK : Conférence quittée.')
      });

    } catch (error) {
      console.error('[Meeting] ERREUR CRITIQUE Jitsi :', error);
    }
  }

  ngOnDestroy(): void {
    console.log('[Meeting] Fermeture du composant...');

    // Nettoyage de la souscription pour éviter les fuites de mémoire
    if (this.userSubscription) {
      this.userSubscription.unsubscribe();
    }

    // Destruction de la caméra et du micro
    if (this.api) {
      this.api.dispose();
      console.log('[Meeting] Caméra/Micro coupés.');
    }
  }
}