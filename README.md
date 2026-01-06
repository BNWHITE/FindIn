# FindIN - Plateforme de Gestion des Compétences

Application web pour la gestion des compétences en entreprise.

## 🚀 Fonctionnalités

- **Gestion des utilisateurs** : Employés, Managers, RH, Administrateurs
- **Compétences** : Création, évaluation, suivi des niveaux
- **Dashboard** : 7 pages interactives (Accueil, Projets, Réunions, Documents, Certifications, Messages, Profil)
- **Recherche** : Recherche de collaborateurs par compétences
- **Authentification** : Connexion standard + Google OAuth

## 📦 Installation

### Prérequis
- PHP 8.0+
- MySQL 5.7+ (XAMPP recommandé)
- Navigateur moderne

### Configuration

1. **Cloner le projet**
```bash
git clone https://github.com/BNWHITE/FindIn.git
cd FindIn
```

2. **Configurer la base de données**
- Démarrer MySQL (XAMPP)
- Créer la base \`gestion_competences\`
- Importer le schéma SQL

3. **Configurer les variables** (optionnel)
```bash
export DB_HOST=127.0.0.1
export DB_NAME=gestion_competences
export DB_USER=root
export DB_PASS=
```

## 🚀 Démarrage

```bash
php start.php
# ou
php -S localhost:8000 router.php
```

Accéder à : http://localhost:8000

## 🔐 Comptes de test

| Email | Mot de passe | Rôle |
|-------|--------------|------|
| admin@findin.fr | admin123 | Admin |
| test@findin.fr | test123 | Employé |

## 🏗️ Architecture

\`\`\`
FindIN/
├── index.php          # Point d'entrée
├── router.php         # Routage des URLs
├── start.php          # Démarrage serveur
├── config/            # Configuration
├── controllers/       # Contrôleurs MVC
├── models/            # Modèles de données
├── views/             # Templates HTML
├── assets/            # CSS, JS, Images
└── uploads/           # Fichiers uploadés
\`\`\`

## 📝 Licence

MIT License - Voir [License.md](License.md)
