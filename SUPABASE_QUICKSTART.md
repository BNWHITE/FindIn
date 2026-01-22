# 🚀 FindIN - Démarrage Rapide avec Supabase

**Status**: ✅ Prêt pour déploiement  
**Date**: 21 janvier 2026  
**Base de données**: Supabase PostgreSQL

---

## ⚡ Démarrage en 5 Étapes

### Étape 1: Créer les tables dans Supabase

```bash
# Ouvre Supabase
# https://app.supabase.com → Projet ugdkdrdgxtfwsehzpmvm

# SQL Editor → Nouvelle requête
# Copie le contenu de : database/supabase_setup.sql
# Exécute
```

**Résultat attendu :**
```
✅ Tables créées
✅ Indexes créés  
✅ Triggers activés
✅ Données de test insérées
```

---

### Étape 2: Tester la connexion PHP

```bash
cd /Users/s.sy/Documents/ISEP/FindIn

# Lancer un serveur de test
php -S localhost:8000 public/index.php

# Teste la connexion
curl http://localhost:8000/test_supabase.php
```

**Résultat attendu :**
```
✅ Connexion Supabase réussie !
✅ Utilisateurs dans la DB : 1
✅ Compétences trouvées : 8
```

---

### Étape 3: Ajouter les routes API dans `public/index.php`

Ouvre `public/index.php` et copie tous les cases depuis `API_ROUTES_TO_ADD.php` dans le switch principal, avant le `default:` final.

**Vérification :**
```bash
curl http://localhost:8000/api/competences/list
# Doit retourner JSON avec liste des compétences
```

---

### Étape 4: Tester les APIs

```bash
# Lister les compétences
curl http://localhost:8000/api/competences/list | jq

# Ajouter une compétence
curl -X POST http://localhost:8000/api/competences/add \
  -H "Content-Type: application/json" \
  -d '{
    "nom": "Kubernetes",
    "description": "Orchestration conteneurs",
    "type_competence": "technique"
  }' | jq

# Lister les utilisateurs
curl http://localhost:8000/api/utilisateurs/list | jq

# Lister les projets
curl http://localhost:8000/api/projets/list | jq
```

---

### Étape 5: Accéder au Dashboard

```
http://localhost:8000/dashboard
```

**Identifiants de test :**
```
Email    : admin@findin.fr
Password : (À définir ou vérifier dans Supabase)
```

---

## 📊 Structure Complète

### Tables Créées (dans Supabase)

| Table | Colonnes | Description |
|-------|----------|-------------|
| `utilisateurs` | id, email, prenom, nom, role, manager_id | Gestion utilisateurs |
| `competences` | id, nom, description, type | Inventaire compétences |
| `competences_utilisateurs` | id, user_id, competence_id, niveau_declare, niveau_valide | Mapping user ↔ compétence |
| `projets` | id, nom, description, responsable_id, statut, date_debut, date_fin | Gestion projets |
| `projets_utilisateurs` | id, projet_id, user_id, role_projet | Qui travaille sur quel projet |
| `reunions` | id, employe_id, manager_id, titre, date_reunion, notes | Réunions 1-to-1 |
| `certifications` | id, user_id, nom, organisme, date_obtention, date_expiration | Certifications obtenues |
| `documents` | id, user_id, nom, type, url_fichier | CVs, portfolios, etc |
| `messages` | id, nom, email, sujet, message | Formulaire contact |

---

## 🔌 APIs Disponibles

### Compétences
```
GET    /api/competences/list                → Lister toutes
POST   /api/competences/add                 → Ajouter
POST   /api/competences/update              → Modifier
POST   /api/competences/delete              → Supprimer
POST   /api/competences/assignUser          → Assigner à utilisateur
GET    /api/competences/user?user_id=uuid   → Compétences d'un user
```

### Utilisateurs
```
GET    /api/utilisateurs/list               → Lister avec filtre ?role=
POST   /api/utilisateurs/add                → Créer
POST   /api/utilisateurs/update             → Modifier
POST   /api/utilisateurs/delete             → Supprimer
GET    /api/utilisateurs/team?manager_id=   → Équipe d'un manager
POST   /api/utilisateurs/changePassword     → Changer mot de passe
```

### Projets
```
GET    /api/projets/list?statut=            → Lister avec filtre
POST   /api/projets/add                     → Créer
POST   /api/projets/update                  → Modifier
POST   /api/projets/delete                  → Supprimer
GET    /api/projets/members?projet_id=      → Membres du projet
POST   /api/projets/addMember               → Ajouter membre
```

### Réunions
```
GET    /api/reunions/list                   → Lister
POST   /api/reunions/add                    → Créer
POST   /api/reunions/update                 → Modifier
POST   /api/reunions/delete                 → Supprimer
```

### Documents
```
GET    /api/documents/list?user_id=         → Lister les documents
POST   /api/documents/add                   → Télécharger
POST   /api/documents/delete                → Supprimer
```

### Certifications
```
GET    /api/certifications/list?user_id=    → Lister
POST   /api/certifications/add              → Ajouter
POST   /api/certifications/update           → Modifier
POST   /api/certifications/delete           → Supprimer
```

---

## 📝 Exemples d'Utilisation

### Ajouter une compétence

```bash
curl -X POST http://localhost:8000/api/competences/add \
  -H "Content-Type: application/json" \
  -d '{
    "nom": "Rust",
    "description": "Langage Rust",
    "type_competence": "technique"
  }'

# Réponse :
# {
#   "success": true,
#   "message": "Compétence ajoutée avec succès",
#   "data": {
#     "id_competence": "uuid-xxx",
#     "nom": "Rust",
#     "description": "Langage Rust",
#     "type_competence": "technique"
#   }
# }
```

### Ajouter un utilisateur

```bash
curl -X POST http://localhost:8000/api/utilisateurs/add \
  -H "Content-Type: application/json" \
  -d '{
    "email": "jean@findin.fr",
    "prenom": "Jean",
    "nom": "Dupont",
    "mot_de_passe": "SecurePassword123",
    "role": "employe"
  }'
```

### Lister les utilisateurs par rôle

```bash
# Tous les managers
curl http://localhost:8000/api/utilisateurs/list?role=manager

# Tous les RH
curl http://localhost:8000/api/utilisateurs/list?role=rh

# Tous les employés
curl http://localhost:8000/api/utilisateurs/list?role=employe
```

### Assigner une compétence à un utilisateur

```bash
curl -X POST http://localhost:8000/api/competences/assignUser \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": "uuid-user",
    "competence_id": "uuid-comp",
    "niveau_declare": 3
  }'
```

### Créer un projet

```bash
curl -X POST http://localhost:8000/api/projets/add \
  -H "Content-Type: application/json" \
  -d '{
    "nom": "Projet Supabase",
    "description": "Migration vers Supabase",
    "statut": "en_cours",
    "date_debut": "2026-01-21",
    "date_fin": "2026-03-21"
  }'
```

---

## ✅ Checklist de Vérification

- [ ] Tables Supabase créées ✓
- [ ] Test connexion PHP réussi ✓
- [ ] Routes API ajoutées à public/index.php
- [ ] APIs testées (GET et POST)
- [ ] Dashboard affiche les données ✓
- [ ] Ajouter compétence fonctionne
- [ ] Modifier compétence fonctionne
- [ ] Supprimer compétence fonctionne
- [ ] Utilisateurs visibles dans dashboard
- [ ] Projets visibles
- [ ] Réunions créables
- [ ] Documents uploadables
- [ ] Certifications ajoutables

---

## 🐛 Dépannage

### Erreur: "SQLSTATE[08006]"
```bash
# Vérifier les credentials Supabase
grep "SUPABASE_HOST\|SUPABASE_USER\|SUPABASE_PASS" src/Config/database.php
```

### Erreur: "relation does not exist"
```bash
# Tables non créées dans Supabase
# Réexécute le SQL : database/supabase_setup.sql
```

### APIs retournent 404
```bash
# Routes API non ajoutées dans public/index.php
# Ajoute les cases depuis API_ROUTES_TO_ADD.php
```

### Données ne s'affichent pas dans le dashboard
```bash
# Vérifier que DB_TYPE = 'supabase' dans database.php
# Vérifier les données existent dans Supabase
```

---

## 📦 Fichiers Créés

```
✅ database/supabase_setup.sql       - Schéma complet SQL
✅ src/Api/CompetenceApi.php         - CRUD compétences
✅ src/Api/UtilisateurApi.php        - CRUD utilisateurs
✅ src/Api/ProjetApi.php             - CRUD projets
✅ src/Api/ReuniionApi.php           - CRUD réunions
✅ src/Api/DocumentApi.php           - CRUD documents
✅ src/Api/CertificationApi.php      - CRUD certifications
✅ public/test_supabase.php          - Test connexion
✅ test_apis.sh                      - Script de test
✅ API_ROUTES_TO_ADD.php             - Routes à copier
✅ SUPABASE_SETUP_GUIDE.md           - Guide détaillé
📝 public/index.php                  - À modifier (ajouter routes)
```

---

## 🎯 Prochaines Étapes

1. ✅ Créer les tables Supabase
2. ✅ Tester la connexion PHP
3. 📝 Ajouter les routes API dans `public/index.php`
4. 📝 Intégrer les APIs dans le dashboard (JavaScript)
5. 📝 Déployer en production

---

## 📞 Support

**Pour debug :** Regarde les logs PHP
```bash
php -S localhost:8000 public/index.php 2>&1 | grep -i error
```

**Pour tester les APIs :** Utilise curl ou Postman avec `https://ugdkdrdgxtfwsehzpmvm.supabase.co`

---

**Dernière maj : 21 janvier 2026**
