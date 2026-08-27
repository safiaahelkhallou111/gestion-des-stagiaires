<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Stagiaire.php';
require_once __DIR__ . '/../models/Encadrant.php';

class AuthController {
    private $userModel;
    private $stagiaireModel;
    private $encadrantModel;

    public function __construct() {
        $this->userModel = new User();
        $this->stagiaireModel = new Stagiaire();
        $this->encadrantModel = new Encadrant();
    }

    public function showLogin() {
        if (isLoggedIn()) {
            redirect($_SESSION['user_role'] === 'encadrant' ? 'encadrant/dashboard' : 'stagiaire/dashboard');
        }
        require_once ROOT_PATH . '/views/auth/login.php';
    }

    public function login() {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            setFlash('danger', 'Veuillez saisir votre adresse email et votre mot de passe.');
            redirect('login');
        }

        $user = $this->userModel->findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nom'] = $user['nom'];
            $_SESSION['user_prenom'] = $user['prenom'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_avatar'] = $user['avatar'] ?? 'default.png';

            if ($user['role'] === 'stagiaire') {
                $st = $this->stagiaireModel->findByUserId($user['id']);
                $_SESSION['specific_id'] = $st ? $st['id'] : null;
                setFlash('success', "Bienvenue, " . $user['prenom'] . " !");
                redirect('stagiaire/dashboard');
            } else {
                $enc = $this->encadrantModel->findByUserId($user['id']);
                $_SESSION['specific_id'] = $enc ? $enc['id'] : null;
                setFlash('success', "Ravi de vous revoir, " . $user['prenom'] . " !");
                redirect('encadrant/dashboard');
            }
        } else {
            setFlash('danger', 'Identifiants incorrects (email ou mot de passe invalide).');
            redirect('login');
        }
    }

    public function showRegister() {
        if (isLoggedIn()) {
            redirect($_SESSION['user_role'] === 'encadrant' ? 'encadrant/dashboard' : 'stagiaire/dashboard');
        }
        require_once ROOT_PATH . '/views/auth/register.php';
    }

    public function register() {
        $role = $_POST['role'] ?? 'stagiaire';
        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        $telephone = trim($_POST['telephone'] ?? '');

        if (empty($nom) || empty($prenom) || empty($email) || empty($password)) {
            setFlash('danger', 'Veuillez remplir tous les champs obligatoires.');
            redirect('register');
        }

        if ($password !== $confirm) {
            setFlash('danger', 'Les mots de passe ne correspondent pas.');
            redirect('register');
        }

        if ($this->userModel->findByEmail($email)) {
            setFlash('danger', 'Un compte existe déjà avec cette adresse email.');
            redirect('register');
        }

        if ($role === 'stagiaire') {
            $cin = strtoupper(trim($_POST['cin'] ?? ''));
            $universite = trim($_POST['universite'] ?? '');
            $filiere = trim($_POST['filiere'] ?? '');
            $typeStage = $_POST['type_stage'] ?? 'PFE';

            if (empty($cin) || empty($universite) || empty($filiere)) {
                setFlash('danger', 'Veuillez compléter les informations relatives au stage (CIN, université, filière).');
                redirect('register');
            }

            if ($this->stagiaireModel->findByCin($cin)) {
                setFlash('danger', 'Un stagiaire est déjà inscrit avec ce numéro de CIN.');
                redirect('register');
            }

            $userId = $this->userModel->create('stagiaire', $nom, $prenom, $email, $password, $telephone);
            $this->stagiaireModel->create($userId, $cin, $universite, $filiere, 'Bac +5', $typeStage);
        } else {
            $departement = trim($_POST['departement'] ?? 'Digitalisation & SI');
            $fonction = trim($_POST['fonction'] ?? 'Encadrant Digital');
            
            $userId = $this->userModel->create('encadrant', $nom, $prenom, $email, $password, $telephone);
            $this->encadrantModel->create($userId, $departement, $fonction);
        }

        setFlash('success', 'Votre compte a été créé avec succès ! Vous pouvez maintenant vous connecter.');
        redirect('login');
    }

    public function showForgotPassword() {
        require_once ROOT_PATH . '/views/auth/forgot_password.php';
    }

    public function forgotPassword() {
        $email = trim($_POST['email'] ?? '');
        $user = $this->userModel->findByEmail($email);

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $this->userModel->setResetToken($email, $token, $expires);

            // Simulation d'envoi d'email
            $resetLink = url('reset-password?token=' . $token);
            setFlash('info', "Lien de réinitialisation généré (Simulation Mail) : <a href='{$resetLink}' class='alert-link'>Cliquez ici pour réinitialiser</a>");
        } else {
            setFlash('danger', "Aucun compte n'est associé à cette adresse email.");
        }

        redirect('forgot-password');
    }

    public function showResetPassword() {
        $token = $_GET['token'] ?? '';
        $user = $this->userModel->findByResetToken($token);

        if (!$user) {
            setFlash('danger', 'Lien de réinitialisation invalide ou expiré.');
            redirect('login');
        }

        require_once ROOT_PATH . '/views/auth/reset_password.php';
    }

    public function resetPassword() {
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        $user = $this->userModel->findByResetToken($token);
        if (!$user) {
            setFlash('danger', 'Token invalide.');
            redirect('login');
        }

        if (empty($password) || $password !== $confirm) {
            setFlash('danger', 'Les mots de passe doivent être identiques et non vides.');
            redirect('reset-password?token=' . $token);
        }

        $this->userModel->updatePassword($user['id'], $password);
        setFlash('success', 'Votre mot de passe a été réinitialisé avec succès ! Connectez-vous.');
        redirect('login');
    }

    public function logout() {
        session_unset();
        session_destroy();
        session_start();
        // Mark that the user just logged out so the client can show the intro
        $_SESSION['show_intro_on_logout'] = 1;
        setFlash('info', 'Vous avez été déconnecté.');
        redirect('login');
    }
}
