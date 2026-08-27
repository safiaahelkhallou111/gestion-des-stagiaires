<?php 
$pageTitle = "Mon Pointage";
$pageHeader = "Pointage & Registre de Présence (RG7)";
require_once ROOT_PATH . '/views/layouts/header.php';
require_once ROOT_PATH . '/views/layouts/sidebar.php';
require_once ROOT_PATH . '/views/layouts/navbar.php';
?>

<div class="container-fluid">
    <div class="row g-4 mb-4">
        <!-- Daily Clock In Card -->
        <div class="col-lg-5">
            <div class="card-ocp">
                <div class="card-header text-white" style="background: linear-gradient(135deg, #3b4252 0%, #4c566a 100%) !important;">
                    <span><i class="bi bi-fingerprint me-2 text-success"></i>Pointage du Jour (RG7)</span>
                    <span class="badge bg-light text-dark fw-bold"><?= date('d/m/Y') ?></span>
                </div>
                <div class="card-body text-center p-4">
                    <div class="my-3">
                        <i class="bi bi-clock-history display-1 text-success" style="color: var(--ocp-green) !important;"></i>
                        <h3 class="fw-bold text-dark mt-2" id="liveClock"><?= date('H:i:s') ?></h3>
                        <p class="text-muted small">Horloge officielle OCP Digitalisation</p>
                    </div>

                    <?php if ($stageActif && $stageActif['statut'] === 'termine'): ?>
                        <div class="alert alert-secondary p-3 rounded-4 text-center">
                            <i class="bi bi-lock-fill fs-3 d-block mb-1 text-secondary"></i>
                            <h6 class="fw-bold text-dark mb-1">Stage Terminé & Validé (RG6)</h6>
                            <span class="small text-muted">Le pointage est clôturé (mode lecture seule).</span>
                        </div>
                    <?php elseif ($todayPointage): ?>
                        <div class="alert alert-success border-success p-3 rounded-4">
                            <i class="bi bi-check-circle-fill fs-3 d-block mb-1 text-success"></i>
                            <h6 class="fw-bold text-success mb-1">Présence Enregistrée !</h6>
                            <span class="small text-muted">Pointage effectué à <?= date('H:i', strtotime($todayPointage['created_at'])) ?></span>
                            <?php if (!empty($todayPointage['remarque'])): ?>
                                <div class="mt-2 text-dark small bg-white p-2 rounded border">"<?= sanitize($todayPointage['remarque']) ?>"</div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <form action="<?= url('stagiaire/do_pointage') ?>" method="POST">
                            <div class="mb-3 text-start">
                                <label class="form-label fw-semibold" for="remarque">Remarque / Activité du jour (optionnel)</label>
                                <textarea class="form-control" id="remarque" name="remarque" rows="2" placeholder="Ex: Déploiement backend, réunion avec encadrant..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-ocp btn-lg w-100 py-3 fw-bold">
                                <i class="bi bi-check2-circle me-2"></i> Pointer Ma Présence Aujourd'hui
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Attendance Stats summary -->
            <?php if ($stats): ?>
                <div class="card-ocp">
                    <div class="card-header">
                        <span class="fw-bold"><i class="bi bi-pie-chart-fill me-2 text-primary"></i>Statistiques d'Assiduité</span>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="fs-4 fw-bold text-success"><?= $stats['nb_presents'] ?></div>
                                <small class="text-muted">Présent(s)</small>
                            </div>
                            <div class="col-4">
                                <div class="fs-4 fw-bold text-warning"><?= $stats['nb_retards'] ?></div>
                                <small class="text-muted">Retard(s)</small>
                            </div>
                            <div class="col-4">
                                <div class="fs-4 fw-bold text-danger"><?= $stats['nb_absents'] ?></div>
                                <small class="text-muted">Absent(s)</small>
                            </div>
                        </div>
                        <div class="progress mt-3" style="height: 10px;">
                            <div class="progress-bar bg-success" style="width: <?= $stats['taux_presence'] ?>%;"></div>
                        </div>
                        <div class="text-center mt-2 small fw-bold text-muted"><?= $stats['taux_presence'] ?>% Taux de Présence</div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pointage History & Visual Grid -->
        <div class="col-lg-7">
            <!-- Calendar Visual Grid -->
            <div class="card-ocp mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="bi bi-calendar3 me-2 text-success"></i>Aperçu Mensuel (<?= date('F Y') ?>)</span>
                    <div class="d-flex gap-2 align-items-center small">
                        <span class="d-inline-block rounded-circle bg-success" style="width:10px; height:10px;"></span> Présent
                        <span class="d-inline-block rounded-circle bg-warning" style="width:10px; height:10px;"></span> Retard
                        <span class="d-inline-block rounded-circle bg-danger" style="width:10px; height:10px;"></span> Absent
                    </div>
                </div>
                <div class="card-body">
                    <?php 
                        // Generate simple calendar grid for current month
                        $pointageMap = [];
                        foreach ($pointages as $pt) {
                            $pointageMap[$pt['date_pointage']] = $pt['statut'];
                        }
                        $daysInMonth = date('t');
                        $currentDay = date('j');
                        $todayStr = date('Y-m-d');
                    ?>
                    <div class="cal-grid">
                        <div class="cal-day header">Lun</div>
                        <div class="cal-day header">Mar</div>
                        <div class="cal-day header">Mer</div>
                        <div class="cal-day header">Jeu</div>
                        <div class="cal-day header">Ven</div>
                        <div class="cal-day header">Sam</div>
                        <div class="cal-day header">Dim</div>

                        <?php 
                        $firstDayOfWeek = date('N', strtotime(date('Y-m-01')));
                        for ($i = 1; $i < $firstDayOfWeek; $i++) {
                            echo '<div class="cal-day empty"></div>';
                        }

                        for ($day = 1; $day <= $daysInMonth; $day++) {
                            $dateKey = date('Y-m-' . sprintf('%02d', $day));
                            $statusClass = $pointageMap[$dateKey] ?? '';
                            $isToday = ($dateKey === $todayStr) ? 'today' : '';
                            echo "<div class='cal-day {$statusClass} {$isToday}' title='{$dateKey} : " . ($statusClass ?: 'Non pointé') . "'>{$day}</div>";
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Pointage Table -->
            <div class="card-ocp">
                <div class="card-header">
                    <span class="fw-bold"><i class="bi bi-list-check me-2 text-success"></i>Journal Complet des Pointages</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-ocp align-middle">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Statut</th>
                                    <th>Remarque</th>
                                    <th>Heure Dépôt</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($pointages)): ?>
                                    <tr><td colspan="4" class="text-center text-muted py-4">Aucun pointage enregistré.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($pointages as $p): ?>
                                        <tr>
                                            <td class="fw-bold text-dark"><i class="bi bi-calendar-event me-2 text-muted"></i><?= date('d/m/Y', strtotime($p['date_pointage'])) ?></td>
                                            <td>
                                                <span class="badge badge-status <?= $p['statut'] ?>"><?= strtoupper($p['statut']) ?></span>
                                            </td>
                                            <td class="small text-muted"><?= !empty($p['remarque']) ? sanitize($p['remarque']) : '—' ?></td>
                                            <td class="small text-muted"><?= date('H:i:s', strtotime($p['created_at'])) ?></td>
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

<script>
    setInterval(function() {
        const now = new Date();
        const el = document.getElementById('liveClock');
        if (el) el.innerText = now.toLocaleTimeString();
    }, 1000);
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
