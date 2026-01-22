#!/bin/bash

# Script de fix rapide pour les indexes - GESTION D'ERREURS COMPLÈTE
echo "🔧 Fix rapide - Nettoyage et recréation des indexes"
echo "════════════════════════════════════════════════════════════════"
echo ""

# 1. Supprimer les indexes en gérant les erreurs
echo "1️⃣  Suppression des indexes existants..."

mysql -u root findin -e "ALTER TABLE competences_utilisateurs DROP INDEX idx_user_statut;" 2>/dev/null && echo "   ✅ idx_user_statut supprimé"
mysql -u root findin -e "ALTER TABLE projets_utilisateurs DROP INDEX idx_projet_user;" 2>/dev/null && echo "   ✅ idx_projet_user supprimé"
mysql -u root findin -e "ALTER TABLE utilisateurs DROP INDEX idx_role_manager;" 2>/dev/null && echo "   ✅ idx_role_manager supprimé"

echo ""
echo "2️⃣  Recréation des indexes (individuellement)..."

# 2. Recréer les indexes individuellement
mysql -u root findin -e "ALTER TABLE competences_utilisateurs ADD INDEX idx_user_val (user_id);" 2>/dev/null && echo "   ✅ competences_utilisateurs.idx_user_val"
mysql -u root findin -e "ALTER TABLE competences_utilisateurs ADD INDEX idx_statut_val (statut);" 2>/dev/null && echo "   ✅ competences_utilisateurs.idx_statut_val"

mysql -u root findin -e "ALTER TABLE projets_utilisateurs ADD INDEX idx_projet_val (projet_id);" 2>/dev/null && echo "   ✅ projets_utilisateurs.idx_projet_val"
mysql -u root findin -e "ALTER TABLE projets_utilisateurs ADD INDEX idx_user_proj (user_id);" 2>/dev/null && echo "   ✅ projets_utilisateurs.idx_user_proj"

mysql -u root findin -e "ALTER TABLE utilisateurs ADD INDEX idx_role_val (role);" 2>/dev/null && echo "   ✅ utilisateurs.idx_role_val"
mysql -u root findin -e "ALTER TABLE utilisateurs ADD INDEX idx_manager_val (manager_id);" 2>/dev/null && echo "   ✅ utilisateurs.idx_manager_val"

echo ""
echo "3️⃣  Vérification des indexes créés..."
echo ""

mysql -u root findin -e "
SELECT 
    TABLE_NAME as 'Table',
    INDEX_NAME as 'Index',
    COLUMN_NAME as 'Colonne',
    SEQ_IN_INDEX as 'Position'
FROM information_schema.STATISTICS 
WHERE TABLE_SCHEMA = 'findin'
AND TABLE_NAME IN ('competences_utilisateurs', 'projets_utilisateurs', 'utilisateurs')
AND INDEX_NAME NOT IN ('PRIMARY')
ORDER BY TABLE_NAME, INDEX_NAME, SEQ_IN_INDEX;
" 2>&1

echo ""
echo "════════════════════════════════════════════════════════════════"
echo "✅ Fix appliqué avec succès!"
echo "════════════════════════════════════════════════════════════════"
echo ""
echo "📊 Vérification des tables..."
mysql -u root findin -e "
SELECT 
    TABLE_NAME,
    TABLE_ROWS as ROWS,
    DATA_LENGTH,
    INDEX_LENGTH
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'findin'
ORDER BY TABLE_NAME;
" 2>&1

echo ""
echo "✨ Système prêt!"
