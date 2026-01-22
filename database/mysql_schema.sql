-- ============================================================================
-- FINDIN - SCHEMA MYSQL (XAMPP)
-- ============================================================================
-- Exécuter ce fichier dans phpMyAdmin ou en CLI

-- Créer la base de données
CREATE DATABASE IF NOT EXISTS findin CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE findin;

-- ============================================================================
-- TABLES
-- ============================================================================

-- Utilisateurs
CREATE TABLE utilisateurs (
    id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    prenom VARCHAR(100),
    nom VARCHAR(100),
    mot_de_passe VARCHAR(255) NOT NULL,
    id_departement INT,
    role VARCHAR(50) DEFAULT 'employe',
    photo VARCHAR(255),
    manager_id INT,
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modifie_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_manager (manager_id)
);

-- Compétences
CREATE TABLE competences (
    id_competence INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    type_competence VARCHAR(50) DEFAULT 'technique',
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_nom (nom),
    INDEX idx_type (type_competence)
);

-- Départements
CREATE TABLE departements (
    id_departement INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    description TEXT,
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Projets
CREATE TABLE projets (
    id_projet INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    description TEXT,
    responsable_id INT,
    statut VARCHAR(50) DEFAULT 'en_cours',
    date_debut DATE,
    date_fin DATE,
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modifie_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (responsable_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE SET NULL,
    INDEX idx_statut (statut)
);

-- Compétences des utilisateurs
CREATE TABLE competences_utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    id_competence INT NOT NULL,
    niveau_declare INT DEFAULT 1,
    niveau_valide INT,
    id_manager_validateur INT,
    date_validation TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    FOREIGN KEY (id_competence) REFERENCES competences(id_competence) ON DELETE CASCADE,
    FOREIGN KEY (id_manager_validateur) REFERENCES utilisateurs(id_utilisateur) ON DELETE SET NULL,
    UNIQUE KEY unique_user_comp (user_id, id_competence),
    INDEX idx_user (user_id)
);

-- Projets et utilisateurs
CREATE TABLE projets_utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    projet_id INT NOT NULL,
    user_id INT NOT NULL,
    role_projet VARCHAR(100),
    FOREIGN KEY (projet_id) REFERENCES projets(id_projet) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    UNIQUE KEY unique_projet_user (projet_id, user_id)
);

-- Certifications
CREATE TABLE certifications (
    id_certification INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nom VARCHAR(255) NOT NULL,
    organisme VARCHAR(255),
    date_obtention DATE,
    date_expiration DATE,
    url_verification VARCHAR(255),
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    INDEX idx_user (user_id)
);

-- Documents
CREATE TABLE documents (
    id_document INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nom VARCHAR(255) NOT NULL,
    type VARCHAR(50),
    url_fichier VARCHAR(255) NOT NULL,
    date_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_type (type)
);

-- Demandes de validation
CREATE TABLE demandes_validation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    competence_id INT NOT NULL,
    niveau_declare INT NOT NULL,
    statut VARCHAR(50) DEFAULT 'en_attente',
    manager_id INT,
    commentaire TEXT,
    date_demande TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_validation TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    FOREIGN KEY (competence_id) REFERENCES competences(id_competence) ON DELETE CASCADE,
    FOREIGN KEY (manager_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE SET NULL,
    UNIQUE KEY unique_user_comp (user_id, competence_id),
    INDEX idx_statut (statut),
    INDEX idx_manager (manager_id)
);

-- Réunions
CREATE TABLE reunions (
    id_reunion INT AUTO_INCREMENT PRIMARY KEY,
    employe_id INT NOT NULL,
    manager_id INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    date_reunion TIMESTAMP NOT NULL,
    duree_minutes INT,
    notes TEXT,
    status VARCHAR(50) DEFAULT 'planifiee',
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employe_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    FOREIGN KEY (manager_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    INDEX idx_date (date_reunion),
    INDEX idx_employe (employe_id)
);

-- Messages de contact
CREATE TABLE messages (
    id_message INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255),
    email VARCHAR(255),
    sujet VARCHAR(255),
    message TEXT,
    is_read BOOLEAN DEFAULT FALSE,
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_is_read (is_read)
);

-- Tests
CREATE TABLE tests (
    id_test INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    competence_id INT,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    score_obtenu INT,
    score_maximum INT,
    date_test TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_completion TIMESTAMP NULL,
    status VARCHAR(50) DEFAULT 'en_cours',
    FOREIGN KEY (user_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    FOREIGN KEY (competence_id) REFERENCES competences(id_competence) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_status (status)
);

-- ============================================================================
-- DONNÉES DE TEST
-- ============================================================================

INSERT INTO utilisateurs (email, prenom, nom, mot_de_passe, role) VALUES
('admin@findin.fr', 'Admin', 'FindIN', '$2y$10$YourHashedPasswordHere', 'admin'),
('test@findin.fr', 'Test', 'User', '$2y$10$YourHashedPasswordHere', 'employe');

INSERT INTO competences (nom, description, type_competence) VALUES
('PHP', 'Programmation PHP', 'technique'),
('JavaScript', 'Programmation JavaScript', 'technique'),
('Python', 'Programmation Python', 'technique'),
('Communication', 'Compétences de communication', 'soft_skill'),
('Leadership', 'Leadership et gestion d''équipe', 'soft_skill'),
('SQL', 'Base de données SQL', 'technique'),
('React', 'Framework React', 'technique'),
('Anglais', 'Langue anglaise', 'langue');

-- ============================================================================
