<?php 
$pageTitle = "Dashboard Encadrant OCP";
$pageHeader = "Tableau de Bord & KPIs d'Encadrement";
require_once ROOT_PATH . '/views/layouts/header.php';
require_once ROOT_PATH . '/views/layouts/sidebar.php';
require_once ROOT_PATH . '/views/layouts/navbar.php';
?>

<div class="container-fluid">
    <!-- Welcome Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 bg-white p-4 rounded-4 shadow-sm border">
        <div>
            <span class="badge bg-dark text-white fw-bold px-3 py-1 mb-2 rounded-pill">ESPACE ENCADRANT </span>
            <h3 class="fw-bold m-0" style="color: var(--ocp-dark);">Bonjour, M. <?= sanitize($user['prenom'] . ' ' . $user['nom']) ?> </h3>
            
        </div>
        <div>
            <a href="<?= url('encadrant/stagiaires') ?>" class="btn btn-ocp"><i class="bi bi-people-fill me-2"></i> Gérer mes Stagiaires</a>
        </div>
    </div>

    <!-- KPI Summary Cards Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card primary">
                <div>
                    <div class="stat-label">Total Stagiaires Encadrés</div>
                    <div class="stat-value text-primary"><?= $stats['total_stagiaires'] ?></div>
                    <div class="small text-muted mt-1"><?= $stats['en_cours'] ?> en cours | <?= $stats['termines'] ?> terminés</div>
                </div>
                <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card success">
                <div>
                    <div class="stat-label">Stages en cours</div>
                    <div class="stat-value text-success"><?= $stats['en_cours'] ?></div>
                    <div class="small text-muted mt-1">Actifs actuellement</div>
                </div>
                <div class="stat-icon"><i class="bi bi-journal-code"></i></div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card warning">
                <div>
                    <div class="stat-label">Sujets Déposés</div>
                    <div class="stat-value text-warning"><?= $stats['nb_sujets'] ?></div>
                    <div class="small text-muted mt-1">Sujets dans le catalogue</div>
                </div>
                <div class="stat-icon"><i class="bi bi-card-checklist"></i></div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card danger">
                <div>
                    <div class="stat-label">Évaluations à Compléter</div>
                    <div class="stat-value text-danger"><?= $stats['evaluations_a_completer'] ?></div>
                    <div class="small text-muted mt-1">Fiches d'évaluation restantes</div>
                </div>
                <div class="stat-icon"><i class="bi bi-star-fill"></i></div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Table of Supervised Interns -->
        <div class="col-lg-8">
            <div class="card-ocp">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="fs-5 fw-bold text-dark"><i class="bi bi-list-stars me-2 text-success"></i>Liste Récapitulative des Stagiaires</span>
                    <a href="<?= url('encadrant/stagiaires') ?>" class="btn btn-outline-success btn-sm">Voir tout <i class="bi bi-arrow-right"></i></a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-ocp align-middle">
                            <thead>
                                <tr>
                                    <th>Stagiaire</th>
                                    <th>Type</th>
                                    <th>Sujet Assigné</th>
                                    <th>Progression</th>
                                    <th>Statut</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($stagiaires)): ?>
                                    <tr><td colspan="6" class="text-center text-muted py-5">Aucun stagiaire affecté pour le moment.</td></tr>
                                <?php else: ?>
                                    <?php foreach (array_slice($stagiaires, 0, 5) as $s): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <?= renderAvatar($s['avatar'] ?? 'default.png', $s['prenom'], $s['nom'], 36) ?>
                                                    <div>
                                                        <div class="fw-bold text-dark"><?= sanitize($s['prenom'] . ' ' . $s['nom']) ?></div>
                                                        <small class="text-muted"><i class="bi bi-card-text me-1"></i>CIN: <?= sanitize($s['cin']) ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="badge bg-light text-dark border"><?= sanitize($s['type_stage']) ?></span></td>
                                            <td style="max-width: 200px;">
                                                <div class="text-truncate fw-semibold" title="<?= sanitize($s['sujet_titre']) ?>"><?= sanitize($s['sujet_titre']) ?></div>
                                            </td>
                                            <td style="width: 15%;">
                                                <div class="progress" style="height: 8px;">
                                                    <div class="progress-bar bg-success" style="width: <?= round($s['progression_moyenne']) ?>%;"></div>
                                                </div>
                                                <small class="fw-bold text-muted"><?= round($s['progression_moyenne']) ?>%</small>
                                            </td>
                                            <td>
                                                <span class="badge badge-status <?= $s['stage_statut'] ?>"><?= strtoupper(str_replace('_', ' ', $s['stage_statut'])) ?></span>
                                            </td>
                                            <td>
                                                <a href="<?= url('encadrant/stagiaire_detail?id=' . $s['stage_id']) ?>" class="btn btn-light btn-sm text-primary fw-semibold" title="Fiche détaillée">
                                                    Voir plus <i class="bi bi-chevron-right"></i>
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

        <!-- Analytics Chart -->
        <div class="col-lg-4">
            <div class="card-ocp">
                <div class="card-header">
                    <span class="fw-bold"><i class="bi bi-pie-chart-fill me-2 text-primary"></i>Répartition par Statut</span>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="220"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('statusChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['En Cours', 'Terminés'],
                datasets: [{
                    data: [<?= $stats['en_cours'] ?>, <?= $stats['termines'] ?>],
                    backgroundColor: ['#6ab04c', '#3b82f6'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    });
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
