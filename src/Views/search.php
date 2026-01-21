<?php if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/layouts/header.php';
require_once __DIR__ . '/../lib/upload_utils.php';
require_once __DIR__ . '/../lib/cv_parser.php';
require_once __DIR__ . '/../Config/database.php';

// Quick search page & CV uploader to create candidate profiles
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['cv'])) {
    $cv = $_FILES['cv'];
    $ext = strtolower(pathinfo($cv['name'], PATHINFO_EXTENSION));
    $allowed_cv = ['pdf','docx','txt'];
    $max_cv = 8 * 1024 * 1024; // 8MB
    if (empty($cv['tmp_name']) || !is_uploaded_file($cv['tmp_name'])) {
        $parsed = ['text'=>'','emails'=>[],'phones'=>[],'names'=>[],'skills'=>[]];
    } elseif (!in_array($ext, $allowed_cv)) {
        $parsed = ['text'=>'','emails'=>[],'phones'=>[],'names'=>[],'skills'=>[]];
        $error = 'Format non supporté pour le CV.';
    } elseif ($cv['size'] > $max_cv) {
        $parsed = ['text'=>'','emails'=>[],'phones'=>[],'names'=>[],'skills'=>[]];
        $error = 'CV trop volumineux (max 8MB).';
    } else {
        $target = move_uploaded_file_safe($cv, __DIR__ . '/../uploads/cvs', $allowed_cv, $max_cv);
        if ($target) {
            $parsed = parse_cv_file($target);
        } else {
            $parsed = ['text'=>'','emails'=>[],'phones'=>[],'names'=>[],'skills'=>[]];
        }
    }
}

?>
<main class="container">
    <section class="section">
        <h1>Recherche & Import CV</h1>
        <p>Vous pouvez uploader un CV pour détecter des compétences et créer un profil candidat.</p>

        <form method="post" enctype="multipart/form-data">
            <label>Uploader un CV (pdf/docx/txt): <input type="file" name="cv" accept=".pdf,.docx,.txt" required></label>
            <button type="submit">Analyser</button>
        </form>

        <?php if (isset($parsed)): ?>
            <hr>
            <h3>Résultats</h3>
            <p><strong>Emails trouvés:</strong> <?php echo htmlspecialchars(implode(', ', $parsed['emails'] ?? [])); ?></p>
            <p><strong>Téléphones:</strong> <?php echo htmlspecialchars(implode(', ', $parsed['phones'] ?? [])); ?></p>
            <p><strong>Compétences détectées:</strong> <?php echo htmlspecialchars(implode(', ', $parsed['skills'] ?? [])); ?></p>

            <form method="get" action="/profile">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($parsed['emails'][0] ?? ''); ?>">
                <button type="submit">Compléter le profil avec ces données</button>
            </form>
        <?php endif; ?>

    </section>
</main>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
