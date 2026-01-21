<?php if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) { header('Location: /login'); exit; }
require_once __DIR__ . '/../views/layouts/header.php';
require_once __DIR__ . '/../Models/Database.php';

// Basic stats from DB
$db = \Database::getInstance();
$total_team = 0;
$total_competences = 0;
$assigned_competences = 0;
try {
    $stmt = $db->query("SELECT COUNT(*) as c FROM utilisateurs WHERE role != 'admin'");
    $row = $stmt->fetch(); $total_team = $row ? (int)$row['c'] : 0;

    $stmt = $db->query("SELECT COUNT(*) as c FROM competences");
    $row = $stmt->fetch(); $total_competences = $row ? (int)$row['c'] : 0;

    $stmt = $db->query("SELECT COUNT(*) as c FROM competences_utilisateurs");
    $row = $stmt->fetch(); $assigned_competences = $row ? (int)$row['c'] : 0;
} catch (Exception $e) {
    // ignore DB errors, show zeros
}
?>
<main class="container">
    <section class="section">
        <h1>Dashboard Manager</h1>
        <p>Vue synthétique pour manager — équipe et performances</p>

        <div class="grid" style="margin-top:1.5rem;">
            <div class="card">
                <h3>Membres de l'équipe</h3>
                <p style="font-size:2rem;font-weight:700"><?php echo $total_team; ?></p>
                <p class="text-muted">Membres hors administrateurs</p>
            </div>
            <div class="card">
                <h3>Compétences disponibles</h3>
                <p style="font-size:2rem;font-weight:700"><?php echo $total_competences; ?></p>
                <p class="text-muted">Compétences référencées sur la plateforme</p>
            </div>
            <div class="card">
                <h3>Compétences assignées</h3>
                <p style="font-size:2rem;font-weight:700"><?php echo $assigned_competences; ?></p>
                <p class="text-muted">Total des paires utilisateur↔compétence</p>
            </div>
        </div>

        <div style="margin-top:2rem;">
            <h2>Équipe</h2>
            <table style="width:100%;border-collapse:collapse;margin-top:1rem;">
                <thead>
                    <tr><th style="text-align:left;padding:8px">Nom</th><th style="text-align:left;padding:8px">Email</th><th style="text-align:left;padding:8px">Rôle</th></tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        $stmt = $db->query("SELECT prenom, nom, email, role FROM utilisateurs WHERE role != 'admin' LIMIT 50");
                        while ($u = $stmt->fetch()) {
                            $fullname = trim(($u['prenom'] ?? '') . ' ' . ($u['nom'] ?? '')) ?: $u['email'];
                            echo "<tr><td style='padding:8px'>{$fullname}</td><td style='padding:8px'>{$u['email']}</td><td style='padding:8px'>{$u['role']}</td></tr>";
                        }
                    } catch (Exception $e) {
                        echo "<tr><td colspan='3' style='padding:8px'>Impossible de charger les membres.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/../views/layouts/footer.php';
