-- ============================================================================
-- FINDIN - AJOUTER LES TRIGGERS (Exécute APRÈS supabase_final.sql)
-- ============================================================================
-- Exécute cette requête APRÈS que toutes les tables soient créées
-- dans Supabase SQL Editor
-- ============================================================================

-- Ajouter les triggers une fois que les tables existent

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
-- Triggers créés avec succès!
-- ============================================================================
