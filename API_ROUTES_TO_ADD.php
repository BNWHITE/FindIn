#!/usr/bin/env php
<?php
/**
 * API_ROUTES.php - Routes API à ajouter dans public/index.php
 * 
 * Copie-colle ces routes dans le switch principal de public/index.php
 * Après le bloc "Routes principales" de dashboard, mais avant le "default:" final
 */

// ============================================================================
// COMPÉTENCES API
// ============================================================================
case 'api/competences/list':
    header('Content-Type: application/json; charset=utf-8');
    require_once __DIR__ . '/../src/Api/CompetenceApi.php';
    $api = new CompetenceApi();
    echo $api->list();
    exit;

case 'api/competences/get':
    header('Content-Type: application/json; charset=utf-8');
    require_once __DIR__ . '/../src/Api/CompetenceApi.php';
    $api = new CompetenceApi();
    echo $api->get();
    exit;

case 'api/competences/add':
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit;
    }
    header('Content-Type: application/json; charset=utf-8');
    require_once __DIR__ . '/../src/Api/CompetenceApi.php';
    $api = new CompetenceApi();
    echo $api->add();
    exit;

case 'api/competences/update':
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit;
    }
    header('Content-Type: application/json; charset=utf-8');
    require_once __DIR__ . '/../src/Api/CompetenceApi.php';
    $api = new CompetenceApi();
    echo $api->update();
    exit;

case 'api/competences/delete':
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit;
    }
    header('Content-Type: application/json; charset=utf-8');
    require_once __DIR__ . '/../src/Api/CompetenceApi.php';
    $api = new CompetenceApi();
    echo $api->delete();
    exit;

case 'api/competences/assignUser':
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit;
    }
    header('Content-Type: application/json; charset=utf-8');
    require_once __DIR__ . '/../src/Api/CompetenceApi.php';
    $api = new CompetenceApi();
    echo $api->assignUser();
    exit;

case 'api/competences/user':
    header('Content-Type: application/json; charset=utf-8');
    require_once __DIR__ . '/../src/Api/CompetenceApi.php';
    $api = new CompetenceApi();
    echo $api->getUserCompetences();
    exit;

// ============================================================================
// UTILISATEURS API
// ============================================================================
case 'api/utilisateurs/list':
    header('Content-Type: application/json; charset=utf-8');
    require_once __DIR__ . '/../src/Api/UtilisateurApi.php';
    $api = new UtilisateurApi();
    echo $api->list();
    exit;

case 'api/utilisateurs/get':
    header('Content-Type: application/json; charset=utf-8');
    require_once __DIR__ . '/../src/Api/UtilisateurApi.php';
    $api = new UtilisateurApi();
    echo $api->get();
    exit;

case 'api/utilisateurs/add':
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit;
    }
    header('Content-Type: application/json; charset=utf-8');
    require_once __DIR__ . '/../src/Api/UtilisateurApi.php';
    $api = new UtilisateurApi();
    echo $api->add();
    exit;

case 'api/utilisateurs/update':
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit;
    }
    header('Content-Type: application/json; charset=utf-8');
    require_once __DIR__ . '/../src/Api/UtilisateurApi.php';
    $api = new UtilisateurApi();
    echo $api->update();
    exit;

case 'api/utilisateurs/delete':
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit;
    }
    header('Content-Type: application/json; charset=utf-8');
    require_once __DIR__ . '/../src/Api/UtilisateurApi.php';
    $api = new UtilisateurApi();
    echo $api->delete();
    exit;

case 'api/utilisateurs/team':
    header('Content-Type: application/json; charset=utf-8');
    require_once __DIR__ . '/../src/Api/UtilisateurApi.php';
    $api = new UtilisateurApi();
    echo $api->getTeam();
    exit;

case 'api/utilisateurs/changePassword':
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit;
    }
    header('Content-Type: application/json; charset=utf-8');
    require_once __DIR__ . '/../src/Api/UtilisateurApi.php';
    $api = new UtilisateurApi();
    echo $api->changePassword();
    exit;

// ============================================================================
// PROJETS API
// ============================================================================
case 'api/projets/list':
    header('Content-Type: application/json; charset=utf-8');
    require_once __DIR__ . '/../src/Api/ProjetApi.php';
    $api = new ProjetApi();
    echo $api->list();
    exit;

case 'api/projets/add':
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit;
    }
    header('Content-Type: application/json; charset=utf-8');
    require_once __DIR__ . '/../src/Api/ProjetApi.php';
    $api = new ProjetApi();
    echo $api->add();
    exit;

case 'api/projets/update':
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit;
    }
    header('Content-Type: application/json; charset=utf-8');
    require_once __DIR__ . '/../src/Api/ProjetApi.php';
    $api = new ProjetApi();
    echo $api->update();
    exit;

case 'api/projets/delete':
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit;
    }
    header('Content-Type: application/json; charset=utf-8');
    require_once __DIR__ . '/../src/Api/ProjetApi.php';
    $api = new ProjetApi();
    echo $api->delete();
    exit;

case 'api/projets/members':
    header('Content-Type: application/json; charset=utf-8');
    require_once __DIR__ . '/../src/Api/ProjetApi.php';
    $api = new ProjetApi();
    echo $api->getMembers();
    exit;

case 'api/projets/addMember':
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit;
    }
    header('Content-Type: application/json; charset=utf-8');
    require_once __DIR__ . '/../src/Api/ProjetApi.php';
    $api = new ProjetApi();
    echo $api->addMember();
    exit;

// ============================================================================
// RÉUNIONS API
// ============================================================================
case 'api/reunions/list':
    header('Content-Type: application/json; charset=utf-8');
    require_once __DIR__ . '/../src/Api/ReuniionApi.php';
    $api = new ReuniionApi();
    echo $api->list();
    exit;

case 'api/reunions/add':
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit;
    }
    header('Content-Type: application/json; charset=utf-8');
    require_once __DIR__ . '/../src/Api/ReuniionApi.php';
    $api = new ReuniionApi();
    echo $api->add();
    exit;

case 'api/reunions/update':
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit;
    }
    header('Content-Type: application/json; charset=utf-8');
    require_once __DIR__ . '/../src/Api/ReuniionApi.php';
    $api = new ReuniionApi();
    echo $api->update();
    exit;

case 'api/reunions/delete':
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit;
    }
    header('Content-Type: application/json; charset=utf-8');
    require_once __DIR__ . '/../src/Api/ReuniionApi.php';
    $api = new ReuniionApi();
    echo $api->delete();
    exit;

// ============================================================================
// DOCUMENTS API
// ============================================================================
case 'api/documents/list':
    header('Content-Type: application/json; charset=utf-8');
    require_once __DIR__ . '/../src/Api/DocumentApi.php';
    $api = new DocumentApi();
    echo $api->list();
    exit;

case 'api/documents/add':
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit;
    }
    header('Content-Type: application/json; charset=utf-8');
    require_once __DIR__ . '/../src/Api/DocumentApi.php';
    $api = new DocumentApi();
    echo $api->add();
    exit;

case 'api/documents/delete':
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit;
    }
    header('Content-Type: application/json; charset=utf-8');
    require_once __DIR__ . '/../src/Api/DocumentApi.php';
    $api = new DocumentApi();
    echo $api->delete();
    exit;

// ============================================================================
// CERTIFICATIONS API
// ============================================================================
case 'api/certifications/list':
    header('Content-Type: application/json; charset=utf-8');
    require_once __DIR__ . '/../src/Api/CertificationApi.php';
    $api = new CertificationApi();
    echo $api->list();
    exit;

case 'api/certifications/add':
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit;
    }
    header('Content-Type: application/json; charset=utf-8');
    require_once __DIR__ . '/../src/Api/CertificationApi.php';
    $api = new CertificationApi();
    echo $api->add();
    exit;

case 'api/certifications/update':
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit;
    }
    header('Content-Type: application/json; charset=utf-8');
    require_once __DIR__ . '/../src/Api/CertificationApi.php';
    $api = new CertificationApi();
    echo $api->update();
    exit;

case 'api/certifications/delete':
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit;
    }
    header('Content-Type: application/json; charset=utf-8');
    require_once __DIR__ . '/../src/Api/CertificationApi.php';
    $api = new CertificationApi();
    echo $api->delete();
    exit;

?>
