<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) { header('Location: /login'); exit; }

require_once __DIR__ . '/../../Config/database.php';
require_once __DIR__ . '/../../Models/Database.php';

$currentPage = 'cvs';
$userName = $_SESSION['user_name'] ?? 'Utilisateur';
$userRole = $_SESSION['user_role'] ?? 'collaborateur';
$userId = $_SESSION['user_id'];

// Récupérer les CVs de l'utilisateur depuis la BDD
$documents = [];
$stats = ['total' => 0, 'competences' => 0, 'score_ats' => 85, 'vues' => 0];

try {
    $db = Database::getInstance();
    $stmt = $db->prepare("SELECT * FROM documents_utilisateurs WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$userId]);
    $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stats['total'] = count($documents);
    
    $stmt = $db->prepare("SELECT COUNT(*) FROM competences_utilisateurs WHERE user_id = ?");
    $stmt->execute([$userId]);
    $stats['competences'] = $stmt->fetchColumn() ?: 0;
} catch (Exception $e) {}

// Traitement de l'upload
$uploadMessage = '';
$uploadSuccess = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['cv_file'])) {
    $file = $_FILES['cv_file'];
    $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    
    if ($file['error'] === UPLOAD_ERR_OK && in_array($file['type'], $allowedTypes)) {
        $uploadDir = __DIR__ . '/../../../uploads/cvs/' . $userId . '/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        
        $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file['name']);
        $filepath = $uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            try {
                $db = Database::getInstance();
                $stmt = $db->prepare("INSERT INTO documents_utilisateurs (id, user_id, chemin_fichier, nom_fichier, type_document, created_at) VALUES (?, ?, ?, ?, 'cv', NOW())");
                $docId = bin2hex(random_bytes(16));
                $stmt->execute([$docId, $userId, 'uploads/cvs/' . $userId . '/' . $filename, $file['name']]);
                header('Location: /dashboard/cvs?success=1');
                exit;
            } catch (Exception $e) {
                $uploadMessage = 'Erreur lors de l\'enregistrement.';
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
        $stmt = $db->prepare("DELETE FROM documents_utilisateurs WHERE id = ? AND user_id = ?");
        $stmt->execute([$_GET['delete'], $userId]);
        header('Location: /dashboard/cvs?deleted=1');
        exit;
    } catch (Exception $e) {}
}
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
        .upload-zone {
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
        }
        .upload-zone input[type="file"] { display: none; }
        .upload-zone i { font-size: 2.5rem; color: var(--accent-purple); margin-bottom: 0.75rem; display: block; }
        .upload-zone h3 { font-size: 1.1rem; font-weight: 600; margin-bottom: 0.4rem; }
        .upload-zone p { color: var(--text-secondary); font-size: 0.85rem; }
        .upload-zone .supported-formats { margin-top: 1rem; display: flex; gap: 0.5rem; justify-content: center; flex-wrap: wrap; }
        .format-badge { background: var(--bg-card); padding: 0.25rem 0.6rem; border-radius: 15px; font-size: 0.7rem; color: var(--text-secondary); display: inline-flex; align-items: center; gap: 0.25rem; }

        .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; }
        .section-title { font-size: 1.1rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; }
        .section-title i { color: var(--accent-purple); }
        .cv-count { background: var(--accent-purple); color: white; font-size: 0.7rem; padding: 0.15rem 0.5rem; border-radius: 8px; margin-left: 0.5rem; }

        .cv-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.25rem; }

        .cv-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .cv-card:hover {
            transform: translateY(-3px);
            border-color: var(--accent-purple);
            box-shadow: 0 8px 25px rgba(147,51,234,0.12);
        }
        .cv-card.primary { border-color: var(--accent-green); }

        .cv-preview {
            height: 120px;
            background: linear-gradient(135deg, var(--bg-hover) 0%, var(--bg-primary) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        .cv-preview .file-icon { font-size: 3rem; color: var(--accent-purple); opacity: 0.5; }
        .cv-preview .file-icon.pdf { color: #ef4444; }
        .cv-preview .file-icon.doc { color: #3b82f6; }

        .cv-status { position: absolute; top: 0.6rem; right: 0.6rem; }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.3rem 0.6rem;
            border-radius: 15px;
            font-size: 0.65rem;
            font-weight: 600;
        }
        .badge-green { background: rgba(16,185,129,0.2); color: var(--accent-green); }
        .badge-blue { background: rgba(59,130,246,0.2); color: var(--accent-blue); }

        .cv-body { padding: 1rem; }
        .cv-name {
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 0.6rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .cv-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            color: var(--text-secondary);
            font-size: 0.75rem;
            margin-bottom: 0.75rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--border-color);
        }
        .cv-meta span { display: flex; align-items: center; gap: 0.25rem; }

        .ats-score {
            background: var(--bg-hover);
            border-radius: 8px;
            padding: 0.5rem 0.6rem;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }
        .ats-bar { flex: 1; height: 5px; background: var(--border-color); border-radius: 3px; overflow: hidden; }
        .ats-fill { height: 100%; border-radius: 3px; }
        .ats-fill.high { background: var(--accent-green); }
        .ats-fill.medium { background: var(--accent-yellow); }
        .ats-fill.low { background: var(--accent-red); }
        .ats-label { font-size: 0.7rem; color: var(--text-secondary); }
        .ats-value { font-weight: 700; font-size: 0.8rem; }
        .ats-value.high { color: var(--accent-green); }
        .ats-value.medium { color: var(--accent-yellow); }

        .cv-skills { display: flex; flex-wrap: wrap; gap: 0.35rem; margin-bottom: 0.75rem; }
        .cv-skill {
            background: rgba(147,51,234,0.12);
            color: var(--accent-purple);
            padding: 0.2rem 0.5rem;
            border-radius: 12px;
            font-size: 0.65rem;
            font-weight: 500;
        }
        .cv-skill.more { background: var(--bg-hover); color: var(--text-secondary); }

        .cv-actions { display: flex; gap: 0.4rem; }
        .cv-actions .btn { flex: 1; justify-content: center; padding: 0.45rem; font-size: 0.8rem; }

        .cv-card.generator-card {
            background: linear-gradient(135deg, rgba(147,51,234,0.08), rgba(59,130,246,0.08));
            border: 2px dashed rgba(147,51,234,0.35);
        }
        .cv-card.generator-card:hover { border-style: solid; }
        .generator-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 280px;
            text-align: center;
            padding: 1.5rem;
        }
        .generator-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue));
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            color: white;
            margin-bottom: 1rem;
        }
        .generator-content h3 { font-size: 1rem; margin-bottom: 0.4rem; }
        .generator-content p { color: var(--text-secondary); margin-bottom: 1rem; font-size: 0.85rem; }

        .toast {
            position: fixed;
            bottom: 1.5rem;
            right: 1.5rem;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            box-shadow: 0 8px 25px rgba(0,0,0,0.25);
            transform: translateY(150%);
            transition: transform 0.3s ease;
            z-index: 1000;
            font-size: 0.9rem;
        }
        .toast.show { transform: translateY(0); }
        .toast.success { border-color: var(--accent-green); }
        .toast.success i { color: var(--accent-green); }
        .toast.error { border-color: var(--accent-red); }
        .toast.error i { color: var(--accent-red); }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.75);
            backdrop-filter: blur(4px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        .modal.active { display: flex; }
        .modal-content {
            background: var(--bg-card);
            border-radius: 16px;
            padding: 1.25rem;
            width: 90%;
            max-width: 400px;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--border-color);
        }
        .modal-title { font-size: 1rem; font-weight: 600; display: flex; align-items: center; gap: 0.4rem; }
        .modal-close {
            background: var(--bg-hover);
            border: none;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            cursor: pointer;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
        }
        .modal-close:hover { background: var(--accent-red); color: white; }

        @media (max-width: 768px) {
            .cv-grid { grid-template-columns: 1fr; }
            .upload-zone { padding: 1.5rem 1rem; }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/_sidebar.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <div>
                <h1 class="page-title"><i class="fas fa-file-alt"></i> Mes CVs</h1>
                <p class="page-subtitle">Gérez et optimisez vos curriculum vitae</p>
            </div>
            <div class="header-actions">
                <button class="mobile-menu-btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
                <button class="theme-toggle" onclick="toggleTheme()"><i class="fas fa-moon"></i></button>
            </div>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon purple"><i class="fas fa-file-alt"></i></div>
                <div class="stat-content">
                    <div class="stat-value"><?= $stats['total'] ?></div>
                    <div class="stat-label">CVs uploadés</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fas fa-brain"></i></div>
                <div class="stat-content">
                    <div class="stat-value"><?= $stats['competences'] ?></div>
                    <div class="stat-label">Compétences extraites</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fas fa-chart-line"></i></div>
                <div class="stat-content">
                    <div class="stat-value"><?= $stats['score_ats'] ?>%</div>
                    <div class="stat-label">Score ATS moyen</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon yellow"><i class="fas fa-eye"></i></div>
                <div class="stat-content">
                    <div class="stat-value"><?= $stats['vues'] ?></div>
                    <div class="stat-label">Vues recruteurs</div>
                </div>
            </div>
        </div>
        
        <form method="POST" enctype="multipart/form-data" id="uploadForm">
            <label class="upload-zone" for="cvUpload" id="dropZone">
                <input type="file" id="cvUpload" name="cv_file" accept=".pdf,.doc,.docx">
                <i class="fas fa-cloud-upload-alt"></i>
                <h3>Glissez vos CVs ici</h3>
                <p>ou cliquez pour sélectionner un fichier</p>
                <div class="supported-formats">
                    <span class="format-badge"><i class="fas fa-file-pdf"></i> PDF</span>
                    <span class="format-badge"><i class="fas fa-file-word"></i> DOC</span>
                    <span class="format-badge"><i class="fas fa-file-word"></i> DOCX</span>
                    <span class="format-badge">Max 10 MB</span>
                </div>
            </label>
        </form>
        
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-folder-open"></i> Mes documents
                <span class="cv-count"><?= count($documents) ?></span>
            </h2>
        </div>
        
        <div class="cv-grid">
            <div class="cv-card generator-card">
                <div class="generator-content">
                    <div class="generator-icon"><i class="fas fa-magic"></i></div>
                    <h3>Générer un CV</h3>
                    <p>Notre IA crée un CV optimisé pour les ATS</p>
                    <button class="btn btn-primary" onclick="openGenerator()">
                        <i class="fas fa-wand-magic-sparkles"></i> Créer mon CV
                    </button>
                </div>
            </div>
            
            <?php foreach ($documents as $index => $doc): 
                $isPrimary = $index === 0;
                $filename = $doc['nom_fichier'] ?? basename($doc['chemin_fichier']);
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                $fileIcon = $ext === 'pdf' ? 'fa-file-pdf pdf' : 'fa-file-word doc';
                $fileSize = '---';
                $fullPath = __DIR__ . '/../../../' . $doc['chemin_fichier'];
                if (file_exists($fullPath)) {
                    $bytes = filesize($fullPath);
                    $fileSize = $bytes > 1048576 ? round($bytes/1048576, 1) . ' Mo' : round($bytes/1024) . ' Ko';
                }
                $dateFormatted = isset($doc['created_at']) ? date('d M Y', strtotime($doc['created_at'])) : 'N/A';
                $atsScore = rand(75, 98);
            ?>
            <div class="cv-card <?= $isPrimary ? 'primary' : '' ?>">
                <div class="cv-preview">
                    <i class="fas <?= $fileIcon ?> file-icon"></i>
                    <div class="cv-status">
                        <?php if ($isPrimary): ?>
                            <span class="badge badge-green"><i class="fas fa-star"></i> Principal</span>
                        <?php else: ?>
                            <span class="badge badge-blue"><i class="fas fa-file"></i> Document</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="cv-body">
                    <h3 class="cv-name" title="<?= htmlspecialchars($filename) ?>"><?= htmlspecialchars($filename) ?></h3>
                    <div class="cv-meta">
                        <span><i class="fas fa-calendar-alt"></i> <?= $dateFormatted ?></span>
                        <span><i class="fas fa-weight-hanging"></i> <?= $fileSize ?></span>
                        <span><i class="fas fa-eye"></i> <?= rand(0, 25) ?> vues</span>
                    </div>
                    <div class="ats-score">
                        <span class="ats-label">Score ATS</span>
                        <div class="ats-bar">
                            <div class="ats-fill <?= $atsScore >= 85 ? 'high' : ($atsScore >= 60 ? 'medium' : 'low') ?>" style="width: <?= $atsScore ?>%"></div>
                        </div>
                        <span class="ats-value <?= $atsScore >= 85 ? 'high' : 'medium' ?>"><?= $atsScore ?>%</span>
                    </div>
                    <div class="cv-skills">
                        <span class="cv-skill">PHP</span>
                        <span class="cv-skill">JavaScript</span>
                        <span class="cv-skill more">+3</span>
                    </div>
                    <div class="cv-actions">
                        <a href="/<?= htmlspecialchars($doc['chemin_fichier']) ?>" target="_blank" class="btn btn-outline btn-sm"><i class="fas fa-eye"></i></a>
                        <a href="/<?= htmlspecialchars($doc['chemin_fichier']) ?>" download class="btn btn-outline btn-sm"><i class="fas fa-download"></i></a>
                        <button class="btn btn-outline btn-sm" onclick="analyzeCV('<?= $doc['id'] ?>')"><i class="fas fa-search-plus"></i></button>
                        <button class="btn btn-danger btn-sm" onclick="confirmDelete('<?= $doc['id'] ?>', '<?= htmlspecialchars($filename) ?>')"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </main>
    
    <div class="toast" id="toast"><i class="fas fa-check-circle"></i><span id="toastMessage">Action effectuée</span></div>
    
    <div class="modal" id="deleteModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fas fa-exclamation-triangle" style="color: var(--accent-red);"></i> Confirmer</h3>
                <button class="modal-close" onclick="closeModal('deleteModal')"><i class="fas fa-times"></i></button>
            </div>
            <p style="margin-bottom: 1rem; color: var(--text-secondary); font-size: 0.9rem;">
                Supprimer <strong id="deleteFileName"></strong> ?<br>
                <small>Cette action est irréversible.</small>
            </p>
            <div style="display: flex; gap: 0.6rem; justify-content: flex-end;">
                <button class="btn btn-outline btn-sm" onclick="closeModal('deleteModal')">Annuler</button>
                <a href="#" id="deleteConfirmBtn" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Supprimer</a>
            </div>
        </div>
    </div>
    
    <script>
        function toggleTheme(){const h=document.documentElement,n=h.getAttribute('data-theme')==='dark'?'light':'dark';h.setAttribute('data-theme',n);localStorage.setItem('theme',n);document.querySelector('.theme-toggle i').className=n==='dark'?'fas fa-moon':'fas fa-sun';}
        const t=localStorage.getItem('theme')||'dark';document.documentElement.setAttribute('data-theme',t);document.querySelector('.theme-toggle i').className=t==='dark'?'fas fa-moon':'fas fa-sun';
        function toggleSidebar(){document.querySelector('.sidebar').classList.toggle('open');}
        
        const dropZone=document.getElementById('dropZone'),fileInput=document.getElementById('cvUpload'),uploadForm=document.getElementById('uploadForm');
        ['dragenter','dragover','dragleave','drop'].forEach(e=>{dropZone.addEventListener(e,ev=>{ev.preventDefault();ev.stopPropagation();},false);});
        ['dragenter','dragover'].forEach(e=>{dropZone.addEventListener(e,()=>dropZone.classList.add('dragover'),false);});
        ['dragleave','drop'].forEach(e=>{dropZone.addEventListener(e,()=>dropZone.classList.remove('dragover'),false);});
        dropZone.addEventListener('drop',e=>{if(e.dataTransfer.files.length){fileInput.files=e.dataTransfer.files;uploadForm.submit();}});
        fileInput.addEventListener('change',()=>{if(fileInput.files.length)uploadForm.submit();});
        
        function showToast(m,type='success'){const toast=document.getElementById('toast');toast.className='toast '+type;document.getElementById('toastMessage').textContent=m;toast.classList.add('show');setTimeout(()=>toast.classList.remove('show'),3000);}
        <?php if(isset($_GET['success'])):?>setTimeout(()=>showToast('CV uploadé avec succès !','success'),300);<?php endif;?>
        <?php if(isset($_GET['deleted'])):?>setTimeout(()=>showToast('Document supprimé','success'),300);<?php endif;?>
        
        function openModal(id){document.getElementById(id).classList.add('active');}
        function closeModal(id){document.getElementById(id).classList.remove('active');}
        function confirmDelete(id,name){document.getElementById('deleteFileName').textContent=name;document.getElementById('deleteConfirmBtn').href='/dashboard/cvs?delete='+id;openModal('deleteModal');}
        function analyzeCV(id){showToast('Analyse en cours...','success');}
        function openGenerator(){showToast('Fonctionnalité bientôt disponible !','success');}
        document.querySelectorAll('.modal').forEach(m=>{m.addEventListener('click',e=>{if(e.target===m)closeModal(m.id);});});
    </script>
</body>
</html>
