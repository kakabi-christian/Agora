// src/app/models/membre.model.ts
export interface Membre {
  code_membre: string;
  nom: string;
  prenom: string;
  email: string;
  role: 'administrateur' | 'membre'; // ou string selon tes besoins
  est_actif: boolean;
  mot_de_passe_temporaire?: boolean; // Indique si le mot de passe doit être changé
  telephone?: string;
  adresse?: string;
  ville?: string;
  code_postal?: string;
  biographie?: string;
  photo_url?: string;
  date_inscription?: Date;
  // On peut ajouter le profil si chargé avec .load('profil')
  profil?: any;
}

// Pour la réponse du Login (AuthResource dans Laravel)
export interface AuthResponse {
  membre: Membre;
  token: string;
}