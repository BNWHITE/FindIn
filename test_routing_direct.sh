#!/bin/bash

echo "🔍 Test direct du routeur FindIN"
echo "================================="
echo ""

# Test 1: Requête à la racine /FindIn/public
echo "1️⃣  Test requête: /FindIn/public"
php << 'PHPEOF'
<?php
$_SERVER['REQUEST_URI'] = '/FindIn/public';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_HOST'] = 'localhost';

if (session_status() === PHP_SESSION_NONE) session_start();

define('ROOT_DIR', dirname(__FILE__));
define('SRC_DIR', ROOT_DIR . '/src');
define('VIEWS_DIR', SRC_DIR . '/Views');
define('CONTROLLERS_DIR', SRC_DIR . '/Controllers');
define('MODELS_DIR', SRC_DIR . '/Models');
define('CONFIG_DIR', SRC_DIR . '/Config');

if (!defined('DB_TYPE')) {
    define('DB_TYPE', 'mysql');
}

require_once CONFIG_DIR . '/database.php';

function parseRoute() {
    $request_uri = $_SERVER['REQUEST_URI'] ?? '/';
    echo "   Raw REQUEST_URI: $request_uri\n";
    
    $path = parse_url($request_uri, PHP_URL_PATH);
    echo "   After parse_url: $path\n";
    
    $path = preg_replace('#^/FindIn/public/?#i', '', $path);
    echo "   After regex replace: '$path'\n";
    
    $path = trim($path, '/');
    echo "   After trim: '$path'\n";
    
    return $path ?: '';
}

$parsedPath = parseRoute();
echo "   Final path: '$parsedPath'\n";
echo "   Match home page: " . (empty($parsedPath) ? "✅ YES" : "❌ NO") . "\n";
?>
PHPEOF

echo ""
echo "2️⃣  Test requête: /FindIn/public/"
php << 'PHPEOF'
<?php
$_SERVER['REQUEST_URI'] = '/FindIn/public/';
$_SERVER['REQUEST_METHOD'] = 'GET';

function parseRoute() {
    $request_uri = $_SERVER['REQUEST_URI'] ?? '/';
    $path = parse_url($request_uri, PHP_URL_PATH);
    $path = preg_replace('#^/FindIn/public/?#i', '', $path);
    $path = trim($path, '/');
    return $path ?: '';
}

$parsedPath = parseRoute();
echo "   REQUEST_URI: {$_SERVER['REQUEST_URI']}\n";
echo "   Final path: '$parsedPath'\n";
echo "   Match home page: " . (empty($parsedPath) ? "✅ YES" : "❌ NO") . "\n";
?>
PHPEOF

echo ""
echo "3️⃣  Test requête: /FindIn/public/login"
php << 'PHPEOF'
<?php
$_SERVER['REQUEST_URI'] = '/FindIn/public/login';
$_SERVER['REQUEST_METHOD'] = 'GET';

function parseRoute() {
    $request_uri = $_SERVER['REQUEST_URI'] ?? '/';
    $path = parse_url($request_uri, PHP_URL_PATH);
    $path = preg_replace('#^/FindIn/public/?#i', '', $path);
    $path = trim($path, '/');
    return $path ?: '';
}

$parsedPath = parseRoute();
echo "   REQUEST_URI: {$_SERVER['REQUEST_URI']}\n";
echo "   Final path: '$parsedPath'\n";
echo "   Match login: " . (($parsedPath === 'login') ? "✅ YES" : "❌ NO") . "\n";
?>
PHPEOF

echo ""
echo "✅ Routing test completed"
