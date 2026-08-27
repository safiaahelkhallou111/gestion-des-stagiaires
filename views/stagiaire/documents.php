<?php 
$pageTitle = "Mes Documents";
$pageHeader = "Espace d'upload & gestion des documents de stage ";
require_once ROOT_PATH . '/views/layouts/header.php';
require_once ROOT_PATH . '/views/layouts/sidebar.php';
require_once ROOT_PATH . '/views/layouts/navbar.php';
?>

<div class="container-fluid">
    <div class="row g-4">
        <!-- Upload Form Card -->
        <div class="col-lg-5">
            <div class="card-ocp">
                <div class="card-header">
                    <span class="fw-bold"><i class="bi bi-cloud-upload-fill me-2 text-success"></i>Déposer un Document </span>
                </div>
                <div class="card-body">
                    <?php if ($stageActif && $stageActif['statut'] === 'termine'): ?>
                        <div class="alert alert-secondary p-3 rounded-3 text-center mb-0">
                            <i class="bi bi-lock-fill fs-3 text-secondary d-block mb-1"></i>
                            <h6 class="fw-bold text-dark mb-1">Stage Terminé & Validé</h6>
                            <span class="small text-muted">Le dépôt de documents est désactivé (mode lecture seule).</span>
                        </div>
                    <?php else: ?>
                        <form action="<?= url('stagiaire/upload_document') ?>" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="titre">Titre du document</label>
                            <input type="text" class="form-control" id="titre" name="titre" required placeholder="Ex: Convention de stage signée">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="type_document">Type de document</label>
                            <select class="form-select" id="type_document" name="type_document" required>
                                <option value="rapport_intermediaire">📘 Rapport Intermédiaire / Avancement</option>
                                <option value="convention">📜 Convention de Stage</option>
                                <option value="cin">🪪 Carte d'Identité Nationale (CIN)</option>
                                <option value="cv">📄 Curriculum Vitae (CV)</option>
                                <option value="autre">📎 Autre document justificatif</option>
                            </select>
                        </div>

                        <div class="upload-zone mb-4" id="doc-upload-zone">
                            <i class="bi bi-file-earmark-arrow-up"></i>
                            <h6 class="fw-bold text-dark mb-1">Glissez-déposez votre fichier ici</h6>
                            <p class="text-muted small mb-2">ou cliquez pour choisir un fichier</p>
                            <span class="badge bg-secondary upload-filename">Aucun fichier sélectionné</span>
                            <input type="file" name="fichier" id="doc-file-input" class="d-none" accept=".pdf,.doc,.docx,.png,.jpg,.jpeg" required>
                        </div>

                        <button type="submit" class="btn btn-ocp w-100 py-2.5">
                            <i class="bi bi-upload me-2"></i> Téléverser le Fichier
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Documents Table -->
        <div class="col-lg-7">
            <div class="card-ocp">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="bi bi-folder-check me-2 text-primary"></i>Vos Documents Déposés</span>
                    <span class="badge bg-success px-3 py-2 fw-bold" style="background: var(--ocp-green) !important;"><?= count($documents) ?> Fichier(s)</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-ocp align-middle">
                            <thead>
                                <tr>
                                    <th>Intitulé & Taille</th>
                                    <th>Type</th>
                                    <th>Date Dépôt</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($documents)): ?>
                                    <tr><td colspan="4" class="text-center text-muted py-5">Aucun document déposé pour le moment.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($documents as $doc): ?>
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-dark">
                                                    <?php if (str_contains($doc['chemin_fichier'], '.pdf')): ?>
                                                        <i class="bi bi-file-earmark-pdf-fill text-danger me-2"></i>
                                                    <?php else: ?>
                                                        <i class="bi bi-file-earmark-text-fill text-primary me-2"></i>
                                                    <?php endif; ?>
                                                    <?= sanitize($doc['titre']) ?>
                                                </div>
                                                <small class="text-muted"><?= Document::formatTaille($doc['taille_octets']) ?></small>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border"><?= sanitize(str_replace('_', ' ', strtoupper($doc['type_document']))) ?></span>
                                            </td>
                                            <td class="small text-muted"><?= date('d/m/Y à H:i', strtotime($doc['created_at'])) ?></td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <a href="<?= url('uploads/documents/' . $doc['chemin_fichier']) ?>" target="_blank" class="btn btn-light btn-sm text-primary" title="Télécharger">
                                                        <i class="bi bi-download"></i>
                                                    </a>
                                                    <form action="<?= url('stagiaire/delete_document') ?>" method="POST" onsubmit="return confirm('Confirmer la suppression de ce document ?');">
                                                        <input type="hidden" name="document_id" value="<?= $doc['id'] ?>">
                                                        <button type="submit" class="btn btn-light btn-sm text-danger" title="Supprimer">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
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
    </div>
</div>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
