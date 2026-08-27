<?php
// Configuration globale de l'application OCP - Gestion des Stagiaires

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('APP_NAME', 'OCP Digitalisation — Gestion des Stagiaires');
define('APP_VERSION', '1.0.0');

// Detection automatique de l'URL de base
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || ($_SERVER['SERVER_PORT'] ?? 80) == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
$script_name = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$script_dir = ($script_name === '/' || $script_name === '.') ? '' : $script_name;
$base_url = rtrim($protocol . $host . $script_dir, '/');

define('BASE_URL', $base_url);
define('ROOT_PATH', dirname(__DIR__));
define('UPLOAD_PATH', ROOT_PATH . '/public/uploads');

// Autoload simple des modèles et contrôleurs
spl_autoload_register(function ($class_name) {
    $dirs = ['models/', 'controllers/'];
    foreach ($dirs as $dir) {
        $file = ROOT_PATH . '/' . $dir . $class_name . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Helper functions
function url($path = '') {
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}

function redirect($path) {
    header('Location: ' . url($path));
    exit;
}

function sanitize($data) {
    return htmlspecialchars(trim($data ?? ''), ENT_QUOTES, 'UTF-8');
}

function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function currentUser() {
    if (!isLoggedIn()) return null;
    return [
        'id'          => $_SESSION['user_id'],
        'nom'         => $_SESSION['user_nom'] ?? '',
        'prenom'      => $_SESSION['user_prenom'] ?? '',
        'email'       => $_SESSION['user_email'] ?? '',
        'role'        => $_SESSION['user_role'] ?? '',
        'avatar'      => $_SESSION['user_avatar'] ?? 'default.png',
        'specific_id' => $_SESSION['specific_id'] ?? null // stagiaire_id ou encadrant_id
    ];
}

/**
 * Renders user profile picture if uploaded, otherwise renders initials circle badge
 */
function renderAvatar($avatar, $prenom, $nom, $size = 40, $classes = '') {
    if (!empty($avatar) && $avatar !== 'default.png') {
        $fullPath = UPLOAD_PATH . '/avatars/' . $avatar;
        if (file_exists($fullPath)) {
            $src = url('uploads/avatars/' . $avatar);
            return "<img src='{$src}' class='rounded-circle object-fit-cover {$classes}' style='width:{$size}px; height:{$size}px;' alt='Avatar'>";
        }
    }
    $initials = strtoupper(substr($prenom ?? 'U', 0, 1) . substr($nom ?? 'N', 0, 1));
    $fontSize = round($size * 0.4);
    return "<div class='d-flex align-items-center justify-content-center fw-bold text-white rounded-circle flex-shrink-0 {$classes}' style='width:{$size}px; height:{$size}px; background: linear-gradient(135deg, var(--ocp-green), var(--ocp-emerald)); font-size:{$fontSize}px;'>{$initials}</div>";
}

function requireLogin() {
    if (!isLoggedIn()) {
        setFlash('danger', 'Veuillez vous connecter pour accéder à cette page.');
        redirect('login');
    }
}

function requireRole($role) {
    requireLogin();
    if ($_SESSION['user_role'] !== $role) {
        setFlash('danger', 'Accès non autorisé à cette section.');
        redirect($_SESSION['user_role'] === 'encadrant' ? 'encadrant/dashboard' : 'stagiaire/dashboard');
    }
}
