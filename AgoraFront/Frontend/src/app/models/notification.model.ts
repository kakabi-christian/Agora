export interface Notification {
  id?: number;
  code_membre: string | null;
  titre: string;
  contenu: string;
  type: 'email' | 'alerte_site' | 'notification_mobile' | 'sms';
  categorie: 'systeme' | 'evenement' | 'projet' | 'administratif' | 'urgence';
  statut: 'non_lu' | 'lu';
  objet_relie_type?: string;
  objet_relie_code?: string;
  date_envoi?: string | Date;
  date_lecture?: string | Date | null;
  lien_action?: string;
  est_urgent: boolean;
  created_at?: string;
  updated_at?: string;
  // Si tu charges la relation 'membre' via le controller
  membre?: any; 
}

export interface UnreadCountResponse {
  unread_count: number;
}