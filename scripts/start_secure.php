<?php
/**
 * FindIN Secure Starter
 * Lance le serveur avec proxy HTTPS local via OpenSSL
 */

echo "\n";
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║          🔐 FindIN - Serveur de Développement Sécurisé       ║\n";
echo "╠══════════════════════════════════════════════════════════════╣\n";
echo "║                                                              ║\n";
echo "║  📍 HTTP:  http://localhost:8000                            ║\n";
echo "║  📍 HTTPS: https://localhost:8443 (si configuré)            ║\n";
echo "║                                                              ║\n";
echo "║  ⚡ Options pour HTTPS:                                      ║\n";
echo "║     1. Installer Caddy: brew install caddy                  ║\n";
echo "║     2. Lancer: caddy run (dans un autre terminal)           ║\n";
echo "║     3. Ou: php start_secure.php                              ║\n";
echo "║                                                              ║\n";
echo "║  ⚠️  Le certificat est auto-signé (avertissement navigateur) ║\n";
echo "║                                                              ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n";
echo "\n";

// Vérifier si Caddy est disponible
$caddyAvailable = shell_exec('which caddy 2>/dev/null');

if ($caddyAvailable) {
    echo "✅ Caddy détecté! HTTPS automatique disponible.\n\n";
    echo "Pour HTTPS, ouvrez un autre terminal et lancez:\n";
    echo "   cd \"" . __DIR__ . "\"\n";
    echo "   caddy run\n\n";
}

// Créer le dossier SSL et générer un certificat si nécessaire
$sslDir = __DIR__ . '/ssl';
$certFile = $sslDir . '/localhost.pem';
$keyFile = $sslDir . '/localhost-key.pem';

if (!is_dir($sslDir)) {
    mkdir($sslDir, 0755, true);
}

if (!file_exists($certFile) || !file_exists($keyFile)) {
    echo "🔐 Génération du certificat SSL auto-signé...\n";
    
    $opensslCmd = sprintf(
        'openssl req -x509 -newkey rsa:2048 -keyout %s -out %s -sha256 -days 365 -nodes -subj "/CN=localhost" 2>/dev/null',
        escapeshellarg($keyFile),
        escapeshellarg($certFile)
    );
    
    exec($opensslCmd, $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "✅ Certificats générés dans: $sslDir\n\n";
    } else {
        echo "⚠️  Impossible de générer les certificats (OpenSSL non disponible?)\n\n";
    }
}

echo "🚀 Démarrage du serveur PHP...\n";
echo "   Appuyez sur Ctrl+C pour arrêter\n\n";

// Lancer le serveur PHP
$cmd = sprintf(
    'php -S localhost:8000 -t %s %s',
    escapeshellarg(__DIR__),
    escapeshellarg(__DIR__ . '/https_router.php')
);

passthru($cmd);
