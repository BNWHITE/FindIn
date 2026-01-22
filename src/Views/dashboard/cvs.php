<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) { header('Location: /login'); exit; }
<<<<<<< HEAD

require_once __DIR__ . '/../../Config/database.php';
require_once __DIR__ . '/../../Models/Database.php';
require_once __DIR__ . '/../../Lib/supabase_storage.php';

$currentPage = 'cvs';
$userName = $_SESSION['user_name'] ?? 'Utilisateur';
$userRole = $_SESSION['user_role'] ?? 'collaborateur';
$userId = $_SESSION['user_id'];

// Initialiser Supabase Storage
$storage = new SupabaseStorage('cvs');

// Récupérer les CVs de l'utilisateur depuis la BDD
$documents = [];
$stats = ['total' => 0, 'competences' => 0, 'score_ats' => 0, 'vues' => 0];

try {
    $db = Database::getInstance();
    $stmt = $db->prepare("SELECT * FROM documents_utilisateurs WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$userId]);
    $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stats['total'] = count($documents);
    
    // Calculer score ATS moyen
    $totalScore = 0;
    foreach ($documents as $doc) {
        $totalScore += $doc['score_ats'] ?? 75;
    }
    $stats['score_ats'] = $stats['total'] > 0 ? round($totalScore / $stats['total']) : 0;
    
    // Compétences extraites
    $stmt = $db->prepare("SELECT COUNT(*) FROM competences_utilisateurs WHERE user_id = ?");
    $stmt->execute([$userId]);
    $stats['competences'] = $stmt->fetchColumn() ?: 0;
    
    // Vues totales
    $totalVues = 0;
    foreach ($documents as $doc) {
        $totalVues += $doc['vues'] ?? 0;
    }
    $stats['vues'] = $totalVues;
} catch (Exception $e) {
    error_log("Erreur récupération CVs: " . $e->getMessage());
}

// Traitement de l'upload
$uploadMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['cv_file'])) {
    $file = $_FILES['cv_file'];
    $allowedTypes = [
        'application/pdf' => 'pdf',
        'application/msword' => 'doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx'
    ];
    
    if ($file['error'] === UPLOAD_ERR_OK && isset($allowedTypes[$file['type']])) {
        if ($file['size'] > 10 * 1024 * 1024) {
            $uploadMessage = 'Fichier trop volumineux. Maximum 10 Mo.';
        } else {
            $ext = $allowedTypes[$file['type']];
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($file['name'], PATHINFO_FILENAME)) . '.' . $ext;
            $storagePath = "{$userId}/{$filename}";
            
            // Calculer le score ATS
            $atsResult = calculateATSScore($file['tmp_name']);
            $atsScore = $atsResult['score'];
            
            // Upload vers Supabase Storage
            $result = $storage->upload($file['tmp_name'], $storagePath, $file['type']);
            
            if (isset($result['success']) && $result['success']) {
                try {
                    $db = Database::getInstance();
                    $docId = bin2hex(random_bytes(16));
                    $stmt = $db->prepare("INSERT INTO documents_utilisateurs (id, user_id, chemin_fichier, nom_fichier, type_document, score_ats, vues, created_at) VALUES (?, ?, ?, ?, 'cv', ?, 0, NOW())");
                    $stmt->execute([$docId, $userId, $storagePath, $file['name'], $atsScore]);
                    header('Location: /dashboard/cvs?success=1&score=' . $atsScore);
                    exit;
                } catch (Exception $e) {
                    $uploadMessage = 'Erreur BDD: ' . $e->getMessage();
                }
            } else {
                // Fallback local
                $uploadDir = __DIR__ . '/../../../storage/uploads/cvs/' . $userId . '/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                    try {
                        $db = Database::getInstance();
                        $docId = bin2hex(random_bytes(16));
                        $stmt = $db->prepare("INSERT INTO documents_utilisateurs (id, user_id, chemin_fichier, nom_fichier, type_document, score_ats, vues, storage_type, created_at) VALUES (?, ?, ?, ?, 'cv', ?, 0, 'local', NOW())");
                        $stmt->execute([$docId, $userId, 'storage/uploads/cvs/' . $userId . '/' . $filename, $file['name'], $atsScore]);
                        header('Location: /dashboard/cvs?success=1&score=' . $atsScore);
                        exit;
                    } catch (Exception $e) {
                        $uploadMessage = 'Erreur: ' . $e->getMessage();
                    }
                }
            }
        }
    } else {
        $uploadMessage = 'Format non supporté. Utilisez PDF, DOC ou DOCX.';
    }
}

// Suppression
if (isset($_GET['delete']) && $_GET['delete']) {
    try {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT chemin_fichier, storage_type FROM documents_utilisateurs WHERE id = ? AND user_id = ?");
        $stmt->execute([$_GET['delete'], $userId]);
        $doc = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($doc) {
            if (!isset($doc['storage_type']) || $doc['storage_type'] !== 'local') {
                $storage->delete($doc['chemin_fichier']);
            } else {
                $localPath = __DIR__ . '/../../../' . $doc['chemin_fichier'];
                if (file_exists($localPath)) unlink($localPath);
            }
            $stmt = $db->prepare("DELETE FROM documents_utilisateurs WHERE id = ? AND user_id = ?");
            $stmt->execute([$_GET['delete'], $userId]);
        }
        header('Location: /dashboard/cvs?deleted=1');
        exit;
    } catch (Exception $e) {}
}

// Téléchargement
if (isset($_GET['download']) && $_GET['download']) {
    try {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT chemin_fichier, nom_fichier, storage_type FROM documents_utilisateurs WHERE id = ? AND user_id = ?");
        $stmt->execute([$_GET['download'], $userId]);
        $doc = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($doc) {
            if (!isset($doc['storage_type']) || $doc['storage_type'] !== 'local') {
                header('Location: ' . $storage->getPublicUrl($doc['chemin_fichier']));
            } else {
                $localPath = __DIR__ . '/../../../' . $doc['chemin_fichier'];
                if (file_exists($localPath)) {
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="' . $doc['nom_fichier'] . '"');
                    readfile($localPath);
                }
            }
            exit;
        }
    } catch (Exception $e) {}
}

// Preview
if (isset($_GET['preview']) && $_GET['preview']) {
    try {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT chemin_fichier, storage_type FROM documents_utilisateurs WHERE id = ? AND user_id = ?");
        $stmt->execute([$_GET['preview'], $userId]);
        $doc = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($doc) {
            $stmt = $db->prepare("UPDATE documents_utilisateurs SET vues = COALESCE(vues, 0) + 1 WHERE id = ?");
            $stmt->execute([$_GET['preview']]);
            
            $url = (!isset($doc['storage_type']) || $doc['storage_type'] !== 'local') 
                ? $storage->getPublicUrl($doc['chemin_fichier']) 
                : '/' . $doc['chemin_fichier'];
            header('Location: ' . $url);
            exit;
        }
    } catch (Exception $e) {}
}
=======
$currentPage = 'cvs';
$userName = $_SESSION['user_name'] ?? 'Utilisateur';
$userRole = $_SESSION['user_role'] ?? 'collaborateur';
>>>>>>> a878b8e (og)
?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes CVs - FindIN</title>
    <link rel="icon" type="image/svg+xml" href="/assets/images/favicon.svg">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="/assets/css/dashboard.css" rel="stylesheet">
    <style>
<<<<<<< HEAD
        .upload-zone {
            display: block;
            background: linear-gradient(135deg, rgba(147,51,234,0.08), rgba(59,130,246,0.08));
            border: 2px dashed rgba(147,51,234,0.4);
            border-radius: 16px;
            padding: 2.5rem 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            margin-bottom: 2rem;
        }
        .upload-zone:hover, .upload-zone.dragover {
            border-color: var(--accent-purple);
            background: linear-gradient(135deg, rgba(147,51,234,0.15), rgba(59,130,246,0.15));
            transform: scale(1.01);
        }
        .upload-zone input[type="file"] { display: none; }
        .upload-zone > i.upload-icon { font-size: 2.5rem; color: var(--accent-purple); margin-bottom: 0.75rem; display: block; }
        .upload-zone > h3 { font-size: 1.1rem; font-weight: 600; margin-bottom: 0.4rem; color: var(--text-primary); }
        .upload-zone > p { color: var(--text-secondary); font-size: 0.85rem; margin: 0; }
        .upload-zone .supported-formats { margin-top: 1rem; display: flex; gap: 0.5rem; justify-content: center; flex-wrap: wrap; }
        .format-badge { 
            background: var(--bg-hover); 
            padding: 0.35rem 0.75rem; 
            border-radius: 20px; 
            font-size: 0.75rem; 
            color: var(--text-secondary); 
            display: inline-flex; 
            align-items: center; 
            gap: 0.4rem;
            border: 1px solid var(--border-color);
        }
        .format-badge i { font-size: 0.75rem; }
        .format-badge.pdf i { color: #ef4444; }
        .format-badge.doc i { color: #3b82f6; }
        .format-badge.size i { color: var(--accent-purple); }

        .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; }
        .section-title { font-size: 1.1rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; }
        .section-title > i { color: var(--accent-purple); font-size: 1rem; }
        .cv-count { background: var(--accent-purple); color: white; font-size: 0.7rem; padding: 0.2rem 0.6rem; border-radius: 10px; margin-left: 0.5rem; }

        .cv-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.25rem; }

        .cv-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 14px; overflow: hidden; transition: all 0.3s ease; }
        .cv-card:hover { transform: translateY(-3px); border-color: var(--accent-purple); box-shadow: 0 8px 25px rgba(147,51,234,0.12); }
        .cv-card.primary { border-color: var(--accent-green); }

        .cv-preview { height: 120px; background: linear-gradient(135deg, var(--bg-hover) 0%, var(--bg-primary) 100%); display: flex; align-items: center; justify-content: center; position: relative; cursor: pointer; text-decoration: none; }
        .cv-preview:hover { background: linear-gradient(135deg, rgba(147,51,234,0.1), rgba(59,130,246,0.1)); }
        .cv-preview .file-icon { font-size: 3rem; opacity: 0.5; }
        .cv-preview .file-icon.pdf { color: #ef4444; }
        .cv-preview .file-icon.doc { color: #3b82f6; }
        .cv-preview .preview-overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.2s; }
        .cv-preview:hover .preview-overlay { opacity: 1; }
        .preview-overlay i { font-size: 1.5rem; color: white; }

        .cv-status { position: absolute; top: 0.6rem; right: 0.6rem; }
        .badge { display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.3rem 0.6rem; border-radius: 15px; font-size: 0.65rem; font-weight: 600; }
        .badge i { font-size: 0.55rem; }
        .badge-green { background: rgba(16,185,129,0.2); color: var(--accent-green); }
        .badge-blue { background: rgba(59,130,246,0.2); color: var(--accent-blue); }

        .cv-body { padding: 1rem; }
        .cv-name { font-weight: 600; font-size: 0.95rem; margin-bottom: 0.6rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .cv-meta { display: flex; flex-wrap: wrap; gap: 0.75rem; color: var(--text-secondary); font-size: 0.75rem; margin-bottom: 0.75rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-color); }
        .cv-meta span { display: flex; align-items: center; gap: 0.25rem; }
        .cv-meta i { font-size: 0.7rem; }

        .ats-score { background: var(--bg-hover); border-radius: 8px; padding: 0.5rem 0.6rem; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.6rem; }
        .ats-bar { flex: 1; height: 6px; background: var(--border-color); border-radius: 3px; overflow: hidden; }
        .ats-fill { height: 100%; border-radius: 3px; transition: width 0.5s ease; }
        .ats-fill.high { background: linear-gradient(90deg, var(--accent-green), #34d399); }
        .ats-fill.medium { background: linear-gradient(90deg, var(--accent-yellow), #fbbf24); }
        .ats-fill.low { background: linear-gradient(90deg, var(--accent-red), #f87171); }
        .ats-label { font-size: 0.7rem; color: var(--text-secondary); white-space: nowrap; }
        .ats-value { font-weight: 700; font-size: 0.85rem; min-width: 35px; text-align: right; }
        .ats-value.high { color: var(--accent-green); }
        .ats-value.medium { color: var(--accent-yellow); }
        .ats-value.low { color: var(--accent-red); }

        .cv-actions { display: flex; gap: 0.4rem; }
        .cv-actions .btn { flex: 1; justify-content: center; padding: 0.5rem; font-size: 0.85rem; }
        .cv-actions .btn i { font-size: 0.8rem; }

        .cv-card.generator-card { background: linear-gradient(135deg, rgba(147,51,234,0.08), rgba(59,130,246,0.08)); border: 2px dashed rgba(147,51,234,0.35); }
        .cv-card.generator-card:hover { border-style: solid; }
        .cv-card.empty-state-card { background: var(--bg-card); border: 1px dashed var(--border-color); }
        .empty-icon { width: 70px; height: 70px; background: var(--bg-hover); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; color: var(--text-secondary); margin-bottom: 1rem; opacity: 0.6; }
        .generator-content { display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 280px; text-align: center; padding: 1.5rem; }
        .generator-icon { width: 70px; height: 70px; background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue)); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; color: white; margin-bottom: 1rem; }
        .generator-content h3 { font-size: 1rem; margin-bottom: 0.4rem; }
        .generator-content p { color: var(--text-secondary); margin-bottom: 1rem; font-size: 0.85rem; }

        .toast { position: fixed; bottom: 1.5rem; right: 1.5rem; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 1rem 1.25rem; display: flex; align-items: center; gap: 0.75rem; box-shadow: 0 10px 40px rgba(0,0,0,0.3); transform: translateY(150%); transition: transform 0.3s ease; z-index: 1000; font-size: 0.9rem; }
        .toast.show { transform: translateY(0); }
        .toast.success { border-color: var(--accent-green); }
        .toast.success i { color: var(--accent-green); font-size: 1.1rem; }

        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.75); backdrop-filter: blur(4px); z-index: 1000; align-items: center; justify-content: center; }
        .modal.active { display: flex; }
        .modal-content { background: var(--bg-card); border-radius: 16px; padding: 1.5rem; width: 90%; max-width: 400px; border: 1px solid var(--border-color); }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-color); }
        .modal-title { font-size: 1rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; }
        .modal-close { background: var(--bg-hover); border: none; width: 32px; height: 32px; border-radius: 50%; cursor: pointer; color: var(--text-secondary); display: flex; align-items: center; justify-content: center; font-size: 0.9rem; transition: all 0.2s; }
        .modal-close:hover { background: var(--accent-red); color: white; }

        .upload-progress { display: none; margin-top: 1rem; }
        .upload-progress.active { display: block; }
        .progress-bar { height: 6px; background: var(--border-color); border-radius: 3px; overflow: hidden; }
        .progress-fill { height: 100%; background: linear-gradient(90deg, var(--accent-purple), var(--accent-blue)); width: 0%; transition: width 0.3s; }

        .alert-error { background: rgba(239,68,68,0.1); border: 1px solid var(--accent-red); color: var(--accent-red); padding: 0.75rem 1rem; border-radius: 10px; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }

        @media (max-width: 768px) {
            .cv-grid { grid-template-columns: 1fr; }
            .upload-zone { padding: 1.5rem 1rem; }
            .cv-actions { flex-wrap: wrap; }
            .cv-actions .btn { flex: 1 1 45%; }
        }
=======
        .cv-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem; }
        .cv-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; overflow: hidden; transition: all 0.3s; }
        .cv-card:hover { transform: translateY(-5px); border-color: var(--accent-purple); }
        .cv-preview { height: 180px; background: linear-gradient(135deg, var(--bg-hover), var(--bg-primary)); display: flex; align-items: center; justify-content: center; position: relative; }
        .cv-preview i { font-size: 4rem; color: var(--text-secondary); opacity: 0.5; }
        .cv-status { position: absolute; top: 1rem; right: 1rem; }
        .cv-body { padding: 1.5rem; }
        .cv-name { font-weight: 600; font-size: 1.1rem; margin-bottom: 0.5rem; }
        .cv-meta { display: flex; flex-wrap: wrap; gap: 1rem; color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 1rem; }
        .cv-skills { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1.25rem; }
        .cv-skill { background: rgba(147, 51, 234, 0.1); color: var(--accent-purple); padding: 0.3rem 0.7rem; border-radius: 20px; font-size: 0.75rem; }
        .cv-actions { display: flex; gap: 0.5rem; }
        .cv-actions .btn { flex: 1; justify-content: center; }
        .generator-card { background: linear-gradient(135deg, rgba(147, 51, 234, 0.15), rgba(59, 130, 246, 0.15)); border: 1px solid rgba(147, 51, 234, 0.3); }
        .generator-content { display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 300px; text-align: center; padding: 2rem; }
        .generator-icon { width: 80px; height: 80px; background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue)); border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 2rem; color: white; margin-bottom: 1.5rem; }
>>>>>>> a878b8e (og)
    </style>
</head>
<body>
    <?php include __DIR__ . '/_sidebar.php'; ?>
    <main class="main-content">
        <div class="page-header">
            <div><h1 class="page-title"><i class="fas fa-file-alt"></i> Mes CVs</h1><p class="page-subtitle">Gérez et optimisez vos curriculum vitae</p></div>
            <div class="header-actions"><button class="mobile-menu-btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button><button class="theme-toggle" onclick="toggleTheme()"><i class="fas fa-moon"></i></button></div>
        </div>
        <div class="stats-grid">
            <div class="stat-card"><div class="stat-icon purple"><i class="fas fa-file-alt"></i></div><div class="stat-value">3</div><div class="stat-label">CVs uploadés</div></div>
            <div class="stat-card"><div class="stat-icon blue"><i class="fas fa-brain"></i></div><div class="stat-value">24</div><div class="stat-label">Compétences extraites</div></div>
            <div class="stat-card"><div class="stat-icon green"><i class="fas fa-chart-line"></i></div><div class="stat-value">92%</div><div class="stat-label">Score ATS</div></div>
            <div class="stat-card"><div class="stat-icon yellow"><i class="fas fa-eye"></i></div><div class="stat-value">18</div><div class="stat-label">Vues recruteurs</div></div>
        </div>
<<<<<<< HEAD
        
        <?php if ($uploadMessage): ?>
        <div class="alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($uploadMessage) ?></div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data" id="uploadForm">
            <label class="upload-zone" for="cvUpload" id="dropZone">
                <input type="file" id="cvUpload" name="cv_file" accept=".pdf,.doc,.docx">
                <i class="fas fa-cloud-upload-alt upload-icon"></i>
                <h3>Glissez votre CV ici</h3>
                <p>ou cliquez pour sélectionner un fichier</p>
                <div class="supported-formats">
                    <span class="format-badge pdf"><i class="fas fa-file-pdf"></i> PDF</span>
                    <span class="format-badge doc"><i class="fas fa-file-word"></i> DOC</span>
                    <span class="format-badge doc"><i class="fas fa-file-word"></i> DOCX</span>
                    <span class="format-badge size"><i class="fas fa-weight-hanging"></i> Max 10 MB</span>
                </div>
                <div class="upload-progress" id="uploadProgress">
                    <div class="progress-bar"><div class="progress-fill" id="progressFill"></div></div>
                    <p style="margin-top: 0.5rem; font-size: 0.8rem; color: var(--text-secondary);">Upload en cours...</p>
                </div>
            </label>
        </form>
        
        <div class="section-header">
            <h2 class="section-title"><i class="fas fa-folder-open"></i> Mes documents <span class="cv-count"><?= count($documents) ?></span></h2>
        </div>
        
        <div class="cv-grid">
            <div class="cv-card generator-card">
                <div class="generator-content">
                    <div class="generator-icon"><i class="fas fa-magic"></i></div>
                    <h3>Générer un CV</h3>
                    <p>Notre IA crée un CV optimisé pour les ATS</p>
                    <button class="btn btn-primary" onclick="openGenerator()"><i class="fas fa-wand-magic-sparkles"></i> Créer mon CV</button>
                </div>
            </div>
            
            <?php if (empty($documents)): ?>
            <div class="cv-card empty-state-card">
                <div class="generator-content">
                    <div class="empty-icon"><i class="fas fa-folder-open"></i></div>
                    <h3>Aucun CV uploadé</h3>
                    <p>Glissez un fichier ci-dessus ou cliquez pour en ajouter un</p>
                </div>
            </div>
            <?php else: ?>
            <?php foreach ($documents as $index => $doc): 
                $isPrimary = $index === 0;
                $filename = $doc['nom_fichier'] ?? basename($doc['chemin_fichier']);
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                $fileIcon = $ext === 'pdf' ? 'fa-file-pdf pdf' : 'fa-file-word doc';
                $dateFormatted = isset($doc['created_at']) ? date('d M Y', strtotime($doc['created_at'])) : 'N/A';
                $atsScore = $doc['score_ats'] ?? 75;
                $vues = $doc['vues'] ?? 0;
                $atsLevel = $atsScore >= 80 ? 'high' : ($atsScore >= 60 ? 'medium' : 'low');
            ?>
            <div class="cv-card <?= $isPrimary ? 'primary' : '' ?>">
                <a href="/dashboard/cvs?preview=<?= $doc['id'] ?>" target="_blank" class="cv-preview">
                    <i class="fas <?= $fileIcon ?> file-icon"></i>
                    <div class="preview-overlay"><i class="fas fa-eye"></i></div>
                    <div class="cv-status">
                        <?php if ($isPrimary): ?><span class="badge badge-green"><i class="fas fa-star"></i> Principal</span>
                        <?php else: ?><span class="badge badge-blue"><i class="fas fa-file"></i> Document</span><?php endif; ?>
                    </div>
                </a>
                <div class="cv-body">
                    <h3 class="cv-name" title="<?= htmlspecialchars($filename) ?>"><?= htmlspecialchars($filename) ?></h3>
                    <div class="cv-meta">
                        <span><i class="fas fa-calendar-alt"></i> <?= $dateFormatted ?></span>
                        <span><i class="fas fa-eye"></i> <?= $vues ?> vue<?= $vues > 1 ? 's' : '' ?></span>
                    </div>
                    <div class="ats-score">
                        <span class="ats-label">Score ATS</span>
                        <div class="ats-bar"><div class="ats-fill <?= $atsLevel ?>" style="width: <?= $atsScore ?>%"></div></div>
                        <span class="ats-value <?= $atsLevel ?>"><?= $atsScore ?>%</span>
                    </div>
                    <div class="cv-actions">
                        <a href="/dashboard/cvs?preview=<?= $doc['id'] ?>" target="_blank" class="btn btn-outline btn-sm" title="Voir"><i class="fas fa-eye"></i></a>
                        <a href="/dashboard/cvs?download=<?= $doc['id'] ?>" class="btn btn-outline btn-sm" title="Télécharger"><i class="fas fa-download"></i></a>
                        <button class="btn btn-danger btn-sm" onclick="confirmDelete('<?= $doc['id'] ?>', '<?= htmlspecialchars(addslashes($filename)) ?>')" title="Supprimer"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
    
    <div class="toast" id="toast"><i class="fas fa-check-circle"></i><span id="toastMessage">Action effectuée</span></div>
    
    <div class="modal" id="deleteModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fas fa-exclamation-triangle" style="color: var(--accent-red);"></i> Confirmer</h3>
                <button class="modal-close" onclick="closeModal('deleteModal')"><i class="fas fa-times"></i></button>
            </div>
            <p style="margin-bottom: 1rem; color: var(--text-secondary);">Supprimer <strong id="deleteFileName" style="color: var(--text-primary);"></strong> ?</p>
            <p style="margin-bottom: 1rem; color: var(--accent-red); font-size: 0.8rem;"><i class="fas fa-info-circle"></i> Cette action est irréversible.</p>
            <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                <button class="btn btn-outline" onclick="closeModal('deleteModal')">Annuler</button>
                <a href="#" id="deleteConfirmBtn" class="btn btn-danger"><i class="fas fa-trash"></i> Supprimer</a>
            </div>
        </div>
    </div>
    
    <script>
        function toggleTheme() { const h = document.documentElement, n = h.getAttribute('data-theme') === 'dark' ? 'light' : 'dark'; h.setAttribute('data-theme', n); localStorage.setItem('theme', n); document.querySelector('.theme-toggle i').className = n === 'dark' ? 'fas fa-moon' : 'fas fa-sun'; }
        const t = localStorage.getItem('theme') || 'dark'; document.documentElement.setAttribute('data-theme', t); document.querySelector('.theme-toggle i').className = t === 'dark' ? 'fas fa-moon' : 'fas fa-sun';
        function toggleSidebar() { document.querySelector('.sidebar').classList.toggle('open'); }
        
        const dropZone = document.getElementById('dropZone'), fileInput = document.getElementById('cvUpload'), uploadForm = document.getElementById('uploadForm'), uploadProgress = document.getElementById('uploadProgress');
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(e => { dropZone.addEventListener(e, ev => { ev.preventDefault(); ev.stopPropagation(); }, false); });
        ['dragenter', 'dragover'].forEach(e => { dropZone.addEventListener(e, () => dropZone.classList.add('dragover'), false); });
        ['dragleave', 'drop'].forEach(e => { dropZone.addEventListener(e, () => dropZone.classList.remove('dragover'), false); });
        dropZone.addEventListener('drop', e => { if (e.dataTransfer.files.length) { fileInput.files = e.dataTransfer.files; submitUpload(); } });
        fileInput.addEventListener('change', () => { if (fileInput.files.length) submitUpload(); });
        
        function submitUpload() { uploadProgress.classList.add('active'); let p = 0; const i = setInterval(() => { p += Math.random() * 15; if (p >= 90) { clearInterval(i); p = 90; } document.getElementById('progressFill').style.width = p + '%'; }, 200); uploadForm.submit(); }
        
        function showToast(m, type = 'success') { const toast = document.getElementById('toast'); toast.className = 'toast ' + type; document.getElementById('toastMessage').textContent = m; toast.classList.add('show'); setTimeout(() => toast.classList.remove('show'), 4000); }
        <?php if (isset($_GET['success'])): ?>setTimeout(() => showToast('CV uploadé ! Score ATS: <?= $_GET['score'] ?? '?' ?>%', 'success'), 300);<?php endif; ?>
        <?php if (isset($_GET['deleted'])): ?>setTimeout(() => showToast('Document supprimé', 'success'), 300);<?php endif; ?>
        
        function openModal(id) { document.getElementById(id).classList.add('active'); }
        function closeModal(id) { document.getElementById(id).classList.remove('active'); }
        function confirmDelete(id, name) { document.getElementById('deleteFileName').textContent = name; document.getElementById('deleteConfirmBtn').href = '/dashboard/cvs?delete=' + id; openModal('deleteModal'); }
        function openGenerator() { showToast('Fonctionnalité bientôt disponible !', 'success'); }
        document.querySelectorAll('.modal').forEach(m => { m.addEventListener('click', e => { if (e.target === m) closeModal(m.id); }); });
        document.addEventListener('keydown', e => { if (e.key === 'Escape') document.querySelectorAll('.modal.active').forEach(m => closeModal(m.id)); });
=======
        <label class="upload-zone" for="cvUpload" style="margin-bottom: 2rem;"><input type="file" id="cvUpload" accept=".pdf,.doc,.docx" multiple><i class="fas fa-cloud-upload-alt"></i><h3>Glissez vos CVs ici</h3><p>PDF, DOC, DOCX - Max 10 MB</p></label>
        <h2 style="font-size: 1.25rem; margin-bottom: 1.5rem;">Mes documents</h2>
        <div class="cv-grid">
            <div class="cv-card generator-card"><div class="generator-content"><div class="generator-icon"><i class="fas fa-magic"></i></div><h3>Générer un CV</h3><p style="color:var(--text-secondary);margin-bottom:1.5rem;">IA crée un CV professionnel</p><button class="btn btn-primary"><i class="fas fa-wand-magic-sparkles"></i> Créer</button></div></div>
            <div class="cv-card"><div class="cv-preview"><i class="fas fa-file-pdf"></i><div class="cv-status"><span class="badge badge-green"><i class="fas fa-star"></i> Principal</span></div></div><div class="cv-body"><h3 class="cv-name">CV_Dev_2024.pdf</h3><div class="cv-meta"><span><i class="fas fa-calendar"></i> 15 Nov</span><span><i class="fas fa-weight"></i> 245Ko</span></div><div class="cv-skills"><span class="cv-skill">PHP</span><span class="cv-skill">JS</span><span class="cv-skill">+5</span></div><div class="cv-actions"><button class="btn btn-outline btn-sm"><i class="fas fa-eye"></i></button><button class="btn btn-outline btn-sm"><i class="fas fa-download"></i></button><button class="btn btn-outline btn-sm"><i class="fas fa-trash"></i></button></div></div></div>
            <div class="cv-card"><div class="cv-preview"><i class="fas fa-file-pdf"></i><div class="cv-status"><span class="badge badge-yellow"><i class="fas fa-clock"></i> Brouillon</span></div></div><div class="cv-body"><h3 class="cv-name">CV_Lead.pdf</h3><div class="cv-meta"><span><i class="fas fa-calendar"></i> 8 Nov</span><span><i class="fas fa-weight"></i> 312Ko</span></div><div class="cv-skills"><span class="cv-skill">Management</span><span class="cv-skill">+3</span></div><div class="cv-actions"><button class="btn btn-outline btn-sm"><i class="fas fa-eye"></i></button><button class="btn btn-outline btn-sm"><i class="fas fa-download"></i></button><button class="btn btn-outline btn-sm"><i class="fas fa-trash"></i></button></div></div></div>
        </div>
    </main>
    <script>
        function toggleTheme(){const h=document.documentElement,n=h.getAttribute('data-theme')==='dark'?'light':'dark';h.setAttribute('data-theme',n);localStorage.setItem('theme',n);document.querySelector('.theme-toggle i').className=n==='dark'?'fas fa-moon':'fas fa-sun';}
        const t=localStorage.getItem('theme')||'dark';document.documentElement.setAttribute('data-theme',t);document.querySelector('.theme-toggle i').className=t==='dark'?'fas fa-moon':'fas fa-sun';
        function toggleSidebar(){document.querySelector('.sidebar').classList.toggle('open');}
>>>>>>> a878b8e (og)
    </script>
</body>
</html>
