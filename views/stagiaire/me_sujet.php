<?php 
$pageTitle = "Mon Sujet de Stage";
$pageHeader = "Sujet & Encadrant Assigné";
require_once ROOT_PATH . '/views/layouts/header.php';
require_once ROOT_PATH . '/views/layouts/sidebar.php';
require_once ROOT_PATH . '/views/layouts/navbar.php';
?>

<div class="container-fluid">
    <?php if (!$stageActif): ?>
        <div class="alert alert-info p-4 rounded-4 shadow-sm">
            <h5><i class="bi bi-info-circle-fill me-2"></i>Aucun sujet assigné</h5>
            <p class="m-0">Aucun stage n'est actuellement actif pour votre profil.</p>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card-ocp">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span class="fs-5 fw-bold text-dark"><i class="bi bi-journal-check me-2 text-success"></i>Fiche du Sujet de Stage</span>
                        <span class="badge badge-status <?= $stageActif['statut'] ?>"><?= strtoupper(str_replace('_', ' ', $stageActif['statut'])) ?></span>
                    </div>
                    <div class="card-body">
                        <h4 class="fw-bold text-dark mb-3"><?= sanitize($stageActif['sujet_titre']) ?></h4>
                        
                        <div class="mb-4">
                            <h6 class="fw-bold text-muted text-uppercase small">Domaine / Spécialité :</h6>
                            <span class="badge bg-success-subtle text-success border border-success px-3 py-1 fw-bold fs-7"><?= sanitize($stageActif['sujet_domaine']) ?></span>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold text-muted text-uppercase small">Description Détaillée du Sujet :</h6>
                            <div class="p-3 bg-light rounded-3 border text-dark lead" style="font-size:1rem; line-height:1.6;">
                                <?= nl2br(sanitize($stageActif['sujet_description'])) ?>
                            </div>
                        </div>

                        <div class="row g-3 border-top pt-3">
                            <div class="col-md-6">
                                <span class="text-muted small d-block">Date de début officielle :</span>
                                <strong class="fs-6 text-dark"><i class="bi bi-calendar-event me-1"></i> <?= date('d/m/Y', strtotime($stageActif['date_debut'])) ?></strong>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted small d-block">Date de fin prévue :</span>
                                <strong class="fs-6 text-dark"><i class="bi bi-calendar-check me-1"></i> <?= date('d/m/Y', strtotime($stageActif['date_fin'])) ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card-ocp">
                    <div class="card-header">
                        <span class="fw-bold"><i class="bi bi-person-badge me-2 text-primary"></i>Encadrant OCP Tuteur</span>
                    </div>
                    <div class="card-body text-center">
                        <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold display-6 mb-3" style="width:80px; height:80px; background: var(--ocp-green) !important;">
                            <?= strtoupper(substr($stageActif['encadrant_prenom'], 0, 1) . substr($stageActif['encadrant_nom'], 0, 1)) ?>
                        </div>
                        <h5 class="fw-bold text-dark m-0"><?= sanitize($stageActif['encadrant_prenom'] . ' ' . $stageActif['encadrant_nom']) ?></h5>
                        <p class="text-muted small mb-3"><?= sanitize($stageActif['encadrant_fonction']) ?></p>
                        
                        <div class="list-group list-group-flush text-start border-top pt-2">
                            <div class="list-group-item bg-transparent px-0 py-2">
                                <span class="text-muted small d-block">Département :</span>
                                <strong><?= sanitize($stageActif['encadrant_departement']) ?></strong>
                            </div>
                            <div class="list-group-item bg-transparent px-0 py-2">
                                <span class="text-muted small d-block">Email de contact :</span>
                                <a href="mailto:<?= sanitize($stageActif['encadrant_email']) ?>" class="text-success fw-semibold text-decoration-none"><?= sanitize($stageActif['encadrant_email']) ?></a>
                            </div>
                            <?php if (!empty($stageActif['encadrant_telephone'])): ?>
                                <div class="list-group-item bg-transparent px-0 py-2">
                                    <span class="text-muted small d-block">Téléphone :</span>
                                    <strong><?= sanitize($stageActif['encadrant_telephone']) ?></strong>
                                </div>
                            <?php endif; ?>
                        </div>

                        <a href="<?= url('stagiaire/messages') ?>" class="btn btn-ocp w-100 mt-3"><i class="bi bi-chat-left-text me-2"></i> Envoyer un message</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
