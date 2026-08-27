<?php
$user = currentUser();
require_once ROOT_PATH . '/models/Notification.php';
require_once ROOT_PATH . '/models/Message.php';

$notifModel = new Notification();
$msgModel = new Message();

$unreadNotifs = $notifModel->getNonLuesCount($user['id']);
$unreadMsgs = $msgModel->getNonLusCount($user['id']);

$notifRoute = $user['role'] === 'encadrant' ? 'encadrant/notifications' : 'stagiaire/notifications';
$msgRoute   = $user['role'] === 'encadrant' ? 'encadrant/messages' : 'stagiaire/messages';
?>

<nav class="navbar-top">
    <div class="d-flex align-items-center gap-3">
        <!-- Mobile sidebar toggle -->
        <button id="sidebar-toggle" class="btn btn-sm d-lg-none" style="background:transparent; border:none; color:var(--text-muted); font-size:1.3rem;">
            <i class="bi bi-list"></i>
        </button>
        <h5 class="page-title">
            <?= isset($pageHeader) ? sanitize($pageHeader) : 'Plateforme OCP' ?>
        </h5>
    </div>

    <div class="d-flex align-items-center gap-2">

        <!-- Dark/Light Mode Toggle -->
        <button id="theme-toggle" title="Basculer le thème">
            <i class="bi bi-moon-fill"></i>
        </button>

        <!-- Messages Badge -->
        <a href="<?= url($msgRoute) ?>" class="btn btn-light rounded-circle position-relative d-flex align-items-center justify-content-center"
           style="width:42px; height:42px;" title="Messagerie">
            <i class="bi bi-chat-text fs-5"></i>
            <?php if ($unreadMsgs > 0): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.65rem;">
                    <?= $unreadMsgs > 99 ? '99+' : $unreadMsgs ?>
                </span>
            <?php endif; ?>
        </a>

        <!-- Notifications Badge -->
        <a href="<?= url($notifRoute) ?>" class="btn btn-light rounded-circle position-relative d-flex align-items-center justify-content-center"
           style="width:42px; height:42px;" title="Notifications">
            <i class="bi bi-bell fs-5"></i>
            <?php if ($unreadNotifs > 0): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill" style="background: var(--ocp-green); font-size:0.65rem;">
                    <?= $unreadNotifs > 99 ? '99+' : $unreadNotifs ?>
                </span>
            <?php endif; ?>
        </a>

        <div class="vr mx-1" style="opacity:0.2;"></div>

        <!-- User Dropdown -->
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle gap-2" data-bs-toggle="dropdown" id="userDropdown">
                <?= renderAvatar($user['avatar'] ?? 'default.png', $user['prenom'], $user['nom'], 40) ?>
                <div class="d-none d-md-block text-start" style="max-width:140px;">
                    <div class="fw-bold text-truncate" style="font-size:0.88rem; color:var(--text-primary);"><?= sanitize($user['prenom'] . ' ' . $user['nom']) ?></div>
                    <div style="font-size:0.72rem; color:var(--text-muted);"><?= ucfirst($user['role']) ?></div>
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-2" style="min-width:200px;">
                <li>
                    <a class="dropdown-item py-2" href="<?= url($user['role'] === 'encadrant' ? 'encadrant/dashboard' : 'stagiaire/dashboard') ?>">
                        <i class="bi bi-speedometer2 me-2 text-success"></i>Tableau de bord
                    </a>
                </li>
                <?php if ($user['role'] === 'stagiaire'): ?>
                <li>
                    <a class="dropdown-item py-2" href="<?= url('stagiaire/profil') ?>">
                        <i class="bi bi-person-circle me-2 text-primary"></i>Mon Profil
                    </a>
                </li>
                <?php endif; ?>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item py-2 text-danger" href="<?= url('logout') ?>">
                        <i class="bi bi-box-arrow-right me-2"></i>Déconnexion
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main class="flex-fill p-4">
<?php $flash = getFlash(); ?>
<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
        <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle' : ($flash['type'] === 'danger' ? 'exclamation-triangle' : 'info-circle') ?>-fill me-2"></i>
        <?= $flash['message'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
