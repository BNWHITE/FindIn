<?php if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) { header('Location: /login'); exit; }
require_once __DIR__ . '/../views/layouts/header.php';
require_once __DIR__ . '/../Models/Database.php';

$db = \Database::getInstance();
$total_employees = 0;
$open_positions = 0; // placeholder
$recruiting_pipeline = 0; // placeholder
try {
    $stmt = $db->query("SELECT COUNT(*) as c FROM utilisateurs WHERE role != 'admin'");
    $row = $stmt->fetch(); $total_employees = $row ? (int)$row['c'] : 0;
    $stmt = $db->query("SELECT COUNT(*) as c FROM competences");
    $row = $stmt->fetch(); $total_comp = $row ? (int)$row['c'] : 0;
} catch (Exception $e) {
}
?>
<main class="container">
    <section class="section">
        <h1>Dashboard RH</h1>
        <p>Vue RH : recrutement, pipeline et mapping des compétences</p>

        <div class="grid" style="margin-top:1.5rem;">
            <div class="card">
                <h3>Total employés</h3>
                <p style="font-size:2rem;font-weight:700"><?php echo $total_employees; ?></p>
                <p class="text-muted">Employés enregistrés</p>
            </div>
            <div class="card">
                <h3>Compétences référencées</h3>
                <p style="font-size:2rem;font-weight:700"><?php echo $total_comp; ?></p>
                <p class="text-muted">Compétences sur la plateforme</p>
            </div>
            <div class="card">
                <h3>Pipeline</h3>
                <p style="font-size:2rem;font-weight:700"><?php echo $recruiting_pipeline; ?></p>
                <p class="text-muted">Candidats en processus (placeholder)</p>
            </div>
        </div>

        <div style="margin-top:2rem;">
            <h2>Recrutements récents</h2>
            <p class="text-muted">(Section à compléter avec des données réelles)</p>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/../views/layouts/footer.php';
