<?php 
$pageTitle = "Tableau de Bord des Stagiaires";
$pageHeader = "Tableau de Bord des Stagiaires";
require_once ROOT_PATH . '/views/layouts/header.php';
require_once ROOT_PATH . '/views/layouts/sidebar.php';
require_once ROOT_PATH . '/views/layouts/navbar.php';
?>

<div class="container-fluid">

    <!-- Filter & Search Bar -->
    <div class="bg-white p-3.5 rounded-4 shadow-sm border mb-4">
        <form action="<?= url('encadrant/stagiaires') ?>" method="GET" class="row g-3 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control border-start-0 ps-0" name="search" placeholder="Recherche par Nom, Prénom, CIN ou Sujet" value="<?= sanitize($_GET['search'] ?? '') ?>">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select" name="type_stage">
                    <option value="">Tous les types de stage</option>
                    <option value="PFE" <?= ($_GET['type_stage'] ?? '') === 'PFE' ? 'selected' : '' ?>>PFE</option>
                    <option value="PFA" <?= ($_GET['type_stage'] ?? '') === 'PFA' ? 'selected' : '' ?>>PFA</option>
                    <option value="Observation" <?= ($_GET['type_stage'] ?? '') === 'Observation' ? 'selected' : '' ?>>Observation</option>
                    <option value="Initiation" <?= ($_GET['type_stage'] ?? '') === 'Initiation' ? 'selected' : '' ?>>Initiation</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" name="statut">
                    <option value="">Tous les statuts</option>
                    <option value="en_cours" <?= ($_GET['statut'] ?? '') === 'en_cours' ? 'selected' : '' ?>>En Cours</option>
                    <option value="termine" <?= ($_GET['statut'] ?? '') === 'termine' ? 'selected' : '' ?>>Terminé</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-ocp flex-fill fw-semibold"><i class="bi bi-funnel-fill me-1"></i> Filtrer</button>
                <a href="<?= url('encadrant/stagiaires') ?>" class="btn btn-light border" title="Réinitialiser"><i class="bi bi-power"></i></a>
            </div>
        </form>
    </div>

    <!-- Top Action Row -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-dark m-0"><i class="bi bi-people-fill me-2 text-success"></i>Stagiaires Affectés (<?= count($stagiaires) ?>)</h5>
        <button class="btn btn-ocp btn-sm rounded-3 shadow-sm px-3" data-bs-toggle="modal" data-bs-target="#assignModal">
            <i class="bi bi-plus-lg me-1"></i> Affecter un Stagiaire
        </button>
    </div>

    <!-- Stagiaires Grid Cards (2 columns layout) -->
    <div class="row g-4 mb-4">
        <?php if (empty($stagiaires)): ?>
            <div class="col-12 text-center py-5">
                <div class="card-ocp p-5 text-center">
                    <i class="bi bi-person-x fs-1 text-muted mb-2"></i>
                    <h5 class="fw-bold text-dark">Aucun stagiaire trouvé</h5>
                    <p class="text-muted small mb-0">Aucun stagiaire ne correspond aux critères de recherche actuels.</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($stagiaires as $s): ?>
                <div class="col-md-6">
                    <div class="card-ocp p-4 h-100 shadow-sm border rounded-4 hover-lift position-relative transition-all bg-white">
                        
                        <!-- Header: Avatar + Info + Chat Button + Badge -->
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <!-- Avatar with Green Circle Ring & Status Dot -->
                                <div class="position-relative flex-shrink-0" style="width: 74px; height: 74px;">
                                    <?php if (!empty($s['avatar']) && file_exists(ROOT_PATH . '/public/uploads/avatars/' . $s['avatar'])): ?>
                                        <img src="<?= url('uploads/avatars/' . $s['avatar']) ?>" alt="<?= sanitize($s['prenom']) ?>" class="rounded-circle object-fit-cover w-100 h-100 border border-3" style="border-color: #6ab04c !important;">
                                    <?php else: ?>
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold fs-4 w-100 h-100 border border-3" style="background: linear-gradient(135deg, #6ab04c 0%, #5a9e3e 100%); border-color: #6ab04c !important;">
                                            <?= strtoupper(substr($s['prenom'], 0, 1) . substr($s['nom'], 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                    <span class="position-absolute bottom-0 end-0 border border-2 border-white rounded-circle" style="width:16px; height:16px; background-color: #6ab04c;"></span>
                                </div>

                                <!-- Name & Contact Icons -->
                                <div>
                                    <a href="<?= url('encadrant/stagiaire_detail?id=' . $s['stage_id']) ?>" class="text-decoration-none text-dark">
                                        <h5 class="fw-bold mb-0" style="font-size: 1.15rem; color: #1e293b;"><?= sanitize($s['prenom'] . ' ' . $s['nom']) ?></h5>
                                    </a>
                                    <div class="text-muted small mb-2" style="font-size: 0.82rem;">CIN: <strong><?= sanitize($s['cin']) ?></strong></div>
                                    
                                    <!-- Action Buttons with Direct Platform Chat Link -->
                                    <div class="d-flex align-items-center">
                                        <a href="<?= url('encadrant/messages?stage_id=' . $s['stage_id']) ?>" class="btn btn-light btn-sm rounded-3 px-2.5 py-1 border shadow-sm fw-semibold" style="color: #6ab04c;" title="Ouvrir la Messagerie de la Plateforme">
                                            <i class="bi bi-chat-left-text-fill me-1"></i> Chat
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Statut Badge -->
                            <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background-color: <?= $s['stage_statut'] === 'en_cours' ? '#e0f2fe' : '#dcfce7' ?>; color: <?= $s['stage_statut'] === 'en_cours' ? '#0369a1' : '#15803d' ?>; font-size: 0.75rem; letter-spacing: 0.5px;">
                                <?= $s['stage_statut'] === 'en_cours' ? 'EN COURS' : 'TERMINÉ' ?>
                            </span>
                        </div>

                        <hr class="my-3 opacity-10">

                        <!-- Meta Info Row: Établissement + Sujet -->
                        <div class="row text-start mb-3">
                            <div class="col-6">
                                <div class="text-muted small fw-semibold" style="font-size: 0.8rem;">Établissement</div>
                                <div class="fw-bold text-dark text-truncate mt-0.5" style="font-size: 0.92rem;"><?= sanitize($s['universite'] ?? 'ENSIAS Rabat') ?></div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted small fw-semibold" style="font-size: 0.8rem;">Sujet Assigné</div>
                                <div class="fw-bold text-dark text-truncate mt-0.5" style="font-size: 0.92rem;" title="<?= sanitize($s['sujet_titre']) ?>"><?= sanitize($s['sujet_titre']) ?></div>
                            </div>
                        </div>

                        <!-- Sub-Card Container: Période Progress & Circular Ring Gauge -->
                        <div class="bg-light p-3 rounded-4 border d-flex align-items-center justify-content-between">
                            <div class="flex-fill me-3">
                                <div class="d-flex justify-content-between text-muted small fw-semibold mb-1">
                                    <span style="font-size: 0.8rem;">Période</span>
                                    <span style="font-size: 0.8rem;">Progression</span>
                                </div>
                                <div class="progress rounded-pill" style="height: 10px; background-color: #e2e8f0;">
                                    <div class="progress-bar rounded-pill" style="width: <?= max(10, min(100, round($s['progression_moyenne']))) ?>%; background-color: #6ab04c;"></div>
                                </div>
                                <div class="d-flex justify-content-between text-muted mt-1.5" style="font-size: 0.75rem;">
                                    <span>du <?= date('d/m/Y', strtotime($s['date_debut'])) ?></span>
                                    <span>au <?= date('d/m/Y', strtotime($s['date_fin'])) ?></span>
                                </div>
                            </div>

                            <!-- Circular Gauge Ring -->
                            <div class="text-center position-relative flex-shrink-0" style="width: 62px; height: 62px;">
                                <svg width="62" height="62" viewBox="0 0 36 36" style="transform: rotate(-90deg);">
                                    <path stroke="#e2e8f0" stroke-width="3.5" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                    <path stroke="#6ab04c" stroke-width="3.8" stroke-dasharray="<?= round($s['progression_moyenne']) ?>, 100" stroke-linecap="round" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                </svg>
                                <div class="position-absolute top-50 start-50 translate-middle fw-bold text-dark" style="font-size: 0.85rem; margin-top: 1px;">
                                    <?= round($s['progression_moyenne']) ?>%
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Footer Copyright Note matching screenshot -->
    <div class="text-center text-muted small py-3" style="font-size: 0.78rem;">
        © <?= date('Y') ?> OCP Group — Entité Digitalisation . Tous droits réservés. Plateforme de Gestion des Stagiaires 
    </div>

</div>

<!-- Modal Assigner Stagiaire -->
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="<?= url('encadrant/assigner_stage') ?>" method="POST">
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #3b4252 0%, #4c566a 100%) !important;">
                    <h5 class="modal-title fw-bold"><i class="bi bi-person-plus me-2 text-success"></i>Affecter un Nouveau Stagiaire </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="cin">Numéro CIN du Stagiaire Inscrit</label>
                        <input type="text" class="form-control" id="cin" name="cin" required placeholder="Ex: BJ543210">
                        <small class="text-muted">Le stagiaire doit avoir au préalable créé son compte avec ce CIN.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="sujet_id">Sujet de Stage à Attribuer</label>
                        <select class="form-select" id="sujet_id" name="sujet_id" required>
                            <?php foreach ($sujets as $sujet): ?>
                                <option value="<?= $sujet['id'] ?>"><?= sanitize($sujet['titre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold" for="date_debut">Date de Début</label>
                            <input type="date" class="form-control" id="date_debut" name="date_debut" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold" for="date_fin">Date de Fin Prévue</label>
                            <input type="date" class="form-control" id="date_fin" name="date_fin" value="<?= date('Y-m-d', strtotime('+3 months')) ?>" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-ocp btn-sm"><i class="bi bi-check-circle me-1"></i> Créer le Stage Active</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
