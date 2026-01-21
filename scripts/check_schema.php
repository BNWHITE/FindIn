<?php
require_once __DIR__ . '/../src/Config/database.php';

$dsn = "pgsql:host=" . SUPABASE_HOST . ";port=" . SUPABASE_PORT . ";dbname=" . SUPABASE_DB . ";sslmode=require";
$pdo = new PDO($dsn, SUPABASE_USER, SUPABASE_PASS);

$tables = ['utilisateurs', 'competences_utilisateurs', 'competences'];
foreach ($tables as $table) {
    $result = $pdo->query("SELECT column_name FROM information_schema.columns WHERE table_name = '$table' ORDER BY ordinal_position");
    echo "$table: " . implode(", ", $result->fetchAll(PDO::FETCH_COLUMN)) . "\n";
}
