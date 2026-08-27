<?php
require_once __DIR__ . '/../models/Encadrant.php';
require_once __DIR__ . '/../models/Stagiaire.php';
require_once __DIR__ . '/../models/Stage.php';
require_once __DIR__ . '/../models/SujetStage.php';
require_once __DIR__ . '/../models/Pointage.php';
require_once __DIR__ . '/../models/Tache.php';
require_once __DIR__ . '/../models/Document.php';
require_once __DIR__ . '/../models/Evaluation.php';
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../models/Message.php';

class EncadrantController {
    private $encadrantModel;
    private $stagiaireModel;
    private $stageModel;
    private $sujetModel;
    private $pointageModel;
    private $tacheModel;
    private $documentModel;
    private $evaluationModel;
    private $notificationModel;
    private $messageModel;

    public function __construct() {
        requireRole('encadrant');
        $this->encadrantModel = new Encadrant();
        $this->stagiaireModel = new Stagiaire();
        $this->stageModel = new Stage();
        $this->sujetModel = new SujetStage();
        $this->pointageModel = new Pointage();
        $this->tacheModel = new Tache();
        $this->documentModel = new Document();
        $this->evaluationModel = new Evaluation();
        $this->notificationModel = new Notification();
        $this->messageModel = new Message();
    }

    public function dashboard() {
        $user = currentUser();
        $encadrant = $this->encadrantModel->findByUserId($user['id']);

        $stats = $this->encadrantModel->getDashboardStats($encadrant['id']);
        $stagiaires = $this->encadrantModel->getStagiaires($encadrant['id']);
        $sujets = $this->sujetModel->getByEncadrant($encadrant['id']);

        require_once ROOT_PATH . '/views/encadrant/dashboard.php';
    }

    public function stagiaires() {
        $user = currentUser();
        $encadrant = $this->encadrantModel->findByUserId($user['id']);

        $search = $_GET['search'] ?? null;
        $typeStage = $_GET['type_stage'] ?? null;
        $statut = $_GET['statut'] ?? null;

        $stagiaires = $this->encadrantModel->getStagiaires($encadrant['id'], $search, $typeStage, $statut);
        $sujets = $this->sujetModel->getByEncadrant($encadrant['id']);

        require_once ROOT_PATH . '/views/encadrant/me_stagiaires.php';
    }

    public function stagiaireDetail() {
        $user = currentUser();
        $encadrant = $this->encadrantModel->findByUserId($user['id']);
        $stageId = (int)($_GET['id'] ?? 0);

        $stage = $this->stageModel->getDetailsComplets($stageId);

        // Verification RG5: L'encadrant ne peut consulter que ses propres stagiaires affectés
        if (!$stage || $stage['encadrant_id'] != $encadrant['id']) {
            setFlash('danger', 'Accès refusé : ce stagiaire ne fait pas partie de vos affectations.');
            redirect('encadrant/stagiaires');
        }

        $pointages = $this->pointageModel->getByStage($stageId);
        $assiduite = $this->pointageModel->getStatistiquesAssiduite($stageId);
        $taches = $this->tacheModel->getByStage($stageId);
        $documents = $this->documentModel->getByStage($stageId);
        $evaluation = $this->evaluationModel->getByStage($stageId);

        require_once ROOT_PATH . '/views/encadrant/stagiaire_detail.php';
    }

    public function assignerStage() {
        $user = currentUser();
        $encadrant = $this->encadrantModel->findByUserId($user['id']);

        $cin = strtoupper(trim($_POST['cin'] ?? ''));
        $sujetId = (int)($_POST['sujet_id'] ?? 0);
        $dateDebut = $_POST['date_debut'] ?? date('Y-m-d');
        $dateFin = $_POST['date_fin'] ?? date('Y-m-d', strtotime('+3 months'));

        $stagiaire = $this->stagiaireModel->findByCin($cin);
        if (!$stagiaire) {
            setFlash('danger', "Aucun stagiaire trouvé avec le CIN : {$cin}. Veuillez vérifier la saisie.");
            redirect('encadrant/stagiaires');
        }

        try {
            $stageId = $this->stageModel->create($stagiaire['id'], $encadrant['id'], $sujetId, $dateDebut, $dateFin);
            
            // Notification au stagiaire
            $this->notificationModel->creer(
                $stagiaire['user_id'],
                "Stage Assigné avec succès",
                "Vous avez été affecté à un nouveau sujet sous l'encadrement de M. {$user['nom']}.",
                "stagiaire/sujet"
            );

            setFlash('success', "Le stage a été assigné avec succès à {$stagiaire['prenom']} {$stagiaire['nom']} !");
        } catch (Exception $e) {
            setFlash('danger', $e->getMessage());
        }

        redirect('encadrant/stagiaires');
    }

    public function terminerStage() {
        $user = currentUser();
        $encadrant = $this->encadrantModel->findByUserId($user['id']);
        $stageId = (int)($_POST['stage_id'] ?? 0);

        $stage = $this->stageModel->getDetailsComplets($stageId);
        
        // Verification RG5: Seul l'encadrant assigné peut valider le stage
        if (!$stage || $stage['encadrant_id'] != $encadrant['id']) {
            setFlash('danger', 'Accès non autorisé (RG5) : Vous n’êtes pas l’encadrant titulaire de ce stage.');
            redirect('encadrant/stagiaires');
        }

        try {
            $this->stageModel->updateStatut($stageId, 'termine');

            // Notification au stagiaire (RG6)
            $this->notificationModel->creer(
                $stage['stagiaire_user_id'],
                "Stage Validé & Terminé (RG6)",
                "Félicitations ! Votre stage a été validé et marqué comme terminé par votre encadrant M. {$user['nom']}.",
                "stagiaire/dashboard"
            );

            setFlash('success', "Le stage de {$stage['stagiaire_prenom']} {$stage['stagiaire_nom']} a été validé et marqué comme terminé avec succès (RG6) !");
        } catch (Exception $e) {
            setFlash('danger', $e->getMessage());
        }

        redirect('encadrant/stagiaire_detail?id=' . $stageId);
    }

    public function sujets() {
        $user = currentUser();
        $encadrant = $this->encadrantModel->findByUserId($user['id']);
        $sujets = $this->sujetModel->getByEncadrant($encadrant['id']);

        require_once ROOT_PATH . '/views/encadrant/me_sujets.php';
    }

    public function createSujet() {
        $user = currentUser();
        $encadrant = $this->encadrantModel->findByUserId($user['id']);

        $titre = trim($_POST['titre'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $domaine = trim($_POST['domaine'] ?? 'Informatique / Digital');

        if (!empty($titre) && !empty($description)) {
            $this->sujetModel->create($encadrant['id'], $titre, $description, $domaine);
            setFlash('success', 'Le sujet de stage a été ajouté avec succès.');
        } else {
            setFlash('danger', 'Veuillez renseigner le titre et la description du sujet.');
        }

        redirect('encadrant/sujets');
    }

    public function deleteSujet() {
        $user = currentUser();
        $encadrant = $this->encadrantModel->findByUserId($user['id']);
        $sujetId = (int)($_POST['sujet_id'] ?? 0);

        $sujet = $this->sujetModel->findById($sujetId);
        if (!$sujet || $sujet['encadrant_id'] != $encadrant['id']) {
            setFlash('danger', 'Accès refusé : sujet introuvable ou non autorisé.');
            redirect('encadrant/sujets');
        }

        // Vérifier s'il y a des stages utilisant ce sujet
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT COUNT(*) as c FROM stages WHERE sujet_id = :sujet_id");
        $stmt->execute([':sujet_id' => $sujetId]);
        $count = (int)$stmt->fetchColumn();
        if ($count > 0) {
            setFlash('danger', 'Impossible de supprimer : ce sujet est utilisé par un ou plusieurs stages.');
            redirect('encadrant/sujets');
        }

        if ($this->sujetModel->delete($sujetId, $encadrant['id'])) {
            setFlash('success', 'Sujet supprimé avec succès.');
        } else {
            setFlash('danger', 'Erreur lors de la suppression du sujet.');
        }

        redirect('encadrant/sujets');
    }

    public function evaluations() {
        $user = currentUser();
        $encadrant = $this->encadrantModel->findByUserId($user['id']);

        $stagiaires = $this->encadrantModel->getStagiaires($encadrant['id']);
        require_once ROOT_PATH . '/views/encadrant/evaluations.php';
    }

    public function saveEvaluation() {
        $user = currentUser();
        $encadrant = $this->encadrantModel->findByUserId($user['id']);

        $stageId = (int)($_POST['stage_id'] ?? 0);
        $noteTech = (int)($_POST['note_technique'] ?? 5);
        $noteComp = (int)($_POST['note_comportement'] ?? 5);
        $noteAssid = (int)($_POST['note_assiduite'] ?? 5);
        $commentaire = trim($_POST['commentaire'] ?? '');

        $stage = $this->stageModel->getDetailsComplets($stageId);
        if ($stage && $stage['encadrant_id'] == $encadrant['id']) {
            $this->evaluationModel->createOrUpdate($stageId, $encadrant['id'], $noteTech, $noteComp, $noteAssid, $commentaire);

            // Notification
            $this->notificationModel->creer(
                $stage['stagiaire_user_id'],
                "Évaluation de stage enregistrée",
                "Votre encadrant M. {$user['nom']} a enregistré votre évaluation de stage.",
                "stagiaire/dashboard"
            );

            setFlash('success', 'Évaluation de stage enregistrée avec succès (RG8).');
        } else {
            setFlash('danger', 'Erreur : vous n’êtes pas l’encadrant titulaire de ce stage.');
        }

        redirect('encadrant/stagiaire_detail?id=' . $stageId);
    }

    public function pointages() {
        $user = currentUser();
        $encadrant = $this->encadrantModel->findByUserId($user['id']);

        $stagiaires = $this->encadrantModel->getStagiaires($encadrant['id']);
        $selectedStageId = (int)($_GET['stage_id'] ?? ($stagiaires[0]['stage_id'] ?? 0));

        $pointages = [];
        $stageCurrent = null;
        if ($selectedStageId > 0) {
            $stageCurrent = $this->stageModel->getDetailsComplets($selectedStageId);
            if ($stageCurrent && $stageCurrent['encadrant_id'] == $encadrant['id']) {
                $pointages = $this->pointageModel->getByStage($selectedStageId);
            }
        }

        require_once ROOT_PATH . '/views/encadrant/pointages.php';
    }

    public function validerPointage() {
        $user = currentUser();
        $encadrant = $this->encadrantModel->findByUserId($user['id']);

        $stageId = (int)($_POST['stage_id'] ?? 0);
        $datePointage = $_POST['date_pointage'] ?? date('Y-m-d');
        $statut = $_POST['statut'] ?? 'present';
        $remarque = trim($_POST['remarque'] ?? '');

        $stage = $this->stageModel->getDetailsComplets($stageId);
        if ($stage && $stage['encadrant_id'] == $encadrant['id']) {
            $this->pointageModel->pointer($stageId, $stage['stagiaire_id'], $datePointage, $statut, $remarque);
            setFlash('success', 'Pointage enregistré / mis à jour avec succès (RG7).');
        } else {
            setFlash('danger', 'Action non autorisée.');
        }

        redirect('encadrant/pointages?stage_id=' . $stageId);
    }

    public function creerTache() {
        $user = currentUser();
        $encadrant = $this->encadrantModel->findByUserId($user['id']);

        $stageId = (int)($_POST['stage_id'] ?? 0);
        $titre = trim($_POST['titre'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $dateLimite = $_POST['date_limite'] ?? date('Y-m-d', strtotime('+1 week'));

        $stage = $this->stageModel->getDetailsComplets($stageId);
        if ($stage && $stage['encadrant_id'] == $encadrant['id']) {
            $this->tacheModel->create($stageId, $titre, $description, $dateLimite);
            
            // Notification
            $this->notificationModel->creer(
                $stage['stagiaire_user_id'],
                "Nouvelle tâche assignée",
                "Tâche '{$titre}' à rendre pour le {$dateLimite}.",
                "stagiaire/taches"
            );

            setFlash('success', 'Nouvelle tâche assignée au stagiaire.');
        } else {
            setFlash('danger', 'Action non autorisée.');
        }

        redirect('encadrant/stagiaire_detail?id=' . $stageId);
    }

    public function messages() {
        $user = currentUser();
        $encadrant = $this->encadrantModel->findByUserId($user['id']);

        $stagiaires = $this->encadrantModel->getStagiaires($encadrant['id']);
        $selectedStageId = (int)($_GET['stage_id'] ?? ($stagiaires[0]['stage_id'] ?? 0));

        $unreadCounts = $this->messageModel->getUnreadCountPerStage($user['id']);

        $messages = [];
        $activeStage = null;

        if ($selectedStageId > 0) {
            $activeStage = $this->stageModel->getDetailsComplets($selectedStageId);
            if ($activeStage && $activeStage['encadrant_id'] == $encadrant['id']) {
                $messages = $this->messageModel->getConversation($selectedStageId);
                $this->messageModel->marquerCommeLus($selectedStageId, $user['id']);
            }
        }

        require_once ROOT_PATH . '/views/encadrant/me_messages.php';
    }

    public function notifications() {
        $user = currentUser();
        $notifications = $this->notificationModel->getByUser($user['id']);
        $this->notificationModel->markAllRead($user['id']);
        require_once ROOT_PATH . '/views/encadrant/me_notifications.php';
    }

    public function markAllNotif() {
        $user = currentUser();
        $this->notificationModel->markAllRead($user['id']);
        setFlash('success', 'Toutes les notifications ont été marquées comme lues.');
        redirect('encadrant/notifications');
    }

    public function profil() {
        $user = currentUser();
        $userModel = new User();
        $userComplet = $userModel->findById($user['id']);
        $encadrant = $this->encadrantModel->findByUserId($user['id']);

        require_once ROOT_PATH . '/views/encadrant/profil.php';
    }

    public function updateProfil() {
        $user = currentUser();
        $userModel = new User();
        $encadrant = $this->encadrantModel->findByUserId($user['id']);

        // Mise à jour infos utilisateur
        $userModel->updateProfil($user['id'], [
            'nom'       => $_POST['nom'] ?? '',
            'prenom'    => $_POST['prenom'] ?? '',
            'email'     => $_POST['email'] ?? '',
            'telephone' => $_POST['telephone'] ?? ''
        ]);

        // Mise à jour infos encadrant
        $this->encadrantModel->update($encadrant['id'], [
            'departement' => $_POST['departement'] ?? '',
            'fonction'    => $_POST['fonction'] ?? ''
        ]);

        // Mise à jour session
        $_SESSION['user_nom']    = strtoupper(trim($_POST['nom'] ?? ''));
        $_SESSION['user_prenom'] = ucfirst(trim($_POST['prenom'] ?? ''));
        $_SESSION['user_email']  = strtolower(trim($_POST['email'] ?? ''));

        // Upload avatar
        if (!empty($_FILES['avatar']['name'])) {
            $file = $_FILES['avatar'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($ext, $allowed) && $file['size'] <= 2 * 1024 * 1024) {
                $avatarDir = UPLOAD_PATH . '/avatars/';
                if (!is_dir($avatarDir)) mkdir($avatarDir, 0755, true);
                $filename = 'avatar_' . $user['id'] . '_' . time() . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], $avatarDir . $filename)) {
                    $userModel->updateAvatar($user['id'], $filename);
                    $_SESSION['user_avatar'] = $filename;
                }
            }
        }

        setFlash('success', 'Votre profil a été mis à jour avec succès !');
        redirect('encadrant/profil');
    }
}

