export interface Evenement {
  // Clé primaire personnalisée (string)
  code_evenement?: string;

  titre: string;
  description: string;

  // Utilisation de string pour la réception API (ISO 8601)
  date_debut: string;
  date_fin: string;

  lieu: string;
  adresse?: string;
  ville?: string;

  frais_inscription: number;
  places_disponibles?: number;

  // Types basés sur ta migration Laravel
  type: 'assemblee' | 'atelier' | 'reunion' | 'formation' | 'autre';

  // Statuts basés sur ta migration Laravel
  statut: 'planifie' | 'en_cours' | 'termine' | 'annule';

  image_url?: string;
  image?: File; // Uniquement pour l'upload via FormData

  instructions?: string;
  paiement_obligatoire: boolean;

  created_at?: string;
  updated_at?: string;

  // --- Propriétés calculées pour le Dashboard (Frontend uniquement) ---
  remaining?: {
    d: number; // Jours
    h: number; // Heures
    m: number; // Minutes
    s: number; // Secondes
  };
  isExpired?: boolean;
}

/**
 * Interface pour la réponse paginée classique de Laravel
 */
export interface EvenementResponse {
  message: string;
  evenements: {
    data: Evenement[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    next_page_url?: string;
    prev_page_url?: string;
  };
}

/**
 * Interface pour la méthode de Dashboard (getUpcomingEvents)
 */
export interface UpcomingEventsResponse {
  evenements: Evenement[];
  server_time: string;
}
