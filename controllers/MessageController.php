<?php
require_once __DIR__ . '/../models/Message.php';
require_once __DIR__ . '/../models/Stage.php';
require_once __DIR__ . '/../models/Notification.php';

class MessageController {
    private $messageModel;
    private $stageModel;
    private $notificationModel;

    public function __construct() {
        requireLogin();
        $this->messageModel = new Message();
        $this->stageModel = new Stage();
        $this->notificationModel = new Notification();
    }

    public function send() {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');
        $user = currentUser();

        $stageId = (int)($_POST['stage_id'] ?? 0);
        $messageText = trim($_POST['message'] ?? '');

        if ($stageId <= 0 || empty($messageText)) {
            echo json_encode(['success' => false, 'error' => 'Message vide ou stage invalide.']);
            exit;
        }

        // Vérification d'accès côté serveur (RG9)
        $stage = $this->messageModel->verifyAccess($stageId, $user['id']);
        if (!$stage) {
            echo json_encode(['success' => false, 'error' => 'Accès refusé : vous ne faites pas partie de ce stage.']);
            exit;
        }

        // Déterminer le destinataire
        $destinataireId = ($user['id'] == $stage['stagiaire_user_id']) ? $stage['encadrant_user_id'] : $stage['stagiaire_user_id'];

        try {
            $msgId = $this->messageModel->envoyer($stageId, $user['id'], $destinataireId, $messageText);

            // Génération de la notification pour le destinataire
            $senderName = $user['prenom'] . ' ' . $user['nom'];
            $excerpt = mb_strlen($messageText) > 60 ? mb_substr($messageText, 0, 57) . '...' : $messageText;
            
            // Lien approprié selon le rôle du destinataire
            $destRole = ($destinataireId == $stage['stagiaire_user_id']) ? 'stagiaire' : 'encadrant';
            $notifLien = ($destRole === 'encadrant') ? "encadrant/messages?stage_id={$stageId}" : "stagiaire/messages";

            $this->notificationModel->creer(
                $destinataireId,
                "Nouveau message de {$senderName}",
                "{$senderName} : \"{$excerpt}\"",
                $notifLien
            );

            echo json_encode([
                'success' => true,
                'message' => [
                    'id' => $msgId,
                    'stage_id' => $stageId,
                    'expediteur_id' => $user['id'],
                    'destinataire_id' => $destinataireId,
                    'exp_nom' => $user['nom'],
                    'exp_prenom' => $user['prenom'],
                    'message' => htmlspecialchars($messageText, ENT_QUOTES, 'UTF-8'),
                    'lu' => 0,
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    public function get() {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');
        $user = currentUser();
        $stageId = (int)($_GET['stage_id'] ?? 0);

        if ($stageId <= 0) {
            echo json_encode(['success' => false, 'messages' => [], 'error' => 'Stage non spécifié.']);
            exit;
        }

        // Verification accès serveur (RG9)
        $stage = $this->messageModel->verifyAccess($stageId, $user['id']);
        if (!$stage) {
            echo json_encode(['success' => false, 'messages' => [], 'error' => 'Accès non autorisé à cette conversation (RG9).']);
            exit;
        }

        $messages = $this->messageModel->getConversation($stageId);
        $this->messageModel->marquerCommeLus($stageId, $user['id']);

        // Format Sanitization for safety
        $cleanMessages = array_map(function($m) {
            $m['message'] = htmlspecialchars($m['message'], ENT_QUOTES, 'UTF-8');
            return $m;
        }, $messages);

        echo json_encode([
            'success' => true,
            'messages' => $cleanMessages,
            'current_user_id' => $user['id']
        ]);
        exit;
    }
}
