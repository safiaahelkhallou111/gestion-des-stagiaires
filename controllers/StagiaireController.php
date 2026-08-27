<?php
require_once __DIR__ . '/../models/Stagiaire.php';
require_once __DIR__ . '/../models/Stage.php';
require_once __DIR__ . '/../models/Pointage.php';
require_once __DIR__ . '/../models/Tache.php';
require_once __DIR__ . '/../models/Document.php';
require_once __DIR__ . '/../models/Evaluation.php';
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../models/User.php';

class StagiaireController {
    private $stagiaireModel;
    private $stageModel;
    private $pointageModel;
    private $tacheModel;
    private $documentModel;
    private $evaluationModel;
    private $notificationModel;
    private $userModel;

    public function __construct() {
        requireRole('stagiaire');
        $this->stagiaireModel    = new Stagiaire();
        $this->stageModel        = new Stage();
        $this->pointageModel     = new Pointage();
        $this->tacheModel        = new Tache();
        $this->documentModel     = new Document();
        $this->evaluationModel   = new Evaluation();
        $this->notificationModel = new Notification();
        $this->userModel         = new User();
    }

    // ── Dashboard ──────────────────────────────────────────────────────────
    public function dashboard() {
        $user = currentUser();
        $stagiaire = $this->stagiaireModel->findByUserId($user['id']);
        $stageActif = $this->stagiaireModel->getStageActif($stagiaire['id']);

        $taches = [];
        $pointages = [];
        $assiduite = ['taux_presence' => 0, 'nb_presents' => 0, 'nb_absents' => 0, 'nb_retards' => 0];
        $evaluation = null;
        $pointageAujourdhui = null;

        if ($stageActif) {
            $taches = $this->tacheModel->getByStage($stageActif['id']);
            $pointages = $this->pointageModel->getByStage($stageActif['id']);
            $assiduite = $this->pointageModel->getStatistiquesAssiduite($stageActif['id']);
            $evaluation = $this->evaluationModel->getByStage($stageActif['id']);
            $pointageAujourdhui = $this->pointageModel->getByStagiaireAndDate($stagiaire['id'], date('Y-m-d'));
        }

        $notifications = $this->notificationModel->getByUser($user['id']);
        require_once ROOT_PATH . '/views/stagiaire/dashboard.php';
    }

    // ── Profil ─────────────────────────────────────────────────────────────
    public function profil() {
        $user = currentUser();
        $stagiaire = $this->stagiaireModel->findByUserId($user['id']);
        $userComplet = $this->userModel->findById($user['id']);
        $stageActif = $this->stagiaireModel->getStageActif($stagiaire['id']);
        require_once ROOT_PATH . '/views/stagiaire/profil.php';
    }

    public function updateProfil() {
        $user = currentUser();
        $stagiaire = $this->stagiaireModel->findByUserId($user['id']);

        // Update user data
        $userData = [
            'nom'       => trim($_POST['nom'] ?? ''),
            'prenom'    => trim($_POST['prenom'] ?? ''),
            'email'     => trim($_POST['email'] ?? ''),
            'telephone' => trim($_POST['telephone'] ?? '')
        ];

        if (empty($userData['nom']) || empty($userData['prenom']) || empty($userData['email'])) {
            setFlash('danger', 'Veuillez remplir tous les champs obligatoires.');
            redirect('stagiaire/profil');
        }

        $this->userModel->updateProfil($user['id'], $userData);

        // Update stagiaire data
        $stagData = [
            'cin'           => trim($_POST['cin'] ?? ''),
            'universite'    => trim($_POST['universite'] ?? ''),
            'filiere'       => trim($_POST['filiere'] ?? ''),
            'niveau_etudes' => trim($_POST['niveau_etudes'] ?? 'Bac +5'),
            'type_stage'    => $_POST['type_stage'] ?? 'PFE'
        ];
        $this->stagiaireModel->update($stagiaire['id'], $stagData);

        // Avatar upload
        if (!empty($_FILES['avatar']['name']) && $_FILES['avatar']['error'] === 0) {
            $file = $_FILES['avatar'];
            $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                $avatarDir = UPLOAD_PATH . '/avatars/';
                if (!is_dir($avatarDir)) mkdir($avatarDir, 0775, true);

                $filename = 'avatar_' . $user['id'] . '_' . time() . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], $avatarDir . $filename)) {
                    $this->userModel->updateAvatar($user['id'], $filename);
                    $_SESSION['user_avatar'] = $filename;
                }
            }
        }

        // Update session data
        $_SESSION['user_nom']    = strtoupper($userData['nom']);
        $_SESSION['user_prenom'] = ucfirst($userData['prenom']);
        $_SESSION['user_email']  = strtolower($userData['email']);

        setFlash('success', 'Profil mis à jour avec succès !');
        redirect('stagiaire/profil');
    }

    // ── Sujet ──────────────────────────────────────────────────────────────
    public function sujet() {
        $user = currentUser();
        $stagiaire = $this->stagiaireModel->findByUserId($user['id']);
        $stageActif = $this->stagiaireModel->getStageActif($stagiaire['id']);
        require_once ROOT_PATH . '/views/stagiaire/me_sujet.php';
    }

    // ── Tâches ─────────────────────────────────────────────────────────────
    public function taches() {
        $user = currentUser();
        $stagiaire = $this->stagiaireModel->findByUserId($user['id']);
        $stageActif = $this->stagiaireModel->getStageActif($stagiaire['id']);
        $taches = $stageActif ? $this->tacheModel->getByStage($stageActif['id']) : [];
        require_once ROOT_PATH . '/views/stagiaire/me_taches.php';
    }

    public function updateTache() {
        $tacheId    = (int)($_POST['tache_id'] ?? 0);
        $progression = (int)($_POST['progression'] ?? 0);
        $tache = $this->tacheModel->findById($tacheId);
        if ($tache) {
            $this->tacheModel->updateProgression($tacheId, $progression);
            setFlash('success', 'Progression de la tâche mise à jour avec succès.');
        } else {
            setFlash('danger', 'Tâche introuvable.');
        }
        redirect('stagiaire/taches');
    }

    // ── Pointage ───────────────────────────────────────────────────────────
    public function pointage() {
        $user = currentUser();
        $stagiaire = $this->stagiaireModel->findByUserId($user['id']);
        $stageActif = $this->stagiaireModel->getStageActif($stagiaire['id']);
        $pointages = $stageActif ? $this->pointageModel->getByStage($stageActif['id']) : [];
        $todayPointage = $this->pointageModel->getByStagiaireAndDate($stagiaire['id'], date('Y-m-d'));
        $stats = $stageActif ? $this->pointageModel->getStatistiquesAssiduite($stageActif['id']) : null;
        require_once ROOT_PATH . '/views/stagiaire/pointage.php';
    }

    public function doPointage() {
        $user = currentUser();
        $stagiaire = $this->stagiaireModel->findByUserId($user['id']);
        $stageActif = $this->stagiaireModel->getStageActif($stagiaire['id']);

        if (!$stageActif) {
            setFlash('danger', 'Aucun stage actif pour effectuer le pointage.');
            redirect('stagiaire/pointage');
        }

        $remarque = trim($_POST['remarque'] ?? '');
        $dateAujourdhui = date('Y-m-d');

        try {
            $this->pointageModel->pointer($stageActif['id'], $stagiaire['id'], $dateAujourdhui, 'present', $remarque);
            setFlash('success', 'Pointage du ' . date('d/m/Y') . ' enregistré avec succès (RG7) !');
        } catch (Exception $e) {
            setFlash('danger', 'Erreur lors du pointage : ' . $e->getMessage());
        }
        redirect('stagiaire/pointage');
    }

    // ── Documents ──────────────────────────────────────────────────────────
    public function documents() {
        $user = currentUser();
        $stagiaire = $this->stagiaireModel->findByUserId($user['id']);
        $stageActif = $this->stagiaireModel->getStageActif($stagiaire['id']);
        $documents = $this->documentModel->getDocumentsCourants($stagiaire['id']);
        require_once ROOT_PATH . '/views/stagiaire/documents.php';
    }

    public function uploadDocument() {
        $user = currentUser();
        $stagiaire = $this->stagiaireModel->findByUserId($user['id']);
        $stageActif = $this->stagiaireModel->getStageActif($stagiaire['id']);

        if (!$stageActif) {
            setFlash('danger', 'Vous devez avoir un stage actif pour déposer un document.');
            redirect('stagiaire/documents');
        }

        $titre   = trim($_POST['titre'] ?? '');
        $typeDoc = $_POST['type_document'] ?? 'autre';

        if (empty($titre) || empty($_FILES['fichier']['name'])) {
            setFlash('danger', 'Veuillez saisir un titre et sélectionner un fichier.');
            redirect('stagiaire/documents');
        }

        $file = $_FILES['fichier'];
        $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['pdf', 'doc', 'docx', 'png', 'jpg', 'jpeg'];

        if (!in_array($ext, $allowed)) {
            setFlash('danger', 'Format non autorisé. Acceptés : PDF, DOC, DOCX, PNG, JPG.');
            redirect('stagiaire/documents');
        }

        $filename  = time() . '_' . uniqid() . '.' . $ext;
        $uploadDir = UPLOAD_PATH . '/documents/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);

        if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            $this->documentModel->create($stagiaire['id'], $stageActif['id'], $titre, $typeDoc, $filename, $file['size']);

            // Notification encadrant
            $this->notificationModel->creer(
                $stageActif['encadrant_user_id'],
                "Nouveau document déposé",
                "Le stagiaire {$stagiaire['prenom']} {$stagiaire['nom']} a déposé le document '{$titre}'.",
                "encadrant/stagiaire_detail?id=" . $stageActif['id']
            );

            setFlash('success', 'Document déposé avec succès (RG10) !');
        } else {
            setFlash('danger', 'Erreur lors du téléchargement. Veuillez réessayer.');
        }
        redirect('stagiaire/documents');
    }

    public function deleteDocument() {
        $user = currentUser();
        $stagiaire = $this->stagiaireModel->findByUserId($user['id']);
        $docId = (int)($_POST['document_id'] ?? 0);

        if ($this->documentModel->delete($docId, $stagiaire['id'])) {
            setFlash('success', 'Document supprimé avec succès.');
        } else {
            setFlash('danger', 'Impossible de supprimer ce document.');
        }
        redirect('stagiaire/documents');
    }

    // ── Rapport Final ──────────────────────────────────────────────────────
    public function rapportFinal() {
        $user = currentUser();
        $stagiaire = $this->stagiaireModel->findByUserId($user['id']);
        $stageActif = $this->stagiaireModel->getStageActif($stagiaire['id']);
        $rapportFinal = $this->documentModel->getRapportFinal($stagiaire['id']);
        require_once ROOT_PATH . '/views/stagiaire/rapport_final.php';
    }

    public function uploadRapport() {
        $user = currentUser();
        $stagiaire = $this->stagiaireModel->findByUserId($user['id']);
        $stageActif = $this->stagiaireModel->getStageActif($stagiaire['id']);

        if (!$stageActif) {
            setFlash('danger', 'Vous devez avoir un stage actif pour déposer votre rapport final.');
            redirect('stagiaire/rapport_final');
        }

        if (empty($_FILES['rapport']['name']) || $_FILES['rapport']['error'] !== 0) {
            setFlash('danger', 'Veuillez sélectionner un fichier PDF pour votre rapport final.');
            redirect('stagiaire/rapport_final');
        }

        $file = $_FILES['rapport'];
        $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if ($ext !== 'pdf') {
            setFlash('danger', 'Le rapport final doit être un fichier PDF uniquement.');
            redirect('stagiaire/rapport_final');
        }

        if ($file['size'] > 20 * 1024 * 1024) { // 20 Mo max
            setFlash('danger', 'Le fichier est trop volumineux (maximum 20 Mo).');
            redirect('stagiaire/rapport_final');
        }

        $filename  = 'rapport_final_' . $stagiaire['id'] . '_' . time() . '.pdf';
        $uploadDir = UPLOAD_PATH . '/documents/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);

        if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            $this->documentModel->create(
                $stagiaire['id'],
                $stageActif['id'],
                'Rapport Final de Stage — ' . sanitize($stagiaire['prenom'] . ' ' . $stagiaire['nom']),
                'rapport_final',
                $filename,
                $file['size'],
                1 // mark rapport_final as permanent
            );

            // Notification encadrant
            $this->notificationModel->creer(
                $stageActif['encadrant_user_id'],
                "Rapport final déposé",
                "Le stagiaire {$stagiaire['prenom']} {$stagiaire['nom']} a soumis son rapport final de stage.",
                "encadrant/stagiaire_detail?id=" . $stageActif['id']
            );

            setFlash('success', 'Rapport final soumis avec succès ! Votre encadrant a été notifié.');
        } else {
            setFlash('danger', 'Erreur lors du téléchargement. Veuillez réessayer.');
        }
        redirect('stagiaire/rapport_final');
    }

    // ── Messages ───────────────────────────────────────────────────────────
    public function messages() {
        $user = currentUser();
        $stagiaire = $this->stagiaireModel->findByUserId($user['id']);
        $stageActif = $this->stagiaireModel->getStageActif($stagiaire['id']);

        $messages = [];
        if ($stageActif) {
            require_once ROOT_PATH . '/models/Message.php';
            $msgModel = new Message();
            $messages = $msgModel->getConversation($stageActif['id']);
            $msgModel->marquerCommeLus($stageActif['id'], $user['id']);
        }
        require_once ROOT_PATH . '/views/stagiaire/me_messages.php';
    }

    // ── Notifications ──────────────────────────────────────────────────────
    public function notifications() {
        $user = currentUser();
        $notifications = $this->notificationModel->getByUser($user['id']);
        // Marquer toutes comme lues
        $this->notificationModel->markAllRead($user['id']);
        require_once ROOT_PATH . '/views/stagiaire/me_notifications.php';
    }
}
