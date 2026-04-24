# Comment trouver l'adresse correcte de ton serveur O2Switch

## 🔍 Le problème
Les adresses `ssh.o2switch.net` et `ftp.o2switch.net` n'existent pas. Il faut trouver la vraie adresse de ton serveur O2Switch.

## 📧 Méthode 1 : Email de bienvenue O2Switch

1. **Cherche l'email de bienvenue** d'O2Switch dans ta boîte mail
2. **Mots-clés à chercher** : "SSH", "FTP", "serveur", "hébergement"
3. **Informations à noter** :
   - Adresse du serveur (ex: `cluster1.o2switch.net`, `srv123.o2switch.fr`)
   - Nom d'utilisateur
   - Mot de passe

## 🖥️ Méthode 2 : cPanel O2Switch

1. **Connecte-toi à ton cPanel** O2Switch
2. **Cherche la section "SSH Access"** ou "Accès SSH"
3. **Note l'adresse du serveur** affichée
4. **Ou va dans "FTP Accounts"** pour voir les informations de connexion

## 🌐 Méthode 3 : Tester les patterns courants

Teste ces adresses dans l'ordre :

### Format cluster :
- `cluster1.o2switch.net`
- `cluster2.o2switch.net`
- `cluster3.o2switch.net`

### Format serveur :
- `srv001.o2switch.fr`
- `srv002.o2switch.fr`
- `srv123.o2switch.fr` (remplace 123 par ton numéro)

### Format domaine :
- `ton-domaine.com` (si tu as un domaine)
- `ssh.ton-domaine.com`

## 🧪 Test local pour vérifier

Une fois que tu as une adresse, teste-la :

```bash
# Test de ping
ping cluster1.o2switch.net

# Test du port SSH/SFTP
telnet cluster1.o2switch.net 22

# Test SFTP complet
sftp ton-username@cluster1.o2switch.net
```

## 📝 Exemples d'adresses O2Switch réelles

Voici des exemples d'adresses que d'autres utilisateurs O2Switch utilisent :

- `cluster1.o2switch.net`
- `cluster2.o2switch.net`
- `srv001.o2switch.fr`
- `srv002.o2switch.fr`
- `web123.o2switch.net`

## ⚡ Action rapide

**Si tu ne trouves pas l'email :**

1. **Connecte-toi à ton espace client O2Switch**
2. **Va dans "Mes services" → "Hébergement"**
3. **Clique sur ton hébergement**
4. **Cherche "Informations de connexion" ou "SSH/FTP"**

## 🔧 Une fois trouvé

Quand tu as la bonne adresse :

1. **Mets à jour le secret GitHub** `O2SWITCH_FTP_HOST`
2. **Utilise JUSTE l'adresse** (ex: `cluster1.o2switch.net`)
3. **PAS de préfixe** comme `sftp://` ou `ssh://`

## 📞 Dernier recours

Si rien ne fonctionne :
- **Contacte le support O2Switch**
- **Demande les informations SSH/SFTP**
- **Ils te donneront l'adresse exacte**