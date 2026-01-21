<?php
// Script to generate all missing pages

$pages = [
    'features' => 'Fonctionnalités',
    'pricing' => 'Tarification',
    'security' => 'Sécurité',
    'roadmap' => 'Roadmap',
    'documentation' => 'Documentation',
    'blog' => 'Blog',
    'tutorials' => 'Tutoriels',
    'community' => 'Communauté',
    'privacy' => 'Confidentialité',
    'terms' => 'Conditions',
    'cookies' => 'Cookies',
    'accessibility' => 'Accessibilité'
];

$template = file_get_contents(__DIR__ . '/views/product.php');

foreach ($pages as $slug => $title) {
    $content = str_replace(
        [
            '<h1>Notre Produit</h1>',
            '<p>Une plateforme complète pour révéler, valider et développer les talents cachés de votre organisation</p>',
            '<title>FindIN - Produit</title>',
            'title="FindIN - Produit"'
        ],
        [
            '<h1>' . $title . '</h1>',
            '<p>Découvrez ' . strtolower($title) . ' de FindIN</p>',
            '<title>FindIN - ' . $title . '</title>',
            'title="FindIN - ' . $title . '"'
        ],
        $template
    );
    
    $filepath = __DIR__ . '/views/' . $slug . '.php';
    file_put_contents($filepath, $content);
    echo "✅ Créé: views/$slug.php<br>";
}

echo "<br>✅ Toutes les pages ont été créées!<br>";
?>
