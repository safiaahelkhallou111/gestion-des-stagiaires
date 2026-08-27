<?php 
$pageTitle = "Suivi des Tâches";
$pageHeader = "Tâches & Avancement de la progression";
require_once ROOT_PATH . '/views/layouts/header.php';
require_once ROOT_PATH . '/views/layouts/sidebar.php';
require_once ROOT_PATH . '/views/layouts/navbar.php';
?>

<div class="container-fluid">
    <div class="card-ocp">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="fs-5 fw-bold text-dark"><i class="bi bi-list-check me-2 text-success"></i>Missions & Tâches Assignées</span>
            <span class="badge bg-success px-3 py-2 fw-bold" style="background: var(--ocp-green) !important;"><?= count($taches) ?> Tâche(s) au total</span>
        </div>
        <div class="card-body">
            <?php if (empty($taches)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-card-checklist display-3 d-block mb-3 text-secondary"></i>
                    <h5 class="fw-bold text-dark">Aucune tâche attribuée pour le moment.</h5>
                    <p class="small">Votre encadrant OCP vous notifiera dès qu'une nouvelle tâche vous sera affectée.</p>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($taches as $t): ?>
                        <div class="col-md-6">
                            <div class="card-ocp p-4 h-100 mb-0 d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-2 gap-2">
                                    <h5 class="fw-bold text-dark m-0"><?= sanitize($t['titre']) ?></h5>
                                    <span class="badge badge-task <?= $t['statut'] ?>"><?= strtoupper(str_replace('_', ' ', $t['statut'])) ?></span>
                                </div>
                                
                                <p class="text-muted small mb-3 flex-fill"><?= nl2br(sanitize($t['description'])) ?></p>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span class="fw-semibold text-muted">Progression actuelle :</span>
                                        <strong class="text-success"><?= $t['progression'] ?>%</strong>
                                    </div>
                                    <div class="progress" style="height: 10px;">
                                        <div class="progress-bar bg-success" style="width: <?= $t['progression'] ?>%;"></div>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center justify-content-between border-top pt-3 mt-auto">
                                    <small class="text-muted"><i class="bi bi-calendar-event me-1"></i> Échéance : <strong><?= date('d/m/Y', strtotime($t['date_limite'])) ?></strong></small>
                                    
                                    <!-- Modal Trigger Button -->
                                    <button class="btn btn-outline-success btn-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#updateModal<?= $t['id'] ?>">
                                        <i class="bi bi-sliders me-1"></i> Mettre à jour
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Update Progress Modal -->
                        <div class="modal fade" id="updateModal<?= $t['id'] ?>" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0">
                                    <form action="<?= url('stagiaire/update_tache') ?>" method="POST">
                                        <input type="hidden" name="tache_id" value="<?= $t['id'] ?>">
                                        <div class="modal-header bg-dark text-white">
                                            <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2 text-success"></i>Mise à jour de l'avancement</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <h6 class="fw-bold text-dark mb-3"><?= sanitize($t['titre']) ?></h6>
                                            
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold d-flex justify-content-between" for="progression_range_<?= $t['id'] ?>">
                                                    <span>Pourcentage réalisé :</span>
                                                    <span id="val_<?= $t['id'] ?>" class="text-success fw-bold fs-5"><?= $t['progression'] ?>%</span>
                                                </label>
                                                <input type="range" class="form-range" min="0" max="100" step="5" name="progression" id="progression_range_<?= $t['id'] ?>" value="<?= $t['progression'] ?>" oninput="document.getElementById('val_<?= $t['id'] ?>').innerText = this.value + '%'">
                                            </div>
                                            <small class="text-muted">Glissez le curseur pour mettre à jour votre avancement (0% à 100%).</small>
                                        </div>
                                        <div class="modal-footer bg-light">
                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                                            <button type="submit" class="btn btn-ocp btn-sm"><i class="bi bi-check-circle me-1"></i> Enregistrer</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
