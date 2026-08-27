<?php 
$pageTitle = "Notifications Encadrant";
$pageHeader = "Toutes vos notifications d'encadrement";
require_once ROOT_PATH . '/views/layouts/header.php';
require_once ROOT_PATH . '/views/layouts/sidebar.php';
require_once ROOT_PATH . '/views/layouts/navbar.php';
?>

<div class="container-fluid">
    <div class="card-ocp">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-bell-fill me-2 text-warning"></i>Historique des Notifications</span>
            <form action="<?= url('encadrant/mark_all_notif') ?>" method="POST" class="m-0">
                <button type="submit" class="btn btn-outline-success btn-sm">
                    <i class="bi bi-check-all me-1"></i> Tout marquer comme lu
                </button>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                <?php if (empty($notifications)): ?>
                    <div class="p-5 text-center text-muted">
                        <i class="bi bi-bell-slash fs-1 d-block mb-2 text-secondary"></i>
                        Aucune notification répertoriée pour le moment.
                    </div>
                <?php else: ?>
                    <?php foreach ($notifications as $n): ?>
                        <div class="list-group-item p-3 d-flex align-items-start justify-content-between gap-3 <?= $n['lu'] ? '' : 'bg-light border-start border-4 border-success' ?>" data-notif-id="<?= $n['id'] ?>">
                            <div class="d-flex gap-3 align-items-start">
                                <div class="stat-icon bg-warning-subtle text-warning rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:40px; height:40px; flex-shrink:0;">
                                    <i class="bi bi-bell-fill fs-6"></i>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <h6 class="fw-bold text-dark m-0"><?= sanitize($n['titre']) ?></h6>
                                        <?php if (!$n['lu']): ?>
                                            <span class="badge bg-success notif-unread-dot" style="font-size:0.65rem;">Nouveau</span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-muted small m-0 mt-1"><?= sanitize($n['message']) ?></p>
                                    <small class="text-secondary d-block mt-2" style="font-size:0.75rem;">
                                        <i class="bi bi-clock me-1"></i><?= date('d/m/Y à H:i', strtotime($n['created_at'])) ?>
                                    </small>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                <?php if (!empty($n['lien'])): ?>
                                    <a href="<?= url($n['lien']) ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-arrow-right-short me-1"></i> Consulter
                                    </a>
                                <?php endif; ?>
                                <?php if (!$n['lu']): ?>
                                    <button class="btn btn-sm btn-light mark-notif-read" data-id="<?= $n['id'] ?>" title="Marquer comme lu">
                                        <i class="bi bi-check2"></i>
                                    </button>
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
