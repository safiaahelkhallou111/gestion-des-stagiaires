<?php 
$pageTitle = "Catalogue des Sujets de Stage";
$pageHeader = "Gestion du Catalogue des Sujets de Stage";
require_once ROOT_PATH . '/views/layouts/header.php';
require_once ROOT_PATH . '/views/layouts/sidebar.php';
require_once ROOT_PATH . '/views/layouts/navbar.php';
?>

<div class="container-fluid">
    <div class="card-ocp mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="fs-5 fw-bold text-dark"><i class="bi bi-card-checklist me-2 text-success"></i>Sujets de Stage Proposés</span>
            <button class="btn btn-ocp btn-sm" data-bs-toggle="modal" data-bs-target="#createSujetModal">
                <i class="bi bi-plus-lg me-1"></i> Créer un Nouveau Sujet
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-ocp align-middle">
                    <thead>
                        <tr>
                            <th>Titre du Sujet</th>
                            <th>Domaine</th>
                            <th>Description</th>
                            <th>Réutilisation</th>
                            <th>Date de Création</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($sujets)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-5">Aucun sujet créé. Cliquez sur le bouton pour ajouter un sujet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($sujets as $sujet): ?>
                                <tr>
                                    <td class="fw-bold text-dark" style="max-width: 250px;"><?= sanitize($sujet['titre']) ?></td>
                                    <td><span class="badge bg-success-subtle text-success border border-success"><?= sanitize($sujet['domaine']) ?></span></td>
                                    <td class="small text-muted" style="max-width: 380px;"><?= nl2br(sanitize($sujet['description'])) ?></td>
                                    <td>
                                        <span class="badge bg-light text-dark border">Assigné <?= $sujet['nb_utilisations'] ?> fois</span>
                                    </td>
                                            <td class="small text-muted">
                                                <?= date('d/m/Y', strtotime($sujet['created_at'])) ?>
                                                <div class="mt-2">
                                                    <form method="POST" action="<?= url('encadrant/delete_sujet') ?>" onsubmit="return confirm('Confirmer la suppression de ce sujet ? Cette action est irréversible.');" style="display:inline-block;">
                                                        <input type="hidden" name="sujet_id" value="<?= (int)$sujet['id'] ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
                                                    </form>
                                                </div>
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

<!-- Modal Création Sujet -->
<div class="modal fade" id="createSujetModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0">
            <form action="<?= url('encadrant/create_sujet') ?>" method="POST">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-file-earmark-plus me-2 text-success"></i>Nouveau Sujet de Stage </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="titre">Intitulé du Sujet</label>
                        <input type="text" class="form-control" id="titre" name="titre" required placeholder="Ex: Déploiement d'une infrastructure Microservices pour les capteurs IoT OCP">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="domaine">Domaine / Pôle d'activité</label>
                        <input type="text" class="form-control" id="domaine" name="domaine" value="Informatique & Digitalisation" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="description">Description Complète du Sujet & Objectifs</label>
                        <textarea class="form-control" id="description" name="description" rows="5" required placeholder="Décrivez les technologies à utiliser, les livrables attendus et les objectifs du stage..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-ocp btn-sm"><i class="bi bi-check-circle me-1"></i> Publier le Sujet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
