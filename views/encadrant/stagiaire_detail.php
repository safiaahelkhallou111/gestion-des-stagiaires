<?php 
$pageTitle = "Fiche Détaillée Stagiaire";
$pageHeader = "Fiche du Stagiaire : " . sanitize($stage['stagiaire_prenom'] . ' ' . $stage['stagiaire_nom']);
require_once ROOT_PATH . '/views/layouts/header.php';
require_once ROOT_PATH . '/views/layouts/sidebar.php';
require_once ROOT_PATH . '/views/layouts/navbar.php';
?>

<div class="container-fluid">
    <!-- Top Action Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 bg-white p-4 rounded-4 shadow-sm border">
        <div class="d-flex align-items-center gap-3">
            <?= renderAvatar($stage['stagiaire_avatar'] ?? 'default.png', $stage['stagiaire_prenom'], $stage['stagiaire_nom'], 60) ?>
            <div>
                <h4 class="fw-bold text-dark m-0"><?= sanitize($stage['stagiaire_prenom'] . ' ' . $stage['stagiaire_nom']) ?></h4>
                <div class="text-muted small">
                    <span class="badge bg-light text-dark border me-1">CIN: <?= sanitize($stage['cin']) ?></span>
                    <span><?= sanitize($stage['type_stage']) ?> (<?= sanitize($stage['universite']) ?> — <?= sanitize($stage['filiere']) ?>)</span>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 align-items-center">
            <a href="<?= url('encadrant/messages?stage_id=' . $stage['id']) ?>" class="btn btn-outline-success">
                <i class="bi bi-chat-left-text me-1"></i> Contacter
            </a>

            <?php if ($stage['statut'] === 'en_cours'): ?>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalValidationStage">
                    <i class="bi bi-patch-check-fill me-1"></i> Valider le Stage 
                </button>
            <?php else: ?>
                <span class="badge bg-success fs-6 px-3 py-2 border align-self-center">
                    <i class="bi bi-check-circle-fill me-1"></i> Stage Terminé & Validé <?= !empty($stage['date_validation']) ? 'le ' . date('d/m/Y', strtotime($stage['date_validation'])) : '' ?>
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-pills nav-fill bg-white p-2 rounded-4 shadow-sm border mb-4" id="detailTabs">
        <li class="nav-item">
            <button class="nav-link active fw-bold" data-bs-toggle="tab" data-bs-target="#tab-profile"><i class="bi bi-person-vcard me-2"></i> Profil & Sujet</button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#tab-taches"><i class="bi bi-list-task me-2"></i> Tâches (<?= count($taches) ?>)</button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#tab-pointages"><i class="bi bi-calendar-check me-2"></i> Pointages & Assiduité</button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#tab-documents"><i class="bi bi-folder2-open me-2"></i> Documents Déposés (<?= count($documents) ?>)</button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-bold text-success" data-bs-toggle="tab" data-bs-target="#tab-evaluation"><i class="bi bi-star-fill me-2"></i> Évaluation</button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content">

        <!-- TAB 1: Profile & Sujet -->
        <div class="tab-pane fade show active" id="tab-profile">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card-ocp h-100">
                        <div class="card-header"><span class="fw-bold"><i class="bi bi-journal-text me-2 text-success"></i>Sujet de Stage</span></div>
                        <div class="card-body">
                            <h5 class="fw-bold text-dark"><?= sanitize($stage['sujet_titre']) ?></h5>
                            <p class="text-muted lead fs-6"><?= nl2br(sanitize($stage['sujet_description'])) ?></p>
                            <div class="border-top pt-3 mt-3">
                                <span class="text-muted small d-block">Période d'affectation :</span>
                                <strong><?= date('d/m/Y', strtotime($stage['date_debut'])) ?> au <?= date('d/m/Y', strtotime($stage['date_fin'])) ?></strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card-ocp h-100">
                        <div class="card-header"><span class="fw-bold"><i class="bi bi-person-lines-fill me-2 text-primary"></i>Coordonnées Stagiaire</span></div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="text-muted">Nom & Prénom :</span>
                                    <strong class="text-dark"><?= sanitize($stage['stagiaire_nom'] . ' ' . $stage['stagiaire_prenom']) ?></strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="text-muted">CIN :</span>
                                    <strong><?= sanitize($stage['cin']) ?></strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="text-muted">Email :</span>
                                    <strong><?= sanitize($stage['stagiaire_email']) ?></strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="text-muted">Téléphone :</span>
                                    <strong><?= sanitize($stage['stagiaire_telephone'] ?? 'N/A') ?></strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="text-muted">Université / Établissement :</span>
                                    <strong><?= sanitize($stage['universite']) ?></strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="text-muted">Filière :</span>
                                    <strong><?= sanitize($stage['filiere']) ?></strong>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: Tâches -->
        <div class="tab-pane fade" id="tab-taches">
            <div class="card-ocp">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="bi bi-list-task me-2 text-primary"></i>Gestion des Tâches</span>
                    <button class="btn btn-ocp btn-sm" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                        <i class="bi bi-plus-lg me-1"></i> Attribuer une Nouvelle Tâche
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-ocp align-middle">
                            <thead>
                                <tr>
                                    <th>Intitulé Tâche</th>
                                    <th>Description</th>
                                    <th>Échéance</th>
                                    <th>Progression</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($taches)): ?>
                                    <tr><td colspan="5" class="text-center text-muted py-5">Aucune tâche assignée.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($taches as $t): ?>
                                        <tr>
                                            <td class="fw-bold text-dark"><?= sanitize($t['titre']) ?></td>
                                            <td class="small text-muted" style="max-width: 300px;"><?= sanitize($t['description']) ?></td>
                                            <td class="small text-muted"><?= date('d/m/Y', strtotime($t['date_limite'])) ?></td>
                                            <td style="width: 20%;">
                                                <div class="progress" style="height: 10px;">
                                                    <div class="progress-bar bg-success" style="width: <?= $t['progression'] ?>%;"></div>
                                                </div>
                                                <small class="fw-bold text-muted"><?= $t['progression'] ?>%</small>
                                            </td>
                                            <td><span class="badge bg-secondary"><?= sanitize($t['statut']) ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 3: Pointages -->
        <div class="tab-pane fade" id="tab-pointages">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card-ocp text-center p-3">
                        <h6 class="fw-bold text-muted">Taux d'Assiduité </h6>
                        <div class="display-4 fw-extrabold text-success"><?= $assiduite['taux_presence'] ?>%</div>
                        <p class="small text-muted m-0 mt-1"><?= $assiduite['nb_presents'] ?> présence(s) enregistrée(s)</p>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card-ocp">
                        <div class="card-header"><span class="fw-bold"><i class="bi bi-calendar-range me-2 text-success"></i>Historique des Pointages de Présence</span></div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-ocp align-middle">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Statut</th>
                                            <th>Remarque</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($pointages)): ?>
                                            <tr><td colspan="3" class="text-center text-muted py-4">Aucun pointage.</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($pointages as $p): ?>
                                                <tr>
                                                    <td class="fw-bold text-dark"><?= date('d/m/Y', strtotime($p['date_pointage'])) ?></td>
                                                    <td><span class="badge badge-status <?= $p['statut'] ?>"><?= strtoupper($p['statut']) ?></span></td>
                                                    <td class="small text-muted"><?= !empty($p['remarque']) ? sanitize($p['remarque']) : '—' ?></td>
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

        <!-- TAB 4: Documents -->
        <div class="tab-pane fade" id="tab-documents">
            <div class="card-ocp">
                <div class="card-header"><span class="fw-bold"><i class="bi bi-folder-check me-2 text-primary"></i>Documents Déposés par le Stagiaire</span></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-ocp align-middle">
                            <thead>
                                <tr>
                                    <th>Intitulé Document</th>
                                    <th>Type</th>
                                    <th>Date Dépôt</th>
                                    <th>Télécharger</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($documents)): ?>
                                    <tr><td colspan="4" class="text-center text-muted py-5">Aucun document déposé.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($documents as $doc): ?>
                                        <tr>
                                            <td class="fw-bold text-dark"><i class="bi bi-file-earmark-text me-2 text-primary"></i><?= sanitize($doc['titre']) ?></td>
                                            <td><span class="badge bg-light text-dark border"><?= sanitize(str_replace('_', ' ', strtoupper($doc['type_document']))) ?></span></td>
                                            <td class="small text-muted"><?= date('d/m/Y H:i', strtotime($doc['created_at'])) ?></td>
                                            <td>
                                                <a href="<?= url('uploads/documents/' . $doc['chemin_fichier']) ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                                                    <i class="bi bi-download me-1"></i> Ouvrir / Télécharger
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

        <!-- TAB 5: Evaluation -->
        <div class="tab-pane fade" id="tab-evaluation">
            <div class="card-ocp">
                <div class="card-header bg-success-subtle text-success">
                    <span class="fw-bold"><i class="bi bi-star-fill me-2"></i>Formulaire d'Évaluation de la Progression & Performance </span>
                </div>
                <div class="card-body p-4">
                    <form action="<?= url('encadrant/save_evaluation') ?>" method="POST">
                        <input type="hidden" name="stage_id" value="<?= $stage['id'] ?>">

                        <div class="row g-4 mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold" for="note_technique">Note Compétences Techniques (1 à 10)</label>
                                <input type="number" class="form-control form-control-lg" id="note_technique" name="note_technique" min="1" max="10" value="<?= $evaluation['note_technique'] ?? 8 ?>" required>
                                <small class="text-muted">Maîtrise des outils, qualité du code / livrables.</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold" for="note_comportement">Note Comportement / Soft Skills (1 à 10)</label>
                                <input type="number" class="form-control form-control-lg" id="note_comportement" name="note_comportement" min="1" max="10" value="<?= $evaluation['note_comportement'] ?? 8 ?>" required>
                                <small class="text-muted">Intégration, autonomie et communication.</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold" for="note_assiduite">Note Assiduité & Respect Délais (1 à 10)</label>
                                <input type="number" class="form-control form-control-lg" id="note_assiduite" name="note_assiduite" min="1" max="10" value="<?= $evaluation['note_assiduite'] ?? 8 ?>" required>
                                <small class="text-muted">Ponctualité et respect des échéances.</small>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold" for="commentaire">Appréciation Globale & Remarques du Tuteur</label>
                            <textarea class="form-control" id="commentaire" name="commentaire" rows="4" placeholder="Points forts du stagiaire, axes d'amélioration, avis général pour la soutenance..."><?= sanitize($evaluation['commentaire'] ?? '') ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-ocp btn-lg">
                            <i class="bi bi-check-circle-fill me-2"></i> Enregistrer l'Évaluation du Stage
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal Modal Tâche -->
<div class="modal fade" id="addTaskModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <form action="<?= url('encadrant/creer_tache') ?>" method="POST">
                <input type="hidden" name="stage_id" value="<?= $stage['id'] ?>">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2 text-success"></i>Assigner une Nouvelle Tâche</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="tache_titre">Titre de la Tâche</label>
                        <input type="text" class="form-control" id="tache_titre" name="titre" required placeholder="Ex: Rédiger le chapitre 2 du rapport">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="tache_desc">Description / Directives</label>
                        <textarea class="form-control" id="tache_desc" name="description" rows="3" placeholder="Détails du travail attendu..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="tache_limite">Date Limite de Rendu</label>
                        <input type="date" class="form-control" id="tache_limite" name="date_limite" value="<?= date('Y-m-d', strtotime('+1 week')) ?>" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-ocp btn-sm"><i class="bi bi-check-circle me-1"></i> Créer la Tâche</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$totalTachesCount = count($taches);
$sumProg = 0;
foreach ($taches as $t) { $sumProg += $t['progression']; }
$avancementTaches = $totalTachesCount > 0 ? round($sumProg / $totalTachesCount) : 0;
$hasRapport = !empty($rapportFinal);
$hasEvaluation = !empty($evaluation);
?>
<!-- Modal Validation Finale du Stage (RG6) -->
<div class="modal fade" id="modalValidationStage" tabindex="-1" aria-labelledby="modalValidationStageLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white py-3">
                <h5 class="modal-title fw-bold" id="modalValidationStageLabel">
                    <i class="bi bi-patch-check-fill text-success me-2"></i>Validation Finale du Stage 
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= url('encadrant/terminer_stage') ?>" method="POST">
                <input type="hidden" name="stage_id" value="<?= $stage['id'] ?>">
                <div class="modal-body p-4">

                    <p class="text-muted small mb-4">
                        Vous êtes sur le point de valider le stage de <strong><?= sanitize($stage['stagiaire_prenom'] . ' ' . $stage['stagiaire_nom']) ?></strong> (CIN: <?= sanitize($stage['cin']) ?>). Veuillez vérifier le récapitulatif ci-dessous avant d'exécuter la validation manuelle.
                    </p>

                    <!-- Tableau Récapitulatif -->
                    <div class="card bg-light border-0 rounded-3 mb-4 p-3">
                        <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">
                            <i class="bi bi-clipboard-check text-success me-2"></i>Récapitulatif des Prérequis de Validation
                        </h6>
                        <div class="row g-3">
                            <!-- Rapport Final -->
                            <div class="col-md-6">
                                <div class="d-flex align-items-center justify-content-between p-3 bg-white rounded-3 border">
                                    <div>
                                        <div class="fw-bold text-dark small"><i class="bi bi-file-earmark-pdf me-1 text-danger"></i>Rapport Final PDF</div>
                                        <small class="text-muted">Dépôt du document final</small>
                                    </div>
                                    <?php if ($hasRapport): ?>
                                        <span class="badge bg-success px-2 py-1"><i class="bi bi-check-lg me-1"></i>Déposé</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark px-2 py-1"><i class="bi bi-exclamation-triangle me-1"></i>Non Déposé</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Évaluation Encadrant -->
                            <div class="col-md-6">
                                <div class="d-flex align-items-center justify-content-between p-3 bg-white rounded-3 border">
                                    <div>
                                        <div class="fw-bold text-dark small"><i class="bi bi-star me-1 text-warning"></i>Évaluation Encadrant</div>
                                        <small class="text-muted">Grille & Note globale </small>
                                    </div>
                                    <?php if ($hasEvaluation): ?>
                                        <span class="badge bg-success px-2 py-1"><i class="bi bi-check-lg me-1"></i><?= $evaluation['note_globale'] ?>/10</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary px-2 py-1"><i class="bi bi-dash-circle me-1"></i>Non Réalisée</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Taux d'Assiduité -->
                            <div class="col-md-6">
                                <div class="d-flex align-items-center justify-content-between p-3 bg-white rounded-3 border">
                                    <div>
                                        <div class="fw-bold text-dark small"><i class="bi bi-calendar-check me-1 text-primary"></i>Taux de Présence</div>
                                        <small class="text-muted"><?= $assiduite['nb_presents'] ?? 0 ?> jour(s) présent(s)</small>
                                    </div>
                                    <span class="fw-bold text-success fs-5"><?= $assiduite['taux_presence'] ?? 0 ?>%</span>
                                </div>
                            </div>

                            <!-- Avancement Tâches -->
                            <div class="col-md-6">
                                <div class="d-flex align-items-center justify-content-between p-3 bg-white rounded-3 border">
                                    <div>
                                        <div class="fw-bold text-dark small"><i class="bi bi-list-task me-1 text-info"></i>Avancement Tâches</div>
                                        <small class="text-muted"><?= $totalTachesCount ?> tâche(s) au total</small>
                                    </div>
                                    <span class="fw-bold text-primary fs-5"><?= $avancementTaches ?>%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if (!$hasRapport): ?>
                        <div class="alert alert-warning d-flex align-items-center gap-2 small mb-3" role="alert">
                            <i class="bi bi-exclamation-triangle-fill fs-5 flex-shrink-0"></i>
                            <div>
                                <strong>Avertissement :</strong> Le rapport final de stage n'a pas encore été téléversé par le stagiaire. En tant qu'encadrant, vous pouvez tout de même valider le stage manuellement si les travaux ont été présentés oralement.
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="alert alert-danger d-flex align-items-center gap-2 small m-0" role="alert">
                        <i class="bi bi-shield-lock-fill fs-5 flex-shrink-0"></i>
                        <div>
                            <strong>Règle — Action Irréversible :</strong> Une fois validé, le statut du stage passe définitivement à <strong>"Terminé"</strong>. Le stagiaire sera immédiatement notifié et son compte passera en mode <strong>lecture seule</strong>.
                        </div>
                    </div>

                </div>
                <div class="modal-footer bg-light px-4 py-3">
                    <button type="button" class="btn btn-light btn-sm border" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="bi bi-patch-check-fill me-1"></i> Confirmer & Valider le Stage 
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
