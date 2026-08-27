<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? sanitize($pageTitle) . ' | ' : '' ?><?= APP_NAME ?></title>
    <meta name="description" content="Plateforme de gestion des stagiaires OCP — Entité Digitalisation & SI">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom OCP CSS -->
    <link rel="stylesheet" href="<?= url('css/style.css') ?>">
    <!-- Ferrari-style intro -->
    <link rel="stylesheet" href="<?= url('css/intro.css') ?>">

    <script>
        // BASE_URL without trailing slash for clean JS concatenation
        const BASE_URL = '<?= rtrim(url(), "/") ?>';
        (function() {
            const t = localStorage.getItem('ocp-theme') || 'light';
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>
</head>
<body>
<div id="wrapper">
