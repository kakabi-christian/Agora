// src/models/don.model.ts
export interface Don {
    id?: number;                  // Optionnel car généré par le serveur
    nom_donateur: string;
    email_donateur: string;
    telephone: string;            // Format: 2376XXXXXXXX
    type: 'don' | 'collecte';     // Tu peux adapter les types selon tes besoins
    montant: number;
    message_donateur?: string;    // Optionnel
    anonyme: boolean;
    mode_paiement?: string;       // Sera 'Campay' côté backend
    statut_paiement?: 'en_attente' | 'succes' | 'echec';
    reference_paiement?: string;  // La référence reçue de Campay
    date_don?: string | Date;
    created_at?: string | Date;
    updated_at?: string | Date;
}

/**
 * Interface pour la réponse envoyée par ton PaymentController
 */
export interface CampayResponse {
    success: boolean;
    message?: string;
    data: {
        reference: string;
        token?: string;
        ussd_code?: string;
        operator?: string;
    };
    error?: any;
}