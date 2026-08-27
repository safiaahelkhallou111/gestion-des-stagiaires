<?php 
$pageTitle = "Erreur Serveur (500)";
?>
<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Erreur Serveur | OCP Digitalisation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= url('css/style.css') ?>">
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100 p-3">
    <div class="text-center" style="max-width: 480px;">
        <div class="display-1 fw-bold text-warning mb-2">500</div>
        <h3 class="fw-bold text-dark mb-2">Erreur Interne du Serveur</h3>
        <p class="text-muted mb-4">Une erreur inattendue est survenue sur la plateforme OCP. Veuillez réactualiser ou réessayer ultérieurement.</p>
        <a href="<?= url() ?>" class="btn btn-ocp px-4">
            <i class="bi bi-house-door-fill me-2"></i> Retour à l'accueil
        </a>
    </div>
</body>
</html>
