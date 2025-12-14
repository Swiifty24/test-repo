<?php

require_once __DIR__ . "/../config/Database.php";
class Notification extends Database {
    
    public function create($userId, $title, $message, $type = 'info', $category = 'general', $link = null) {
        $sql = "INSERT INTO notifications (user_id, title, message, type, category, link) 
                VALUES (:user_id, :title, :message, :type, :category, :link)";
        
        $stmt = $this->connect()->prepare($sql);
        return $stmt->execute([
            ':user_id' => $userId,
            ':title' => $title,
            ':message' => $message,
            ':type' => $type,
            ':category' => $category,
            ':link' => $link
        ]);
    }

    public function getUserNotifications($userId, $limit = 20, $unreadOnly = false) {
        $sql = "SELECT * FROM notifications WHERE user_id = :user_id";
        
        if($unreadOnly) {
            $sql .= " AND is_read = 0";
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT :limit";
        
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function getUnreadCount($userId) {
        $sql = "SELECT COUNT(*) as count FROM notifications 
                WHERE user_id = :user_id AND is_read = 0";
        
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'];
    }

    public function markAsRead($notificationId, $userId) {
        $sql = "UPDATE notifications 
                SET is_read = 1, read_at = NOW() 
                WHERE notification_id = :notification_id 
                AND user_id = :user_id";
        
        $stmt = $this->connect()->prepare($sql);
        return $stmt->execute([
            ':notification_id' => $notificationId,
            ':user_id' => $userId
        ]);
    }

    public function markAllAsRead($userId) {
        $sql = "UPDATE notifications 
                SET is_read = 1, read_at = NOW() 
                WHERE user_id = :user_id AND is_read = 0";
        
        $stmt = $this->connect()->prepare($sql);
        return $stmt->execute([':user_id' => $userId]);
    }
    

    public function deleteOldNotifications($days = 30) {
        $sql = "DELETE FROM notifications 
                WHERE created_at < DATE_SUB(NOW(), INTERVAL :days DAY) 
                AND is_read = 1";
        
        $stmt = $this->connect()->prepare($sql);
        return $stmt->execute([':days' => $days]);
    }
    
    public function notifyByRole($role, $title, $message, $type = 'info', $category = 'general', $link = null) {
        $sql = "INSERT INTO notifications (user_id, title, message, type, category, link)
                SELECT user_id, :title, :message, :type, :category, :link
                FROM members WHERE role = :role AND is_active = 1";
        
        $stmt = $this->connect()->prepare($sql);
        return $stmt->execute([
            ':title' => $title,
            ':message' => $message,
            ':type' => $type,
            ':category' => $category,
            ':link' => $link,
            ':role' => $role
        ]);
    }
}
?>