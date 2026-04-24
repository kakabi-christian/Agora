export interface DemandeAdhesion {
  id?: number;
  nom: string;
  prenom: string;
  email: string;
  telephone: string;
  adresse: string;
  ville: string;
  code_postal: string;
  date_naissance: string;
  profession: string;
  motivation: string;
  competences: any;
  statut?: 'en_attente' | 'approuvee' | 'rejetee';
  commentaire_admin?: string;
  date_demande?: string;
}

export interface DemandeAdhesionResponse {
  data: DemandeAdhesion;
}

export interface DemandeAdhesionListResponse {
  data: DemandeAdhesion[];
  meta?: any; // Pour la pagination Laravel
  links?: any;
}
