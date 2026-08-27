<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription | OCP Digitalisation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= url('css/style.css') ?>">
    <link rel="stylesheet" href="<?= url('css/intro.css') ?>">
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
            max-width: 560px;
            padding: 2.5rem;
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

        /* Labels */
        .form-label {
            color: #aaa;
            font-size: 0.78rem;
            font-weight: 500;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            margin-bottom: 0.35rem;
        }

        /* Inputs */
        .form-control,
        .form-select {
            background: #1a1a1a;
            border: 1px solid #2a2a2a;
            color: #e8e8e8;
            border-radius: 8px;
        }

        .form-control::placeholder { color: #3a3a3a; }

        .form-control:focus,
        .form-select:focus {
            background: #1e1e1e;
            border-color: #6ab04c;
            box-shadow: 0 0 0 3px rgba(106,176,76,0.15);
            color: #e8e8e8;
        }

        .form-select option {
            background: #1a1a1a;
            color: #e8e8e8;
        }

        /* Role radio buttons */
        .role-btn {
            display: block;
            width: 100%;
            padding: 0.6rem 1rem;
            background: #1a1a1a;
            border: 1px solid #2a2a2a;
            border-radius: 8px;
            color: #6b6b6b;
            font-size: 0.88rem;
            font-weight: 500;
            cursor: pointer;
            text-align: center;
            transition: all 0.15s ease;
            user-select: none;
        }

        .btn-check:checked + .role-btn {
            background: #6ab04c;
            border-color: #6ab04c;
            color: #fff;
        }

        .role-btn:hover {
            border-color: #6ab04c;
            color: #6ab04c;
        }

        /* Section card */
        .section-card {
            background: #161616;
            border: 1px solid #222;
            border-radius: 10px;
            padding: 1.25rem;
            margin-bottom: 1rem;
        }

        .section-card-title {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #6ab04c;
            margin-bottom: 1rem;
        }

        /* Submit button */
        .btn-submit {
            background: linear-gradient(135deg, #6ab04c 0%, #5a9e3e 100%);
            color: #fff;
            font-weight: 600;
            padding: 0.75rem;
            border-radius: 10px;
            border: none;
            width: 100%;
            font-size: 0.9rem;
            letter-spacing: 0.03em;
            box-shadow: 0 4px 18px rgba(106,176,76,0.3);
            transition: all 0.2s ease;
            margin-top: 0.5rem;
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 22px rgba(106,176,76,0.4);
        }

        /* Footer */
        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.8rem;
            color: #6b6b6b;
        }

        .auth-footer a {
            color: #6ab04c;
            text-decoration: none;
            font-weight: 600;
        }

        /* Alert */
        .alert {
            background: #1a1a1a;
            border: 1px solid #2a2a2a;
            color: #ccc;
            font-size: 0.85rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }

        .btn-close { filter: invert(1) opacity(0.4); }

        /* Role label pill */
        .role-label {
            font-size: 0.7rem;
            color: #6b6b6b;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body class="auth-bg">

<div class="auth-card">

    <div class="auth-title">Inscription</div>
    <div class="auth-subtitle">OCP Digitalisation </div>

    <?php $flash = getFlash(); ?>
    <?php if ($flash): ?>
        <div class="alert alert-dismissible fade show" role="alert">
            <?= $flash['message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form action="<?= url('register') ?>" method="POST">

        <!-- Role Picker -->
        <div class="mb-4">
            <div class="role-label">Vous êtes</div>
            <div class="row g-2">
                <div class="col-6">
                    <input type="radio" class="btn-check" name="role" id="role_stagiaire" value="stagiaire" checked onclick="toggleRoleFields('stagiaire')">
                    <label class="role-btn" for="role_stagiaire">Stagiaire</label>
                </div>
                <div class="col-6">
                    <input type="radio" class="btn-check" name="role" id="role_encadrant" value="encadrant" onclick="toggleRoleFields('encadrant')">
                    <label class="role-btn" for="role_encadrant">Encadrant OCP</label>
                </div>
            </div>
        </div>

        <!-- Common Fields -->
        <div class="row g-2 mb-3">
            <div class="col-6">
                <label class="form-label" for="nom">Nom</label>
                <input type="text" class="form-control" id="nom" name="nom" required placeholder="BENNANI">
            </div>
            <div class="col-6">
                <label class="form-label" for="prenom">Prénom</label>
                <input type="text" class="form-control" id="prenom" name="prenom" required placeholder="Sarah">
            </div>
        </div>

        <div class="row g-2 mb-3">
            <div class="col-6">
                <label class="form-label" for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" required placeholder="email@exemple.ma">
            </div>
            <div class="col-6">
                <label class="form-label" for="telephone">Téléphone</label>
                <input type="text" class="form-control" id="telephone" name="telephone" placeholder="0661000000">
            </div>
        </div>

        <!-- Stagiaire Fields -->
        <div id="stagiaire_fields">
            <div class="section-card">
                <div class="section-card-title">Informations Stagiaire</div>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label" for="cin">CIN</label>
                        <input type="text" class="form-control" id="cin" name="cin" placeholder="BJ123456">
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="type_stage">Type de Stage</label>
                        <select class="form-select" id="type_stage" name="type_stage">
                            <option value="PFE">PFE (Fin d'études)</option>
                            <option value="PFA">PFA (Fin d'année)</option>
                            <option value="Observation">Observation</option>
                            <option value="Initiation">Initiation</option>
                        </select>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label" for="universite">Université / École</label>
                        <input type="text" class="form-control" id="universite" name="universite" placeholder="EMSI, UM6P…">
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="filiere">Filière</label>
                        <input type="text" class="form-control" id="filiere" name="filiere" placeholder="Génie Informatique">
                    </div>
                </div>
            </div>
        </div>

        <!-- Encadrant Fields -->
        <div id="encadrant_fields" class="d-none">
            <div class="section-card">
                <div class="section-card-title">Profil Professionnel OCP</div>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label" for="departement">Département</label>
                        <input type="text" class="form-control" id="departement" name="departement" value="Digitalisation & SI">
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="fonction">Fonction</label>
                        <input type="text" class="form-control" id="fonction" name="fonction" value="Chef de Projet Digital">
                    </div>
                </div>
            </div>
        </div>

        <!-- Passwords -->
        <div class="row g-2 mb-3">
            <div class="col-6">
                <label class="form-label" for="password">Mot de passe</label>
                <input type="password" class="form-control" id="password" name="password" required placeholder="••••••••">
            </div>
            <div class="col-6">
                <label class="form-label" for="confirm_password">Confirmation</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required placeholder="••••••••">
            </div>
        </div>

        <button type="submit" class="btn-submit">Créer mon compte</button>
    </form>

    <div class="auth-footer">
        Déjà un compte ? <a href="<?= url('login') ?>">Se connecter</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= url('js/intro.js') ?>"></script>
<script>
    function toggleRoleFields(role) {
        document.getElementById('stagiaire_fields').classList.toggle('d-none', role !== 'stagiaire');
        document.getElementById('encadrant_fields').classList.toggle('d-none', role !== 'encadrant');
    }
</script>
</body>
</html>
