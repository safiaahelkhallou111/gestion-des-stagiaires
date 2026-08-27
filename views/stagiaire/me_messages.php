<?php 
$pageTitle = "Messagerie avec l'Encadrant";
$pageHeader = "Canal de communication direct avec votre encadrant OCP ";
require_once ROOT_PATH . '/views/layouts/header.php';
require_once ROOT_PATH . '/views/layouts/sidebar.php';
require_once ROOT_PATH . '/views/layouts/navbar.php';
?>

<div class="container-fluid">
    <?php if (!$stageActif): ?>
        <div class="alert alert-warning p-4 rounded-4 shadow-sm border-0">
            <h5 class="fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Stage non actif</h5>
            <p class="m-0">Règle : Vous devez disposer d'un stage actif assigné par un encadrant pour accéder au canal de messagerie instantanée.</p>
        </div>
    <?php else: ?>
        <div class="card-ocp">
            <!-- Header bar of conversation -->
            <div class="card-header text-white d-flex align-items-center justify-content-between py-3" style="background: linear-gradient(135deg, #3b4252 0%, #4c566a 100%) !important;">
                <div class="d-flex align-items-center gap-3">
                    <?= renderAvatar($stageActif['encadrant_avatar'] ?? 'default.png', $stageActif['encadrant_prenom'], $stageActif['encadrant_nom'], 44) ?>
                    <div>
                        <h6 class="fw-bold text-white m-0">Encadrant : M. <?= sanitize($stageActif['encadrant_prenom'] . ' ' . $stageActif['encadrant_nom']) ?></h6>
                        <small class="text-secondary"><?= sanitize($stageActif['encadrant_fonction']) ?> — <?= sanitize($stageActif['encadrant_departement']) ?></small>
                    </div>
                </div>
                <div>
                    <span class="badge bg-success px-3 py-2" style="background: var(--ocp-green) !important;"><i class="bi bi-shield-lock me-1"></i> Canal Sécurisé</span>
                </div>
            </div>

            <div class="card-body p-3">
                <!-- Chat Container -->
                <div id="chat-container" class="chat-container mb-3">
                    <!-- Messages dynamically rendered via chat.js -->
                </div>

                <!-- Chat Input Form -->
                <form id="chat-form" class="d-flex gap-2">
                    <input type="text" id="message-input" class="form-control form-control-lg fs-6" placeholder="Écrire ici..." required autocomplete="off">
                    <button type="submit" class="btn btn-ocp rounded-circle p-0 d-flex align-items-center justify-content-center flex-shrink-0" style="width:48px; height:48px;" title="Envoyer">
                        <i class="bi bi-send-fill fs-5"></i>
                    </button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="<?= url('js/chat.js') ?>"></script>
<?php if ($stageActif): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        initChat(<?= $stageActif['id'] ?>, <?= $user['id'] ?>);
    });
</script>
<?php endif; ?>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
