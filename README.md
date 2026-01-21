# 🎯 FindIN - Plateforme de Gestion des Compétences

[![PHP](https://img.shields.io/badge/PHP-8.0+-purple.svg)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange.svg)](https://mysql.com)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](License.md)

**FindIN** est une plateforme de gestion et valorisation des compétences en entreprise. Elle permet d'identifier, valider et développer les talents au sein des équipes.

## ✨ Fonctionnalités

- **Gestion des utilisateurs** : Employés, Managers, RH, Administrateurs
- **Compétences** : Déclaration, validation par managers, niveaux 1-5
- **Dashboard** : 7 pages interactives (Accueil, Projets, Réunions, Documents, Certifications, Messages, Profil)
- **Recherche** : Recherche de collaborateurs par compétences et niveau
- **Authentification** : Connexion standard + Google OAuth

## 🚀 Démarrage Rapide

### Prérequis
- PHP 8.0+
- MySQL 5.7+ (XAMPP recommandé)
- Apache avec mod_rewrite

### Installation

```bash
# 1. Cloner le projet
git clone https://github.com/BNWHITE/FindIn.git
cd FindIn

# 2. Configurer la base de données
php scripts/setup/setup_mysql.php

# 3. Démarrer le serveur
php -S localhost:8000 -t public
```

Accéder à : http://localhost:8000

### 🔐 Comptes de test

| Email | Mot de passe | Rôle |
|-------|--------------|------|
| admin@findin.fr | admin123 | Admin |
| test@findin.fr | test123 | Employé |

## 🏗️ Structure du Projet

```
FindIn/
├── public/              # Point d'entrée web (index.php)
│   └── assets/          # CSS, JS, images
├── src/
│   ├── Controllers/     # Contrôleurs MVC
│   ├── Models/          # Modèles PDO (Singleton)
│   ├── Views/           # Templates PHP
│   └── Config/          # Configuration
├── database/            # Schémas et migrations SQL
├── docs/                # Documentation complète
└── scripts/             # Scripts utilitaires
```

## 📚 Documentation

- [Guide d'installation](docs/guides/INSTALLATION.md)
- [Guide de développement](docs/guides/DEVELOPMENT.md)
- [Architecture technique](docs/technical/ARCHITECTURE.md)
- [Documentation base de données](docs/technical/DATABASE.md)

## 🛠️ Technologies

| Composant | Technologie |
|-----------|-------------|
| Backend | PHP 8.x, MVC Pattern |
| Base de données | MySQL 8.0 / SQLite |
| Frontend | HTML5, CSS3, JavaScript |
| Serveur | Apache 2.4 |

## 🔒 Sécurité

- ✅ Mots de passe hashés (bcrypt)
- ✅ Protection XSS (échappement HTML)
- ✅ Protection SQL Injection (requêtes préparées PDO)
- ✅ Sessions sécurisées
- ✅ Validation des entrées

## 📝 License

MIT License - voir [License.md](License.md)

---

**Fait avec ❤️ par l'équipe FindIN**
