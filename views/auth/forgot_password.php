<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié | OCP Digitalisation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= url('css/style.css') ?>">
    <script>const BASE_URL = '<?= url() ?>';</script>
    <style>
        body.auth-bg {
            background: #0a0a0a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .auth-card {
            background: #111111;
            border: 1px solid #222;
            border-radius: 20px;
            box-shadow: 0 24px 64px rgba(0,0,0,0.7);
            width: 100%;
            max-width: 440px;
            padding: 2.75rem 2.5rem;
        }

        .auth-card .form-label {
            color: #aaa;
            font-size: 0.82rem;
            font-weight: 500;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            margin-bottom: 0.4rem;
        }

        .auth-card .input-group-text {
            background: #1a1a1a;
            border: 1px solid #2a2a2a;
            color: #555;
        }

        .auth-card .form-control {
            background: #1a1a1a;
            border: 1px solid #2a2a2a;
            color: #e8e8e8;
            border-left: none;
        }

        .auth-card .form-control::placeholder { color: #3a3a3a; }

        .auth-card .form-control:focus {
            background: #1e1e1e;
            border-color: #6ab04c;
            box-shadow: 0 0 0 3px rgba(106,176,76,0.15);
            color: #e8e8e8;
        }

        .auth-card .input-group:focus-within .input-group-text {
            border-color: #6ab04c;
        }

        .auth-card .btn-ocp {
            background: linear-gradient(135deg, #6ab04c 0%, #5a9e3e 100%);
            box-shadow: 0 4px 18px rgba(106,176,76,0.3);
            padding: 0.75rem;
            font-size: 0.9rem;
            letter-spacing: 0.03em;
            border-radius: 10px;
            margin-top: 0.5rem;
            border: none;
            color: white;
            font-weight: 600;
        }

        .auth-card .btn-ocp:hover {
            background: linear-gradient(135deg, #5a9e3e 0%, #4d8f33 100%);
            box-shadow: 0 6px 22px rgba(106,176,76,0.4);
            transform: translateY(-1px);
        }

        .auth-title {
            font-size: 1.55rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.25rem;
            letter-spacing: -0.02em;
        }

        .auth-subtitle {
            font-size: 0.82rem;
            color: #6b6b6b;
            margin-bottom: 2rem;
        }

        .auth-footer {
            text-align: center;
            margin-top: 1.75rem;
            font-size: 0.8rem;
            color: #6b6b6b;
        }

        .auth-footer a {
            color: #6ab04c;
            text-decoration: none;
            font-weight: 600;
        }

        .alert {
            background: #1a1a1a;
            border: 1px solid #2a2a2a;
            color: #ccc;
            font-size: 0.85rem;
            border-radius: 10px;
        }

        .btn-close { filter: invert(1) opacity(0.4); }
    </style>
</head>
<body class="auth-bg">

<div class="auth-card">
    <div class="auth-title">Mot de passe oublié</div>
    <div class="auth-subtitle">Saisissez votre email pour recevoir le lien de réinitialisation.</div>

    <?php $flash = getFlash(); ?>
    <?php if ($flash): ?>
        <div class="alert alert-dismissible fade show mb-4" role="alert">
            <?= $flash['message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form action="<?= url('forgot_password_post') ?>" method="POST">
        <div class="mb-4">
            <label class="form-label" for="email">Votre Adresse Email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" class="form-control" id="email" name="email"
                       placeholder="nom@ocp.ma" required autocomplete="email">
            </div>
        </div>

        <button type="submit" class="btn btn-ocp w-100">Envoyer le lien de réinitialisation</button>
    </form>

    <div class="auth-footer">
        Vous vous en souvenez ? <a href="<?= url('login') ?>">Retour à la connexion</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
