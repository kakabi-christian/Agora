import { Routes } from '@angular/router';
import { Home } from './pages/home/home';
import { Partenaires } from './pages/partenaires/partenaires';
import { Login } from './pages/login/login';
import { Member } from './pages/member/member';
import { HomeAdmin } from './pages/admin/home/home';
import { Project } from './pages/admin/project/project';
import { Partners } from './pages/admin/partenaire/partenaire';
import { FaqComponent } from './pages/admin/faq/faq';
import { MeetingComponent } from './pages/admin/meeting/meeting';
import { Dons } from './pages/dons/dons';
import { Contact } from './pages/contact/contact';
import { AdminContactComponent } from './pages/admin/contact/contact';
import { Notification } from './pages/admin/notification/notification';
// import { Faq } from './pages/faq/faq';
import { Evenement } from './pages/admin/evenement/evenement';
// Importation du Layout Admin
import { AdminLayout } from './components/admin/admin-layout/admin-layout';

import { MemberLayout } from './components/member/member-layout/member-layout';
import { TableauDeBordMembre } from './pages/membre/tableau-de-bord/tableau-de-bord';
import { ProfilMembre } from './pages/membre/profil/profil';
import { HistoriqueMembre } from './pages/membre/historique/historique';
import { RessourcesMembre } from './pages/membre/ressources/ressources';
import { EvenementsMembre } from './pages/membre/evenements/evenements';
import { EvenementDetailMembre } from './pages/membre/evenements/detail/detail';
import { ProjetsMembre } from './pages/membre/projets/projets';
import { ProjetDetailMembre } from './pages/membre/projets/detail/detail';

import { authGuard } from './guards/auth.guard';
import { adminGuard } from './guards/admin.guard';

// Importation des vrais composants Admin que nous avons créés
import { Profil } from './pages/admin/profil/profil';
import { Demandes } from './pages/admin/demande/demande';
import { Projets } from './pages/projets/projets';
import { Evenements } from './pages/evenements/evenements';
import { Retrait } from './pages/admin/retrait/retrait';
import { Faq } from './pages/faq/faq';
import { ForgotPassword } from './pages/auth/forgot-password/forgot-password';
import { ResetPassword } from './pages/auth/reset-password/reset-password';
import { VerifyOtp } from './pages/auth/verify-otp/verify-otp';
import { Stats } from './pages/admin/stats/stats';
import { StatsGraphe } from './pages/admin/stats-graphe/stats-graphe';
import { MembresAdmin } from './pages/admin/membres/membres';
import { RessourcesAdmin } from './pages/admin/ressources/ressources';
export const routes: Routes = [
  // --- ROUTES PUBLIQUES ---
  { path: '', component: Home },
  { path: 'partenaires', component: Partenaires },
  { path: 'login', component: Login },
  // --- NOUVELLES ROUTES MOT DE PASSE OUBLIÉ ---
  { path: 'forgot-password', component: ForgotPassword },
  { path: 'verify-otp', component: VerifyOtp },
  { path: 'reset-password', component: ResetPassword },

  { path: 'devenir-membre', component: Member },
  { path: 'Projets', component: Projets },
  { path: 'Evenements', component: Evenements },
  { path: 'member', redirectTo: 'devenir-membre', pathMatch: 'full' },
  { path: 'contact', component: Contact },
  { path: 'dons', component: Dons },
  { path: 'faq', component: Faq },

  // --- ROUTES ESPACE MEMBRE (protégées) ---
  {
    path: 'membre',
    component: MemberLayout,
    canActivate: [authGuard],
    children: [
      { path: 'tableau-de-bord', component: TableauDeBordMembre },
      { path: 'profil', component: ProfilMembre },
      { path: 'historique', component: HistoriqueMembre },
      { path: 'ressources', component: RessourcesMembre },
      { path: 'evenements', component: EvenementsMembre },
      { path: 'evenements/:code', component: EvenementDetailMembre },
      { path: 'projets', component: ProjetsMembre },
      { path: 'meetings', component: MeetingComponent },
      { path: 'projets/:id', component: ProjetDetailMembre },
      { path: '', redirectTo: 'tableau-de-bord', pathMatch: 'full' }
    ]
  },

  // --- ROUTES ADMINISTRATION (Dashboard) ---
  {
    path: 'admin',
    component: AdminLayout, // Contient ta Sidebar et le <router-outlet>
    canActivate: [adminGuard],
    children: [
      // Page d'accueil du dashboard (le message de bienvenue)
      { path: 'dashboard', component: HomeAdmin },

      // Gestion des demandes d'adhésion (Fiches détaillées)
      { path: 'demandes', component: Demandes },

      // Gestion des membres
      { path: 'membres', component: MembresAdmin },

      // Gestion des ressources
      { path: 'ressources', component: RessourcesAdmin },

      // Profil personnel de l'administrateur connecté
      { path: 'profil', component: Profil },
      { path: 'projects', component: Project },
      { path: 'partner', component: Partners },
      { path: 'faq', component: FaqComponent },
      { path: 'meetings', component: MeetingComponent },
      { path: 'contact', component: AdminContactComponent },
      { path: 'notifications', component: Notification },
      { path: 'evenements', component: Evenement },
      { path: 'retrait', component: Retrait },
      { path: 'stats', component: Stats },
      { path: 'stats-graph', component: StatsGraphe },

      // Redirection si l'admin tape juste /admin
      { path: '', redirectTo: 'dashboard', pathMatch: 'full' }
    ]
  },

  // Redirection par défaut pour les erreurs 404 (doit toujours être en dernier)
  { path: '**', redirectTo: '' }
];