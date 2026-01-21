<?php if (session_status() === PHP_SESSION_NONE) session_start();
// Only admin
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: /login');
    exit;
}
require_once __DIR__ . '/../views/layouts/header.php';
require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../Models/Database.php';

$db = \Database::getInstance();

// Ensure CSRF token
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
    try {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
    } catch (Exception $e) {
        // fallback
        $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(24));
    }
    $_SESSION['csrf_token_time'] = time();
}

// Flash (read once)
$flash = $_SESSION['flash'] ?? null;
if (isset($_SESSION['flash'])) unset($_SESSION['flash']);

// Handle POST actions: delete, mark_read, mark_unread, mark_all_read
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $mid = $_POST['message_id'] ?? null;

    // Basic CSRF check + expiry (1 hour)
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Jeton CSRF invalide.'];
        header('Location: /admin_messages');
        exit;
    }
    $maxAge = 3600; // seconds
    $tokenTime = $_SESSION['csrf_token_time'] ?? 0;
    if (time() - $tokenTime > $maxAge) {
        // regenerate token
        try { $_SESSION['csrf_token'] = bin2hex(random_bytes(24)); } catch (Exception $e) { $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(24)); }
        $_SESSION['csrf_token_time'] = time();
        $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Jeton CSRF expiré. Réessayez.'];
        header('Location: /admin_messages');
        exit;
    }

    try {
        if ($action === 'delete' && $mid) {
            $stmt = $db->prepare("DELETE FROM messages WHERE id_message = :id OR id = :id");
            $stmt->execute([':id' => $mid]);
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Message supprimé.'];
        } elseif ($action === 'mark_read' && $mid) {
            $stmt = $db->prepare("UPDATE messages SET is_read = 1 WHERE id_message = :id OR id = :id");
            $stmt->execute([':id' => $mid]);
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Message marqué comme lu.'];
        } elseif ($action === 'mark_unread' && $mid) {
            $stmt = $db->prepare("UPDATE messages SET is_read = 0 WHERE id_message = :id OR id = :id");
            $stmt->execute([':id' => $mid]);
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Message marqué comme non lu.'];
        } elseif ($action === 'mark_all_read') {
            $stmt = $db->prepare("UPDATE messages SET is_read = 1 WHERE is_read = 0");
            $stmt->execute();
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Tous les messages ont été marqués comme lus.'];
        } elseif ($action === 'delete_smoke') {
            // Remove messages created by smoke tests (email like 'smoke+%')
            $stmt = $db->prepare("DELETE FROM messages WHERE email LIKE :pat");
            $stmt->execute([':pat' => 'smoke+%']);
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Messages de test supprimés.'];
        } else {
            $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Action inconnue.'];
        }
    } catch (Exception $e) {
        $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Erreur lors de l\'action: ' . $e->getMessage()];
    }

    // Redirect to avoid re-submission
    header('Location: /admin_messages');
    exit;
}

// Pagination + filter
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;
$filter = ($_GET['filter'] ?? '') === 'unread' ? 'unread' : '';

$messages = [];
try {
    if ($filter === 'unread') {
        $countStmt = $db->query("SELECT COUNT(*) as c FROM messages WHERE is_read = 0");
        $total = (int)($countStmt->fetch()['c'] ?? 0);
        $stmt = $db->prepare("SELECT * FROM messages WHERE is_read = 0 ORDER BY cree_le DESC LIMIT :lim OFFSET :off");
    } else {
        $countStmt = $db->query("SELECT COUNT(*) as c FROM messages");
        $total = (int)($countStmt->fetch()['c'] ?? 0);
        $stmt = $db->prepare("SELECT * FROM messages ORDER BY cree_le DESC LIMIT :lim OFFSET :off");
    }
    $stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $messages = $stmt->fetchAll();
} catch (Exception $e) {
    // ignore
    $total = 0;
}

$totalPages = max(1, (int)ceil($total / $perPage));
?>
<main class="container">
    <section class="section">
        <h1>Messages — Contact</h1>
        <p>Liste des messages envoyés via le formulaire de contact.</p>

        <?php if ($flash): ?>
            <div style="margin-top:1rem;padding:12px;border-radius:8px;background:<?php echo $flash['type']==='success' ? '#ecfdf5' : '#fff4f4'; ?>;border:1px solid <?php echo $flash['type']==='success' ? '#10b981' : '#ef4444'; ?>;color:<?php echo $flash['type']==='success' ? '#065f46' : '#7f1d1d'; ?>;">
                <?php echo htmlspecialchars($flash['msg']); ?>
            </div>
        <?php endif; ?>

        <div style="margin-top:1rem;display:flex;gap:10px;align-items:center;">
            <form method="get" style="margin:0;">
                <input type="hidden" name="filter" value="<?php echo $filter === 'unread' ? '' : 'unread'; ?>">
                <button class="btn btn-sm" type="submit"><?php echo $filter === 'unread' ? 'Afficher tout' : 'Afficher non lus'; ?></button>
            </form>

            <form method="post" style="margin:0;">
                <input type="hidden" name="action" value="mark_all_read">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <button class="btn btn-sm" type="submit">Marquer tout lu</button>
            </form>

            <form method="post" style="margin:0;">
                <input type="hidden" name="action" value="delete_smoke">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <button class="btn btn-sm" type="submit" onclick="return confirm('Supprimer tous les messages de test (smoke+@) ?')">Supprimer tests</button>
            </form>

            <div style="margin-left:auto;color:var(--text-light);">Total: <?php echo $total; ?></div>
        </div>

        <div style="margin-top:1.5rem;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr>
                        <th style="padding:8px;text-align:left">#</th>
                        <th style="padding:8px;text-align:left">Nom</th>
                        <th style="padding:8px;text-align:left">Email</th>
                        <th style="padding:8px;text-align:left">Message</th>
                        <th style="padding:8px;text-align:left">Date</th>
                        <th style="padding:8px;text-align:left">Statut</th>
                        <th style="padding:8px;text-align:left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($messages) === 0): ?>
                        <tr><td colspan="7" style="padding:12px">Aucun message trouvé.</td></tr>
                    <?php else: foreach ($messages as $m): ?>
                        <tr>
                            <td style="padding:8px"><?php echo htmlspecialchars($m['id'] ?? $m['id_message'] ?? ''); ?></td>
                            <td style="padding:8px"><?php echo htmlspecialchars($m['nom'] ?? ''); ?></td>
                            <td style="padding:8px"><?php echo htmlspecialchars($m['email'] ?? ''); ?></td>
                            <td style="padding:8px;max-width:500px;white-space:pre-wrap"><?php echo nl2br(htmlspecialchars($m['message'] ?? '')); ?></td>
                            <td style="padding:8px"><?php echo htmlspecialchars($m['cree_le'] ?? ''); ?></td>
                            <td style="padding:8px">
                                <?php $isRead = (isset($m['is_read']) && $m['is_read']); ?>
                                <?php if ($isRead): ?>
                                    <span style="color: #10b981; font-weight:700;">Lu</span>
                                <?php else: ?>
                                    <span style="color: #f59e0b; font-weight:700;">Non lu</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding:8px">
                                <div style="display:flex;gap:6px;">
                                    <form method="post" style="display:inline;margin:0;">
                                        <input type="hidden" name="message_id" value="<?php echo htmlspecialchars($m['id'] ?? $m['id_message'] ?? ''); ?>">
                                        <?php if (isset($m['is_read']) && $m['is_read']): ?>
                                            <input type="hidden" name="action" value="mark_unread">
                                            <button class="btn btn-sm" type="submit">Marquer non lu</button>
                                        <?php else: ?>
                                            <input type="hidden" name="action" value="mark_read">
                                            <button class="btn btn-sm" type="submit">Marquer lu</button>
                                        <?php endif; ?>
                                    </form>
                                    <form method="post" onsubmit="return confirm('Supprimer ce message ?');" style="display:inline;margin:0;">
                                        <input type="hidden" name="message_id" value="<?php echo htmlspecialchars($m['id'] ?? $m['id_message'] ?? ''); ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <button class="btn btn-sm btn-danger" type="submit">Supprimer</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div style="margin-top:1rem;display:flex;gap:8px;align-items:center;">
                <?php if ($page > 1): ?>
                    <a href="/admin_messages?page=<?php echo $page-1; ?><?php echo $filter==='unread' ? '&filter=unread' : ''; ?>" class="btn btn-sm">&laquo; Précédent</a>
                <?php endif; ?>

                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <a href="/admin_messages?page=<?php echo $p; ?><?php echo $filter==='unread' ? '&filter=unread' : ''; ?>" style="padding:6px 10px;border-radius:6px;background:<?php echo $p===$page ? 'rgba(147,51,234,0.9)' : 'transparent'; ?>;color:<?php echo $p===$page ? 'white' : 'var(--text-light)'; ?>;text-decoration:none"><?php echo $p; ?></a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="/admin_messages?page=<?php echo $page+1; ?><?php echo $filter==='unread' ? '&filter=unread' : ''; ?>" class="btn btn-sm">Suivant &raquo;</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php require_once __DIR__ . '/../views/layouts/footer.php';
