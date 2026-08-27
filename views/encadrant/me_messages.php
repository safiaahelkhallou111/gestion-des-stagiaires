<?php 
$pageTitle = "Messagerie Stagiaires";
$pageHeader = "Messagerie directe avec vos stagiaires";
require_once ROOT_PATH . '/views/layouts/header.php';
require_once ROOT_PATH . '/views/layouts/sidebar.php';
require_once ROOT_PATH . '/views/layouts/navbar.php';
?>

<div class="container-fluid">
    <div class="row g-4">
        <!-- Stagiaires List Left Pane -->
        <div class="col-md-4 col-lg-3">
            <div class="card-ocp">
                <div class="card-header bg-dark text-white">
                    <span class="fw-bold small"><i class="bi bi-chat-left-text-fill me-2 text-success"></i>Conversations (<?= count($stagiaires) ?>)</span>
                </div>
                <div class="list-group list-group-flush" style="max-height: 540px; overflow-y: auto;">
                    <?php if (empty($stagiaires)): ?>
                        <div class="p-4 text-center text-muted small">Aucun stagiaire affecté.</div>
                    <?php else: ?>
                        <?php foreach ($stagiaires as $s): ?>
                            <?php $unread = $unreadCounts[$s['stage_id']] ?? 0; ?>
                            <a href="<?= url('encadrant/messages?stage_id=' . $s['stage_id']) ?>" class="list-group-item list-group-item-action p-3 d-flex align-items-center justify-content-between <?= ($s['stage_id'] == $selectedStageId) ? 'active bg-success border-success text-white' : '' ?>">
                                <div class="d-flex align-items-center gap-2 overflow-hidden me-2">
                                    <?= renderAvatar($s['avatar'] ?? 'default.png', $s['prenom'], $s['nom'], 38) ?>
                                    <div class="overflow-hidden">
                                        <div class="fw-bold text-truncate" style="font-size:0.88rem;"><?= sanitize($s['prenom'] . ' ' . $s['nom']) ?></div>
                                        <div class="small opacity-75 text-truncate" style="font-size:0.75rem;"><?= sanitize($s['type_stage']) ?> — CIN: <?= sanitize($s['cin']) ?></div>
                                    </div>
                                </div>
                                <?php if ($unread > 0): ?>
                                    <span class="badge rounded-pill bg-danger flex-shrink-0" title="<?= $unread ?> message(s) non lu(s)"><?= $unread ?></span>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Chat Main Window Right Pane -->
        <div class="col-md-8 col-lg-9">
            <?php if (!$activeStage): ?>
                <div class="card-ocp p-5 text-center text-muted">
                    <i class="bi bi-chat-left-dots display-3 d-block mb-3 text-secondary"></i>
                    <h5 class="fw-bold text-dark">Sélectionnez un stagiaire dans la liste pour démarrer la discussion.</h5>
                    <p class="small m-0">Conformément à la règle RG9, la messagerie est strictly cloisonnée entre chaque stagiaire et son encadrant.</p>
                </div>
            <?php else: ?>
                <div class="card-ocp">
                    <div class="card-header text-white d-flex align-items-center justify-content-between py-3" style="background: linear-gradient(135deg, #3b4252 0%, #4c566a 100%) !important;">
                        <div class="d-flex align-items-center gap-3">
                            <?= renderAvatar($activeStage['stagiaire_avatar'] ?? 'default.png', $activeStage['stagiaire_prenom'], $activeStage['stagiaire_nom'], 44) ?>
                            <div>
                                <h6 class="fw-bold text-white m-0">Stagiaire : <?= sanitize($activeStage['stagiaire_prenom'] . ' ' . $activeStage['stagiaire_nom']) ?></h6>
                                <small class="text-secondary">Sujet: <?= sanitize($activeStage['sujet_titre']) ?></small>
                            </div>
                        </div>
                        <a href="<?= url('encadrant/stagiaire_detail?id=' . $activeStage['id']) ?>" class="btn btn-outline-light btn-sm"><i class="bi bi-person-vcard me-1"></i> Fiche Détaillée</a>
                    </div>

                    <div class="card-body p-3">
                        <!-- Chat Box -->
                        <div id="chat-container" class="chat-container mb-3">
                            <!-- Loaded via AJAX chat.js -->
                        </div>

                        <!-- Chat Form -->
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
    </div>
</div>

<script src="<?= url('js/chat.js') ?>"></script>
<?php if ($activeStage): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        initChat(<?= $activeStage['id'] ?>, <?= $user['id'] ?>);
    });
</script>
<?php endif; ?>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
