-- ============================================
-- MIGRATION COMPLÈTE: utilisateurs -> users
-- ============================================
-- Ce script migre toutes les données de la table utilisateurs vers users
-- et met à jour toutes les références

USE gestion_competences;

-- ÉTAPE 1: Modifier la structure de la table users pour correspondre à utilisateurs
-- ================================================================================

ALTER TABLE users 
    MODIFY COLUMN id CHAR(36) NOT NULL,
    ADD COLUMN id_departement CHAR(36) DEFAULT NULL AFTER departement,
    DROP PRIMARY KEY,
    ADD PRIMARY KEY (id);

-- ÉTAPE 2: Supprimer toutes les contraintes de clés étrangères
-- ============================================================

ALTER TABLE besoins_competences_projets 
    DROP FOREIGN KEY fk_besoins_projets_utilisateur;

ALTER TABLE competences_utilisateurs 
    DROP FOREIGN KEY fk_comp_utilisateur_manager,
    DROP FOREIGN KEY fk_comp_utilisateur_utilisateur;

ALTER TABLE documents_utilisateurs 
    DROP FOREIGN KEY fk_documents_utilisateurs_utilisateur;

ALTER TABLE membres_projets 
    DROP FOREIGN KEY fk_membres_projets_utilisateur;

ALTER TABLE projets 
    DROP FOREIGN KEY fk_projets_responsable;

-- ÉTAPE 3: Migrer les données de utilisateurs vers users
-- =======================================================

-- D'abord, supprimer les données de test dans users si elles existent
DELETE FROM users WHERE email NOT IN (SELECT email FROM utilisateurs);

-- Migrer toutes les données
INSERT INTO users (id, email, prenom, nom, id_departement, role, password, created_at)
SELECT 
    id_utilisateur,
    email,
    prenom,
    nom,
    id_departement,
    'employe' as role,
    NULL as password,
    cree_le
FROM utilisateurs
ON DUPLICATE KEY UPDATE
    id = VALUES(id),
    prenom = VALUES(prenom),
    nom = VALUES(nom),
    id_departement = VALUES(id_departement);

-- ÉTAPE 4: Mettre à jour toutes les références
-- =============================================

-- Renommer les colonnes pour correspondre au nouveau schéma
ALTER TABLE besoins_competences_projets 
    CHANGE COLUMN id_utilisateur_affecte user_id CHAR(36) DEFAULT NULL;

ALTER TABLE competences_utilisateurs 
    CHANGE COLUMN id_utilisateur user_id CHAR(36) NOT NULL,
    CHANGE COLUMN id_manager_validateur manager_id CHAR(36) DEFAULT NULL;

ALTER TABLE documents_utilisateurs 
    CHANGE COLUMN id_utilisateur user_id CHAR(36) NOT NULL;

ALTER TABLE membres_projets 
    CHANGE COLUMN id_utilisateur user_id CHAR(36) NOT NULL;

ALTER TABLE projets 
    CHANGE COLUMN id_responsable user_id CHAR(36) NOT NULL;

-- ÉTAPE 5: Recréer les contraintes de clés étrangères vers users
-- ==============================================================

ALTER TABLE besoins_competences_projets
    ADD CONSTRAINT fk_besoins_projets_user 
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL;

ALTER TABLE competences_utilisateurs
    ADD CONSTRAINT fk_comp_utilisateur_user 
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    ADD CONSTRAINT fk_comp_utilisateur_manager 
    FOREIGN KEY (manager_id) REFERENCES users(id) ON DELETE SET NULL;

ALTER TABLE documents_utilisateurs
    ADD CONSTRAINT fk_documents_user 
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;

ALTER TABLE membres_projets
    ADD CONSTRAINT fk_membres_projets_user 
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;

ALTER TABLE projets
    ADD CONSTRAINT fk_projets_responsable 
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT;

-- ÉTAPE 6: Supprimer la table utilisateurs
-- =========================================

DROP TABLE utilisateurs;

-- ÉTAPE 7: Mettre à jour l'admin existant
-- ========================================

UPDATE users 
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    role = 'admin'
WHERE email = 'admin@findin.com';

-- ============================================
-- MIGRATION TERMINÉE
-- ============================================

SELECT 'Migration terminée avec succès!' AS status;
SELECT COUNT(*) AS nombre_utilisateurs FROM users;
