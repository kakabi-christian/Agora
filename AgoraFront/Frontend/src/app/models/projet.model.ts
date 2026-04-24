export interface Projet {
  id?: number;
  nom: string;
  description?: string;
  type: 'agricole' | 'social' | 'environnemental' | 'educatif' | 'autre';
  statut: 'propose' | 'en_etude' | 'approuve' | 'en_cours' | 'termine' | 'annule';
  date_debut?: string | Date;
  date_fin_prevue?: string | Date;
  date_fin_reelle?: string | Date;
  budget_estime?: number;
  budget_reel?: number;
  coordinateur?: string;
  objectifs?: string[]; // Casté en array par Laravel
  resultats?: string[]; // Casté en array par Laravel
  image_url?: string;
  notes?: string;
  est_public: boolean;
  created_at?: string;
  updated_at?: string;
}

// Pour gérer la pagination retournée par Laravel
export interface ProjetResponse {
  message: string;
  projets: {
    data: Projet[];
    current_page: number;
    last_page: number;
    total: number;
    per_page: number;
  };
}
