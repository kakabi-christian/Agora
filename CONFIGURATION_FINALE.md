# Configuration finale pour O2Switch

## ✅ Adresse serveur trouvée : `cdiu8226.odns.fr`

## 🔐 Secrets GitHub à configurer

Va sur : https://github.com/kakabi-christian/Agora/settings/secrets/actions

### Secrets de connexion O2Switch

| Nom du secret | Valeur à utiliser | Description |
|---------------|-------------------|-------------|
| `O2SWITCH_FTP_HOST` | `cdiu8226.odns.fr` | Adresse du serveur (SANS sftp://) |
| `O2SWITCH_FTP_USER` | `ton-username` | Nom d'utilisateur O2Switch |
| `O2SWITCH_FTP_PASSWORD` | `ton-mot-de-passe` | Mot de passe O2Switch |

### Secrets de chemins de déploiement

| Nom du secret | Valeur recommandée | Description |
|---------------|-------------------|-------------|
| `O2SWITCH_BACKEND_PATH` | `/public_html/api` | Chemin backend (SANS slash final) |
| `O2SWITCH_FRONTEND_PATH` | `/public_html` | Chemin frontend (SANS slash final) |

### Secrets Laravel (pour le backend)

| Nom du secret | Valeur exemple | Description |
|---------------|----------------|-------------|
| `LARAVEL_APP_KEY` | `base64:abc123...` | Clé Laravel (générer avec `php artisan key:generate --show`) |
| `APP_URL` | `https://cdiu8226.odns.fr/api` | URL de l'API backend |
| `DB_HOST` | `localhost` | Hôte base de données |
| `DB_PORT` | `3306` | Port base de données |
| `DB_DATABASE` | `agora_db` | Nom de la base de données |
| `DB_USERNAME` | `db_user` | Utilisateur base de données |
| `DB_PASSWORD` | `db_password` | Mot de passe base de données |

### Secrets optionnels (pour les URLs finales)

| Nom du secret | Valeur avec ton domaine | Description |
|---------------|-------------------------|-------------|
| `BACKEND_URL` | `https://cdiu8226.odns.fr/api` | URL finale du backend |
| `FRONTEND_URL` | `https://cdiu8226.odns.fr` | URL finale du frontend |

## 🧪 Test de connectivité local

Pour vérifier que l'adresse fonctionne depuis ta machine :

```bash
# Test de ping
ping cdiu8226.odns.fr

# Test du port SSH/SFTP
telnet cdiu8226.odns.fr 22

# Test SFTP complet
sftp ton-username@cdiu8226.odns.fr
```

## 📁 Structure de déploiement sur O2Switch

```
/public_html/
├── index.html (Frontend Angular)
├── assets/
├── main-*.js
├── styles-*.css
├── api/ (Backend Laravel)
│   ├── app/
│   ├── public/
│   │   └── index.php
│   ├── storage/
│   ├── bootstrap/
│   └── ...
```

## 🚀 Déploiement

Une fois tous les secrets configurés :

```bash
git add .
git commit -m "feat: configure correct O2Switch server address"
git push
```

Le workflow va :
1. ✅ Tester la connectivité à `cdiu8226.odns.fr:22`
2. ✅ Construire le backend Laravel
3. ✅ Construire le frontend Angular
4. ✅ Déployer via SFTP sur ton serveur O2Switch

## 🎯 URLs finales après déploiement

- **Frontend** : https://cdiu8226.odns.fr
- **Backend API** : https://cdiu8226.odns.fr/api

## ✅ Checklist finale

- [ ] Secret `O2SWITCH_FTP_HOST` = `cdiu8226.odns.fr`
- [ ] Secret `O2SWITCH_FTP_USER` = ton nom d'utilisateur
- [ ] Secret `O2SWITCH_FTP_PASSWORD` = ton mot de passe
- [ ] Secret `O2SWITCH_BACKEND_PATH` = `/public_html/api`
- [ ] Secret `O2SWITCH_FRONTEND_PATH` = `/public_html`
- [ ] Tous les secrets Laravel configurés
- [ ] Push des changements sur GitHub
- [ ] Workflow lancé et réussi