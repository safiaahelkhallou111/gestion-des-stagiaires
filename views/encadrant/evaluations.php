<?php 
$pageTitle = "Évaluations de Stage";
$pageHeader = "Formulaires & Notes des Stagiaires ";
require_once ROOT_PATH . '/views/layouts/header.php';
require_once ROOT_PATH . '/views/layouts/sidebar.php';
require_once ROOT_PATH . '/views/layouts/navbar.php';
?>

<div class="container-fluid">
    <div class="card-ocp">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="fs-5 fw-bold text-dark"><i class="bi bi-star-fill me-2 text-warning"></i>Évaluations des Stagiaires Encadrés </span>
            <span class="badge bg-success px-3 py-2 fw-bold" style="background: var(--ocp-green) !important;"><?= count($stagiaires) ?> Stagiaire(s)</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-ocp align-middle">
                    <thead>
                        <tr>
                            <th>Stagiaire</th>
                            <th>Type Stage</th>
                            <th>Sujet Assigné</th>
                            <th>Note Globale</th>
                            <th>Statut Fiche</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($stagiaires)): ?>
                            <tr><td colspan="6" class="text-center text-muted py-5">Aucun stagiaire disponible pour l'évaluation.</td></tr>
                        <?php else: ?>
                            <?php foreach ($stagiaires as $s): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <?= renderAvatar($s['avatar'] ?? 'default.png', $s['prenom'], $s['nom'], 36) ?>
                                            <div>
                                                <div class="fw-bold text-dark"><?= sanitize($s['prenom'] . ' ' . $s['nom']) ?></div>
                                                <small class="text-muted"><i class="bi bi-card-text me-1"></i>CIN: <?= sanitize($s['cin']) ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-light text-dark border"><?= sanitize($s['type_stage']) ?></span></td>
                                    <td style="max-width: 250px;">
                                        <div class="text-truncate fw-semibold" title="<?= sanitize($s['sujet_titre']) ?>"><?= sanitize($s['sujet_titre']) ?></div>
                                    </td>
                                    <td>
                                        <?php if (!empty($s['note_globale'])): ?>
                                            <span class="badge bg-success-subtle text-success border border-success fs-6 fw-bold"><?= number_format($s['note_globale'], 2) ?> / 10</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning-subtle text-warning border border-warning">Non évalué</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($s['note_globale'])): ?>
                                            <span class="text-success small fw-bold"><i class="bi bi-check-circle-fill me-1"></i> Évaluation complétée</span>
                                        <?php else: ?>
                                            <span class="text-muted small"><i class="bi bi-clock me-1"></i> En attente de saisie</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= url('encadrant/stagiaire_detail?id=' . $s['stage_id']) ?>" class="btn btn-outline-success btn-sm">
                                            <i class="bi bi-pencil-square me-1"></i> Saisir / Modifier Fiche
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
