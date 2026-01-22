-- ============================================================================
-- FINDIN - SUPABASE FRESH START (PostgreSQL)
-- ============================================================================
-- SUPPRIME TOUT ET RECRÉE LE SCHÉMA DE ZÉRO
-- À exécuter dans Supabase SQL Editor
-- ============================================================================

-- ÉTAPE 1: SUPPRIMER TOUS LES TRIGGERS
DROP TRIGGER IF EXISTS utilisateurs_update_modifie_le ON utilisateurs;
DROP TRIGGER IF EXISTS projets_update_modifie_le ON projets;
DROP TRIGGER IF EXISTS documents_update_modifie_le ON documents;

-- ÉTAPE 2: SUPPRIMER TOUTES LES FONCTIONS
DROP FUNCTION IF EXISTS update_modifie_le() CASCADE;

-- ÉTAPE 3: SUPPRIMER TOUTES LES TABLES (dans l'ordre des dépendances)
DROP TABLE IF EXISTS demandes_validation CASCADE;
DROP TABLE IF EXISTS tests CASCADE;
DROP TABLE IF EXISTS certifications CASCADE;
DROP TABLE IF EXISTS documents CASCADE;
DROP TABLE IF EXISTS messages CASCADE;
DROP TABLE IF EXISTS reunions CASCADE;
DROP TABLE IF EXISTS projets_utilisateurs CASCADE;
DROP TABLE IF EXISTS projets CASCADE;
DROP TABLE IF EXISTS competences_utilisateurs CASCADE;
DROP TABLE IF EXISTS competences CASCADE;
DROP TABLE IF EXISTS departements CASCADE;
DROP TABLE IF EXISTS utilisateurs CASCADE;

-- ============================================================================
-- ÉTAPE 4: RECRÉER LES TABLES
-- ============================================================================

-- TABLE: UTILISATEURS
CREATE TABLE utilisateurs (
    id_utilisateur UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    email VARCHAR(255) UNIQUE NOT NULL,
    prenom VARCHAR(100),
    nom VARCHAR(100),
    mot_de_passe VARCHAR(255) NOT NULL,
    id_departement UUID,
    role VARCHAR(50) DEFAULT 'employe',
    photo VARCHAR(255),
    manager_id UUID,
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modifie_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_utilisateurs_role ON utilisateurs(role);
CREATE INDEX idx_utilisateurs_manager ON utilisateurs(manager_id);
CREATE INDEX idx_utilisateurs_email ON utilisateurs(email);

-- TABLE: COMPETENCES
CREATE TABLE competences (
    id_competence UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    nom VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    type_competence VARCHAR(50) DEFAULT 'technique',
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_competences_nom ON competences(nom);
CREATE INDEX idx_competences_type ON competences(type_competence);

-- TABLE: COMPETENCES_UTILISATEURS
CREATE TABLE competences_utilisateurs (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID NOT NULL REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    id_competence UUID NOT NULL REFERENCES competences(id_competence) ON DELETE CASCADE,
    niveau_declare INTEGER DEFAULT 1,
    niveau_valide INTEGER,
    id_manager_validateur UUID REFERENCES utilisateurs(id_utilisateur),
    date_validation TIMESTAMP,
    UNIQUE(user_id, id_competence)
);

CREATE INDEX idx_comp_user_user_id ON competences_utilisateurs(user_id);
CREATE INDEX idx_comp_user_competence ON competences_utilisateurs(id_competence);
CREATE INDEX idx_comp_user_manager ON competences_utilisateurs(id_manager_validateur);

-- TABLE: DEPARTEMENTS
CREATE TABLE departements (
    id_departement UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    nom VARCHAR(255) NOT NULL,
    description TEXT,
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- TABLE: PROJETS
CREATE TABLE projets (
    id_projet UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    nom VARCHAR(255) NOT NULL,
    description TEXT,
    responsable_id UUID REFERENCES utilisateurs(id_utilisateur),
    statut VARCHAR(50) DEFAULT 'en_cours',
    date_debut DATE,
    date_fin DATE,
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modifie_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_projets_responsable ON projets(responsable_id);
CREATE INDEX idx_projets_statut ON projets(statut);

-- TABLE: PROJETS_UTILISATEURS
CREATE TABLE projets_utilisateurs (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    projet_id UUID NOT NULL REFERENCES projets(id_projet) ON DELETE CASCADE,
    user_id UUID NOT NULL REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    role_projet VARCHAR(100),
    UNIQUE(projet_id, user_id)
);

-- TABLE: CERTIFICATIONS
CREATE TABLE certifications (
    id_certification UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID NOT NULL REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    nom VARCHAR(255) NOT NULL,
    organisme VARCHAR(255),
    date_obtention DATE,
    date_expiration DATE,
    url_verification VARCHAR(255),
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_certifications_user ON certifications(user_id);

-- TABLE: DOCUMENTS
CREATE TABLE documents (
    id_document UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID NOT NULL REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    nom VARCHAR(255) NOT NULL,
    type VARCHAR(50),
    url_fichier VARCHAR(255) NOT NULL,
    date_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_documents_user ON documents(user_id);
CREATE INDEX idx_documents_type ON documents(type);

-- TABLE: DEMANDES_VALIDATION
CREATE TABLE demandes_validation (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID NOT NULL REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    competence_id UUID NOT NULL REFERENCES competences(id_competence) ON DELETE CASCADE,
    niveau_declare INTEGER NOT NULL,
    statut VARCHAR(50) DEFAULT 'en_attente',
    manager_id UUID REFERENCES utilisateurs(id_utilisateur),
    commentaire TEXT,
    date_demande TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_validation TIMESTAMP,
    UNIQUE(user_id, competence_id)
);

CREATE INDEX idx_demandes_user ON demandes_validation(user_id);
CREATE INDEX idx_demandes_manager ON demandes_validation(manager_id);
CREATE INDEX idx_demandes_statut ON demandes_validation(statut);

-- TABLE: REUNIONS
CREATE TABLE reunions (
    id_reunion UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    employe_id UUID NOT NULL REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    manager_id UUID NOT NULL REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    date_reunion TIMESTAMP NOT NULL,
    duree_minutes INTEGER,
    notes TEXT,
    status VARCHAR(50) DEFAULT 'planifiee',
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_reunions_employe ON reunions(employe_id);
CREATE INDEX idx_reunions_manager ON reunions(manager_id);
CREATE INDEX idx_reunions_date ON reunions(date_reunion);

-- TABLE: MESSAGES
CREATE TABLE messages (
    id_message UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    nom VARCHAR(255),
    email VARCHAR(255),
    sujet VARCHAR(255),
    message TEXT,
    is_read BOOLEAN DEFAULT FALSE,
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_messages_email ON messages(email);
CREATE INDEX idx_messages_is_read ON messages(is_read);

-- TABLE: TESTS
CREATE TABLE tests (
    id_test UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID NOT NULL REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    competence_id UUID REFERENCES competences(id_competence) ON DELETE SET NULL,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    score_obtenu INTEGER,
    score_maximum INTEGER,
    date_test TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_completion TIMESTAMP,
    status VARCHAR(50) DEFAULT 'en_cours'
);

CREATE INDEX idx_tests_user ON tests(user_id);
CREATE INDEX idx_tests_competence ON tests(competence_id);

-- ============================================================================
-- ÉTAPE 5: CRÉER LA FONCTION TRIGGER (avant les triggers)
-- ============================================================================

CREATE OR REPLACE FUNCTION update_modifie_le()
RETURNS TRIGGER AS $$
BEGIN
    NEW.modifie_le = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- ============================================================================
-- ÉTAPE 6: CRÉER LES TRIGGERS
-- ============================================================================

CREATE TRIGGER utilisateurs_update_modifie_le BEFORE UPDATE ON utilisateurs
FOR EACH ROW
EXECUTE FUNCTION update_modifie_le();

CREATE TRIGGER projets_update_modifie_le BEFORE UPDATE ON projets
FOR EACH ROW
EXECUTE FUNCTION update_modifie_le();

CREATE TRIGGER documents_update_modifie_le BEFORE UPDATE ON documents
FOR EACH ROW
EXECUTE FUNCTION update_modifie_le();

-- ============================================================================
-- ÉTAPE 7: INSÉRER LES DONNÉES DE TEST
-- ============================================================================

-- Admin utilisateur de test
INSERT INTO utilisateurs (email, prenom, nom, mot_de_passe, role)
VALUES (
    'admin@findin.fr',
    'Admin',
    'FindIN',
    '$2y$10$YourHashedPasswordHere',
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
-- VÉRIFICATION: Les tables créées
-- ============================================================================
-- SELECT tablename FROM pg_tables WHERE schemaname = 'public' ORDER BY tablename;
-- Devrait afficher: certifications, competences, competences_utilisateurs, departements, 
--                   demandes_validation, documents, messages, projets, 
--                   projets_utilisateurs, reunions, tests, utilisateurs
