-- ============================================================
-- Script de Nettoyage Base de Données FindIN
-- Date : 13 janvier 2026
-- Objectif : Supprimer les tables redondantes après consolidation
-- ============================================================

-- ATTENTION : Exécuter ce script APRÈS avoir vérifié que l'application fonctionne
-- avec les tables consolidées (utilisateurs et competences_utilisateurs)

-- ============================================================
-- ÉTAPE 1 : Migration des données (si nécessaire)
-- ============================================================

-- Migrer les utilisateurs de 'users' vers 'utilisateurs' si données manquantes
INSERT IGNORE INTO utilisateurs (id_utilisateur, email, mot_de_passe, prenom, nom, role)
SELECT 
    COALESCE(id, UUID()) as id_utilisateur,
    email,
    password as mot_de_passe,
    prenom,
    nom,
    role
FROM users 
WHERE email COLLATE utf8mb4_unicode_ci NOT IN (SELECT email FROM utilisateurs);

-- Migrer les compétences de 'user_competences' vers 'competences_utilisateurs' si données manquantes
INSERT IGNORE INTO competences_utilisateurs (user_id, id_competence, niveau_declare, niveau_valide, manager_id, date_validation)
SELECT 
    user_id,
    competence_id as id_competence,
    niveau_declare,
    niveau_valide,
    manager_id,
    date_validation
FROM user_competences
WHERE NOT EXISTS (
    SELECT 1 FROM competences_utilisateurs cu 
    WHERE cu.user_id COLLATE utf8mb4_unicode_ci = user_competences.user_id COLLATE utf8mb4_unicode_ci
    AND cu.id_competence = user_competences.competence_id
);

-- ============================================================
-- ÉTAPE 2 : Vérification avant suppression
-- ============================================================

-- Afficher le nombre d'enregistrements dans chaque table
SELECT 'utilisateurs' as table_name, COUNT(*) as count FROM utilisateurs
UNION ALL
SELECT 'users' as table_name, COUNT(*) as count FROM users
UNION ALL
SELECT 'competences_utilisateurs' as table_name, COUNT(*) as count FROM competences_utilisateurs
UNION ALL
SELECT 'user_competences' as table_name, COUNT(*) as count FROM user_competences;

-- ============================================================
-- ÉTAPE 3 : Suppression des tables redondantes
-- ============================================================

-- Supprimer les contraintes de clés étrangères pointant vers 'users'
ALTER TABLE besoins_competences_projets DROP FOREIGN KEY IF EXISTS fk_besoins_projets_user;
ALTER TABLE competences_utilisateurs DROP FOREIGN KEY IF EXISTS fk_comp_utilisateur_user;
ALTER TABLE competences_utilisateurs DROP FOREIGN KEY IF EXISTS fk_comp_utilisateur_manager;
ALTER TABLE documents_utilisateurs DROP FOREIGN KEY IF EXISTS fk_documents_user;
ALTER TABLE membres_projets DROP FOREIGN KEY IF EXISTS fk_membres_projets_user;
ALTER TABLE projets DROP FOREIGN KEY IF EXISTS fk_projets_responsable;

-- Maintenant supprimer les tables redondantes
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS user_competences;

-- Recréer les contraintes vers 'utilisateurs'
ALTER TABLE besoins_competences_projets 
    ADD CONSTRAINT fk_besoins_projets_user 
    FOREIGN KEY (user_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE SET NULL;

ALTER TABLE competences_utilisateurs 
    ADD CONSTRAINT fk_comp_utilisateur_user 
    FOREIGN KEY (user_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE;

ALTER TABLE competences_utilisateurs 
    ADD CONSTRAINT fk_comp_utilisateur_manager 
    FOREIGN KEY (manager_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE SET NULL;

ALTER TABLE documents_utilisateurs 
    ADD CONSTRAINT fk_documents_user 
    FOREIGN KEY (user_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE;

ALTER TABLE membres_projets 
    ADD CONSTRAINT fk_membres_projets_user 
    FOREIGN KEY (user_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE;

ALTER TABLE projets 
    ADD CONSTRAINT fk_projets_responsable 
    FOREIGN KEY (user_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE RESTRICT;

-- ============================================================
-- ÉTAPE 4 : Nettoyage des colonnes inutilisées (optionnel)
-- ============================================================

-- Supprimer les colonnes legacy non utilisées dans 'utilisateurs'
-- ALTER TABLE utilisateurs DROP COLUMN IF EXISTS competences;
-- ALTER TABLE utilisateurs DROP COLUMN IF EXISTS last_cv;

-- ============================================================
-- RÉSUMÉ APRÈS NETTOYAGE
-- ============================================================

-- Tables conservées :
-- ✅ utilisateurs      - Table principale des utilisateurs
-- ✅ competences       - Catalogue des compétences
-- ✅ competences_utilisateurs - Liaison utilisateurs-compétences
-- ✅ departements      - Structure organisationnelle
-- ✅ projets           - Gestion des projets
-- ✅ demandes_validation - Workflow de validation

-- Tables supprimées :
-- ❌ users             - Remplacée par 'utilisateurs'
-- ❌ user_competences  - Remplacée par 'competences_utilisateurs'
