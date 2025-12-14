<?php
require_once __DIR__ . "/../Controller.php";
require_once __DIR__ . "/../models/notification.php";

class NotificationController extends Controller {
    private $notificationModel;
    
    public function __construct() {
        $this->notificationModel = new Notification();
    }
    
    /**
     * Get notifications for current user (AJAX endpoint)
     */
    public function getNotifications() {
        session_start();
        header('Content-Type: application/json');
        
        if(!isset($_SESSION['user_id'])) {
            echo json_encode(['error' => 'Unauthorized']);
            exit();
        }
        
        $unreadOnly = isset($_GET['unread_only']) && $_GET['unread_only'] == 'true';
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
        
        $notifications = $this->notificationModel->getUserNotifications(
            $_SESSION['user_id'], 
            $limit, 
            $unreadOnly
        );
        
        echo json_encode($notifications);
    }
    
    /**
     * Get unread count (AJAX endpoint)
     */
    public function getUnreadCount() {
        session_start();
        header('Content-Type: application/json');
        
        if(!isset($_SESSION['user_id'])) {
            echo json_encode(['count' => 0]);
            exit();
        }
        
        $count = $this->notificationModel->getUnreadCount($_SESSION['user_id']);
        echo json_encode(['count' => $count]);
    }
    
    /**
     * Mark notification as read
     */
    public function markAsRead() {
        session_start();
        header('Content-Type: application/json');
        if(!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit();
        }
        
        $notificationId = isset($_POST['notification_id']) ? (int)$_POST['notification_id'] : 0;
        
        if($notificationId > 0) {
            $success = $this->notificationModel->markAsRead($notificationId, $_SESSION['user_id']);
            echo json_encode(['success' => $success]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid notification ID']);
        }
    }
    
    /**
     * Mark all as read
     */
    public function markAllAsRead() {
        session_start();
        header('Content-Type: application/json');
        
        if(!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit();
        }
        
        $success = $this->notificationModel->markAllAsRead($_SESSION['user_id']);
        echo json_encode(['success' => $success]);
    }
}
?>