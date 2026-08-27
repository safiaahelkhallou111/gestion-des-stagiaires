<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Page Introuvable (404) | OCP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= url('css/style.css') ?>">
</head>
<body class="auth-bg">
    <div class="auth-card p-5 text-center">
        <h1 class="fw-bold text-success" style="font-size: 5rem; color: var(--ocp-green) !important;">404</h1>
        <h3 class="fw-bold mt-2">Page Introuvable</h3>
        <p class="text-muted">La page que vous recherchez n'existe pas ou a été déplacée.</p>
        <a href="<?= url('login') ?>" class="btn btn-ocp mt-3"><i class="bi bi-arrow-left me-2"></i>Retourner au portail</a>
    </div>
</body>
</html>
