<?php if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/layouts/header.php';
require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../lib/upload_utils.php';
require_once __DIR__ . '/../lib/cv_parser.php';

// Connect PDO
try {
    if (defined('DB_TYPE') && DB_TYPE === 'mysql') {
        $pdo = new PDO(sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME), DB_USER, DB_PASS, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
    } else {
        $pdo = new PDO('sqlite:' . DB_PATH);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
} catch (PDOException $e) {
    echo '<div class="alert">DB error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    require_once __DIR__ . '/layouts/footer.php';
    exit;
}

$message = '';
$prefill = [];

// Handle uploaded photo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'upload_photo' && isset($_FILES['photo'])) {
        // photo validations
        $photo = $_FILES['photo'];
        $ext = strtolower(pathinfo($photo['name'], PATHINFO_EXTENSION));
        $allowed_photo = ['jpg','jpeg','png','webp'];
        $max_photo = 5 * 1024 * 1024; // 5MB
        if (empty($photo['tmp_name']) || !is_uploaded_file($photo['tmp_name'])) {
            $message = 'No photo uploaded.';
        } elseif (!in_array($ext, $allowed_photo)) {
            $message = 'Type de fichier non autorisé pour la photo.';
        } elseif ($photo['size'] > $max_photo) {
            $message = 'Photo trop volumineuse (max 5MB).';
        } else {
            $target = move_uploaded_file_safe($photo, __DIR__ . '/../uploads/photos', $allowed_photo, $max_photo);
            if ($target) {
            // optionally save to user by email
            $email = $_POST['email'] ?? null;
            if ($email) {
                // ensure photo column exists
                try {
                    $pdo->exec("ALTER TABLE utilisateurs ADD COLUMN photo VARCHAR(255) DEFAULT NULL");
                } catch (Exception $e) { /* ignore if exists or not allowed */ }
                $stmt = $pdo->prepare('UPDATE utilisateurs SET photo = ? WHERE email = ?');
                $stmt->execute([basename($target), $email]);
            }
                $message = 'Photo uploaded successfully.';
            } else {
                $message = 'Photo upload failed.';
            }
        }
    }

    if ($_POST['action'] === 'upload_cv' && isset($_FILES['cv_file'])) {
        $cv = $_FILES['cv_file'];
        $ext = strtolower(pathinfo($cv['name'], PATHINFO_EXTENSION));
        $allowed_cv = ['pdf','docx','txt'];
        $max_cv = 8 * 1024 * 1024; // 8MB
        if (empty($cv['tmp_name']) || !is_uploaded_file($cv['tmp_name'])) {
            $message = 'Aucun fichier CV uploadé.';
        } elseif (!in_array($ext, $allowed_cv)) {
            $message = 'Format de CV non supporté (autorisé: pdf, docx, txt).';
        } elseif ($cv['size'] > $max_cv) {
            $message = 'CV trop volumineux (max 8MB).';
        } else {
            $target = move_uploaded_file_safe($cv, __DIR__ . '/../uploads/cvs', $allowed_cv, $max_cv);
            if ($target) {
                $parsed = parse_cv_file($target);
                $prefill = $parsed;
                $message = 'CV uploaded and parsed (see suggestions below).';
                // Keep parsed text in session for later save
                $_SESSION['last_parsed_cv'] = $parsed;
                $_SESSION['last_parsed_cv_path'] = $target;
                // If email provided, save last_cv filename to utilisateur
                $emailForCv = $_POST['email'] ?? null;
                if ($emailForCv) {
                    try { $pdo->exec("ALTER TABLE utilisateurs ADD COLUMN last_cv TEXT DEFAULT NULL"); } catch (Exception $e) {}
                    $stmt = $pdo->prepare('UPDATE utilisateurs SET last_cv = ? WHERE email = ?');
                    $stmt->execute([basename($target), $emailForCv]);
                }
            } else {
                $message = 'CV upload failed.';
            }
        }
    }

    if ($_POST['action'] === 'save_profile') {
        $email = $_POST['email'] ?? '';
        $prenom = $_POST['prenom'] ?? '';
        $nom = $_POST['nom'] ?? '';
        $skills = $_POST['skills'] ?? '';

        // ensure columns exist
        try { $pdo->exec("ALTER TABLE utilisateurs ADD COLUMN photo VARCHAR(255) DEFAULT NULL"); } catch (Exception $e) {}
        try { $pdo->exec("ALTER TABLE utilisateurs ADD COLUMN competences TEXT DEFAULT NULL"); } catch (Exception $e) {}

        // Upsert: check existing
        $stmt = $pdo->prepare('SELECT id_utilisateur FROM utilisateurs WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $u = $stmt->fetch();
        if ($u) {
            $stmt = $pdo->prepare('UPDATE utilisateurs SET prenom = ?, nom = ?, competences = ? WHERE email = ?');
            $stmt->execute([$prenom, $nom, $skills, $email]);
            $message = 'Profile updated.';
        } else {
            // create with basic fields
            if (defined('DB_TYPE') && DB_TYPE === 'mysql') {
                $sql = 'INSERT INTO utilisateurs (id_utilisateur, email, prenom, nom, role, departement, competences) VALUES (UUID(), ?, ?, ?, ?, ?, ?)';
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$email, $prenom, $nom, 'employe', null, $skills]);
            } else {
                $sql = 'INSERT INTO utilisateurs (email, prenom, nom, role, departement, competences) VALUES (?, ?, ?, ?, ?, ?)';
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$email, $prenom, $nom, 'employe', null, $skills]);
            }
            $message = 'Profile created.';
        }
    }
}

// Show current profile if email provided
$current = null;
if (isset($_GET['email'])) {
    $stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE email = ? LIMIT 1');
    $stmt->execute([$_GET['email']]);
    $current = $stmt->fetch();
}

?>

<main class="container">
    <section class="section">
        <h1>Profil utilisateur</h1>
        <?php if ($message): ?><div class="alert"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>

        <form method="get" class="inline" style="margin-bottom:1rem;">
            <label>Rechercher par email: <input type="email" name="email" value="<?php echo isset($_GET['email'])?htmlspecialchars($_GET['email']):'';?>"></label>
            <button type="submit">Charger</button>
        </form>

        <?php if ($current): ?>
            <h2>Profil: <?php echo htmlspecialchars($current['email']); ?></h2>
            <p>Prénom: <?php echo htmlspecialchars($current['prenom'] ?? ''); ?></p>
            <p>Nom: <?php echo htmlspecialchars($current['nom'] ?? ''); ?></p>
            <p>Compétences: <?php echo htmlspecialchars($current['competences'] ?? ''); ?></p>
            <?php if (!empty($current['photo'])): ?>
                <p>Photo: <img src="/uploads/photos/<?php echo htmlspecialchars($current['photo']); ?>" alt="photo" style="max-width:120px;border-radius:6px"></p>
            <?php endif; ?>
        <?php endif; ?>

        <hr>

        <h3>Télécharger une photo de profil</h3>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="upload_photo">
            <label>Email associé: <input type="email" name="email" required></label>
            <label>Photo: <input type="file" name="photo" accept="image/*" capture="user" required></label>
            <button type="submit">Upload</button>
        </form>

        <hr>

        <h3>Télécharger un CV (pdf, docx, txt)</h3>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="upload_cv">
            <label>Email associé (pour lier le CV au profil): <input type="email" name="email" value="<?php echo isset($_GET['email'])?htmlspecialchars($_GET['email']):'';?>" required></label>
            <label>Fichier CV: <input type="file" name="cv_file" accept=".pdf,.docx,.txt" required></label>
            <button type="submit">Uploader et analyser</button>
        </form>

        <?php if (!empty($prefill) || isset($_SESSION['last_parsed_cv'])):
            $p = $prefill ?: $_SESSION['last_parsed_cv'];
        ?>
            <hr>
            <h3>Suggestions extraites</h3>
            <form method="post">
                <input type="hidden" name="action" value="save_profile">
                <label>Prénom: <input type="text" name="prenom" value="<?php echo htmlspecialchars($p['names'][0] ?? ''); ?>"></label>
                <label>Nom: <input type="text" name="nom" value="<?php echo htmlspecialchars(''); ?>"></label>
                <label>Email: <input type="email" name="email" value="<?php echo htmlspecialchars($p['emails'][0] ?? ''); ?>" required></label>
                <label>Compétences (séparées par virgule): <input type="text" name="skills" value="<?php echo htmlspecialchars(implode(', ', $p['skills'] ?? [])); ?>"></label>
                <button type="submit">Confirmer et enregistrer</button>
            </form>
            <details style="margin-top:1rem;"><summary>Texte extrait</summary><pre style="white-space:pre-wrap;max-height:300px;overflow:auto;background:#0f172a;padding:12px;color:#e6eef8;border-radius:6px"><?php echo htmlspecialchars($p['text'] ?? ''); ?></pre></details>
        <?php endif; ?>
    </section>
</main>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindIN - Mon Profil</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #0a0118;
            --bg-card: #1a0d2e;
            --text-white: #ffffff;
            --text-light: #e0e0e0;
            --accent-primary: #9333ea;
            --accent-blue: #3b82f6;
            --accent-pink: #ec4899;
            --border-light: rgba(255, 255, 255, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #0a0118 0%, #1a0d2e 100%);
            color: var(--text-white);
            line-height: 1.6;
        }

        .dashboard-container {
            display: grid;
            grid-template-columns: 280px 1fr;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            background: rgba(26, 13, 46, 0.8);
            backdrop-filter: blur(10px);
            border-right: 1px solid var(--border-light);
            padding: 2rem 0;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 0 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-logo {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #9333ea 0%, #3b82f6 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.25rem;
        }

        .sidebar-title {
            font-size: 1.25rem;
            font-weight: 700;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            padding: 0 1rem;
        }

        .nav-item {
            padding: 0.75rem 1rem;
            color: var(--text-light);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
        }

        .nav-item:hover,
        .nav-item.active {
            background: rgba(147, 51, 234, 0.2);
            color: var(--accent-primary);
        }

        .nav-item i {
            width: 20px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            padding: 2rem;
            overflow-y: auto;
            max-width: 900px;
        }

        .page-header {
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        /* Profile Header */
        .profile-header {
            background: linear-gradient(135deg, rgba(147, 51, 234, 0.1) 0%, rgba(59, 130, 246, 0.05) 100%);
            border: 1px solid var(--border-light);
            border-radius: 12px;
            padding: 3rem 2rem;
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .profile-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(147, 51, 234, 0.3) 0%, transparent 70%);
            border-radius: 50%;
            filter: blur(40px);
        }

        .profile-content {
            position: relative;
            z-index: 1;
        }

        .profile-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: linear-gradient(135deg, #9333ea 0%, #3b82f6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            font-weight: 700;
            margin: 0 auto 1.5rem;
            border: 3px solid var(--border-light);
        }

        .profile-name {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .profile-role {
            color: var(--accent-primary);
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
        }

        .profile-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .profile-stat {
            text-align: center;
        }

        .profile-stat-value {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--accent-primary);
        }

        .profile-stat-label {
            color: var(--text-light);
            font-size: 0.85rem;
        }

        /* Sections */
        .profile-section {
            background: linear-gradient(135deg, rgba(147, 51, 234, 0.1) 0%, rgba(59, 130, 246, 0.05) 100%);
            border: 1px solid var(--border-light);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-title i {
            color: var(--accent-primary);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text-white);
        }

        .form-input,
        .form-textarea,
        .form-select {
            width: 100%;
            padding: 0.75rem 1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-light);
            border-radius: 8px;
            color: var(--text-white);
            font-family: inherit;
            transition: all 0.3s ease;
        }

        .form-input:focus,
        .form-textarea:focus,
        .form-select:focus {
            outline: none;
            border-color: var(--accent-primary);
            background: rgba(147, 51, 234, 0.1);
        }

        .form-textarea {
            min-height: 120px;
            resize: vertical;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #9333ea 0%, #3b82f6 100%);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--border-light);
            color: var(--text-white);
        }

        .btn-group {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .badge {
            display: inline-block;
            padding: 0.35rem 0.75rem;
            background: rgba(147, 51, 234, 0.2);
            color: var(--accent-primary);
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .badge-group {
            margin-bottom: 1.5rem;
        }

        @media (max-width: 768px) {
            .dashboard-container {
                grid-template-columns: 1fr;
            }

            .sidebar {
                display: none;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .profile-stats {
                grid-template-columns: 1fr;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .profile-avatar {
                width: 100px;
                height: 100px;
                font-size: 2rem;
            }

            .profile-name {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">F</div>
                <div class="sidebar-title">FindIN</div>
            </div>

            <nav class="sidebar-nav">
                <a href="/dashboard" class="nav-item">
                    <i class="fas fa-chart-line"></i> Tableau de bord
                </a>
                <a href="/competences" class="nav-item">
                    <i class="fas fa-certificate"></i> Mes compétences
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-search"></i> Rechercher
                </a>
                <a href="#" class="nav-item active">
                    <i class="fas fa-user"></i> Mon profil
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-title">Mon Profil</h1>
            </div>

            <!-- Profile Header -->
            <div class="profile-header">
                <div class="profile-content">
                    <div class="profile-avatar">JD</div>
                    <h2 class="profile-name">Jean Dupont</h2>
                    <p class="profile-role">Senior Software Developer</p>
                    <p style="color: var(--text-light); margin-bottom: 0;">Basé à Paris, France</p>
                    
                    <div class="profile-stats">
                        <div class="profile-stat">
                            <div class="profile-stat-value">8</div>
                            <div class="profile-stat-label">Années d'expérience</div>
                        </div>
                        <div class="profile-stat">
                            <div class="profile-stat-value">12</div>
                            <div class="profile-stat-label">Compétences</div>
                        </div>
                        <div class="profile-stat">
                            <div class="profile-stat-value">95%</div>
                            <div class="profile-stat-label">Profil complété</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Information Personnelle -->
            <div class="profile-section">
                <h3 class="section-title">
                    <i class="fas fa-user"></i> Information Personnelle
                </h3>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Prénom</label>
                        <input type="text" class="form-input" value="Jean">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nom</label>
                        <input type="text" class="form-input" value="Dupont">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-input" value="jean.dupont@example.com">
                </div>
                <div class="form-group">
                    <label class="form-label">Téléphone</label>
                    <input type="tel" class="form-input" value="+33 6 XX XX XX XX">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Lieu</label>
                        <input type="text" class="form-input" value="Paris, France">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Département</label>
                        <input type="text" class="form-input" value="Ingénierie">
                    </div>
                </div>
            </div>

            <!-- Professionnel -->
            <div class="profile-section">
                <h3 class="section-title">
                    <i class="fas fa-briefcase"></i> Informations Professionnelles
                </h3>
                <div class="form-group">
                    <label class="form-label">Titre actuel</label>
                    <input type="text" class="form-input" value="Senior Software Developer">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Années d'expérience</label>
                        <input type="number" class="form-input" value="8">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Secteur</label>
                        <select class="form-select">
                            <option selected>Technologie</option>
                            <option>Finance</option>
                            <option>Santé</option>
                            <option>Éducation</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Bio</label>
                    <textarea class="form-textarea" placeholder="Parlez de vous...">Développeur passionné avec 8 ans d'expérience en développement web et mobile. Expert en PHP, JavaScript et architecture logicielle.</textarea>
                </div>
            </div>

            <!-- Compétences Clés -->
            <div class="profile-section">
                <h3 class="section-title">
                    <i class="fas fa-star"></i> Compétences Clés
                </h3>
                <div class="badge-group">
                    <span class="badge">PHP</span>
                    <span class="badge">JavaScript</span>
                    <span class="badge">React</span>
                    <span class="badge">DevOps</span>
                    <span class="badge">Leadership</span>
                    <span class="badge">Communication</span>
                </div>
                <p style="color: var(--text-light); font-size: 0.9rem;">
                    Ajoutez ou modifiez vos compétences dans la section <a href="/competences" style="color: var(--accent-primary);">Mes Compétences</a>
                </p>
            </div>

            <!-- Liens Sociaux -->
            <div class="profile-section">
                <h3 class="section-title">
                    <i class="fas fa-link"></i> Liens Sociaux
                </h3>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">LinkedIn</label>
                        <input type="url" class="form-input" placeholder="https://linkedin.com/in/...">
                    </div>
                    <div class="form-group">
                        <label class="form-label">GitHub</label>
                        <input type="url" class="form-input" placeholder="https://github.com/...">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Portfolio</label>
                        <input type="url" class="form-input" placeholder="https://yourportfolio.com">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Twitter</label>
                        <input type="url" class="form-input" placeholder="https://twitter.com/...">
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="profile-section">
                <div class="btn-group">
                    <button class="btn"><i class="fas fa-save"></i> Enregistrer les modifications</button>
                    <button class="btn btn-secondary"><i class="fas fa-redo"></i> Réinitialiser</button>
                    <button class="btn btn-secondary" style="margin-left: auto;">
                        <i class="fas fa-cog"></i> Paramètres du compte
                    </button>
                </div>
            </div>
        </main>
    </div>

    <script src="/assets/js/main.js"></script>
</body>
</html>
