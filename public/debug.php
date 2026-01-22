<?php
/**
 * debug.php - Affiche les informations de debug
 * Accès: http://localhost/FindIn/public/debug.php
 */

echo "<pre>";
echo "🔍 DEBUG INFORMATION\n";
echo "====================\n\n";

echo "1️⃣  REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'NOT SET') . "\n";
echo "2️⃣  REQUEST_METHOD: " . ($_SERVER['REQUEST_METHOD'] ?? 'NOT SET') . "\n";
echo "3️⃣  SERVER_NAME: " . ($_SERVER['SERVER_NAME'] ?? 'NOT SET') . "\n";
echo "4️⃣  SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'NOT SET') . "\n";
echo "5️⃣  PHP_SELF: " . ($_SERVER['PHP_SELF'] ?? 'NOT SET') . "\n";
echo "6️⃣  DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'NOT SET') . "\n";
echo "7️⃣  SCRIPT_FILENAME: " . ($_SERVER['SCRIPT_FILENAME'] ?? 'NOT SET') . "\n";
echo "\n";

// Parse the route
$request_uri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($request_uri, PHP_URL_PATH);
$path = preg_replace('#^/FindIn/public/?#i', '', $path);
$path = trim($path, '/');

echo "8️⃣  PARSED ROUTE: '$path'\n";
echo "\n";

if (empty($path)) {
    echo "✅ ROUTING: Match HOME PAGE (empty path)\n";
} elseif ($path === 'login') {
    echo "✅ ROUTING: Match LOGIN PAGE\n";
} elseif ($path === 'register') {
    echo "✅ ROUTING: Match REGISTER PAGE\n";
} else {
    echo "❌ ROUTING: NO MATCH for '$path'\n";
}

echo "</pre>";
?>
