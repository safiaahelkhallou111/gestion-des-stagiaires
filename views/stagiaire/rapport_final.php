<?php 
$pageTitle = "Dépôt du Rapport Final";
$pageHeader = "Espace dédié au dépôt du rapport de stage final (PDF)";
require_once ROOT_PATH . '/views/layouts/header.php';
require_once ROOT_PATH . '/views/layouts/sidebar.php';
require_once ROOT_PATH . '/views/layouts/navbar.php';
?>

<div class="container-fluid">
    <div class="row g-4">
        <!-- État du dépôt à gauche -->
        <div class="col-lg-5">
            <div class="card-ocp h-100">
                <div class="card-header">
                    <span><i class="bi bi-file-earmark-check me-2 text-success"></i>Statut de Soumission</span>
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                    <?php if ($rapportFinal): ?>
                        <div class="text-center py-4">
                            <div class="display-1 text-success mb-3"><i class="bi bi-file-earmark-pdf-fill"></i></div>
                            <span class="badge bg-success-subtle text-success border border-success px-3 py-2 fw-bold fs-6 mb-2">
                                <i class="bi bi-check-circle-fill me-1"></i> Rapport Déposé
                            </span>
                            <h5 class="fw-bold text-dark mt-2 mb-1"><?= sanitize($rapportFinal['titre']) ?></h5>
                            <p class="text-muted small mb-3">Déposé le <?= date('d/m/Y à H:i', strtotime($rapportFinal['created_at'])) ?> (<?= Document::formatTaille($rapportFinal['taille_octets']) ?>)</p>
                            
                            <div class="d-flex justify-content-center gap-2">
                                <a href="<?= url('uploads/documents/' . $rapportFinal['chemin_fichier']) ?>" target="_blank" class="btn btn-ocp">
                                    <i class="bi bi-download me-1"></i> Consulter mon Rapport
                                </a>
                            </div>
                        </div>

                        <div class="bg-light p-3 rounded-3 border mt-3">
                            <small class="text-muted d-block fw-semibold mb-1"><i class="bi bi-info-circle me-1"></i>Note importante :</small>
                            <small class="text-muted">Vous pouvez remplacer votre rapport final en déposant un nouveau fichier ci-contre. Seule la dernière version sera transmise à votre encadrant.</small>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <div class="display-1 text-warning mb-3"><i class="bi bi-cloud-upload"></i></div>
                            <span class="badge bg-warning-subtle text-warning border border-warning px-3 py-2 fw-bold fs-6 mb-2">
                                <i class="bi bi-clock-history me-1"></i> En attente de dépôt
                            </span>
                            <h5 class="fw-bold text-dark mt-2 mb-1">Aucun rapport déposé</h5>
                            <p class="text-muted small">Vous n'avez pas encore soumis la version finale de votre rapport de stage.</p>
                        </div>

                        <div class="bg-light p-3 rounded-3 border mt-auto">
                            <small class="text-muted d-block fw-semibold mb-1"><i class="bi bi-shield-exclamation me-1"></i>Exigence OCP :</small>
                            <small class="text-muted">Le rapport final est obligatoire pour la validation de votre stage et la programmation de la soutenance.</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Formulaire de dépôt Drag & Drop à droite -->
        <div class="col-lg-7">
            <div class="card-ocp">
                <div class="card-header">
                    <span><i class="bi bi-upload me-2 text-primary"></i>Zone de Dépôt du Rapport (PDF uniquement)</span>
                </div>
                <div class="card-body">
                    <?php if (!$stageActif): ?>
                        <div class="alert alert-warning p-4 rounded-3 border-0">
                            <h6 class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Stage non actif</h6>
                            <p class="m-0 small">Vous devez disposer d'un stage actif pour soumettre votre rapport final.</p>
                        </div>
                    <?php elseif ($stageActif['statut'] === 'termine'): ?>
                        <div class="alert alert-success p-4 rounded-3 border-0 text-center">
                            <i class="bi bi-patch-check-fill display-4 text-success d-block mb-2"></i>
                            <h6 class="fw-bold text-dark mb-1">Stage Validé & Clôturé </h6>
                            <p class="m-0 small text-muted">Votre rapport final a été traité et validé par votre encadrant. Les dépôts sont désormais clôturés.</p>
                        </div>
                    <?php else: ?>
                        <form action="<?= url('stagiaire/upload_rapport') ?>" method="POST" enctype="multipart/form-data">
                            
                            <div class="upload-zone mb-4" id="rapport-upload-zone">
                                <i class="bi bi-file-earmark-pdf"></i>
                                <h6 class="fw-bold text-dark mb-1">Glissez-déposez votre fichier PDF ici</h6>
                                <p class="text-muted small mb-2">ou cliquez pour parcourir vos fichiers</p>
                                <span class="badge bg-secondary upload-filename">Aucun fichier sélectionné</span>
                                <input type="file" name="rapport" id="rapport-file-input" class="d-none" accept=".pdf" required>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <div class="p-3 bg-light rounded-3 border">
                                        <small class="text-muted d-block"><i class="bi bi-file-type-pdf text-danger me-1"></i> Format autorisé</small>
                                        <strong class="text-dark small">Document PDF (.pdf)</strong>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 bg-light rounded-3 border">
                                        <small class="text-muted d-block"><i class="bi bi-hdd me-1"></i> Taille maximale</small>
                                        <strong class="text-dark small">20 Mo</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-ocp btn-lg">
                                    <i class="bi bi-send-check me-2"></i> Soumettre le Rapport Final
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
