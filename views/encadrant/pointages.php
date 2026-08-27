<?php 
$pageTitle = "Suivi des Pointages Stagiaires";
$pageHeader = "Contrôle & Saisie des Pointages";
require_once ROOT_PATH . '/views/layouts/header.php';
require_once ROOT_PATH . '/views/layouts/sidebar.php';
require_once ROOT_PATH . '/views/layouts/navbar.php';
?>

<div class="container-fluid">
    <div class="row g-4">
        <!-- Stagiaire Selector & Pointage Form -->
        <div class="col-lg-4">
            <div class="card-ocp">
                <div class="card-header bg-dark text-white">
                    <span class="fw-bold"><i class="bi bi-clock-history me-2 text-success"></i>Saisie / Validation Pointage </span>
                </div>
                <div class="card-body">
                    <form action="<?= url('encadrant/valider_pointage') ?>" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="stage_id">Sélectionner le Stagiaire</label>
                            <select class="form-select" id="stage_id" name="stage_id" required onchange="window.location.href='<?= url('encadrant/pointages?stage_id=') ?>' + this.value">
                                <?php foreach ($stagiaires as $s): ?>
                                    <option value="<?= $s['stage_id'] ?>" <?= ($s['stage_id'] == $selectedStageId) ? 'selected' : '' ?>>
                                        <?= sanitize($s['prenom'] . ' ' . $s['nom']) ?> (CIN: <?= sanitize($s['cin']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="date_pointage">Date du Pointage</label>
                            <input type="date" class="form-control" id="date_pointage" name="date_pointage" value="<?= date('Y-m-d') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="statut">Statut de Présence</label>
                            <select class="form-select" id="statut" name="statut" required>
                                <option value="present">🟢 Présent</option>
                                <option value="retard">🟠 Retard</option>
                                <option value="absent">🔴 Absent</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold" for="remarque">Remarque ou Motif</label>
                            <textarea class="form-control" id="remarque" name="remarque" rows="2" placeholder="Motif de l'absence / justification..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-ocp w-100 py-2.5">
                            <i class="bi bi-check2-circle me-1"></i> Valider / Enregistrer le Pointage
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- History Table for Selected Stagiaire -->
        <div class="col-lg-8">
            <div class="card-ocp">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <?php if ($stageCurrent): ?>
                            <?= renderAvatar($stageCurrent['stagiaire_avatar'] ?? 'default.png', $stageCurrent['stagiaire_prenom'], $stageCurrent['stagiaire_nom'], 34) ?>
                            <span class="fw-bold text-dark fs-6"><?= sanitize($stageCurrent['stagiaire_prenom'] . ' ' . $stageCurrent['stagiaire_nom']) ?></span>
                        <?php else: ?>
                            <span class="fw-bold text-dark fs-6">Sélectionnez un stagiaire</span>
                        <?php endif; ?>
                    </div>
                    <span class="badge bg-light text-muted border"><i class="bi bi-calendar-check me-1 text-primary"></i>Registre de Présence </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-ocp align-middle">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Statut</th>
                                    <th>Remarque</th>
                                    <th>Enregistré le</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($pointages)): ?>
                                    <tr><td colspan="4" class="text-center text-muted py-5">Aucun pointage trouvé pour ce stagiaire.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($pointages as $p): ?>
                                        <tr>
                                            <td class="fw-bold text-dark"><?= date('d/m/Y', strtotime($p['date_pointage'])) ?></td>
                                            <td><span class="badge badge-status <?= $p['statut'] ?>"><?= strtoupper($p['statut']) ?></span></td>
                                            <td class="small text-muted"><?= !empty($p['remarque']) ? sanitize($p['remarque']) : '—' ?></td>
                                            <td class="small text-muted"><?= date('d/m/Y H:i', strtotime($p['created_at'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
