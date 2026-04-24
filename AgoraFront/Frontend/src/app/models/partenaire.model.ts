export interface Partenaire {
  code_partenaire?: string;
  nom: string;
  type: string; // ex: 'sponsor', 'donateur', 'institutionnel'
  description?: string;
  logo_url?: string;
  site_web?: string;
  contact_nom?: string;
  contact_email?: string;
  contact_telephone?: string;
  niveau_partenariat: string; // ex: 'principal', 'secondaire'
  date_debut?: string;
  date_fin?: string;
  est_actif: boolean;
  ordre_affichage: number;
  created_at?: string;
  updated_at?: string;
}

export interface PartenaireResponse {
  message: string;
  partenaires: {
    data: Partenaire[];
    current_page: number;
    last_page: number;
    total: number;
    per_page: number;
  };
}