#!/usr/bin/env bash

#============================================================================
# SUPABASE TEST SCRIPT - FindIN API Testing
# Usage: bash test_apis.sh
#============================================================================

BASE_URL="http://localhost:8000"
ADMIN_TOKEN="test-token"  # À remplacer par vrai token

echo "🚀 Supabase FindIN - API Testing"
echo "=================================="
echo ""

# Test 1: Liste des compétences
echo "1️⃣  GET /api/competences/list"
curl -s "$BASE_URL/api/competences/list" | jq '.'
echo ""

# Test 2: Ajouter une compétence
echo "2️⃣  POST /api/competences/add"
curl -s -X POST "$BASE_URL/api/competences/add" \
  -H "Content-Type: application/json" \
  -d '{
    "nom": "Docker",
    "description": "Conteneurisation avec Docker",
    "type_competence": "technique"
  }' | jq '.'
echo ""

# Test 3: Liste des utilisateurs
echo "3️⃣  GET /api/utilisateurs/list"
curl -s "$BASE_URL/api/utilisateurs/list" | jq '.'
echo ""

# Test 4: Liste des projets
echo "4️⃣  GET /api/projets/list"
curl -s "$BASE_URL/api/projets/list" | jq '.'
echo ""

# Test 5: Ajouter un projet
echo "5️⃣  POST /api/projets/add"
curl -s -X POST "$BASE_URL/api/projets/add" \
  -H "Content-Type: application/json" \
  -d '{
    "nom": "Projet Test",
    "description": "Projet de test",
    "statut": "en_cours"
  }' | jq '.'
echo ""

echo "✅ Tests terminés !"
