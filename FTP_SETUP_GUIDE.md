# Guide de Configuration FTP pour O2Switch

## 🎯 Secrets GitHub à configurer

Va sur GitHub : https://github.com/kakabi-christian/Agora/settings/secrets/actions

Ajoute ces secrets :

### 1. O2SWITCH_FTP_HOST
- **Nom** : `O2SWITCH_FTP_HOST`
- **Valeur** : L'adresse de ton serveur O2Switch (SANS le protocole)
- **Exemple** : `ton-domaine.com` ou `ssh.o2switch.net`
- **PAS** : `ftp://` ou `sftp://` (juste l'adresse)

### 2. O2SWITCH_FTP_USER
- **Nom** : `O2SWITCH_FTP_USER`
- **Valeur** : Ton nom d'utilisateur O2Switch
- **Exemple** : `ton-username`

### 3. O2SWITCH_FTP_PASSWORD
- **Nom** : `O2SWITCH_FTP_PASSWORD`
- **Valeur** : Ton mot de passe O2Switch

### 4. O2SWITCH_BACKEND_PATH
- **Nom** : `O2SWITCH_BACKEND_PATH`
- **Valeur** : Le chemin où déployer le backend Laravel
- **Exemple** : `/public_html/api` ou `/agora-backend`
- **Note** : SANS slash final

### 5. O2SWITCH_FRONTEND_PATH
- **Nom** : `O2SWITCH_FRONTEND_PATH`
- **Valeur** : Le chemin où déployer le frontend Angular
- **Exemple** : `/public_html` ou `/agora-frontend`
- **Note** : SANS slash final

## � Protocoles testés automatiquement

Le workflow teste automatiquement :
1. **FTPS** (FTP sécurisé) sur le port 21
2. **SFTP** (SSH File Transfer) sur le port 22 (fallback)

## �📋 Comment trouver tes informations O2Switch

### Via cPanel :
1. Connecte-toi à ton cPanel O2Switch
2. Va dans "Comptes FTP" ou "FTP Accounts"
3. Tu y trouveras :
   - **Serveur** : généralement `ton-domaine.com` ou `ssh.o2switch.net`
   - **Nom d'utilisateur** : ton nom d'utilisateur principal
   - **Mot de passe** : celui de ton compte O2Switch

### Via email de bienvenue O2Switch :
- Cherche l'email de bienvenue d'O2Switch
- Il contient les informations de connexion

### Informations typiques O2Switch :
- **Host** : `ssh.o2switch.net` ou ton domaine
- **User** : ton nom d'utilisateur O2Switch
- **Password** : ton mot de passe O2Switch
- **Protocole** : SFTP (port 22) ou FTPS (port 21)

## 🗂️ Structure de déploiement recommandée

```
/public_html/
├── index.html (Frontend Angular)
├── assets/
├── api/ (Backend Laravel)
│   ├── app/
│   ├── public/
│   │   └── index.php
│   ├── storage/
│   └── ...
```

## ✅ Avantages du FTP vs SSH

- ✅ Plus simple à configurer
- ✅ Pas besoin de clés SSH
- ✅ Supporté par défaut sur O2Switch
- ✅ Interface graphique disponible (FileZilla, etc.)
- ✅ Moins de problèmes d'authentification

## 🚀 Test du déploiement

Une fois les secrets configurés :

```bash
git add .
git commit -m "feat: switch to FTP deployment for O2Switch"
git push
```

Le workflow va :
1. Construire le backend Laravel
2. Construire le frontend Angular
3. Déployer via FTP sur O2Switch
4. Pas besoin de SSH ou de commandes serveur

## 🔧 Dépannage

Si le déploiement FTP échoue :

1. **Vérifier les credentials** : Teste avec FileZilla ou un client FTP
2. **Vérifier les chemins** : Assure-toi que les dossiers de destination existent
3. **Permissions** : O2Switch gère automatiquement les permissions via FTP

## 📁 Chemins typiques O2Switch

- **Racine web** : `/public_html/`
- **Sous-dossiers** : `/public_html/mon-app/`
- **API séparée** : `/public_html/api/`