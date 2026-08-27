<?php 
$pageTitle = "Mon Profil Encadrant";
$pageHeader = "Gestion de mon profil & informations professionnelles";
require_once ROOT_PATH . '/views/layouts/header.php';
require_once ROOT_PATH . '/views/layouts/sidebar.php';
require_once ROOT_PATH . '/views/layouts/navbar.php';
?>

<div class="container-fluid">
    <div class="row g-4">
        <!-- Carte de présentation à gauche -->
        <div class="col-lg-4">
            <div class="card-ocp text-center p-4">
                <div class="position-relative d-inline-block mx-auto mb-3">
                    <?php 
                        $avatarPath = !empty($userComplet['avatar']) && $userComplet['avatar'] !== 'default.png' 
                            ? url('uploads/avatars/' . $userComplet['avatar']) 
                            : null;
                    ?>
                    <?php if ($avatarPath): ?>
                        <img src="<?= $avatarPath ?>" id="avatar-preview" class="avatar-upload-circle" alt="Avatar">
                    <?php else: ?>
                        <div id="avatar-preview-placeholder" class="avatar-upload-circle d-flex align-items-center justify-content-center bg-success text-white fw-bold display-4 mx-auto" style="background: var(--ocp-green) !important;">
                            <?= strtoupper(substr($userComplet['prenom'], 0, 1) . substr($userComplet['nom'], 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <h4 class="fw-bold text-dark mb-1"><?= sanitize($userComplet['prenom'] . ' ' . $userComplet['nom']) ?></h4>
                <p class="text-muted small mb-3"><i class="bi bi-envelope me-1"></i><?= sanitize($userComplet['email']) ?></p>

                <div class="d-flex justify-content-center gap-2 mb-3">
                    <span class="badge bg-success px-3 py-2" style="background: var(--ocp-green) !important;">Encadrant</span>
                    <span class="badge bg-light text-dark border px-3 py-2"><?= sanitize($encadrant['departement']) ?></span>
                </div>

                <div class="border-top pt-3 text-start">
                    <div class="d-flex justify-content-between align-items-center py-1 gap-2">
                        <span class="text-muted small flex-shrink-0">Département :</span>
                        <strong class="small text-dark text-end text-break"><?= sanitize($encadrant['departement']) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-1 gap-2">
                        <span class="text-muted small flex-shrink-0">Fonction :</span>
                        <strong class="small text-dark text-end text-break"><?= sanitize($encadrant['fonction']) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-1 gap-2">
                        <span class="text-muted small flex-shrink-0">Téléphone :</span>
                        <strong class="small text-dark text-end text-break"><?= sanitize($userComplet['telephone'] ?? 'Non renseigné') ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulaire de modification à droite -->
        <div class="col-lg-8">
            <div class="card-ocp">
                <div class="card-header">
                    <span><i class="bi bi-pencil-square me-2 text-success"></i>Modifier mes informations</span>
                </div>
                <div class="card-body">
                    <form action="<?= url('encadrant/update_profil') ?>" method="POST" enctype="multipart/form-data">
                        
                        <h6 class="fw-bold text-success mb-3"><i class="bi bi-person me-2"></i>Informations Personnelles</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="nom" class="form-label fw-semibold">Nom</label>
                                <input type="text" class="form-control" id="nom" name="nom" value="<?= sanitize($userComplet['nom']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="prenom" class="form-label fw-semibold">Prénom</label>
                                <input type="text" class="form-control" id="prenom" name="prenom" value="<?= sanitize($userComplet['prenom']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">Adresse Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= sanitize($userComplet['email']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="telephone" class="form-label fw-semibold">Téléphone</label>
                                <input type="text" class="form-control" id="telephone" name="telephone" value="<?= sanitize($userComplet['telephone'] ?? '') ?>" placeholder="06XXXXXXXX">
                            </div>
                            <div class="col-md-12">
                                <label for="avatar-input" class="form-label fw-semibold">Photo de profil (optionnel)</label>
                                <input type="file" class="form-control" id="avatar-input" name="avatar" accept="image/png, image/jpeg, image/webp">
                                <small class="text-muted">Formats acceptés: JPG, PNG, WEBP. Max 2Mo.</small>
                            </div>
                        </div>

                        <h6 class="fw-bold text-success mb-3"><i class="bi bi-building me-2"></i>Informations Professionnelles</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="departement" class="form-label fw-semibold">Département</label>
                                <input type="text" class="form-control" id="departement" name="departement" value="<?= sanitize($encadrant['departement']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="fonction" class="form-label fw-semibold">Fonction</label>
                                <input type="text" class="form-control" id="fonction" name="fonction" value="<?= sanitize($encadrant['fonction']) ?>" required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-ocp">
                                <i class="bi bi-check-circle me-1"></i> Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Prévisualisation de l'avatar sélectionné
document.getElementById('avatar-input')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(ev) {
        const existingImg = document.getElementById('avatar-preview');
        const placeholder = document.getElementById('avatar-preview-placeholder');
        if (existingImg) {
            existingImg.src = ev.target.result;
        } else if (placeholder) {
            const img = document.createElement('img');
            img.src = ev.target.result;
            img.id = 'avatar-preview';
            img.className = 'avatar-upload-circle';
            img.alt = 'Avatar';
            placeholder.replaceWith(img);
        }
    };
    reader.readAsDataURL(file);
});
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
