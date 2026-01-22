<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="dashboard-container">
    <?php require_once __DIR__ . '/_sidebar.php'; ?>

    <main class="dashboard-content">
        <div class="page-header">
            <div>
                <h1><i class="fas fa-check-circle"></i> Validations de Compétences</h1>
                <p>Approuvez ou rejetez les demandes de validation de vos équipes</p>
            </div>
        </div>

        <!-- Validations en attente -->
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-hourglass-half"></i> En attente de validation</h2>
                <span class="badge-count"><?php echo count($validations); ?></span>
            </div>
            <div class="card-body">
                <?php if (empty($validations)): ?>
                    <p class="text-muted">
                        <i class="fas fa-check"></i> Aucune validation en attente. Bravo!
                    </p>
                <?php else: ?>
                    <div class="validations-list">
                        <?php foreach ($validations as $v): ?>
                            <div class="validation-card" data-id="<?php echo $v['id']; ?>">
                                <div class="validation-header">
                                    <div class="employee-info">
                                        <h3><?php echo htmlspecialchars($v['prenom'] . ' ' . $v['nom']); ?></h3>
                                        <p class="email"><?php echo htmlspecialchars($v['email']); ?></p>
                                    </div>
                                    <div class="validation-date">
                                        <small>Demandé le</small>
                                        <p><?php echo date('d/m/Y H:i', strtotime($v['date_demande'])); ?></p>
                                    </div>
                                </div>

                                <div class="competence-info">
                                    <div class="competence-name">
                                        <i class="fas fa-star"></i>
                                        <strong><?php echo htmlspecialchars($v['competence_nom']); ?></strong>
                                    </div>
                                    <div class="competence-level">
                                        <span class="level-badge level-<?php echo $v['niveau_demande']; ?>">
                                            Niveau <?php echo $v['niveau_demande']; ?>/5
                                        </span>
                                    </div>
                                </div>

                                <div class="validation-actions">
                                    <button class="btn-approve" data-id="<?php echo $v['id']; ?>">
                                        <i class="fas fa-check"></i> Approuver
                                    </button>
                                    <button class="btn-reject" data-id="<?php echo $v['id']; ?>">
                                        <i class="fas fa-times"></i> Rejeter
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Historique des validations -->
        <?php if (!empty($recent)): ?>
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-history"></i> Validations récentes</h2>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="validations-table">
                        <thead>
                            <tr>
                                <th>Employé</th>
                                <th>Compétence</th>
                                <th>Statut</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent as $r): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($r['prenom'] . ' ' . $r['nom']); ?></strong>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($r['competence_nom']); ?>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?php echo str_replace('é', 'e', $r['statut']); ?>">
                                            <?php 
                                                $statuts = ['approuve' => '✓ Approuvée', 'rejete' => '✗ Rejetée'];
                                                echo $statuts[$r['statut']] ?? $r['statut'];
                                            ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small><?php echo date('d/m/Y H:i', strtotime($r['date_validation'])); ?></small>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </main>
</div>

<style>
.dashboard-container {
    display: flex;
    min-height: 100vh;
    background: linear-gradient(135deg, #0a0118 0%, #1a0d2e 100%);
}

.dashboard-content {
    flex: 1;
    padding: 30px;
}

.page-header {
    margin-bottom: 30px;
}

.page-header h1 {
    font-size: 28px;
    margin-bottom: 5px;
}

.page-header p {
    color: #a0a0a0;
}

.card {
    background: rgba(26, 13, 46, 0.8);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 20px;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    background: rgba(0, 0, 0, 0.2);
}

.card-header h2 {
    font-size: 18px;
    margin: 0;
}

.badge-count {
    background: #9333ea;
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 14px;
}

.card-body {
    padding: 20px;
}

.text-muted {
    color: #a0a0a0;
    text-align: center;
    padding: 30px 20px;
}

.validations-list {
    display: grid;
    gap: 15px;
}

.validation-card {
    background: rgba(0, 0, 0, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.1);
    padding: 20px;
    border-radius: 10px;
    transition: all 0.2s;
}

.validation-card:hover {
    border-color: rgba(147, 51, 234, 0.3);
    background: rgba(147, 51, 234, 0.05);
}

.validation-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.employee-info h3 {
    margin-bottom: 5px;
    font-size: 16px;
}

.employee-info .email {
    color: #a0a0a0;
    font-size: 14px;
    margin: 0;
}

.validation-date {
    text-align: right;
}

.validation-date small {
    color: #a0a0a0;
    display: block;
    font-size: 12px;
}

.validation-date p {
    margin: 5px 0 0 0;
    font-size: 14px;
}

.competence-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    gap: 15px;
}

.competence-name {
    display: flex;
    align-items: center;
    gap: 8px;
}

.competence-name i {
    color: #f59e0b;
}

.competence-name strong {
    color: #fff;
}

.level-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.level-1 { background: rgba(59, 130, 246, 0.2); color: #60a5fa; }
.level-2 { background: rgba(34, 197, 94, 0.2); color: #4ade80; }
.level-3 { background: rgba(168, 85, 247, 0.2); color: #d8b4fe; }
.level-4 { background: rgba(245, 158, 11, 0.2); color: #fbbf24; }
.level-5 { background: rgba(239, 68, 68, 0.2); color: #f87171; }

.validation-actions {
    display: flex;
    gap: 10px;
}

.btn-approve,
.btn-reject {
    flex: 1;
    padding: 10px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.2s;
}

.btn-approve {
    background: rgba(16, 185, 129, 0.2);
    color: #4ade80;
    border: 1px solid rgba(16, 185, 129, 0.3);
}

.btn-approve:hover {
    background: rgba(16, 185, 129, 0.3);
}

.btn-reject {
    background: rgba(239, 68, 68, 0.2);
    color: #ff6b6b;
    border: 1px solid rgba(239, 68, 68, 0.3);
}

.btn-reject:hover {
    background: rgba(239, 68, 68, 0.3);
}

.table-responsive {
    overflow-x: auto;
}

.validations-table {
    width: 100%;
    border-collapse: collapse;
}

.validations-table thead {
    background: rgba(0, 0, 0, 0.3);
}

.validations-table th {
    padding: 12px;
    text-align: left;
    font-weight: 600;
    color: #a0a0a0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.validations-table td {
    padding: 12px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.status-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
}

.status-approuve {
    background: rgba(16, 185, 129, 0.1);
    color: #4ade80;
}

.status-rejete {
    background: rgba(239, 68, 68, 0.1);
    color: #ff6b6b;
}

@media (max-width: 768px) {
    .validation-header {
        flex-direction: column;
    }

    .validation-date {
        text-align: left;
        margin-top: 10px;
    }

    .competence-info {
        flex-direction: column;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Approuver une validation
    document.querySelectorAll('.btn-approve').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const commentaire = prompt('Commentaire (optionnel):');
            
            if (commentaire !== null) {
                fetch('/validation/approve', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        validation_id: id,
                        commentaire: commentaire
                    })
                }).then(r => r.json()).then(data => {
                    if (data.success) {
                        alert('Validation approuvée!');
                        location.reload();
                    } else {
                        alert('Erreur: ' + (data.error || 'Impossible d\'approuver'));
                    }
                });
            }
        });
    });

    // Rejeter une validation
    document.querySelectorAll('.btn-reject').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const reason = prompt('Motif du rejet:');
            
            if (reason !== null) {
                fetch('/validation/reject', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        validation_id: id,
                        reason: reason
                    })
                }).then(r => r.json()).then(data => {
                    if (data.success) {
                        alert('Validation rejetée');
                        location.reload();
                    } else {
                        alert('Erreur: ' + (data.error || 'Impossible de rejeter'));
                    }
                });
            }
        });
    });
});
</script>
