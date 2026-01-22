-- ============================================================================
-- FIX RAPIDE - Supprimer et recréer les indexes correctement
-- ============================================================================

USE findin;

-- 1. Supprimer les indexes problématiques (ignorer les erreurs si n'existent pas)
ALTER TABLE competences_utilisateurs DROP INDEX idx_user_statut;
ALTER TABLE projets_utilisateurs DROP INDEX idx_projet_user;
ALTER TABLE utilisateurs DROP INDEX idx_role_manager;

-- 2. Recréer les indexes correctement (un par un, pas composé)
ALTER TABLE competences_utilisateurs ADD INDEX idx_user (user_id);
ALTER TABLE competences_utilisateurs ADD INDEX idx_statut (statut);

ALTER TABLE projets_utilisateurs ADD INDEX idx_projet (projet_id);
ALTER TABLE projets_utilisateurs ADD INDEX idx_user (user_id);

ALTER TABLE utilisateurs ADD INDEX idx_role (role);
ALTER TABLE utilisateurs ADD INDEX idx_manager (manager_id);

-- 3. Vérifier que tout est correct
SELECT 
    TABLE_NAME,
    INDEX_NAME,
    COLUMN_NAME
FROM information_schema.STATISTICS 
WHERE TABLE_SCHEMA = 'findin'
AND TABLE_NAME IN ('competences_utilisateurs', 'projets_utilisateurs', 'utilisateurs')
AND INDEX_NAME NOT IN ('PRIMARY')
ORDER BY TABLE_NAME, INDEX_NAME, SEQ_IN_INDEX;

-- ============================================================================
-- Fin du fix
-- ============================================================================
