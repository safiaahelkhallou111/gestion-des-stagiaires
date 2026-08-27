</main>

<footer class="bg-white border-top py-3 text-center text-muted" style="font-size:0.8rem;">
    <div class="container-fluid">
        <span>&copy; <?= date('Y') ?> <strong>OCP Group — Entité Digitalisation </strong>. Tous droits réservés. Plateforme de Gestion des Stagiaires </span>
    </div>
</footer>

</div> <!-- End content-wrapper -->
</div> <!-- End wrapper -->

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Chart.js for analytics -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Custom App JS -->
<script src="<?= url('js/app.js') ?>"></script>
<!-- Ferrari-style logo intro (once per session) -->
<?php if (!empty($_SESSION['show_intro_on_logout'])): ?>
    <script>
        window.ocp_show_intro_on_logout = true;
    </script>
    <?php unset($_SESSION['show_intro_on_logout']); ?>
<?php endif; ?>
<script src="<?= url('js/intro.js') ?>"></script>
</body>
</html>
