<?php if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) { header('Location: /login'); exit; }
require_once __DIR__ . '/../views/layouts/header.php';
require_once __DIR__ . '/../Models/Database.php';

$db = \Database::getInstance();
$user_id = $_SESSION['user_id'];
// Try to find user by email if id is not numeric
$user = null;
try {
    if (is_numeric($user_id)) {
        $stmt = $db->prepare("SELECT * FROM utilisateurs WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $user_id]);
        $user = $stmt->fetch();
    } else {
        $stmt = $db->prepare("SELECT * FROM utilisateurs WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $_SESSION['user_email'] ?? '']);
        $user = $stmt->fetch();
    }
} catch (Exception $e) {
}

// Fetch user competences
$competences = [];
try {
    $stmt = $db->query("SELECT c.nom as competence, cu.niveau FROM competences_utilisateurs cu JOIN competences c ON c.id = cu.competence_id LIMIT 50");
    while ($r = $stmt->fetch()) { $competences[] = $r; }
} catch (Exception $e) {}
?>
<main class="container">
    <section class="section">
        <h1>Mon tableau de bord</h1>
        <p>Bonjour <?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?> — voici vos compétences et progrès.</p>

        <div class="grid" style="margin-top:1.5rem;">
            <div class="card">
                <h3>Compétences totales</h3>
                <p style="font-size:2rem;font-weight:700"><?php echo count($competences); ?></p>
                <p class="text-muted">Compétences détectées pour votre profil</p>
            </div>
            <div class="card">
                <h3>Validées</h3>
                <p style="font-size:2rem;font-weight:700">--</p>
                <p class="text-muted">(à venir)</p>
            </div>
            <div class="card">
                <h3>Progression</h3>
                <p style="font-size:2rem;font-weight:700">--</p>
                <p class="text-muted">(calcul à implémenter)</p>
            </div>
        </div>

        <div style="margin-top:2rem;">
            <h2>Vos compétences</h2>
            <div class="grid" style="margin-top:1rem;">
                <?php if (count($competences)===0): ?>
                    <div class="card">Aucune compétence enregistrée pour le moment.</div>
                <?php else: foreach ($competences as $c): ?>
                    <div class="card">
                        <h4><?php echo htmlspecialchars($c['competence']); ?></h4>
                        <p>Niveau: <?php echo htmlspecialchars($c['niveau']); ?></p>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/../views/layouts/footer.php';
