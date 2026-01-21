# 📥 Guide d'Installation FindIN

## 🎯 Prérequis

### Logiciels requis
- **PHP** 8.0 ou supérieur
- **MySQL** 5.7+ ou **MariaDB** 10.4+
- **Composer** (optionnel, pour les dépendances futures)
- **Apache** 2.4+ avec mod_rewrite activé

### Extensions PHP requises
```bash
php -m | grep -E 'pdo|pdo_mysql|mbstring|openssl|json'
```

Extensions nécessaires :
- `pdo`
- `pdo_mysql`
- `mbstring`
- `openssl`
- `json`
- `fileinfo`

---

## 🚀 Installation avec XAMPP (Recommandé pour développement)

### 1. Installation de XAMPP

**macOS** :
```bash
# Télécharger depuis https://www.apachefriends.org/
# Installer XAMPP dans /Applications/XAMPP
```

**Windows** :
```bash
# Télécharger depuis https://www.apachefriends.org/
# Installer XAMPP dans C:\xampp
```

### 2. Configuration du projet

```bash
# Cloner le projet dans htdocs
cd /Applications/XAMPP/htdocs  # macOS
# cd C:\xampp\htdocs           # Windows

git clone https://github.com/votre-repo/FindIn.git findin
cd findin
```

### 3. Configuration du Virtual Host

**Éditer le fichier hosts** :
```bash
# macOS/Linux
sudo nano /etc/hosts

# Windows (en tant qu'administrateur)
notepad C:\Windows\System32\drivers\etc\hosts
```

Ajouter :
```
127.0.0.1 findin.local
```

**Configurer Apache Virtual Host** :
```bash
# macOS
sudo nano /Applications/XAMPP/etc/extra/httpd-vhosts.conf

# Windows
notepad C:\xampp\apache\conf\extra\httpd-vhosts.conf
```

Ajouter :
```apache
<VirtualHost *:80>
    ServerName findin.local
    DocumentRoot "/Applications/XAMPP/htdocs/findin"
    
    <Directory "/Applications/XAMPP/htdocs/findin">
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog "logs/findin-error.log"
    CustomLog "logs/findin-access.log" common
</VirtualHost>
```

**Activer les virtual hosts dans httpd.conf** :
```bash
# Décommenter la ligne :
Include etc/extra/httpd-vhosts.conf
```

### 4. Configuration de la base de données

**Créer la base de données** :
```bash
# Lancer XAMPP et démarrer MySQL
# Ouvrir phpMyAdmin : http://localhost/phpmyadmin

# Ou via ligne de commande :
mysql -u root -p
CREATE DATABASE gestion_competences CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

**Importer le schéma** :
```bash
# Via phpMyAdmin : Importer le fichier database/schema.sql

# Ou via ligne de commande :
mysql -u root -p gestion_competences < database/schema.sql
```

### 5. Configuration de l'application

**Copier le fichier de configuration** :
```bash
cp config/database.php.example config/database.php
```

**Éditer les paramètres** :
```php
// config/database.php
define('DB_TYPE', 'mysql');
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'gestion_competences');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### 6. Redémarrer Apache

```bash
# Via XAMPP Manager
# Ou en ligne de commande :

# macOS
sudo /Applications/XAMPP/xamppfiles/bin/apachectl restart

# Linux
sudo /opt/lampp/lampp restart

# Windows (dans le panneau XAMPP)
Cliquer sur "Restart" pour Apache
```

### 7. Vérification de l'installation

Ouvrir dans le navigateur :
```
http://findin.local
```

**Compte administrateur par défaut** :
- Email : `admin@findin.com`
- Mot de passe : `password`

⚠️ **Important** : Changez ce mot de passe après la première connexion !

---

## 🐳 Installation avec Docker (Production)

```bash
# À venir dans une future version
```

---

## ✅ Vérification de l'installation

### Tests de connexion

**1. Tester la page d'accueil** :
```bash
curl http://findin.local
```

**2. Tester la base de données** :
```bash
php -r "
require 'config/database.php';
require 'models/Database.php';
echo 'Connexion réussie à la base de données';
"
```

**3. Tester les permissions** :
```bash
# Vérifier les permissions d'écriture
touch public/uploads/test.txt
rm public/uploads/test.txt
```

---

## 🔧 Dépannage

### Erreur 404 - Page non trouvée

**Problème** : mod_rewrite non activé

**Solution** :
```bash
# Vérifier que mod_rewrite est activé dans httpd.conf
LoadModule rewrite_module modules/mod_rewrite.so
```

### Erreur de connexion à la base de données

**Problème** : Identifiants incorrects

**Solution** :
1. Vérifier les identifiants dans `config/database.php`
2. Vérifier que MySQL est démarré
3. Tester la connexion : `mysql -u root -p`

### Erreur de permissions

**Problème** : Impossible d'écrire des fichiers

**Solution** :
```bash
# macOS/Linux
chmod -R 755 public/uploads
chmod -R 755 database

# Ou donner les droits à l'utilisateur Apache
chown -R _www:_www public/uploads
```

---

## 📱 Accès mobile (développement)

Pour tester sur mobile en réseau local :

```bash
# Trouver votre IP locale
# macOS
ifconfig | grep "inet " | grep -v 127.0.0.1

# Linux
hostname -I

# Windows
ipconfig
```

Ajouter dans httpd-vhosts.conf :
```apache
ServerAlias 192.168.1.XXX  # Votre IP locale
```

Accéder depuis mobile : `http://192.168.1.XXX`

---

## 🎓 Prochaines étapes

Après l'installation :
1. Lire le [Guide de développement](DEVELOPMENT.md)
2. Explorer l'[Architecture](../technical/ARCHITECTURE.md)
3. Consulter la [Documentation de l'API](../api/ENDPOINTS.md)
