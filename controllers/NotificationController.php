<?php
require_once __DIR__ . '/../models/Notification.php';

class NotificationController {
    private $notificationModel;

    public function __construct() {
        requireLogin();
        $this->notificationModel = new Notification();
    }

    public function markRead() {
        header('Content-Type: application/json');
        $user = currentUser();
        $notifId = (int)($_POST['id'] ?? 0);

        if ($notifId > 0) {
            $this->notificationModel->marquerCommeLue($notifId, $user['id']);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    }
}
