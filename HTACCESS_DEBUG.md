# Dépannage des redirections .htaccess

## 🔍 Problèmes courants

### 1. Les routes Angular ne fonctionnent pas
**Symptôme** : 404 sur `/login`, `/dashboard`, etc.

**Solutions** :
1. Vérifier que le fichier `.htaccess` est dans `/public_html/`
2. Vérifier que `mod_rewrite` est activé sur O2Switch
3. Tester avec la version simple

### 2. L'API Laravel ne répond pas
**Symptôme** : 404 sur `/api/login`, `/api/membres`

**Solutions** :
1. Vérifier que le fichier `.htaccess` est dans `/public_html/api/`
2. Vérifier que le dossier `public/` existe dans `/api/`
3. Vérifier les permissions

## 🧪 Tests de diagnostic

### Test 1 : Vérifier les fichiers
```bash
# Via FTP ou cPanel File Manager
/public_html/.htaccess (doit exister)
/public_html/api/.htaccess (doit exister)
/public_html/api/public/index.php (doit exister)
```

### Test 2 : Tester les redirections
```bash
# Frontend - doit retourner index.html
curl -I https://cdiu8226.odns.fr/login

# API - doit retourner une réponse Laravel
curl -I https://cdiu8226.odns.fr/api/login
```

### Test 3 : Vérifier mod_rewrite
Ajoute temporairement dans `.htaccess` :
```apache
# Test mod_rewrite
RewriteEngine On
RewriteRule ^test$ /index.html [L]
```
Puis teste : `https://cdiu8226.odns.fr/test`

## 🔧 Versions alternatives

### Version simple Frontend
Si la version complète ne fonctionne pas, utilise :
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_URI} !^/api
RewriteRule ^(.*)$ /index.html [L]
```

### Version simple Backend
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ public/index.php/$1 [QSA,L]

Header always set Access-Control-Allow-Origin "*"
Header always set Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS"
Header always set Access-Control-Allow-Headers "Content-Type, Authorization"
```

## 🔄 Changer de version

Pour utiliser les versions simples :
1. Édite `.github/workflows/deploy-o2switch.yaml`
2. Décommente les lignes avec `-simple`
3. Commente les lignes normales
4. Push les changements

## 📋 Checklist de dépannage

- [ ] Fichier `.htaccess` présent dans `/public_html/`
- [ ] Fichier `.htaccess` présent dans `/public_html/api/`
- [ ] Dossier `public/` existe dans `/api/`
- [ ] Fichier `index.php` existe dans `/api/public/`
- [ ] `mod_rewrite` activé sur le serveur
- [ ] Permissions correctes (644 pour .htaccess)
- [ ] Pas d'erreurs dans les logs Apache

## 🆘 Support O2Switch

Si rien ne fonctionne :
1. Contacte le support O2Switch
2. Demande l'activation de `mod_rewrite`
3. Demande les logs d'erreur Apache
4. Vérifie la configuration PHP

## 🎯 URLs de test

Après correction :
- ✅ `https://cdiu8226.odns.fr/` → Page d'accueil Angular
- ✅ `https://cdiu8226.odns.fr/login` → Page de login Angular
- ✅ `https://cdiu8226.odns.fr/api/login` → Endpoint Laravel
- ✅ `https://cdiu8226.odns.fr/api/membres` → API Laravel