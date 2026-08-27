<?php 
$pageTitle = "Tableau de Bord Stagiaire";
$pageHeader = "Tableau de Bord Stagiaire";
require_once ROOT_PATH . '/views/layouts/header.php';
require_once ROOT_PATH . '/views/layouts/sidebar.php';
require_once ROOT_PATH . '/views/layouts/navbar.php';
?>

<div class="container-fluid">
    <!-- Welcome Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 bg-white p-4 rounded-4 shadow-sm border">
        <div>
            <span class="badge bg-success-subtle text-success fw-bold px-3 py-1 mb-2 rounded-pill">ESPACE STAGIAIRE OCP</span>
            <h3 class="fw-bold m-0" style="color: var(--ocp-dark);">Bienvenue, <?= sanitize($user['prenom'] . ' ' . $user['nom']) ?> </h3>
            <p class="text-muted m-0 mt-1">Suivi de votre avancement de stage, pointage et tâches assignées par l'encadrant.</p>
        </div>
        <div class="d-none d-md-block text-end">
            <span class="badge bg-light text-dark p-2 border fw-semibold"><i class="bi bi-calendar-event me-1"></i> <?= date('d/m/Y') ?></span>
        </div>
    </div>

    <?php if (!$stageActif): ?>
        <div class="alert alert-warning p-4 rounded-4 shadow-sm border-0">
            <h5 class="fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Aucun stage actif attribué</h5>
            <p class="m-0">Votre compte est créé mais vous n'avez pas encore été affecté à un sujet de stage par votre encadrant OCP. Veuillez patienter ou contacter votre responsable d'entité.</p>
        </div>
    <?php else: ?>

        <?php if ($stageActif['statut'] === 'termine'): ?>
            <div class="alert alert-success p-3 rounded-4 shadow-sm border-0 d-flex align-items-center gap-3 mb-4">
                <i class="bi bi-patch-check-fill display-5 text-success"></i>
                <div>
                    <h5 class="fw-bold text-dark mb-1">Stage Validé & Terminé (Règle RG6)</h5>
                    <p class="small text-muted mb-0">Félicitations ! Votre stage a été officiellement validé par votre encadrant. Votre espace est désormais conservé en <strong>lecture seule</strong>.</p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Stat KPIs Row -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stat-card success">
                    <div>
                        <div class="stat-label">Assiduité (Taux)</div>
                        <div class="stat-value text-success"><?= $assiduite['taux_presence'] ?>%</div>
                        <div class="small text-muted mt-1"><?= $assiduite['nb_presents'] ?> présence(s) sur <?= count($pointages) ?> jour(s)</div>
                    </div>
                    <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card primary">
                    <div>
                        <div class="stat-label">Tâches Réalisées</div>
                        <div class="stat-value text-primary">
                            <?php 
                            $terminees = array_filter($taches, fn($t) => $t['statut'] === 'termine');
                            echo count($terminees) . '/' . count($taches);
                            ?>
                        </div>
                        <div class="small text-muted mt-1">Tâches assignées</div>
                    </div>
                    <div class="stat-icon"><i class="bi bi-list-task"></i></div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card warning">
                    <div>
                        <div class="stat-label">Pointage du Jour</div>
                        <div class="stat-value">
                            <?php if ($pointageAujourdhui): ?>
                                <span class="badge bg-success fs-6"><i class="bi bi-check-lg"></i> Effectué</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark fs-6"><i class="bi bi-clock"></i> À faire</span>
                            <?php endif; ?>
                        </div>
                        <div class="small text-muted mt-1"><?= date('d/m/Y') ?></div>
                    </div>
                    <div class="stat-icon"><i class="bi bi-fingerprint"></i></div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card danger">
                    <div>
                        <div class="stat-label">Note Globale Stage</div>
                        <div class="stat-value text-dark">
                            <?= $evaluation ? number_format($evaluation['note_globale'], 2) . '/10' : 'N/A' ?>
                        </div>
                        <div class="small text-muted mt-1"><?= $evaluation ? 'Évaluation disponible' : 'En attente' ?></div>
                    </div>
                    <div class="stat-icon"><i class="bi bi-award-fill"></i></div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Stage Details Card -->
            <div class="col-lg-7">
                <div class="card-ocp">
                    <div class="card-header">
                        <span><i class="bi bi-journal-text me-2 text-success"></i>Sujet de Stage Assigné</span>
                        <span class="badge badge-status <?= $stageActif['statut'] ?>"><?= strtoupper(str_replace('_', ' ', $stageActif['statut'])) ?></span>
                    </div>
                    <div class="card-body">
                        <h5 class="fw-bold text-dark mb-2"><?= sanitize($stageActif['sujet_titre']) ?></h5>
                        <p class="text-muted mb-4"><?= nl2br(sanitize($stageActif['sujet_description'])) ?></p>

                        <div class="row g-3 bg-light p-3 rounded-3 mb-3 border">
                            <div class="col-sm-6">
                                <span class="text-muted small d-block">Encadrant OCP :</span>
                                <strong class="text-dark"><i class="bi bi-person-badge me-1"></i> M. <?= sanitize($stageActif['encadrant_prenom'] . ' ' . $stageActif['encadrant_nom']) ?></strong>
                                <div class="small text-muted"><?= sanitize($stageActif['encadrant_fonction']) ?></div>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted small d-block">Période du stage :</span>
                                <strong><i class="bi bi-calendar3 me-1"></i> <?= date('d/m/Y', strtotime($stageActif['date_debut'])) ?> au <?= date('d/m/Y', strtotime($stageActif['date_fin'])) ?></strong>
                                <div class="small text-muted"><?= sanitize($stagiaire['type_stage']) ?> (<?= sanitize($stagiaire['universite']) ?>)</div>
                            </div>
                        </div>

                        <!-- Daily Pointage Quick Action -->
                        <div class="p-3 border rounded-3 bg-white">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="fw-bold m-0"><i class="bi bi-clock-history text-success me-2"></i>Pointage de Présence Quotidienne </h6>
                                    <small class="text-muted">Enregistrez votre présence pour aujourd'hui</small>
                                </div>
                                <?php if (!$pointageAujourdhui): ?>
                                    <form action="<?= url('stagiaire/do_pointage') ?>" method="POST">
                                        <button type="submit" class="btn btn-ocp btn-sm"><i class="bi bi-check2-circle me-1"></i> Pointer ma présence</button>
                                    </form>
                                <?php else: ?>
                                    <span class="badge bg-success-subtle text-success border border-success px-3 py-2 fw-bold"><i class="bi bi-check-circle-fill me-1"></i> Présence Validée (<?= date('H:i', strtotime($pointageAujourdhui['created_at'])) ?>)</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tâches en cours -->
                <div class="card-ocp">
                    <div class="card-header">
                        <span><i class="bi bi-check2-square me-2 text-primary"></i>Avancement des Tâches</span>
                        <a href="<?= url('stagiaire/taches') ?>" class="btn btn-link btn-sm text-success text-decoration-none">Voir tout <i class="bi bi-arrow-right"></i></a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-ocp">
                                <thead>
                                    <tr>
                                        <th>Tâche</th>
                                        <th>Échéance</th>
                                        <th>Progression</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($taches)): ?>
                                        <tr><td colspan="4" class="text-center text-muted py-4">Aucune tâche assignée pour le moment.</td></tr>
                                    <?php else: ?>
                                        <?php foreach (array_slice($taches, 0, 4) as $t): ?>
                                            <tr>
                                                <td class="fw-semibold text-dark"><?= sanitize($t['titre']) ?></td>
                                                <td><small class="text-muted"><?= date('d/m/Y', strtotime($t['date_limite'])) ?></small></td>
                                                <td style="width: 25%;">
                                                    <div class="progress" style="height: 10px;">
                                                        <div class="progress-bar bg-success" style="width: <?= $t['progression'] ?>%;"></div>
                                                    </div>
                                                    <small class="fw-bold text-muted"><?= $t['progression'] ?>%</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary"><?= sanitize($t['statut']) ?></span>
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

            <!-- Side Cards -->
            <div class="col-lg-5">
                <!-- Evaluation Summary Card (if evaluation exists) -->
                <?php if ($evaluation): ?>
                    <div class="card-ocp border-success">
                        <div class="card-header bg-success-subtle text-success">
                            <span><i class="bi bi-star-fill me-2"></i>Votre Évaluation de Stage </span>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <div class="display-5 fw-extrabold text-success"><?= number_format($evaluation['note_globale'], 2) ?><span class="fs-6 text-muted">/10</span></div>
                                <small class="text-muted">Moyenne Générale</small>
                            </div>
                            <div class="row text-center border-top pt-3 g-2">
                                <div class="col-4">
                                    <div class="small text-muted">Technique</div>
                                    <strong class="fs-6"><?= $evaluation['note_technique'] ?>/10</strong>
                                </div>
                                <div class="col-4">
                                    <div class="small text-muted">Comportement</div>
                                    <strong class="fs-6"><?= $evaluation['note_comportement'] ?>/10</strong>
                                </div>
                                <div class="col-4">
                                    <div class="small text-muted">Assiduité</div>
                                    <strong class="fs-6"><?= $evaluation['note_assiduite'] ?>/10</strong>
                                </div>
                            </div>
                            <?php if (!empty($evaluation['commentaire'])): ?>
                                <div class="mt-3 p-3 bg-light rounded-3 italic small">
                                    "<?= sanitize($evaluation['commentaire']) ?>"
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Notifications Panel -->
                <div class="card-ocp">
                    <div class="card-header">
                        <span><i class="bi bi-bell-fill me-2 text-warning"></i>Dernières Notifications</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <?php if (empty($notifications)): ?>
                                <div class="p-4 text-center text-muted">Aucune notification répertoriée.</div>
                            <?php else: ?>
                                <?php foreach (array_slice($notifications, 0, 5) as $n): ?>
                                    <div class="list-group-item p-3 <?= $n['lu'] ? '' : 'bg-light' ?>">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <strong class="small text-dark"><?= sanitize($n['titre']) ?></strong>
                                            <small class="text-muted" style="font-size:0.75rem;"><?= date('d/m H:i', strtotime($n['created_at'])) ?></small>
                                        </div>
                                        <div class="small text-muted"><?= sanitize($n['message']) ?></div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php endif; ?>
</div>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
