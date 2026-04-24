# Guide de Configuration SFTP pour O2Switch

## 🎯 Secrets GitHub à configurer

Va sur GitHub : https://github.com/kakabi-christian/Agora/settings/secrets/actions

Ajoute ces secrets :

### 1. O2SWITCH_FTP_HOST
- **Nom** : `O2SWITCH_FTP_HOST`
- **Valeur** : L'adresse de ton serveur O2Switch (SANS le protocole)
- **Exemple** : `ssh.o2switch.net` ou `ton-domaine.com`
- **PAS** : `sftp://` (juste l'adresse)

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

## 🔧 Protocole utilisé

Le workflow utilise maintenant **SFTP uniquement** (SSH File Transfer Protocol) sur le port 22, car O2Switch ne supporte pas le FTP classique.

## 📋 Comment trouver tes informations O2Switch

### Via cPanel :
1. Connecte-toi à ton cPanel O2Switch
2. Les informations de connexion sont les mêmes que pour SSH :
   - **Serveur** : `ssh.o2switch.net` ou ton domaine
   - **Nom d'utilisateur** : ton nom d'utilisateur O2Switch
   - **Mot de passe** : ton mot de passe O2Switch
   - **Port** : 22 (SFTP)

### Via email de bienvenue O2Switch :
- Cherche l'email de bienvenue d'O2Switch
- Il contient les informations de connexion SSH/SFTP

### Informations typiques O2Switch :
- **Host** : `ssh.o2switch.net`
- **User** : ton nom d'utilisateur O2Switch
- **Password** : ton mot de passe O2Switch
- **Port** : 22 (SFTP)
- **Protocole** : SFTP (SSH File Transfer Protocol)

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

## ✅ Avantages du SFTP

- ✅ Protocole sécurisé (chiffré)
- ✅ Supporté nativement par O2Switch
- ✅ Même authentification que SSH
- ✅ Plus fiable que FTP classique
- ✅ Transfert de fichiers rapide

## 🚀 Test du déploiement

Une fois les secrets configurés :

```bash
git add .
git commit -m "feat: switch to SFTP-only deployment for O2Switch"
git push
```

Le workflow va :
1. Construire le backend Laravel
2. Construire le frontend Angular
3. Déployer via SFTP sur O2Switch (port 22)
4. Utiliser les mêmes credentials que SSH

## 🔧 Dépannage

Si le déploiement SFTP échoue :

1. **Vérifier les credentials** : Teste avec un client SFTP (FileZilla, WinSCP)
2. **Vérifier l'host** : Utilise `ssh.o2switch.net` ou ton domaine
3. **Vérifier les chemins** : Assure-toi que les dossiers de destination existent
4. **Port** : Utilise le port 22 (SFTP/SSH)

## 📁 Chemins typiques O2Switch

- **Racine web** : `/public_html/`
- **Sous-dossiers** : `/public_html/mon-app/`
- **API séparée** : `/public_html/api/`