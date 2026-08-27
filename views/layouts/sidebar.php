<?php
$user = currentUser();
$role = $user['role'] ?? 'stagiaire';
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$currentPath = trim(str_replace(dirname($_SERVER['SCRIPT_NAME']), '', $currentPath), '/');

function isActive(string $path, string $currentPath): string {
    return strpos($currentPath, $path) !== false ? 'active' : '';
}
?>

<div id="sidebar">
    <!-- Brand -->
    <div class="sidebar-brand d-flex align-items-center justify-content-center">
        <a href="<?= url($role === 'encadrant' ? 'encadrant/dashboard' : 'stagiaire/dashboard') ?>" class="d-flex align-items-center text-decoration-none sidebar-brand-logo">
            <img src="<?= url('img/loggo.png') ?>" alt="OCP Emblem" class="ocp-emblem-img">
            <span class="brand-cp-text">CP</span>
        </a>
    </div>

    <ul class="sidebar-nav">

        <?php if ($role === 'stagiaire'): ?>
            <div class="sidebar-heading">Espace Stagiaire</div>

            <li class="nav-item">
                <a class="nav-link <?= isActive('stagiaire/dashboard', $currentPath) ?>" href="<?= url('stagiaire/dashboard') ?>" title="Tableau de bord">
                    <i class="bi bi-grid-1x2-fill"></i> <span class="nav-text">Tableau de bord</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= isActive('stagiaire/profil', $currentPath) ?>" href="<?= url('stagiaire/profil') ?>" title="Mon Profil">
                    <i class="bi bi-person-circle"></i> <span class="nav-text">Mon Profil</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= isActive('stagiaire/sujet', $currentPath) ?>" href="<?= url('stagiaire/sujet') ?>" title="Mon Sujet & Encadrant">
                    <i class="bi bi-journal-bookmark-fill"></i> <span class="nav-text">Mon Sujet & Encadrant</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= isActive('stagiaire/taches', $currentPath) ?>" href="<?= url('stagiaire/taches') ?>" title="Suivi des Tâches">
                    <i class="bi bi-check2-square"></i> <span class="nav-text">Suivi des Tâches</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= isActive('stagiaire/pointage', $currentPath) ?>" href="<?= url('stagiaire/pointage') ?>" title="Mon Pointage">
                    <i class="bi bi-calendar-check-fill"></i> <span class="nav-text">Mon Pointage</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= isActive('stagiaire/documents', $currentPath) ?>" href="<?= url('stagiaire/documents') ?>" title="Mes Documents">
                    <i class="bi bi-folder-fill"></i> <span class="nav-text">Mes Documents</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= isActive('stagiaire/rapport_final', $currentPath) ?>" href="<?= url('stagiaire/rapport_final') ?>" title="Rapport Final">
                    <i class="bi bi-file-earmark-pdf-fill"></i> <span class="nav-text">Rapport Final</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= isActive('stagiaire/messages', $currentPath) ?>" href="<?= url('stagiaire/messages') ?>" title="Messagerie Encadrant">
                    <i class="bi bi-chat-left-text-fill"></i> <span class="nav-text">Messagerie Encadrant</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= isActive('stagiaire/notifications', $currentPath) ?>" href="<?= url('stagiaire/notifications') ?>" title="Notifications">
                    <i class="bi bi-bell-fill"></i> <span class="nav-text">Notifications</span>
                </a>
            </li>

        <?php else: ?>
            <div class="sidebar-heading">Espace Encadrant</div>

            <li class="nav-item">
                <a class="nav-link <?= isActive('encadrant/dashboard', $currentPath) ?>" href="<?= url('encadrant/dashboard') ?>" title="Dashboard KPIs">
                    <i class="bi bi-speedometer2"></i> <span class="nav-text">Dashboard KPIs</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= isActive('encadrant/profil', $currentPath) ?>" href="<?= url('encadrant/profil') ?>" title="Mon Profil">
                    <i class="bi bi-person-circle"></i> <span class="nav-text">Mon Profil</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= isActive('encadrant/stagiaires', $currentPath) ?>" href="<?= url('encadrant/stagiaires') ?>" title="Mes Stagiaires">
                    <i class="bi bi-people-fill"></i> <span class="nav-text">Mes Stagiaires</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= isActive('encadrant/sujets', $currentPath) ?>" href="<?= url('encadrant/sujets') ?>" title="Sujets de Stage">
                    <i class="bi bi-card-checklist"></i> <span class="nav-text">Sujets de Stage</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= isActive('encadrant/evaluations', $currentPath) ?>" href="<?= url('encadrant/evaluations') ?>" title="Évaluations">
                    <i class="bi bi-star-fill"></i> <span class="nav-text">Évaluations</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= isActive('encadrant/pointages', $currentPath) ?>" href="<?= url('encadrant/pointages') ?>" title="Suivi des Pointages">
                    <i class="bi bi-clock-history"></i> <span class="nav-text">Suivi des Pointages</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= isActive('encadrant/messages', $currentPath) ?>" href="<?= url('encadrant/messages') ?>" title="Messagerie Stagiaires">
                    <i class="bi bi-chat-dots-fill"></i> <span class="nav-text">Messagerie Stagiaires</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= isActive('encadrant/notifications', $currentPath) ?>" href="<?= url('encadrant/notifications') ?>" title="Notifications">
                    <i class="bi bi-bell-fill"></i> <span class="nav-text">Notifications</span>
                </a>
            </li>
        <?php endif; ?>

    </ul>

    <!-- Sidebar user card -->
    <div class="sidebar-user-area">
        <div class="d-flex align-items-center gap-2">
            <?= renderAvatar($user['avatar'] ?? 'default.png', $user['prenom'], $user['nom'], 38) ?>
            <div class="overflow-hidden user-info-text">
                <div class="fw-bold text-white text-truncate" style="font-size:0.82rem;"><?= sanitize($user['prenom'] . ' ' . $user['nom']) ?></div>
                <div class="text-truncate" style="font-size:0.72rem; color:#64748b;"><?= sanitize($user['email']) ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Content Wrapper: Navbar + Main -->
<div id="content-wrapper">
