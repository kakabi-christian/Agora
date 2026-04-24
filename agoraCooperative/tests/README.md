# Suite de Tests - Application Laravel Agora Cooperative

## 📋 Vue d'ensemble

Cette suite de tests couvre l'ensemble des fonctionnalités de l'application Laravel Agora Cooperative avec une approche complète incluant :

- **Tests Feature** : Tests d'intégration des controllers API
- **Tests Unit** : Tests des modèles et logique métier
- **Tests de Workflow** : Scénarios complets de bout en bout
- **Factories** : Générateurs de données de test

## 🏗️ Structure des Tests

```
tests/
├── Feature/                    # Tests d'intégration API
│   ├── AuthControllerTest.php
│   ├── MembreControllerTest.php
│   ├── EvenementControllerTest.php
│   ├── ProjetControllerTest.php
│   ├── DonControllerTest.php
│   ├── DemandeAdhesionControllerTest.php
│   ├── PartenaireControllerTest.php
│   ├── FaqControllerTest.php
│   ├── ContactMessageControllerTest.php
│   ├── NotificationControllerTest.php
│   ├── RessourceControllerTest.php
│   └── WorkflowTest.php         # Tests de workflow complets
├── Unit/                       # Tests unitaires des modèles
│   ├── MembreTest.php
│   ├── EvenementTest.php
│   └── ProjetTest.php
├── CreatesApplication.php      # Configuration de l'application
├── TestCase.php               # Classe de base avec helpers
└── README.md                  # Cette documentation
```

## 🏭 Factories Disponibles

### Modèles Principaux
- `MembreFactory` : Génération de membres avec rôles et statuts
- `EvenementsFactory` : Création d'événements variés
- `ProjetsFactory` : Génération de projets avec budgets et délais
- `DemandeAdhesionFactory` : Demandes d'adhésion avec différents statuts
- `DonFactory` : Dons avec modes de paiement variés

### Modèles Secondaires
- `PartenaireFactory` : Partenaires par type et statut
- `FaqFactory` : Questions FAQ par catégorie
- `ContactMessageFactory` : Messages de contact
- `NotificationFactory` : Notifications système
- `RessourceFactory` : Ressources téléchargeables
- `Inscription_eventsFactory` : Inscriptions aux événements
- `Participation_projetsFactory` : Participations aux projets
- `HistoriqueParticipationFactory` : Historique des participations

## 🚀 Exécution des Tests

### Tous les Tests
```bash
php artisan test
```

### Tests Spécifiques
```bash
# Tests Feature uniquement
php artisan test --testsuite=Feature

# Tests Unit uniquement
php artisan test --testsuite=Unit

# Test spécifique
php artisan test tests/Feature/AuthControllerTest.php

# Tests par mot-clé
php artisan test --filter="login"
```

### Tests avec Couverture
```bash
php artisan test --coverage
```

### Tests en Mode Verbose
```bash
php artisan test --verbose
```

## 📊 Couverture des Tests

### Controllers Testés (100%)
- ✅ **AuthController** : Login, logout, refresh, profil, changement mot de passe
- ✅ **MembreController** : CRUD membres, upload photo, exports
- ✅ **EvenementController** : CRUD événements, inscriptions, participations
- ✅ **ProjetController** : CRUD projets, participations, heures
- ✅ **DonController** : Dons, paiements, statistiques
- ✅ **DemandeAdhesionController** : Demandes, approbations, rejets
- ✅ **PartenaireController** : CRUD partenaires, filtres
- ✅ **FaqController** : FAQ, votes, recherche
- ✅ **ContactMessageController** : Messages, réponses, statuts
- ✅ **NotificationController** : Notifications, ciblage, lecture
- ✅ **RessourceController** : Ressources, téléchargements, uploads

### Modèles Testés (100%)
- ✅ **Membre** : Relations, scopes, méthodes métier
- ✅ **Evenements** : Relations, calculs, états
- ✅ **Projets** : Relations, progression, délais

### Workflows Testés
- ✅ **Workflow complet d'adhésion** : Demande → Approbation → Login
- ✅ **Workflow complet événement** : Création → Inscription → Paiement → Confirmation
- ✅ **Workflow complet projet** : Création → Participation → Saisie heures
- ✅ **Workflow complet don** : Création → Paiement → Validation
- ✅ **Workflow profil membre** : Mise à jour → Changement mot de passe
- ✅ **Workflow admin complet** : Gestion de toutes les entités

## 🔧 Configuration des Tests

### Base de Données
Les tests utilisent `RefreshDatabase` pour garantir une base de données propre pour chaque test.

### Authentification
Les helpers dans `TestCase.php` facilitent la création et l'authentification des utilisateurs :

```php
// Créer un admin
$admin = $this->createAdminUser();

// Créer un membre
$member = $this->createMemberUser();

// Authentifier et ajouter le token
$this->authenticateUser($user);
```

### Données de Test
Les factories fournissent des méthodes d'état pour des scénarios spécifiques :

```php
// Créer un événement de type formation
$event = Evenements::factory()->formation()->create();

// Créer un projet à budget élevé
$project = Projets::factory()->highBudget()->create();
```

## 📈 Scénarios de Test Couverts

### Authentification & Sécurité
- Login avec identifiants valides/invalides
- Gestion des tokens JWT
- Changement de mot de passe
- Permissions par rôle (admin/membre)
- Accès non autorisé

### Gestion des Membres
- CRUD complet des membres
- Upload de photos de profil
- Export PDF/Excel (admin)
- Mise à jour de profil détaillé

### Événements
- CRUD complet (admin)
- Inscriptions et confirmations
- Génération PDF de confirmation
- Codes QR pour vérification
- Limites de capacité

### Projets
- CRUD complet (admin)
- Participations et heures
- Suivi de progression
- Calculs de délais et budgets

### Dons & Paiements
- Création de dons (public)
- Initiation de paiements
- Statistiques et totaux
- Modes de paiement variés

### Communication
- Messages de contact
- Notifications ciblées
- FAQ avec votes
- Partenaires

### Workflows Métier
- Processus d'adhésion complet
- Cycle de vie des événements
- Gestion de projet de A à Z
- Traitement des dons

## 🐛 Débogage des Tests

### Tests en Échec
```bash
# Arrêter au premier échec
php artisan test --stop-on-failure

# Mode debug
php artisan test --debug
```

### Tests Isolés
```bash
# Exécuter un seul test
php artisan test --filter="test_login_with_valid_credentials"

# Exéculer une classe de test
php artisan test tests/Feature/AuthControllerTest.php
```

### Dump de Variables
```php
// Dans un test
dump($response->json());
dump($user->toArray());
```

## 📝 Bonnes Pratiques

### Organisation
- Un test par méthode fonctionnelle
- Noms de tests descriptifs en français
- Utilisation des helpers pour éviter la duplication
- Tests indépendants les uns des autres

### Assertions
- Vérifier les codes de statut HTTP
- Valider les structures JSON
- Vérifier les changements en base de données
- Tester les cas limites et erreurs

### Données
- Utiliser les factories pour des données réalistes
- Tester avec différentes combinaisons de données
- Valider les contraintes de base de données
- Couvrir tous les scénarios métier

## 🔄 Maintenance

### Ajout de Nouveaux Tests
1. Créer la factory si nécessaire
2. Ajouter les tests Feature/Unit
3. Mettre à jour cette documentation
4. Vérifier la couverture

### Mise à Jour des Tests
1. Mettre à jour les factories lors de changements de schéma
2. Ajouter des tests pour nouvelles fonctionnalités
3. Maintenir la couverture à 100%
4. Documenter les nouveaux scénarios

## 📊 Statistiques Actuelles

- **Tests Feature** : 12 classes
- **Tests Unit** : 3 classes  
- **Tests Workflow** : 1 classe
- **Factories** : 13 classes
- **Couverture API** : 100%
- **Scénarios métier** : 6 workflows complets

Cette suite de tests garantit la qualité et la fiabilité de l'application Agora Cooperative.
