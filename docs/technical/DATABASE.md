# 💾 Structure de la base de données

## 📊 Schéma général

```
users (utilisateurs)
├── competences_utilisateurs ──► competences
│                              └── categories_competences
├── documents_utilisateurs
├── membres_projets ──► projets
│                      └── besoins_competences_projets
└── departements
```

---

## 📋 Tables principales

### 🧑 users
Table centrale des utilisateurs avec authentification.

```sql
CREATE TABLE users (
    id CHAR(36) PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) DEFAULT NULL,
    prenom VARCHAR(100) DEFAULT NULL,
    nom VARCHAR(100) DEFAULT NULL,
    departement VARCHAR(100) DEFAULT NULL,
    id_departement CHAR(36) DEFAULT NULL,
    role ENUM('employe', 'manager', 'rh', 'admin') DEFAULT 'employe',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_departement (id_departement)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Colonnes** :
- `id` : UUID unique
- `email` : Email de connexion (unique)
- `password` : Hash bcrypt du mot de passe
- `prenom`, `nom` : Identité
- `departement` : Nom du département (legacy)
- `id_departement` : FK vers table departements
- `role` : Niveau d'accès
  - `employe` : Utilisateur standard
  - `manager` : Gestionnaire d'équipe
  - `rh` : Ressources humaines
  - `admin` : Administrateur système
- `created_at` : Date de création du compte

**Relations** :
- **1-N** vers `competences_utilisateurs`
- **1-N** vers `documents_utilisateurs`
- **1-N** vers `membres_projets`
- **N-1** vers `departements`

---

### 🎯 competences
Catalogue des compétences disponibles.

```sql
CREATE TABLE competences (
    id_competence CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    nom VARCHAR(150) NOT NULL,
    description TEXT DEFAULT NULL,
    id_categorie CHAR(36) NOT NULL,
    type_competence ENUM('savoir_faire', 'savoir_etre', 'expertise') NOT NULL,
    
    INDEX idx_categorie (id_categorie),
    INDEX idx_type (type_competence),
    FOREIGN KEY (id_categorie) REFERENCES categories_competences(id_categorie)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Types de compétences** :
- `savoir_faire` : Compétences techniques (ex: Python, Excel)
- `savoir_etre` : Soft skills (ex: Communication, Leadership)
- `expertise` : Domaines d'expertise (ex: Data Science, Marketing)

---

### 🔗 Tables de liaison Utilisateurs-Compétences

> ⚠️ **Architecture dual-table** : Le projet maintient deux tables parallèles pour la rétrocompatibilité.

#### Table `user_competences` (Moderne - Anglais)
```sql
CREATE TABLE user_competences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id CHAR(36) NOT NULL,
    competence_id INT NOT NULL,
    niveau_declare TINYINT(1) DEFAULT 1,
    niveau_valide TINYINT(1) DEFAULT NULL,
    manager_id CHAR(36) DEFAULT NULL,
    date_declaration TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_validation TIMESTAMP NULL DEFAULT NULL,
    
    INDEX idx_user (user_id),
    INDEX idx_competence (competence_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### Table `competences_utilisateurs` (Legacy - Français)
```sql
CREATE TABLE competences_utilisateurs (
    id_comp_utilisateur CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    user_id CHAR(36) NOT NULL,
    id_competence CHAR(36) NOT NULL,
    niveau_declare TINYINT CHECK (niveau_declare BETWEEN 1 AND 5),
    niveau_valide TINYINT CHECK (niveau_valide BETWEEN 1 AND 5),
    manager_id CHAR(36) DEFAULT NULL,
    date_validation TIMESTAMP NULL DEFAULT NULL,
    id_source CHAR(36) DEFAULT NULL,
    
    INDEX idx_user (user_id),
    INDEX idx_competence (id_competence),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (id_competence) REFERENCES competences(id_competence),
    FOREIGN KEY (manager_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Niveaux (1-5)** :
- **1** : Débutant (connaissance théorique)
- **2** : Intermédiaire (pratique occasionnelle)
- **3** : Confirmé (utilisation régulière)
- **4** : Expert (maîtrise avancée)
- **5** : Référent (peut former d'autres)

**Workflow de validation** :
```
User déclare compétence → niveau_declare = X
                         ↓
Manager valide          → niveau_valide = Y
                         ↓
                         manager_id + date_validation
```

---

### 📁 projets
Gestion des projets et besoins en compétences.

```sql
CREATE TABLE projets (
    id_projet CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    nom VARCHAR(200) NOT NULL,
    description TEXT,
    date_debut DATE,
    date_fin DATE,
    statut ENUM('planifie', 'en_cours', 'termine', 'annule') DEFAULT 'planifie',
    user_id CHAR(36) NOT NULL COMMENT 'Responsable du projet',
    
    INDEX idx_statut (statut),
    INDEX idx_dates (date_debut, date_fin),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

### 🏢 departements
Structure organisationnelle de l'entreprise.

```sql
CREATE TABLE departements (
    id_departement CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    nom VARCHAR(150) NOT NULL,
    id_entreprise CHAR(36) NOT NULL,
    type_departement ENUM('entreprise', 'division', 'departement', 'unite', 'equipe') NOT NULL,
    id_parent CHAR(36) DEFAULT NULL COMMENT 'Département parent (hiérarchie)',
    
    INDEX idx_parent (id_parent),
    FOREIGN KEY (id_parent) REFERENCES departements(id_departement) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Hiérarchie** :
```
Entreprise
└── Division
    └── Département
        └── Unité
            └── Équipe
```

---

### 🏷️ categories_competences
Catégorisation des compétences.

```sql
CREATE TABLE categories_competences (
    id_categorie CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    nom VARCHAR(100) NOT NULL UNIQUE,
    
    INDEX idx_nom (nom)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Exemples de catégories** :
- Langages de programmation
- Outils & Logiciels
- Méthodologies
- Langues
- Soft Skills
- Certifications

---

### 📄 documents_utilisateurs
Gestion des CV et documents.

```sql
CREATE TABLE documents_utilisateurs (
    id_document CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    user_id CHAR(36) NOT NULL,
    chemin_fichier TEXT NOT NULL,
    nom_fichier VARCHAR(255) DEFAULT NULL,
    type_document VARCHAR(50) DEFAULT NULL COMMENT 'pdf, docx, txt',
    id_source_generee CHAR(36) DEFAULT NULL,
    date_televersement TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_user (user_id),
    INDEX idx_type (type_document),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

### 👥 membres_projets
Affectation des utilisateurs aux projets.

```sql
CREATE TABLE membres_projets (
    id_membre_projet CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    id_projet CHAR(36) NOT NULL,
    user_id CHAR(36) NOT NULL,
    role_dans_projet VARCHAR(100) DEFAULT NULL,
    
    INDEX idx_projet (id_projet),
    INDEX idx_user (user_id),
    UNIQUE KEY unique_membre_projet (id_projet, user_id),
    FOREIGN KEY (id_projet) REFERENCES projets(id_projet) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

### 🎯 besoins_competences_projets
Besoins en compétences pour les projets.

```sql
CREATE TABLE besoins_competences_projets (
    id_besoin CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    id_projet CHAR(36) NOT NULL,
    id_competence CHAR(36) NOT NULL,
    niveau_requis TINYINT CHECK (niveau_requis BETWEEN 1 AND 5),
    description TEXT DEFAULT NULL,
    statut ENUM('ouvert', 'en_cours', 'comble') DEFAULT 'ouvert',
    user_id CHAR(36) DEFAULT NULL COMMENT 'Utilisateur affecté',
    
    INDEX idx_projet (id_projet),
    INDEX idx_competence (id_competence),
    INDEX idx_statut (statut),
    FOREIGN KEY (id_projet) REFERENCES projets(id_projet) ON DELETE CASCADE,
    FOREIGN KEY (id_competence) REFERENCES competences(id_competence),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## 🔍 Requêtes SQL utiles

### Rechercher des utilisateurs par compétence
```sql
SELECT 
    u.id,
    u.prenom,
    u.nom,
    u.email,
    c.nom AS competence,
    cu.niveau_valide
FROM users u
INNER JOIN competences_utilisateurs cu ON u.id = cu.user_id
INNER JOIN competences c ON cu.id_competence = c.id_competence
WHERE c.nom LIKE '%Python%'
  AND cu.niveau_valide >= 3
ORDER BY cu.niveau_valide DESC;
```

### Compétences les plus répandues
```sql
SELECT 
    c.nom AS competence,
    COUNT(DISTINCT cu.user_id) AS nombre_utilisateurs,
    AVG(cu.niveau_valide) AS niveau_moyen
FROM competences c
INNER JOIN competences_utilisateurs cu ON c.id_competence = cu.id_competence
WHERE cu.niveau_valide IS NOT NULL
GROUP BY c.id_competence
ORDER BY nombre_utilisateurs DESC
LIMIT 10;
```

### Utilisateurs sans compétences validées
```sql
SELECT 
    u.id,
    u.prenom,
    u.nom,
    COUNT(cu.id_comp_utilisateur) AS competences_declarees,
    SUM(CASE WHEN cu.niveau_valide IS NOT NULL THEN 1 ELSE 0 END) AS competences_validees
FROM users u
LEFT JOIN competences_utilisateurs cu ON u.id = cu.user_id
GROUP BY u.id
HAVING competences_validees = 0;
```

### Projets avec besoins non comblés
```sql
SELECT 
    p.nom AS projet,
    c.nom AS competence_manquante,
    bcp.niveau_requis,
    bcp.statut
FROM projets p
INNER JOIN besoins_competences_projets bcp ON p.id_projet = bcp.id_projet
INNER JOIN competences c ON bcp.id_competence = c.id_competence
WHERE bcp.statut = 'ouvert'
  AND p.statut IN ('planifie', 'en_cours')
ORDER BY p.date_debut;
```

---

## 📈 Statistiques

### Dashboard manager
```sql
-- Nombre de personnes dans son équipe
SELECT COUNT(*) 
FROM users 
WHERE id_departement = :manager_department;

-- Compétences les plus présentes dans l'équipe
SELECT 
    c.nom,
    COUNT(DISTINCT cu.user_id) AS effectif
FROM competences c
INNER JOIN competences_utilisateurs cu ON c.id_competence = cu.id_competence
INNER JOIN users u ON cu.user_id = u.id
WHERE u.id_departement = :manager_department
  AND cu.niveau_valide >= 3
GROUP BY c.id_competence
ORDER BY effectif DESC
LIMIT 5;
```

---

## 🔒 Sécurité

### Indexes pour performance
```sql
-- Indexes déjà créés :
CREATE INDEX idx_email ON users(email);
CREATE INDEX idx_user_competence ON competences_utilisateurs(user_id, id_competence);
CREATE INDEX idx_projet_statut ON projets(statut);
```

### Contraintes d'intégrité
- ✅ Clés étrangères avec CASCADE/SET NULL appropriés
- ✅ Contraintes CHECK pour les niveaux (1-5)
- ✅ UNIQUE pour éviter doublons
- ✅ UUID pour éviter énumération

---

## 📊 Diagramme ER

```
┌─────────────────┐
│     users       │
│  ─────────────  │
│  PK id          │◄────┐
│     email       │     │
│     password    │     │
│     prenom      │     │
│     nom         │     │
│     role        │     │
└────────┬────────┘     │
         │              │
         │ 1:N          │ N:1
         │              │
┌────────▼─────────┐    │
│ competences_     │    │
│   utilisateurs   │    │
│  ──────────────  │    │
│  PK id           │    │
│  FK user_id      │────┘
│  FK competence   │────┐
│     niveau       │    │
└──────────────────┘    │
                        │ N:1
                        │
                 ┌──────▼────────┐
                 │  competences  │
                 │  ───────────  │
                 │  PK id        │
                 │     nom       │
                 │  FK categorie │
                 └───────────────┘
```

---

## 🔄 Migrations

### Créer une migration
```sql
-- database/migrations/YYYYMMDD_description.sql
-- Ex: 20251216_add_formation_table.sql

-- UP
CREATE TABLE formations (...);

-- DOWN (pour rollback)
-- DROP TABLE IF EXISTS formations;
```

### Appliquer une migration
```bash
mysql -u root -p gestion_competences < database/migrations/20251216_add_formation_table.sql
```

---

## 📦 Sauvegarde & Restauration

### Backup complet
```bash
mysqldump -u root -p gestion_competences > backup_$(date +%Y%m%d).sql
```

### Backup structure seule
```bash
mysqldump -u root -p --no-data gestion_competences > schema.sql
```

### Restauration
```bash
mysql -u root -p gestion_competences < backup_20251216.sql
```

---

## ✅ Checklist maintenance DB

- [ ] Sauvegardes automatiques quotidiennes
- [ ] Logs des requêtes lentes activés
- [ ] Indexes optimisés
- [ ] Contraintes d'intégrité vérifiées
- [ ] Données sensibles cryptées
- [ ] Accès restreints par rôle
