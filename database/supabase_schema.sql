-- ============================================================
-- Schema FindIN pour Supabase (PostgreSQL)
-- Date : 13 janvier 2026
-- ============================================================

-- Activer l'extension UUID
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- ============================================================
-- Types ENUM personnalisés
-- ============================================================

DO $$ BEGIN
    CREATE TYPE role_type AS ENUM ('employe', 'manager', 'rh', 'admin');
EXCEPTION
    WHEN duplicate_object THEN null;
END $$;

DO $$ BEGIN
    CREATE TYPE type_competence AS ENUM ('savoir_faire', 'savoir_etre', 'expertise', 'technique', 'soft');
EXCEPTION
    WHEN duplicate_object THEN null;
END $$;

DO $$ BEGIN
    CREATE TYPE statut_projet AS ENUM ('planifie', 'actif', 'en_cours', 'termine', 'annule');
EXCEPTION
    WHEN duplicate_object THEN null;
END $$;

DO $$ BEGIN
    CREATE TYPE type_departement AS ENUM ('entreprise', 'division', 'departement', 'unite', 'equipe');
EXCEPTION
    WHEN duplicate_object THEN null;
END $$;

-- ============================================================
-- Table: categories_competences
-- ============================================================

CREATE TABLE IF NOT EXISTS categories_competences (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    nom VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- ============================================================
-- Table: departements
-- ============================================================

CREATE TABLE IF NOT EXISTS departements (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    nom VARCHAR(150) NOT NULL,
    id_entreprise UUID,
    type_departement type_departement DEFAULT 'departement',
    id_parent UUID REFERENCES departements(id) ON DELETE SET NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_departements_parent ON departements(id_parent);

-- ============================================================
-- Table: utilisateurs
-- ============================================================

CREATE TABLE IF NOT EXISTS utilisateurs (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    email VARCHAR(255) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255),
    prenom VARCHAR(100),
    nom VARCHAR(100),
    role role_type DEFAULT 'employe',
    id_departement UUID REFERENCES departements(id) ON DELETE SET NULL,
    manager_id UUID REFERENCES utilisateurs(id) ON DELETE SET NULL,
    photo VARCHAR(500),
    google_id VARCHAR(255),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_utilisateurs_email ON utilisateurs(email);
CREATE INDEX IF NOT EXISTS idx_utilisateurs_role ON utilisateurs(role);
CREATE INDEX IF NOT EXISTS idx_utilisateurs_departement ON utilisateurs(id_departement);

-- ============================================================
-- Table: competences
-- ============================================================

CREATE TABLE IF NOT EXISTS competences (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    nom VARCHAR(150) NOT NULL UNIQUE,
    description TEXT,
    id_categorie UUID REFERENCES categories_competences(id) ON DELETE SET NULL,
    type_competence type_competence DEFAULT 'technique',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_competences_categorie ON competences(id_categorie);
CREATE INDEX IF NOT EXISTS idx_competences_type ON competences(type_competence);

-- ============================================================
-- Table: competences_utilisateurs (liaison)
-- ============================================================

CREATE TABLE IF NOT EXISTS competences_utilisateurs (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    user_id UUID NOT NULL REFERENCES utilisateurs(id) ON DELETE CASCADE,
    competence_id UUID NOT NULL REFERENCES competences(id) ON DELETE CASCADE,
    niveau_declare SMALLINT DEFAULT 1 CHECK (niveau_declare BETWEEN 1 AND 5),
    niveau_valide SMALLINT CHECK (niveau_valide BETWEEN 1 AND 5),
    manager_id UUID REFERENCES utilisateurs(id) ON DELETE SET NULL,
    date_validation TIMESTAMP WITH TIME ZONE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    UNIQUE(user_id, competence_id)
);

CREATE INDEX IF NOT EXISTS idx_comp_utilisateurs_user ON competences_utilisateurs(user_id);
CREATE INDEX IF NOT EXISTS idx_comp_utilisateurs_competence ON competences_utilisateurs(competence_id);

-- ============================================================
-- Table: projets
-- ============================================================

CREATE TABLE IF NOT EXISTS projets (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    nom VARCHAR(200) NOT NULL,
    description TEXT,
    date_debut DATE,
    date_fin DATE,
    statut statut_projet DEFAULT 'planifie',
    responsable_id UUID REFERENCES utilisateurs(id) ON DELETE SET NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_projets_statut ON projets(statut);
CREATE INDEX IF NOT EXISTS idx_projets_responsable ON projets(responsable_id);

-- ============================================================
-- Table: membres_projets
-- ============================================================

CREATE TABLE IF NOT EXISTS membres_projets (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    projet_id UUID NOT NULL REFERENCES projets(id) ON DELETE CASCADE,
    user_id UUID NOT NULL REFERENCES utilisateurs(id) ON DELETE CASCADE,
    role_dans_projet VARCHAR(100),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    UNIQUE(projet_id, user_id)
);

-- ============================================================
-- Table: besoins_competences_projets
-- ============================================================

CREATE TABLE IF NOT EXISTS besoins_competences_projets (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    projet_id UUID NOT NULL REFERENCES projets(id) ON DELETE CASCADE,
    competence_id UUID NOT NULL REFERENCES competences(id) ON DELETE CASCADE,
    niveau_requis SMALLINT CHECK (niveau_requis BETWEEN 1 AND 5),
    description TEXT,
    statut VARCHAR(50) DEFAULT 'ouvert',
    user_id UUID REFERENCES utilisateurs(id) ON DELETE SET NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- ============================================================
-- Table: documents_utilisateurs
-- ============================================================

CREATE TABLE IF NOT EXISTS documents_utilisateurs (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    user_id UUID NOT NULL REFERENCES utilisateurs(id) ON DELETE CASCADE,
    chemin_fichier TEXT NOT NULL,
    nom_fichier VARCHAR(255),
    type_document VARCHAR(50),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- ============================================================
-- Table: demandes_validation
-- ============================================================

CREATE TABLE IF NOT EXISTS demandes_validation (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    user_id UUID NOT NULL REFERENCES utilisateurs(id) ON DELETE CASCADE,
    competence_id UUID NOT NULL REFERENCES competences(id) ON DELETE CASCADE,
    niveau_demande SMALLINT CHECK (niveau_demande BETWEEN 1 AND 5),
    statut VARCHAR(50) DEFAULT 'en_attente',
    manager_id UUID REFERENCES utilisateurs(id) ON DELETE SET NULL,
    commentaire TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- ============================================================
-- Table: messages
-- ============================================================

CREATE TABLE IF NOT EXISTS messages (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    sender_id UUID REFERENCES utilisateurs(id) ON DELETE SET NULL,
    recipient_id UUID REFERENCES utilisateurs(id) ON DELETE SET NULL,
    sujet VARCHAR(255),
    contenu TEXT,
    lu BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- ============================================================
-- Données initiales
-- ============================================================

-- Catégories de compétences
INSERT INTO categories_competences (nom) VALUES
    ('Langages de programmation'),
    ('Frameworks & Outils'),
    ('Bases de données'),
    ('Soft Skills'),
    ('Langues'),
    ('Certifications')
ON CONFLICT (nom) DO NOTHING;

-- Compétences de base
INSERT INTO competences (nom, description, type_competence) VALUES
    ('PHP', 'Langage de programmation web côté serveur', 'technique'),
    ('JavaScript', 'Langage de programmation web côté client', 'technique'),
    ('Python', 'Langage de programmation polyvalent', 'technique'),
    ('SQL', 'Langage de requêtes pour bases de données', 'technique'),
    ('HTML/CSS', 'Langages de structuration et style web', 'technique'),
    ('Communication', 'Capacité à communiquer efficacement', 'soft'),
    ('Leadership', 'Capacité à diriger une équipe', 'soft'),
    ('Travail en équipe', 'Capacité à collaborer', 'soft'),
    ('Gestion de projet', 'Méthodologies de gestion de projet', 'expertise'),
    ('React', 'Framework JavaScript pour interfaces', 'technique')
ON CONFLICT (nom) DO NOTHING;

-- Utilisateur admin par défaut
INSERT INTO utilisateurs (email, mot_de_passe, prenom, nom, role) VALUES
    ('admin@findin.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'FindIN', 'admin')
ON CONFLICT (email) DO NOTHING;

-- ============================================================
-- Row Level Security (RLS) - Optionnel mais recommandé
-- ============================================================

-- Activer RLS sur les tables sensibles
ALTER TABLE utilisateurs ENABLE ROW LEVEL SECURITY;
ALTER TABLE competences_utilisateurs ENABLE ROW LEVEL SECURITY;
ALTER TABLE documents_utilisateurs ENABLE ROW LEVEL SECURITY;
ALTER TABLE messages ENABLE ROW LEVEL SECURITY;

-- Policies basiques (à adapter selon vos besoins)
-- Pour l'instant, autoriser tout pour le service role
CREATE POLICY "Service role can do anything on utilisateurs" ON utilisateurs
    FOR ALL USING (true) WITH CHECK (true);

CREATE POLICY "Service role can do anything on competences_utilisateurs" ON competences_utilisateurs
    FOR ALL USING (true) WITH CHECK (true);

CREATE POLICY "Service role can do anything on documents_utilisateurs" ON documents_utilisateurs
    FOR ALL USING (true) WITH CHECK (true);

CREATE POLICY "Service role can do anything on messages" ON messages
    FOR ALL USING (true) WITH CHECK (true);

-- ============================================================
-- Fonction de mise à jour automatique updated_at
-- ============================================================

CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ language 'plpgsql';

CREATE TRIGGER update_utilisateurs_updated_at
    BEFORE UPDATE ON utilisateurs
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_demandes_validation_updated_at
    BEFORE UPDATE ON demandes_validation
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();
