<?php 
$pageTitle = "Notifications";
$pageHeader = "Centre de Notifications & Alertes";
require_once ROOT_PATH . '/views/layouts/header.php';
require_once ROOT_PATH . '/views/layouts/sidebar.php';
require_once ROOT_PATH . '/views/layouts/navbar.php';
?>

<div class="container-fluid">
    <div class="card-ocp">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="fs-5 fw-bold text-dark"><i class="bi bi-bell-fill me-2 text-warning"></i>Historique des Notifications</span>
            <span class="badge bg-light text-dark border"><?= count($notifications) ?> alerte(s)</span>
        </div>
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                <?php if (empty($notifications)): ?>
                    <div class="p-5 text-center text-muted">
                        <i class="bi bi-bell-slash fs-1 d-block mb-2 text-secondary"></i>
                        <h5 class="fw-bold">Aucune notification répertoriée.</h5>
                    </div>
                <?php else: ?>
                    <?php foreach ($notifications as $n): ?>
                        <div class="list-group-item p-3 border-bottom <?= $n['lu'] ? '' : 'bg-light' ?>">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div class="d-flex align-items-center gap-2">
                                    <?php if (!$n['lu']): ?>
                                        <span class="badge bg-success rounded-circle p-1" style="background: var(--ocp-green) !important;"> </span>
                                    <?php endif; ?>
                                    <h6 class="fw-bold text-dark m-0"><?= sanitize($n['titre']) ?></h6>
                                </div>
                                <small class="text-muted"><i class="bi bi-clock me-1"></i><?= date('d/m/Y H:i', strtotime($n['created_at'])) ?></small>
                            </div>
                            <p class="text-muted small m-0 mb-2"><?= sanitize($n['message']) ?></p>
                            
                            <div class="d-flex gap-2">
                                <?php if (!empty($n['lien'])): ?>
                                    <a href="<?= url($n['lien']) ?>" class="btn btn-outline-success btn-sm"><i class="bi bi-box-arrow-up-right me-1"></i> Consulter</a>
                                <?php endif; ?>
                                <?php if (!$n['lu']): ?>
                                    <button class="btn btn-light btn-sm mark-notif-read" data-id="<?= $n['id'] ?>"><i class="bi bi-check2"></i> Marquer comme lue</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
