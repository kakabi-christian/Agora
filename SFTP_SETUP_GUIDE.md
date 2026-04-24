# Guide de Configuration SFTP pour O2Switch

## 🎯 Secrets GitHub à configurer

Va sur GitHub : https://github.com/kakabi-christian/Agora/settings/secrets/actions

Ajoute ces secrets :

### 1. O2SWITCH_FTP_HOST
- **Nom** : `O2SWITCH_FTP_HOST`
- **Valeur** : L'adresse RÉELLE de ton serveur O2Switch (SANS le protocole)
- **⚠️ IMPORTANT** : `ssh.o2switch.net` et `ftp.o2switch.net` N'EXISTENT PAS
- **Exemples réels** :
  - `cluster1.o2switch.net`
  - `cluster2.o2switch.net`
  - `srv001.o2switch.fr`
  - `srv002.o2switch.fr`
  - `ton-domaine.com` (si tu as un domaine personnalisé)

**🔍 Comment trouver la bonne adresse :**
1. **Regarde ton email de bienvenue O2Switch** (le plus fiable)
2. **Connecte-toi à ton cPanel → section "SSH Access"**
3. **Ou va dans ton espace client O2Switch → Mes services**
4. **Voir le guide complet** : `TROUVER_ADRESSE_O2SWITCH.md`

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
- **Host** : `ssh.o2switch.net` (le plus courant)
- **Alternatives** : `ftp.o2switch.net` ou ton domaine
- **User** : ton nom d'utilisateur O2Switch
- **Password** : ton mot de passe O2Switch
- **Port** : 22 (SFTP)
- **Protocole** : SFTP (SSH File Transfer Protocol)

### Test de connectivité local :
Pour tester depuis ta machine :
```bash
# Test de connectivité
nc -zv ssh.o2switch.net 22

# Test SFTP
sftp -P 22 ton-username@ssh.o2switch.net
```

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

### Erreur "Operation timed out" :
1. **Vérifier l'adresse du serveur** :
   - Essaie `ssh.o2switch.net` en premier
   - Puis `ftp.o2switch.net`
   - Enfin ton domaine personnalisé

2. **Vérifier la connectivité réseau** :
   - Assure-toi d'être sur une connexion stable (WiFi/Ethernet)
   - Certains réseaux d'entreprise bloquent le port 22

3. **Tester localement** :
   ```bash
   # Test de ping
   ping ssh.o2switch.net
   
   # Test du port 22
   telnet ssh.o2switch.net 22
   
   # Test SFTP complet
   sftp ton-username@ssh.o2switch.net
   ```

### Erreur d'authentification :
1. **Vérifier les credentials** dans GitHub Secrets
2. **Tester avec un client SFTP** (FileZilla, WinSCP)
3. **Vérifier que le compte n'est pas suspendu**

### Erreur de chemin :
1. **Vérifier que les dossiers existent** sur O2Switch
2. **Créer les dossiers manuellement** via cPanel ou SFTP
3. **Utiliser des chemins absolus** : `/public_html/api`

## 📁 Chemins typiques O2Switch

- **Racine web** : `/public_html/`
- **Sous-dossiers** : `/public_html/mon-app/`
- **API séparée** : `/public_html/api/`