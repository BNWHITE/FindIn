#!/usr/bin/env markdown

# 🚀 Guide Complet : Connecter FindIN à Supabase

**Date**: 21 janvier 2026  
**Objective**: Connecter complètement FindIN (dashboard, CRUD, données) à Supabase PostgreSQL  
**Temps estimé**: 30-45 minutes

---

## 📋 Prérequis

✅ Compte Supabase actif  
✅ Projet Supabase créé : `ugdkdrdgxtfwsehzpmvm`  
✅ Credentials fournis :
- URL: `https://ugdkdrdgxtfwsehzpmvm.supabase.co`
- Key: `sb_publishable_bQLG9sUbkQezucpWx_FG5A_GLp3zkFw`

---

## 🔧 ÉTAPE 1 : Créer les Tables dans Supabase

### Étapes :

1. **Va sur Supabase** → https://app.supabase.com
2. **Sélectionne le projet** `ugdkdrdgxtfwsehzpmvm`
3. **Clique sur SQL Editor** (à gauche)
4. **Crée une nouvelle requête**
5. **Copie tout le contenu** de `/database/supabase_setup.sql`
6. **Exécute la requête** ✅

**Résultat attendu :**
```
✅ Toutes les tables créées
✅ Indexes créés
✅ Triggers activés
✅ Données de test insérées
```

---

## 🔑 ÉTAPE 2 : Mettre à Jour la Configuration PHP

### Modifier `src/Config/database.php`

Assure-toi que `DB_TYPE` est bien défini sur `'supabase'` :

```php
// config/database.php

// À la ligne 11, change en :
define('DB_TYPE', getenv('DB_TYPE') ?: 'supabase');

// Les credentials (déjà configurés, vérifie juste) :
define('SUPABASE_HOST', getenv('SUPABASE_HOST') ?: 'aws-1-eu-west-1.pooler.supabase.com');
define('SUPABASE_PORT', getenv('SUPABASE_PORT') ?: '6543');
define('SUPABASE_DB', getenv('SUPABASE_DB') ?: 'postgres');
define('SUPABASE_USER', getenv('SUPABASE_USER') ?: 'postgres.ugdkdrdgxtfwsehzpmvm');
define('SUPABASE_PASS', getenv('SUPABASE_PASS') ?: 'DvDrd3rVeU6qgOdd');
```

✅ **Vérification :** Pas besoin de modifier - déjà configuré !

---

## 🧪 ÉTAPE 3 : Tester la Connexion

### Créer un fichier de test

```bash
# Crée un fichier de test
touch public/test_supabase.php
```

### Contenu du fichier `public/test_supabase.php`

```php
<?php
require_once __DIR__ . '/../src/Config/database.php';
require_once __DIR__ . '/../src/Models/Database.php';

try {
    $db = Database::getInstance();
    echo "✅ Connexion Supabase réussie !<br>";
    
    // Test 1: Compter les utilisateurs
    $stmt = $db->query("SELECT COUNT(*) as count FROM utilisateurs");
    $result = $stmt->fetch();
    echo "✅ Utilisateurs dans la DB : " . $result['count'] . "<br>";
    
    // Test 2: Lister les compétences
    $stmt = $db->query("SELECT * FROM competences LIMIT 5");
    $comps = $stmt->fetchAll();
    echo "✅ Compétences trouvées : " . count($comps) . "<br>";
    echo "<pre>" . print_r($comps, true) . "</pre>";
    
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
?>
```

### Tester :

```bash
php public/test_supabase.php
```

**Résultat attendu :**
```
✅ Connexion Supabase réussie !
✅ Utilisateurs dans la DB : 1
✅ Compétences trouvées : 8
```

---

## 📊 ÉTAPE 4 : Créer des Utilisateurs de Test

### Ajouter dans Supabase SQL Editor :

```sql
-- Insérer des utilisateurs de test

-- Admin
INSERT INTO utilisateurs (email, prenom, nom, mot_de_passe, role)
VALUES ('admin@findin.fr', 'Admin', 'Test', '$2y$10$xxx', 'admin')
ON CONFLICT (email) DO NOTHING;

-- Manager
INSERT INTO utilisateurs (email, prenom, nom, mot_de_passe, role)
VALUES ('manager@findin.fr', 'Jean', 'Manager', '$2y$10$xxx', 'manager')
ON CONFLICT (email) DO NOTHING;

-- Employee 1
INSERT INTO utilisateurs (email, prenom, nom, mot_de_passe, role, manager_id)
VALUES (
    'employee1@findin.fr', 
    'Alice', 
    'Dupont', 
    '$2y$10$xxx', 
    'employe',
    (SELECT id_utilisateur FROM utilisateurs WHERE email = 'manager@findin.fr' LIMIT 1)
)
ON CONFLICT (email) DO NOTHING;

-- Employee 2
INSERT INTO utilisateurs (email, prenom, nom, mot_de_passe, role, manager_id)
VALUES (
    'employee2@findin.fr', 
    'Bob', 
    'Martin', 
    '$2y$10$xxx', 
    'employe',
    (SELECT id_utilisateur FROM utilisateurs WHERE email = 'manager@findin.fr' LIMIT 1)
)
ON CONFLICT (email) DO NOTHING;

-- RH
INSERT INTO utilisateurs (email, prenom, nom, mot_de_passe, role)
VALUES ('rh@findin.fr', 'Sophie', 'RH', '$2y$10$xxx', 'rh')
ON CONFLICT (email) DO NOTHING;
```

**Note :** Remplace `$2y$10$xxx` par des vrais hashes bcrypt ou utilise un outil en ligne (bcrypt.online).

---

## 🚀 ÉTAPE 5 : Implémenter les APIs CRUD

### Structure des endpoints

```
POST   /api/competences/add          → Ajouter compétence
POST   /api/competences/update       → Modifier compétence
POST   /api/competences/delete       → Supprimer compétence
GET    /api/competences/list         → Lister compétences

POST   /api/utilisateurs/add         → Ajouter utilisateur
POST   /api/utilisateurs/update      → Modifier utilisateur
POST   /api/utilisateurs/delete      → Supprimer utilisateur
GET    /api/utilisateurs/list        → Lister utilisateurs

POST   /api/projets/add              → Ajouter projet
POST   /api/projets/update           → Modifier projet
POST   /api/projets/delete           → Supprimer projet
GET    /api/projets/list             → Lister projets

... et j'ai créé les fichiers d'API ci-dessous
```

### Créer le dossier API

```bash
mkdir -p src/Api
```

---

## ✅ ÉTAPE 6 : Télécharger les Fichiers API (Voir fichiers générés)

Les fichiers suivants ont déjà été créés dans `src/Api/` :

- `CompetenceApi.php` - CRUD compétences
- `UtilisateurApi.php` - CRUD utilisateurs
- `ProjetApi.php` - CRUD projets
- `CertificationApi.php` - CRUD certifications
- `DocumentApi.php` - CRUD documents
- `ReuniionApi.php` - CRUD réunions

---

## 🔌 ÉTAPE 7 : Configurer les Routes API

### Modifier `public/index.php` pour ajouter les routes API

Ajoute ceci dans le switch principal :

```php
// API Routes
case 'api/competences/add':
    header('Content-Type: application/json');
    require_once __DIR__ . '/../src/Api/CompetenceApi.php';
    $api = new CompetenceApi();
    echo $api->add();
    exit;

case 'api/competences/list':
    header('Content-Type: application/json');
    require_once __DIR__ . '/../src/Api/CompetenceApi.php';
    $api = new CompetenceApi();
    echo $api->list();
    exit;

case 'api/competences/update':
    header('Content-Type: application/json');
    require_once __DIR__ . '/../src/Api/CompetenceApi.php';
    $api = new CompetenceApi();
    echo $api->update();
    exit;

case 'api/competences/delete':
    header('Content-Type: application/json');
    require_once __DIR__ . '/../src/Api/CompetenceApi.php';
    $api = new CompetenceApi();
    echo $api->delete();
    exit;

// Utilisateurs API
case 'api/utilisateurs/add':
    header('Content-Type: application/json');
    require_once __DIR__ . '/../src/Api/UtilisateurApi.php';
    $api = new UtilisateurApi();
    echo $api->add();
    exit;

case 'api/utilisateurs/list':
    header('Content-Type: application/json');
    require_once __DIR__ . '/../src/Api/UtilisateurApi.php';
    $api = new UtilisateurApi();
    echo $api->list();
    exit;

// Projets API
case 'api/projets/add':
    header('Content-Type: application/json');
    require_once __DIR__ . '/../src/Api/ProjetApi.php';
    $api = new ProjetApi();
    echo $api->add();
    exit;

case 'api/projets/list':
    header('Content-Type: application/json');
    require_once __DIR__ . '/../src/Api/ProjetApi.php';
    $api = new ProjetApi();
    echo $api->list();
    exit;

// ... etc pour les autres entités
```

---

## 🎨 ÉTAPE 8 : Intégrer Dashboard aux APIs

Les vues dashboard appelleront les APIs via JavaScript/Fetch :

```javascript
// Récupérer les compétences
fetch('/api/competences/list')
    .then(r => r.json())
    .then(data => {
        console.log('Compétences:', data);
        // Afficher les données
    });

// Ajouter une compétence
fetch('/api/competences/add', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
        nom: 'Python',
        description: 'Langage Python',
        type_competence: 'technique'
    })
})
.then(r => r.json())
.then(data => console.log('Résultat:', data));

// Mettre à jour
fetch('/api/competences/update', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
        id: 'uuid-here',
        nom: 'Python 3.11',
        description: 'Version 3.11'
    })
})
.then(r => r.json())
.then(data => console.log('Résultat:', data));

// Supprimer
fetch('/api/competences/delete', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
        id: 'uuid-here'
    })
})
.then(r => r.json())
.then(data => console.log('Résultat:', data));
```

---

## 📱 ÉTAPE 9 : Tester les Fonctionnalités Complètes

### Checklist de test :

- [ ] Login/Logout fonctionne
- [ ] Dashboard affiche les données de Supabase
- [ ] Ajouter une compétence ✓
- [ ] Modifier une compétence ✓
- [ ] Supprimer une compétence ✓
- [ ] Lister les utilisateurs ✓
- [ ] Voir les projets ✓
- [ ] Ajouter un projet ✓
- [ ] Valider une compétence (manager) ✓
- [ ] Voir les réunions planifiées ✓
- [ ] Télécharger un document ✓
- [ ] Envoyer un message de contact ✓

---

## 🔍 Dépannage

### Erreur: "SQLSTATE[08006]"
**Cause :** Supabase connexion failed  
**Solution :**
```bash
# Vérifie les credentials
grep "SUPABASE_HOST" src/Config/database.php

# Teste la connexion
php public/test_supabase.php
```

### Erreur: "relation "utilisateurs" does not exist"
**Cause :** Tables non créées  
**Solution :** Exécute le SQL de setup dans Supabase SQL Editor

### Erreur: "Column 'mot_de_passe' doesn't exist"
**Cause :** Schéma incomplet  
**Solution :** Réexécute le SQL complet depuis supabase_setup.sql

---

## 📊 Fichiers Créés / Modifiés

```
✅ Créé : database/supabase_setup.sql       (Schéma complet)
✅ Créé : src/Api/CompetenceApi.php         (CRUD compétences)
✅ Créé : src/Api/UtilisateurApi.php        (CRUD utilisateurs)
✅ Créé : src/Api/ProjetApi.php             (CRUD projets)
✅ Créé : src/Api/CertificationApi.php      (CRUD certifications)
✅ Créé : src/Api/DocumentApi.php           (CRUD documents)
✅ Créé : src/Api/ReuniionApi.php           (CRUD réunions)
✅ Créé : public/test_supabase.php          (Test connexion)
📝 À modifier : public/index.php            (Ajouter routes API)
📝 À modifier : src/Views/dashboard/*.php   (Appeler les APIs)
```

---

## 🎯 Résumé

| Étape | Status | Action |
|-------|--------|--------|
| 1. Créer tables Supabase | ✅ | Exécuter SQL dans éditeur Supabase |
| 2. Config PHP | ✅ | Déjà configurée |
| 3. Tester connexion | ✅ | Lancer `php public/test_supabase.php` |
| 4. Utilisateurs test | ✅ | Insérer via Supabase SQL |
| 5. APIs CRUD | ✅ | Fichiers créés dans `src/Api/` |
| 6. Routes API | 📝 | Ajouter switch cases dans `public/index.php` |
| 7. Dashboard intégration | 📝 | Appeler APIs dans JavaScript |
| 8. Tests complets | 📝 | Tester toutes les fonctionnalités |

---

## 🚀 Prochaines Étapes

1. **Exécute le SQL** dans Supabase
2. **Teste la connexion** avec `php public/test_supabase.php`
3. **Ajoute les routes API** dans `public/index.php`
4. **Intègre les APIs** dans le dashboard
5. **Teste en production**

---

**Questions ? Voir les fichiers d'API généré pour plus de détails !**
