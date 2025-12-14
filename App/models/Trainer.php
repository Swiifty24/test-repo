<?php
require_once __DIR__ . '/../config/Database.php';

class Trainer extends Database {

    public function getAllTrainers() {
        $sql = "SELECT t.trainer_id, m.user_id, CONCAT(m.first_name, ' ', m.last_name) as name, m.first_name, m.last_name, m.email, t.contact_no, t.specialization, t.experience_years, t.status, t.join_date FROM members m
        JOIN trainers t ON t.user_id = m.user_id 
        ";

        $query = $this->connect()->prepare($sql);
        if($query->execute()) {
            return $query->fetchAll();
        } else {
            return null;
        }
    }
    public function getTrainerById($trainer_id) {
        $sql = "SELECT m.user_id, t.trainer_id, m.first_name, m.last_name, m.middle_name, CONCAT(m.first_name, ' ', m.last_name) as name, m.email, t.specialization, t.experience_years, t.contact_no, t.status, t.join_date FROM members m
        JOIN trainers t ON t.user_id = m.user_id
        WHERE t.trainer_id = :trainer_id";

        $query = $this->connect()->prepare($sql);
        $query->bindParam(':trainer_id', $trainer_id);
        if($query->execute()) {
            return $query->fetch(PDO::FETCH_ASSOC);
        } else {
            return null;
        }
    }
    public function findById($trainerId) {
        $query = "SELECT u.user_id as trainer_id, CONCAT(u.first_name, ' ', u.last_name) as name, 
                  u.email, t.specialization, t.experience_years, t.contact_no, t.status, t.join_date
                  FROM members u
                  JOIN trainers t ON u.user_id = t.user_id
                  WHERE u.user_id = :trainer_id";
        
        $stmt = $this->connect()->prepare($query);
        $stmt->bindParam(':trainer_id', $trainerId);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAssignedMembers($trainerId) {
        $query = "SELECT u.user_id, CONCAT(u.first_name, ' ', u.last_name) as name, 
                  u.email, tm.assigned_date, tm.status
                  FROM members u
                  JOIN trainer_members tm ON u.user_id = tm.user_id
                  WHERE tm.trainer_id = :trainer_id AND tm.status = 'active'
                  ORDER BY tm.assigned_date DESC";
        
        $stmt = $this->connect()->prepare($query);
        $stmt->bindParam(':trainer_id', $trainerId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStats($trainerId) {
        $stats = [];
        
        // Total members
        $query = "SELECT COUNT(*) as count FROM trainer_members WHERE trainer_id = :trainer_id AND status = 'active'";
        $stmt = $this->connect()->prepare($query);
        $stmt->bindParam(':trainer_id', $trainerId);
        $stmt->execute();
        $stats['total_members'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // Upcoming sessions
        $query = "SELECT COUNT(*) as count FROM sessions WHERE trainer_id = :trainer_id AND status = 'scheduled' AND session_date >= NOW()";
        $stmt = $this->connect()->prepare($query);
        $stmt->bindParam(':trainer_id', $trainerId);
        $stmt->execute();
        $stats['upcoming_sessions'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // Completed this month
        $query = "SELECT COUNT(*) as count FROM sessions WHERE trainer_id = :trainer_id AND status = 'completed' AND MONTH(session_date) = MONTH(NOW())";
        $stmt = $this->connect()->prepare($query);
        $stmt->bindParam(':trainer_id', $trainerId);
        $stmt->execute();
        $stats['completed_sessions'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // Pending requests
        $query = "SELECT COUNT(*) as count FROM trainer_requests WHERE trainer_id = :trainer_id AND status = 'pending'";
        $stmt = $this->connect()->prepare($query);
        $stmt->bindParam(':trainer_id', $trainerId);
        $stmt->execute();
        $stats['pending_requests'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        return $stats;
    }

    public function getPendingRequests($trainerId) {
        $query = "SELECT tr.request_id, u.user_id, CONCAT(u.first_name, ' ', u.last_name) as member_name,
                  u.email, tr.created_at as request_date
                  FROM trainer_requests tr
                  JOIN members u ON tr.user_id = u.user_id
                  WHERE tr.trainer_id = :trainer_id AND tr.status = 'pending'
                  ORDER BY tr.created_at DESC";
        
        $stmt = $this->connect()->prepare($query);
        $stmt->bindParam(':trainer_id', $trainerId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function handleRequest($requestId, $action) {
        $status = $action === 'accepted' ? 'accepted' : 'rejected';
        
        $db = $this->connect();
        
        $db->beginTransaction();
        
        try {
            // Update request status
            $query = "UPDATE trainer_requests SET status = :status WHERE request_id = :request_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':request_id', $requestId);
            $stmt->execute();
            
            // If accepted, add to trainer_members
            if($action === 'accepted') {
                $query = "SELECT trainer_id, user_id FROM trainer_requests WHERE request_id = :request_id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':request_id', $requestId);
                $stmt->execute();
                $request = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $query = "INSERT INTO trainer_members (trainer_id, user_id, assigned_date, status) 
                          VALUES (:trainer_id, :user_id, NOW(), 'active')";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':trainer_id', $request['trainer_id']);
                $stmt->bindParam(':user_id', $request['user_id']);
                $stmt->execute();
            }
            
            $db->commit();
            return true;
        } catch(Exception $e) {
            $db->rollBack();
            return false;
        }
    }
    public function deleteTrainerViaId($trainer_id) {
        $sql = "UPDATE trainers SET status='inactive' WHERE trainer_id = :trainer_id";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":trainer_id", $trainer_id);

        if($query->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function hasPendingOrActiveRequest($userId, $trainerId) {
        $sql = "SELECT request_id FROM trainer_requests 
                WHERE user_id = :user_id AND trainer_id = :trainer_id 
                AND status IN ('pending','accepted')";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(':user_id', $userId);
        $query->bindParam(':trainer_id', $trainerId);
        $query->execute();
        return (bool) $query->fetch();
    }

    public function requestTrainer($userId, $trainerId, $note = '') {
        $sql = "INSERT INTO trainer_requests (user_id, trainer_id, note, status, created_at)
                VALUES (:user_id, :trainer_id, :note, 'pending', NOW())";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(':user_id', $userId);
        $query->bindParam(':trainer_id', $trainerId);
        $query->bindParam(':note', $note);
        return $query->execute();
    }
    
}