<?php
// Point d'entrée principal (Front Controller) - OCP Stagiaires MVC

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../database/init_db.php';

// Initialisation silencieuse de la BDD si vide (output suppressed)
ob_start();
initDatabase();
ob_end_clean();

// Extraction du path d'URL
$request_uri = $_SERVER['REQUEST_URI'] ?? '/';
$script_name = $_SERVER['SCRIPT_NAME'] ?? '';
$path = parse_url($request_uri, PHP_URL_PATH);

// Normalize double slashes
$path = preg_replace('#/+#', '/', $path);

// Nettoyage du base path
$base_path = dirname($script_name);
if ($base_path !== '/' && strpos($path, $base_path) === 0) {
    $path = substr($path, strlen($base_path));
}
$path = trim($path, '/');
if (empty($path)) {
    $path = 'login';
}

// System Routes
$routes = [
    // Auth
    'login' => ['AuthController', 'showLogin', 'GET'],
    'login_post' => ['AuthController', 'login', 'POST'],
    'register' => ['AuthController', 'showRegister', 'GET'],
    'register_post' => ['AuthController', 'register', 'POST'],
    'forgot-password' => ['AuthController', 'showForgotPassword', 'GET'],
    'forgot_password_post' => ['AuthController', 'forgotPassword', 'POST'],
    'reset-password' => ['AuthController', 'showResetPassword', 'GET'],
    'reset_password_post' => ['AuthController', 'resetPassword', 'POST'],
    'logout' => ['AuthController', 'logout', 'GET'],

    // Stagiaire Module
    'stagiaire/dashboard'      => ['StagiaireController', 'dashboard', 'GET'],
    'stagiaire/profil'         => ['StagiaireController', 'profil', 'GET'],
    'stagiaire/update_profil'  => ['StagiaireController', 'updateProfil', 'POST'],
    'stagiaire/sujet'          => ['StagiaireController', 'sujet', 'GET'],
    'stagiaire/taches'         => ['StagiaireController', 'taches', 'GET'],
    'stagiaire/update_tache'   => ['StagiaireController', 'updateTache', 'POST'],
    'stagiaire/pointage'       => ['StagiaireController', 'pointage', 'GET'],
    'stagiaire/do_pointage'    => ['StagiaireController', 'doPointage', 'POST'],
    'stagiaire/documents'      => ['StagiaireController', 'documents', 'GET'],
    'stagiaire/upload_document'=> ['StagiaireController', 'uploadDocument', 'POST'],
    'stagiaire/delete_document'=> ['StagiaireController', 'deleteDocument', 'POST'],
    'stagiaire/rapport_final'  => ['StagiaireController', 'rapportFinal', 'GET'],
    'stagiaire/upload_rapport' => ['StagiaireController', 'uploadRapport', 'POST'],
    'stagiaire/messages'       => ['StagiaireController', 'messages', 'GET'],
    'stagiaire/notifications'  => ['StagiaireController', 'notifications', 'GET'],

    // Encadrant Module
    'encadrant/dashboard' => ['EncadrantController', 'dashboard', 'GET'],
    'encadrant/stagiaires' => ['EncadrantController', 'stagiaires', 'GET'],
    'encadrant/stagiaire_detail' => ['EncadrantController', 'stagiaireDetail', 'GET'],
    'encadrant/assigner_stage' => ['EncadrantController', 'assignerStage', 'POST'],
    'encadrant/terminer_stage' => ['EncadrantController', 'terminerStage', 'POST'],
    'encadrant/sujets' => ['EncadrantController', 'sujets', 'GET'],
    'encadrant/create_sujet' => ['EncadrantController', 'createSujet', 'POST'],
    'encadrant/delete_sujet' => ['EncadrantController', 'deleteSujet', 'POST'],
    'encadrant/evaluations' => ['EncadrantController', 'evaluations', 'GET'],
    'encadrant/save_evaluation' => ['EncadrantController', 'saveEvaluation', 'POST'],
    'encadrant/pointages' => ['EncadrantController', 'pointages', 'GET'],
    'encadrant/valider_pointage' => ['EncadrantController', 'validerPointage', 'POST'],
    'encadrant/creer_tache' => ['EncadrantController', 'creerTache', 'POST'],
    'encadrant/messages'         => ['EncadrantController', 'messages', 'GET'],
    'encadrant/notifications'    => ['EncadrantController', 'notifications', 'GET'],
    'encadrant/mark_all_notif'   => ['EncadrantController', 'markAllNotif', 'POST'],
    'encadrant/profil'           => ['EncadrantController', 'profil', 'GET'],
    'encadrant/update_profil'    => ['EncadrantController', 'updateProfil', 'POST'],

    // APIs AJAX
    'api/send_message' => ['MessageController', 'send', 'POST'],
    'api/get_messages' => ['MessageController', 'get', 'GET'],
    'api/mark_notification_read' => ['NotificationController', 'markRead', 'POST']
];

$method = $_SERVER['REQUEST_METHOD'];

// Gestion des formulaires POST sous la même URL si besoin
if ($method === 'POST') {
    if (isset($routes[$path . '_post'])) {
        $path = $path . '_post';
    }
}

if (array_key_exists($path, $routes)) {
    $controllerName = $routes[$path][0];
    $actionName = $routes[$path][1];

    if (class_exists($controllerName)) {
        $controller = new $controllerName();
        if (method_exists($controller, $actionName)) {
            $controller->$actionName();
            exit;
        }
    }
}

// 404 Fallback
http_response_code(404);
require_once ROOT_PATH . '/views/errors/404.php';
