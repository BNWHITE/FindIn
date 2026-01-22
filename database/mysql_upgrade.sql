-- ============================================================================
-- FINDIN - SCHEMA MYSQL UPGRADE (Enhanced Connectivity & Performance)
-- ============================================================================
-- Exécuter ce fichier pour upgrader la base de données existante
-- Cette version améliore:
-- - Les indexes pour la performance
-- - Les contraintes de clés étrangères
-- - Le charset UTF8MB4 pour l'unicode
-- - Les colonnes de gestion (created_at, updated_at)

-- ============================================================================
-- VÉRIFIER/CRÉER LA BASE DE DONNÉES
-- ============================================================================
CREATE DATABASE IF NOT EXISTS findin 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;

USE findin;

-- ============================================================================
-- TABLE RÔLES (NEW)
-- ============================================================================
CREATE TABLE IF NOT EXISTS roles (
    id_role INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) UNIQUE NOT NULL COMMENT 'Nom du rôle: employe, manager, rh, admin',
    description TEXT NULL COMMENT 'Description du rôle',
    permissions JSON NULL COMMENT 'Permissions JSON',
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modifie_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE INDEX idx_nom (nom),
    INDEX idx_cree_le (cree_le)
    
) ENGINE=InnoDB 
DEFAULT CHARSET=utf8mb4 
COLLATE=utf8mb4_unicode_ci
COMMENT='Rôles disponibles dans le système';

-- ============================================================================
-- TABLE UTILISATEURS (Enhanced)
-- ============================================================================
CREATE TABLE IF NOT EXISTS utilisateurs (
    id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL COMMENT 'Email unique de l\'utilisateur',
    prenom VARCHAR(100) NOT NULL COMMENT 'Prénom',
    nom VARCHAR(100) NOT NULL COMMENT 'Nom',
    mot_de_passe VARCHAR(255) NOT NULL COMMENT 'Mot de passe hashé',
    id_departement INT NULL COMMENT 'Référence au département',
    role VARCHAR(50) DEFAULT 'employe' COMMENT 'Rôle: employe, manager, rh, admin',
    photo VARCHAR(255) NULL COMMENT 'URL photo profil',
    manager_id INT NULL COMMENT 'ID du manager direct',
    actif TINYINT(1) DEFAULT 1 COMMENT 'Compte actif?',
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Date création',
    modifie_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Date modification',
    
    -- Indexes optimisés
    UNIQUE INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_manager (manager_id),
    INDEX idx_actif (actif),
    INDEX idx_departement (id_departement),
    INDEX idx_cree_le (cree_le),
    
    -- Contrainte de clé étrangère
    FOREIGN KEY (manager_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE SET NULL,
    FOREIGN KEY (id_departement) REFERENCES departements(id_departement) ON DELETE SET NULL
    
) ENGINE=InnoDB 
DEFAULT CHARSET=utf8mb4 
COLLATE=utf8mb4_unicode_ci
COMMENT='Utilisateurs du système';

-- ============================================================================
-- TABLE DÉPARTEMENTS (Enhanced)
-- ============================================================================
CREATE TABLE IF NOT EXISTS departements (
    id_departement INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL COMMENT 'Nom du département',
    description TEXT NULL COMMENT 'Description',
    responsable_id INT NULL COMMENT 'ID du responsable',
    actif TINYINT(1) DEFAULT 1 COMMENT 'Département actif?',
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modifie_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE INDEX idx_nom (nom),
    INDEX idx_actif (actif),
    INDEX idx_responsable (responsable_id),
    
    FOREIGN KEY (responsable_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE SET NULL
    
) ENGINE=InnoDB 
DEFAULT CHARSET=utf8mb4 
COLLATE=utf8mb4_unicode_ci
COMMENT='Départements de l\'entreprise';

-- ============================================================================
-- TABLE COMPÉTENCES (Enhanced)
-- ============================================================================
CREATE TABLE IF NOT EXISTS competences (
    id_competence INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) UNIQUE NOT NULL COMMENT 'Nom unique de la compétence',
    description TEXT NULL COMMENT 'Description détaillée',
    type_competence VARCHAR(50) DEFAULT 'technique' COMMENT 'Type: technique, soft, métier',
    niveau_min INT DEFAULT 1 COMMENT 'Niveau minimum',
    niveau_max INT DEFAULT 5 COMMENT 'Niveau maximum',
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modifie_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE INDEX idx_nom (nom),
    INDEX idx_type (type_competence),
    INDEX idx_cree_le (cree_le)
    
) ENGINE=InnoDB 
DEFAULT CHARSET=utf8mb4 
COLLATE=utf8mb4_unicode_ci
COMMENT='Catalogue des compétences';

-- ============================================================================
-- TABLE COMPÉTENCES UTILISATEURS (Enhanced)
-- ============================================================================
CREATE TABLE IF NOT EXISTS competences_utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL COMMENT 'ID utilisateur',
    id_competence INT NOT NULL COMMENT 'ID compétence',
    niveau_declare INT DEFAULT 1 COMMENT 'Niveau déclaré (1-5)',
    niveau_valide INT NULL COMMENT 'Niveau validé par manager',
    id_manager_validateur INT NULL COMMENT 'Manager qui a validé',
    statut VARCHAR(50) DEFAULT 'en_attente' COMMENT 'en_attente, approuve, rejete',
    commentaire TEXT NULL COMMENT 'Commentaires du manager',
    date_declaration TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Date déclaration',
    date_validation TIMESTAMP NULL COMMENT 'Date validation',
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modifie_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_user_comp (user_id, id_competence),
    INDEX idx_user (user_id),
    INDEX idx_competence (id_competence),
    INDEX idx_statut (statut),
    INDEX idx_manager (id_manager_validateur),
    INDEX idx_date_validation (date_validation),
    
    FOREIGN KEY (user_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    FOREIGN KEY (id_competence) REFERENCES competences(id_competence) ON DELETE CASCADE,
    FOREIGN KEY (id_manager_validateur) REFERENCES utilisateurs(id_utilisateur) ON DELETE SET NULL
    
) ENGINE=InnoDB 
DEFAULT CHARSET=utf8mb4 
COLLATE=utf8mb4_unicode_ci
COMMENT='Compétences déclarées par les utilisateurs';

-- ============================================================================
-- TABLE PROJETS (Enhanced)
-- ============================================================================
CREATE TABLE IF NOT EXISTS projets (
    id_projet INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL COMMENT 'Nom du projet',
    description TEXT NULL COMMENT 'Description',
    responsable_id INT NOT NULL COMMENT 'ID responsable',
    statut VARCHAR(50) DEFAULT 'en_cours' COMMENT 'Statut: planifie, en_cours, termine, archive',
    date_debut DATE COMMENT 'Date de début',
    date_fin DATE COMMENT 'Date de fin prévue',
    actif TINYINT(1) DEFAULT 1 COMMENT 'Projet actif?',
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modifie_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_statut (statut),
    INDEX idx_responsable (responsable_id),
    INDEX idx_actif (actif),
    INDEX idx_dates (date_debut, date_fin),
    
    FOREIGN KEY (responsable_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE SET NULL
    
) ENGINE=InnoDB 
DEFAULT CHARSET=utf8mb4 
COLLATE=utf8mb4_unicode_ci
COMMENT='Projets de l\'entreprise';

-- ============================================================================
-- TABLE PROJETS UTILISATEURS (Enhanced)
-- ============================================================================
CREATE TABLE IF NOT EXISTS projets_utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    projet_id INT NOT NULL COMMENT 'ID projet',
    user_id INT NOT NULL COMMENT 'ID utilisateur',
    role_projet VARCHAR(100) COMMENT 'Rôle: lead, membre, contributeur',
    date_debut DATE COMMENT 'Date d\'affectation',
    date_fin DATE COMMENT 'Date de fin',
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_projet_user (projet_id, user_id),
    INDEX idx_user (user_id),
    INDEX idx_projet (projet_id),
    
    FOREIGN KEY (projet_id) REFERENCES projets(id_projet) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE
    
) ENGINE=InnoDB 
DEFAULT CHARSET=utf8mb4 
COLLATE=utf8mb4_unicode_ci
COMMENT='Assignation des utilisateurs aux projets';

-- ============================================================================
-- TABLE INVITATIONS (Enhanced)
-- ============================================================================
CREATE TABLE IF NOT EXISTS invitations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL COMMENT 'Email invité',
    prenom VARCHAR(100) NOT NULL COMMENT 'Prénom',
    nom VARCHAR(100) NOT NULL COMMENT 'Nom',
    token VARCHAR(255) UNIQUE NOT NULL COMMENT 'Token d\'acceptation unique',
    role VARCHAR(50) DEFAULT 'employe' COMMENT 'Rôle attribué',
    manager_id INT NULL COMMENT 'Manager assigné',
    departement VARCHAR(255) NULL COMMENT 'Département',
    statut VARCHAR(50) DEFAULT 'pending' COMMENT 'pending, accepted, expired',
    expires_at TIMESTAMP NOT NULL COMMENT 'Expiration du token',
    accepted_at TIMESTAMP NULL COMMENT 'Date d\'acceptation',
    user_id INT NULL COMMENT 'ID utilisateur après acceptation',
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modifie_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE INDEX idx_token (token),
    INDEX idx_email (email),
    INDEX idx_statut (statut),
    INDEX idx_expires (expires_at),
    INDEX idx_user_id (user_id),
    
    FOREIGN KEY (user_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE SET NULL,
    FOREIGN KEY (manager_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE SET NULL
    
) ENGINE=InnoDB 
DEFAULT CHARSET=utf8mb4 
COLLATE=utf8mb4_unicode_ci
COMMENT='Invitations pour les nouveaux utilisateurs';

-- ============================================================================
-- TABLE CERTIFICATIONS (Enhanced)
-- ============================================================================
CREATE TABLE IF NOT EXISTS certifications (
    id_certification INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL COMMENT 'ID utilisateur',
    nom VARCHAR(255) NOT NULL COMMENT 'Nom certification',
    organisme VARCHAR(255) NULL COMMENT 'Organisme certifiant',
    numero_certification VARCHAR(255) UNIQUE NULL COMMENT 'Numéro unique',
    date_obtention DATE NOT NULL COMMENT 'Date obtention',
    date_expiration DATE NULL COMMENT 'Date expiration',
    fichier_url VARCHAR(255) NULL COMMENT 'URL du fichier/document',
    verifiee TINYINT(1) DEFAULT 0 COMMENT 'Certification vérifiée?',
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modifie_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_user (user_id),
    INDEX idx_organisme (organisme),
    INDEX idx_date_expiration (date_expiration),
    INDEX idx_numero (numero_certification),
    
    FOREIGN KEY (user_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE
    
) ENGINE=InnoDB 
DEFAULT CHARSET=utf8mb4 
COLLATE=utf8mb4_unicode_ci
COMMENT='Certifications des utilisateurs';

-- ============================================================================
-- TABLE VALIDATIONS (Enhanced)
-- ============================================================================
CREATE TABLE IF NOT EXISTS demandes_validation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL COMMENT 'Utilisateur qui demande',
    manager_id INT NOT NULL COMMENT 'Manager à qui demander',
    competence_id INT NOT NULL COMMENT 'Compétence à valider',
    niveau_declare INT NOT NULL COMMENT 'Niveau déclaré',
    statut VARCHAR(50) DEFAULT 'en_attente' COMMENT 'en_attente, approuve, rejete',
    commentaire TEXT NULL COMMENT 'Commentaires du manager',
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    validee_le TIMESTAMP NULL COMMENT 'Date validation',
    modifie_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_validation (user_id, manager_id, competence_id),
    INDEX idx_user (user_id),
    INDEX idx_manager (manager_id),
    INDEX idx_statut (statut),
    INDEX idx_competence (competence_id),
    INDEX idx_cree_le (cree_le),
    
    FOREIGN KEY (user_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    FOREIGN KEY (manager_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    FOREIGN KEY (competence_id) REFERENCES competences(id_competence) ON DELETE CASCADE
    
) ENGINE=InnoDB 
DEFAULT CHARSET=utf8mb4 
COLLATE=utf8mb4_unicode_ci
COMMENT='Demandes de validation des compétences';

-- ============================================================================
-- TABLE DOCUMENTS (Enhanced)
-- ============================================================================
CREATE TABLE IF NOT EXISTS documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL COMMENT 'Propriétaire du document',
    type_document VARCHAR(50) NOT NULL COMMENT 'Type: cv, portfolio, attestation',
    nom VARCHAR(255) NOT NULL COMMENT 'Nom du document',
    chemin_fichier VARCHAR(500) NOT NULL COMMENT 'Chemin du fichier',
    taille_fichier INT COMMENT 'Taille en bytes',
    mime_type VARCHAR(100) COMMENT 'Type MIME',
    date_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_user (user_id),
    INDEX idx_type (type_document),
    INDEX idx_date_upload (date_upload),
    
    FOREIGN KEY (user_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE
    
) ENGINE=InnoDB 
DEFAULT CHARSET=utf8mb4 
COLLATE=utf8mb4_unicode_ci
COMMENT='Documents téléchargés par les utilisateurs';

-- ============================================================================
-- INDEXES GLOBAUX POUR PERFORMANCE
-- ============================================================================
-- Indexes créés individuellement pour éviter les conflits avec les colonnes existantes

-- Index sur competences_utilisateurs (individuels, pas composés)
-- Les colonnes: user_id, statut, id_competence, id_manager_validateur
-- sont déjà indexées dans la définition de table
-- Ajouter des indexes supplémentaires seulement si nécessaires

-- INDEX OPTIONNEL: Recherche combinée user + statut (si besoin de performance)
-- ALTER TABLE competences_utilisateurs ADD INDEX idx_user_statut (user_id, statut);

-- Index sur projets_utilisateurs (individuels)
-- ALTER TABLE projets_utilisateurs ADD INDEX idx_projet_user (projet_id, user_id);

-- Index sur utilisateurs (individuels)
-- ALTER TABLE utilisateurs ADD INDEX idx_role_manager (role, manager_id);

-- ============================================================================
-- DONNÉES DE TEST
-- ============================================================================

-- Insérer les rôles
INSERT IGNORE INTO roles (id_role, nom, description, permissions) VALUES
(1, 'employe', 'Employé standard', '["view_profile", "declare_competencies", "view_dashboard"]'),
(2, 'manager', 'Manager/Responsable', '["view_profile", "declare_competencies", "view_dashboard", "validate_competencies", "manage_team"]'),
(3, 'rh', 'Ressources Humaines', '["manage_users", "manage_invitations", "view_reports", "manage_competencies", "view_dashboard"]'),
(4, 'admin', 'Administrateur système', '["*"]');

-- Insérer les départements
INSERT IGNORE INTO departements (id_departement, nom, description) VALUES
(1, 'IT', 'Informatique et Technologie'),
(2, 'RH', 'Ressources Humaines'),
(3, 'Ventes', 'Département Commercial'),
(4, 'Marketing', 'Marketing et Communication');

-- Insérer des compétences de base
INSERT IGNORE INTO competences (id_competence, nom, description, type_competence) VALUES
(1, 'PHP', 'Langage de programmation serveur', 'technique'),
(2, 'JavaScript', 'Langage de programmation client', 'technique'),
(3, 'MySQL', 'Système de gestion de base de données', 'technique'),
(4, 'Leadership', 'Capacité de direction d\'équipe', 'soft'),
(5, 'Communication', 'Capacité de communication efficace', 'soft'),
(6, 'Gestion de projet', 'Gestion de projets informatiques', 'métier');

-- ============================================================================
-- FIN DES MIGRATIONS
-- ============================================================================
