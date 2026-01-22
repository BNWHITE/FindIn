-- ============================================================================
-- FINDIN - SUPABASE SCHEMA (PostgreSQL)
-- ============================================================================
-- Ce fichier crée toutes les tables nécessaires pour le dashboard FindIN
-- À exécuter dans l'éditeur SQL de Supabase
-- ============================================================================

-- 1. TABLE: UTILISATEURS (Gestion utilisateurs)
CREATE TABLE IF NOT EXISTS utilisateurs (
    id_utilisateur UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    email VARCHAR(255) UNIQUE NOT NULL,
    prenom VARCHAR(100),
    nom VARCHAR(100),
    mot_de_passe VARCHAR(255) NOT NULL,
    id_departement UUID,
    role VARCHAR(50) DEFAULT 'employe', -- employe, manager, rh, admin
    photo VARCHAR(255),
    competences TEXT,
    last_cv VARCHAR(255),
    manager_id UUID,
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modifie_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(email)
);

-- Index pour les requêtes fréquentes
CREATE INDEX IF NOT EXISTS idx_utilisateurs_role ON utilisateurs(role);
CREATE INDEX IF NOT EXISTS idx_utilisateurs_manager ON utilisateurs(manager_id);
CREATE INDEX IF NOT EXISTS idx_utilisateurs_email ON utilisateurs(email);

-- 2. TABLE: COMPETENCES (Inventaire des compétences)
CREATE TABLE IF NOT EXISTS competences (
    id_competence UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    nom VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    type_competence VARCHAR(50) DEFAULT 'technique', -- technique, soft_skill, langue, certification
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Index
CREATE INDEX IF NOT EXISTS idx_competences_nom ON competences(nom);
CREATE INDEX IF NOT EXISTS idx_competences_type ON competences(type_competence);

-- 3. TABLE: COMPETENCES_UTILISATEURS (Mapping utilisateur <-> compétence)
CREATE TABLE IF NOT EXISTS competences_utilisateurs (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID NOT NULL REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    id_competence UUID NOT NULL REFERENCES competences(id_competence) ON DELETE CASCADE,
    niveau_declare INTEGER DEFAULT 1, -- 1-5 (auto-déclaré par utilisateur)
    niveau_valide INTEGER, -- 1-5 (validé par manager)
    id_manager_validateur UUID REFERENCES utilisateurs(id_utilisateur),
    date_validation TIMESTAMP,
    UNIQUE(user_id, id_competence)
);

-- Index
CREATE INDEX IF NOT EXISTS idx_comp_user_user_id ON competences_utilisateurs(user_id);
CREATE INDEX IF NOT EXISTS idx_comp_user_competence ON competences_utilisateurs(id_competence);
CREATE INDEX IF NOT EXISTS idx_comp_user_manager ON competences_utilisateurs(id_manager_validateur);

-- 4. TABLE: DEPARTEMENTS
CREATE TABLE IF NOT EXISTS departements (
    id_departement UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    nom VARCHAR(255) NOT NULL,
    description TEXT,
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 5. TABLE: PROJETS (Gestion des projets)
CREATE TABLE IF NOT EXISTS projets (
    id_projet UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    nom VARCHAR(255) NOT NULL,
    description TEXT,
    responsable_id UUID REFERENCES utilisateurs(id_utilisateur),
    statut VARCHAR(50) DEFAULT 'en_cours', -- en_cours, termine, suspendu
    date_debut DATE,
    date_fin DATE,
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modifie_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Index
CREATE INDEX IF NOT EXISTS idx_projets_responsable ON projets(responsable_id);
CREATE INDEX IF NOT EXISTS idx_projets_statut ON projets(statut);

-- 6. TABLE: PROJETS_UTILISATEURS (Qui travaille sur quel projet)
CREATE TABLE IF NOT EXISTS projets_utilisateurs (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    projet_id UUID NOT NULL REFERENCES projets(id_projet) ON DELETE CASCADE,
    user_id UUID NOT NULL REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    role_projet VARCHAR(100), -- Rôle dans le projet (dev, manager, designer, etc)
    UNIQUE(projet_id, user_id)
);

-- 7. TABLE: CERTIFICATIONS (Certifications obtenues)
CREATE TABLE IF NOT EXISTS certifications (
    id_certification UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID NOT NULL REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    nom VARCHAR(255) NOT NULL,
    organisme VARCHAR(255),
    date_obtention DATE,
    date_expiration DATE,
    url_verification VARCHAR(255),
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Index
CREATE INDEX IF NOT EXISTS idx_certifications_user ON certifications(user_id);

-- 8. TABLE: DOCUMENTS (CVs, portofolios, etc)
CREATE TABLE IF NOT EXISTS documents (
    id_document UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID NOT NULL REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    nom VARCHAR(255) NOT NULL,
    type VARCHAR(50), -- cv, portfolio, certification, autre
    url_fichier VARCHAR(255) NOT NULL,
    date_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Index
CREATE INDEX IF NOT EXISTS idx_documents_user ON documents(user_id);
CREATE INDEX IF NOT EXISTS idx_documents_type ON documents(type);

-- 9. TABLE: DEMANDES_VALIDATION (Validation de compétences par manager)
CREATE TABLE IF NOT EXISTS demandes_validation (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID NOT NULL REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    competence_id UUID NOT NULL REFERENCES competences(id_competence) ON DELETE CASCADE,
    niveau_declare INTEGER NOT NULL,
    statut VARCHAR(50) DEFAULT 'en_attente', -- en_attente, approuvee, rejetee
    manager_id UUID REFERENCES utilisateurs(id_utilisateur),
    commentaire TEXT,
    date_demande TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_validation TIMESTAMP,
    UNIQUE(user_id, competence_id)
);

-- Index
CREATE INDEX IF NOT EXISTS idx_demandes_user ON demandes_validation(user_id);
CREATE INDEX IF NOT EXISTS idx_demandes_manager ON demandes_validation(manager_id);
CREATE INDEX IF NOT EXISTS idx_demandes_statut ON demandes_validation(statut);

-- 10. TABLE: REUNIONS (Réunions 1-to-1, reviews, etc)
CREATE TABLE IF NOT EXISTS reunions (
    id_reunion UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    employe_id UUID NOT NULL REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    manager_id UUID NOT NULL REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    date_reunion TIMESTAMP NOT NULL,
    duree_minutes INTEGER,
    notes TEXT,
    status VARCHAR(50) DEFAULT 'planifiee', -- planifiee, completee, repoussee
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Index
CREATE INDEX IF NOT EXISTS idx_reunions_employe ON reunions(employe_id);
CREATE INDEX IF NOT EXISTS idx_reunions_manager ON reunions(manager_id);
CREATE INDEX IF NOT EXISTS idx_reunions_date ON reunions(date_reunion);

-- 11. TABLE: MESSAGES/CONTACT
CREATE TABLE IF NOT EXISTS messages (
    id_message UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    nom VARCHAR(255),
    email VARCHAR(255),
    sujet VARCHAR(255),
    message TEXT,
    is_read BOOLEAN DEFAULT FALSE,
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Index
CREATE INDEX IF NOT EXISTS idx_messages_email ON messages(email);
CREATE INDEX IF NOT EXISTS idx_messages_is_read ON messages(is_read);

-- 12. TABLE: TESTS/EVALUATIONS
CREATE TABLE IF NOT EXISTS tests (
    id_test UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID NOT NULL REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    competence_id UUID REFERENCES competences(id_competence) ON DELETE SET NULL,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    score_obtenu INTEGER,
    score_maximum INTEGER,
    date_test TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_completion TIMESTAMP,
    status VARCHAR(50) DEFAULT 'en_cours' -- en_cours, complete, expire
);

-- Index
CREATE INDEX IF NOT EXISTS idx_tests_user ON tests(user_id);
CREATE INDEX IF NOT EXISTS idx_tests_competence ON tests(competence_id);

-- ============================================================================
-- DONNÉES DE TEST (Optionnel - À insérer pour démarrage rapide)
-- ============================================================================

-- Admin utilisateur de test
INSERT INTO utilisateurs (email, prenom, nom, mot_de_passe, role)
VALUES (
    'admin@findin.fr',
    'Admin',
    'FindIN',
    '$2y$10$YourHashedPasswordHere', -- Remplace par hash bcrypt real
    'admin'
) ON CONFLICT (email) DO NOTHING;

-- Compétences de test
INSERT INTO competences (nom, description, type_competence)
VALUES 
    ('PHP', 'Programmation PHP', 'technique'),
    ('JavaScript', 'Programmation JavaScript', 'technique'),
    ('Python', 'Programmation Python', 'technique'),
    ('Communication', 'Compétences de communication', 'soft_skill'),
    ('Leadership', 'Leadership et gestion d''équipe', 'soft_skill'),
    ('SQL', 'Base de données SQL', 'technique'),
    ('React', 'Framework React', 'technique'),
    ('Anglais', 'Langue anglaise', 'langue')
ON CONFLICT (nom) DO NOTHING;

-- ============================================================================
-- TRIGGERS (Optionnel - Pour automatic updates)
-- ============================================================================

-- Trigger pour mettre à jour modifie_le automatiquement
CREATE OR REPLACE FUNCTION update_modifie_le()
RETURNS TRIGGER AS $$
BEGIN
    NEW.modifie_le = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS utilisateurs_update_modifie_le ON utilisateurs;
CREATE TRIGGER utilisateurs_update_modifie_le BEFORE UPDATE ON utilisateurs
FOR EACH ROW
EXECUTE FUNCTION update_modifie_le();

DROP TRIGGER IF EXISTS projets_update_modifie_le ON projets;
CREATE TRIGGER projets_update_modifie_le BEFORE UPDATE ON projets
FOR EACH ROW
EXECUTE FUNCTION update_modifie_le();

DROP TRIGGER IF EXISTS documents_update_modifie_le ON documents;
CREATE TRIGGER documents_update_modifie_le BEFORE UPDATE ON documents
FOR EACH ROW
EXECUTE FUNCTION update_modifie_le();

-- ============================================================================
-- Row Level Security (Optionnel - Pour sécurité)
-- ============================================================================
-- À activer si tu veux ajouter de la sécurité niveau row

-- Activer RLS sur les tables sensibles
-- ALTER TABLE utilisateurs ENABLE ROW LEVEL SECURITY;
-- ALTER TABLE competences_utilisateurs ENABLE ROW LEVEL SECURITY;
-- ALTER TABLE demandes_validation ENABLE ROW LEVEL SECURITY;
