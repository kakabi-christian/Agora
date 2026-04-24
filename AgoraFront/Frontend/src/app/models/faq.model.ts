export interface Faq {
  id?: number;
  question: string;
  reponse: string;
  categorie: 'generale' | 'membres' | 'projets' | 'dons' | 'evenements' | 'administratif';
  ordre_affichage: number;
  est_actif: boolean;
  nombre_vues?: number;
  nombre_utile?: number;
  nombre_inutile?: number;
  created_at?: string;
  updated_at?: string;
}

// Pour gérer la réponse paginée de Laravel
export interface FaqPagination {
  data: Faq[];
  current_page: number;
  last_page: number;
  total: number;
  per_page: number;
}