export interface Contact {
    id?: number;                  // Optionnel car généré par le backend
    nom_expediteur: string;
    email_expediteur: string;
    sujet: string;
    message: string;
    type_demande: 'information' | 'support' | 'partenariat' | 'autre';
    telephone?: string;           // Optionnel (?) car nullable dans ton backend
    code_membre?: string;         // Optionnel
    lu?: boolean;
    date_lu?: string | Date;
    created_at?: string | Date;
}