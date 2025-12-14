
<?php
require_once __DIR__ . '/../config/Database.php';

class Session extends Database {


    public function create($data) {
        $query = "INSERT INTO sessions (user_id, trainer_id, session_date, notes, status, created_at) 
                  VALUES (:user_id, :trainer_id, :session_date, :notes, :status, NOW())";
        
        $stmt = $this->connect()->prepare($query);
        $stmt->bindParam(':user_id', $data['user_id']);
        $stmt->bindParam(':trainer_id', $data['trainer_id']);
        $stmt->bindParam(':session_date', $data['session_date']);
        $stmt->bindParam(':notes', $data['notes']);
        $stmt->bindParam(':status', $data['status']);
        
        if($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function getUpcomingByTrainer($trainerId) {
        $query = "SELECT s.session_id, s.user_id, s.session_date, s.status,
                  CONCAT(u.first_name, ' ', u.last_name) as member_name, u.email
                  FROM sessions s
                  JOIN members u ON s.user_id = u.user_id
                  WHERE s.trainer_id = :trainer_id 
                  AND s.session_date >= NOW()
                  AND s.status IN ('scheduled', 'completed', 'cancelled')
                  ORDER BY s.session_date ASC";
        
        $stmt = $this->connect()->prepare($query);
        $stmt->bindParam(':trainer_id', $trainerId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus($sessionId, $status) {
        $query = "UPDATE sessions SET status = :status WHERE session_id = :session_id";
        
        $stmt = $this->connect()->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':session_id', $sessionId);
        
        return $stmt->execute();
    }

    public function getById($sessionId) {
        $query = "SELECT * FROM sessions WHERE session_id = :session_id";
        
        $stmt = $this->connect()->prepare($query);
        $stmt->bindParam(':session_id', $sessionId);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByUser($userId) {
        $query = "SELECT s.*, CONCAT(u.first_name, ' ', u.last_name) as trainer_name
                  FROM sessions s
                  JOIN members u ON s.trainer_id = u.user_id
                  WHERE s.user_id = :user_id
                  ORDER BY s.session_date DESC";
        
        $stmt = $this->connect()->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserSessions($user_id) {
        $sql = "SELECT CONCAT(m.first_name, ' ', m.last_name) as trainer_name, s.session_date FROM trainers t
            JOIN members m ON m.user_id = t.user_id
            JOIN sessions s ON s.trainer_id = t.trainer_id
            WHERE s.user_id = :user_id";
            
        $query = $this->connect()->prepare($sql);

        $query->bindParam(":user_id", $user_id);

        if($query->execute()) {
            return $query->fetchAll();
        } else {
            return null;
        }
    }
}