<?php

namespace Database\Seeders;

use App\Models\Ressources;
use App\Models\Membre;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class RessourcesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Récupérer un admin pour l'uploader
        $admin = Membre::where('role', 'administrateur')->first();
        
        if (!$admin) {
            $this->command->error('Aucun admin trouvé. Veuillez créer un admin d\'abord.');
            return;
        }

        // Créer les dossiers de stockage si nécessaire
        $categories = ['administratif', 'comptable', 'juridique', 'technique', 'pedagogique'];
        foreach ($categories as $categorie) {
            Storage::disk('ressources')->makeDirectory($categorie);
        }

        $ressources = [
            // Documents administratifs
            [
                'titre' => 'Règlement intérieur de la coopérative',
                'type' => 'reglement',
                'categorie' => 'administratif',
                'description' => 'Document officiel définissant les règles de fonctionnement interne de la coopérative, les droits et devoirs des membres.',
                'est_public' => false,
                'necessite_authentification' => true,
            ],
            [
                'titre' => 'Statuts de la coopérative',
                'type' => 'document',
                'categorie' => 'juridique',
                'description' => 'Statuts juridiques de la coopérative Agora, définissant sa structure, ses objectifs et son mode de gouvernance.',
                'est_public' => false,
                'necessite_authentification' => true,
            ],
            [
                'titre' => 'Formulaire d\'adhésion',
                'type' => 'formulaire',
                'categorie' => 'administratif',
                'description' => 'Formulaire à remplir pour devenir membre de la coopérative. Disponible en format PDF modifiable.',
                'est_public' => true,
                'necessite_authentification' => false,
            ],
            
            // Documents comptables
            [
                'titre' => 'Rapport financier annuel 2025',
                'type' => 'rapport',
                'categorie' => 'comptable',
                'description' => 'Bilan financier de l\'année 2025 incluant les revenus, dépenses et investissements de la coopérative.',
                'est_public' => false,
                'necessite_authentification' => true,
            ],
            [
                'titre' => 'Budget prévisionnel 2026',
                'type' => 'document',
                'categorie' => 'comptable',
                'description' => 'Prévisions budgétaires pour l\'année 2026 avec répartition par poste de dépense.',
                'est_public' => false,
                'necessite_authentification' => true,
            ],
            
            // Documents techniques
            [
                'titre' => 'Guide d\'utilisation de la plateforme',
                'type' => 'document',
                'categorie' => 'technique',
                'description' => 'Manuel d\'utilisation complet de la plateforme Agora pour les membres.',
                'est_public' => false,
                'necessite_authentification' => true,
            ],
            [
                'titre' => 'Procédures de sécurité informatique',
                'type' => 'document',
                'categorie' => 'technique',
                'description' => 'Bonnes pratiques et procédures à suivre pour garantir la sécurité des données de la coopérative.',
                'est_public' => false,
                'necessite_authentification' => true,
            ],
            
            // Documents pédagogiques
            [
                'titre' => 'Introduction au coopérativisme',
                'type' => 'document',
                'categorie' => 'pedagogique',
                'description' => 'Document d\'introduction aux principes et valeurs du mouvement coopératif.',
                'est_public' => true,
                'necessite_authentification' => false,
            ],
            [
                'titre' => 'Formation à la gouvernance participative',
                'type' => 'document',
                'categorie' => 'pedagogique',
                'description' => 'Support de formation sur les méthodes de prise de décision collective et de gouvernance partagée.',
                'est_public' => false,
                'necessite_authentification' => true,
            ],
            [
                'titre' => 'Charte des valeurs de la coopérative',
                'type' => 'document',
                'categorie' => 'pedagogique',
                'description' => 'Document présentant les valeurs fondamentales et l\'éthique de la coopérative Agora.',
                'est_public' => true,
                'necessite_authentification' => false,
            ],
            
            // Autres documents
            [
                'titre' => 'Procès-verbal de l\'assemblée générale 2025',
                'type' => 'rapport',
                'categorie' => 'administratif',
                'description' => 'Compte-rendu officiel de l\'assemblée générale annuelle de 2025.',
                'est_public' => false,
                'necessite_authentification' => true,
            ],
            [
                'titre' => 'Politique de confidentialité et RGPD',
                'type' => 'document',
                'categorie' => 'juridique',
                'description' => 'Document détaillant la politique de protection des données personnelles conforme au RGPD.',
                'est_public' => true,
                'necessite_authentification' => false,
            ],
        ];

        foreach ($ressources as $ressourceData) {
            // Créer un fichier PDF factice
            $filename = \Illuminate\Support\Str::slug($ressourceData['titre']) . '_' . time() . '.pdf';
            $cheminFichier = $ressourceData['categorie'] . '/' . $filename;
            
            // Contenu PDF factice (header PDF minimal)
            $pdfContent = "%PDF-1.4\n1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n3 0 obj\n<< /Type /Page /Parent 2 0 R /Resources << /Font << /F1 << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> >> >> /MediaBox [0 0 612 792] /Contents 4 0 R >>\nendobj\n4 0 obj\n<< /Length 44 >>\nstream\nBT\n/F1 12 Tf\n100 700 Td\n(" . $ressourceData['titre'] . ") Tj\nET\nendstream\nendobj\nxref\n0 5\n0000000000 65535 f\n0000000009 00000 n\n0000000058 00000 n\n0000000115 00000 n\n0000000317 00000 n\ntrailer\n<< /Size 5 /Root 1 0 R >>\nstartxref\n408\n%%EOF";
            
            Storage::disk('ressources')->put($cheminFichier, $pdfContent);
            
            // Créer l'enregistrement en base
            Ressources::create([
                'titre' => $ressourceData['titre'],
                'type' => $ressourceData['type'],
                'categorie' => $ressourceData['categorie'],
                'chemin_fichier' => $cheminFichier,
                'nom_fichier' => $ressourceData['titre'] . '.pdf',
                'extension_fichier' => 'pdf',
                'description' => $ressourceData['description'],
                'date_publication' => now(),
                'date_expiration' => null,
                'est_public' => $ressourceData['est_public'],
                'necessite_authentification' => $ressourceData['necessite_authentification'],
                'nombre_telechargements' => rand(0, 50),
                'code_membre' => $admin->code_membre,
            ]);
        }

        $this->command->info('✅ ' . count($ressources) . ' ressources créées avec succès !');
    }
}
