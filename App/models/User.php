<?php 
    require_once __DIR__ . "../../config/Database.php";
    

    class User extends Database {
        public $user_id = "";
        public $first_name = "";
        public $last_name = "";
        public $middle_name = "";
        public $email = "";
        public $password = "";
        public $role = "";
        public $created_at = "";

        protected $db;

        public function getAllMembers() {
            $sql = "SELECT user_id FROM members";

            $query = $this->connect()->prepare($sql);


            if($query->execute()) {
                return $query->fetchAll();
            } else {
                return null;
            }
        }
        public function displayAllUsers() {
            $sql = "SELECT 
                    m.user_id, CONCAT(m.first_name, ' ', m.last_name) as name, m.email, m.created_at, p.plan_name, s.end_date, m.status 
                    FROM members m 
                    LEFT JOIN subscriptions s on s.user_id = m.user_id 
                    LEFT JOIN membership_plans p ON p.plan_id = s.plan_id 
                    WHERE role = 'member' GROUP BY m.user_id  ORDER BY m.created_at DESC";

            $query = $this->connect()->prepare($sql);

            if($query->execute()) {
                return $query->fetchAll();
            } else {
                return null;
            }
        }

        public function displayAllWalkInMembers() {
            $sql = "SELECT walkin_id, CONCAT(first_name, ' ', last_name) as name, email, contact_no, session_type, payment_amount, visit_time, end_date FROM walk_ins";

            $query = $this->connect()->prepare($sql);

            if($query->execute()) {
                return $query->fetchAll();
            } else {
                return null;
            }


        }

        public function findByEmail($email) {
            $sql = "SELECT user_id, role, email, password, status FROM members WHERE email = :email";

            $query = $this->connect()->prepare($sql);
            $query->bindParam(":email", $email);

            if($query->execute()) {
                return $query->fetch();
            } else {
                return null;
            }

        }
        public function getMember($user_id) {
            $sql = "SELECT 
                    user_id, CONCAT(first_name, ' ', last_name) as name, first_name, last_name, phone_no, email, role, created_at, profile_picture 
                    FROM members 
                    WHERE user_id = :user_id";

            $query = $this->connect()->prepare($sql);
            $query->bindParam(":user_id", $user_id);

            if($query->execute()) {
                return $query->fetch();
            } else {
                return null;
            }
        }

        public function getMemberSubcription($user_id) {
            $sql = "SELECT 
                    CONCAT(m.first_name, ' ', m.last_name) as name, m.phone_no, m.created_at, p.plan_name, s.end_date, s.status FROM members m
                    JOIN subscriptions s ON s.user_id = m.user_id
                    JOIN membership_plans p ON p.plan_id = s.plan_id
                    WHERE m.user_id = :user_id";

            $query = $this->connect()->prepare($sql);
            $query->bindParam(":user_id", $user_id);

            if($query->execute()) {
                return $query->fetch();
            } else {
                return null;
            }
        }

        public function getMemberDetailsById($userId) {
             $sql = "SELECT m.user_id, m.first_name, m.middle_name, m.last_name, m.email, m.role, m.status, m.created_at, m.profile_picture, p.plan_name FROM members m JOIN subscriptions s ON s.user_id = m.user_id JOIN membership_plans p ON p.plan_id = s.plan_id WHERE m.user_id = :user_id";

            $query = $this->connect()->prepare($sql);
            $query->bindParam(":user_id", $userId);

            if($query->execute()) {
                return $query->fetch();
            } else {
                return null;
            }
        }
        public function addMember($data) {
            $sql = "INSERT INTO members (first_name, last_name, middle_name, email, phone_no, date_of_birth, gender, password, role, created_at, status, valid_id_picture) 
                    VALUES (:first_name, :last_name, :middle_name, :email, :phone_no, :date_of_birth, :gender, :password, :role, :created_at, 'pending_approval', :valid_id_picture)";
            
            $db = $this->connect(); // Capture connection
            $query = $db->prepare($sql);
            
            $created_at = date('Y-m-d');
            $role = 'member';
            
            $query->bindParam(":first_name", $data['first_name']);
            $query->bindParam(":last_name", $data['last_name']);
            $query->bindParam(":middle_name", $data['middle_name']);
            $query->bindParam(":email", $data['email']);
            $query->bindParam(":phone_no", $data['phone_no']);
            $query->bindParam(":date_of_birth", $data['date_of_birth']);
            $query->bindParam(":gender", $data['gender']);
            $query->bindParam(":password", $data['password']);
            $query->bindParam(":role", $role);
            $query->bindParam(":created_at", $created_at);
            $query->bindParam(":valid_id_picture", $data['valid_id_picture']);
            
            if($query->execute()) {
                $user_id = $db->lastInsertId(); // Use same connection
                return $this->addUserAddress($user_id, $data['zip'], $data['street_address'], $data['city']);
            }
            return false;
        }
        
        // Helper function for adding user address, called by addMember
        public function addUserAddress($user_id, $zip, $street, $city) {
            $db = $this->connect();
            try {
                $db->beginTransaction();

                // 1. Insert or Update the Address Table
                // Uses ON DUPLICATE KEY UPDATE to handle if the ZIP already exists
                $sqlAddr = "INSERT INTO member_address (zip, street_address, city) 
                            VALUES (:zip, :street, :city) 
                            ON DUPLICATE KEY UPDATE street_address = :street, city = :city";
                
                $stmtAddr = $db->prepare($sqlAddr);
                $stmtAddr->bindParam(':zip', $zip);
                $stmtAddr->bindParam(':street', $street);
                $stmtAddr->bindParam(':city', $city);
                $stmtAddr->execute();

                // 2. Update the Link Table to point to this Zip
                // Uses INSERT ... ON DUPLICATE KEY UPDATE in case the link doesn't exist yet
                $sqlLink = "INSERT INTO member_address_link (user_id, zip, address_type) 
                            VALUES (:user_id, :zip, 'Home') 
                            ON DUPLICATE KEY UPDATE zip = :zip";
                
                $stmtLink = $db->prepare($sqlLink);
                $stmtLink->bindParam(':user_id', $user_id);
                $stmtLink->bindParam(':zip', $zip);
                $stmtLink->execute();

                $db->commit();
                return true;
            } catch (Exception $e) {
                $db->rollBack();
                // Optional: Log error $e->getMessage();
                return false;
            }
        }
        public function addWalkinMember($userData) {
            $sql = "INSERT INTO 
            walk_ins(first_name, last_name, middle_name, email, contact_no, session_type, payment_method, payment_amount, visit_time, end_date) 
            VALUES 
            (:first_name, :last_name, :middle_name, :email, :contact_no, :session_type, :payment_method ,:payment_amount, :visit_time, :end_date)";

            $query = $this->connect()->prepare($sql);
            $query->bindParam(":first_name", $userData['first_name']);
            $query->bindParam(":last_name", $userData['last_name']);
            $query->bindParam(":middle_name", $userData['middle_name']);
            $query->bindParam(":email", $userData['email']);
            $query->bindParam(":contact_no", $userData['contact_no']);
            $query->bindParam(":session_type", $userData['session_type']);
            $query->bindParam(":payment_method", $userData['payment_method']);
            $query->bindParam(":payment_amount", $userData['payment_amount']);
            $query->bindParam(":visit_time", $userData['visit_time']);
            $query->bindParam(":end_date", $userData['end_date']);

            if($query->execute()) {
                return true;
            } else {
                return false;
            }

        } 
        
        public function getWalkinById($walkin_id) {
            $sql = "SELECT * FROM walk_ins WHERE walkin_id = :walkin_id";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":walkin_id", $walkin_id);

            if($query->execute()) {
                return $query->fetch();
            } else {
                return null;
            }
        }

        public function updateWalkinMember($data, $walkin_id) {
            $sql = "UPDATE walk_ins SET 
                    first_name = :first_name,
                    last_name = :last_name,
                    middle_name = :middle_name,
                    email = :email,
                    contact_no = :contact_no,
                    session_type = :session_type,
                    payment_method = :payment_method,
                    payment_amount = :payment_amount,
                    visit_time = :visit_time,
                    end_date = :end_date
                    WHERE walkin_id = :walkin_id";

            $query = $this->connect()->prepare($sql);
            
            $query->bindParam(":first_name", $data['first_name']);
            $query->bindParam(":last_name", $data['last_name']);
            $query->bindParam(":middle_name", $data['middle_name']);
            $query->bindParam(":email", $data['email']);
            $query->bindParam(":contact_no", $data['contact_no']);
            $query->bindParam(":session_type", $data['session_type']);
            $query->bindParam(":payment_method", $data['payment_method']);
            $query->bindParam(":payment_amount", $data['payment_amount']);
            $query->bindParam(":visit_time", $data['visit_time']);
            $query->bindParam(":end_date", $data['end_date']);
            $query->bindParam(":walkin_id", $walkin_id);

            return $query->execute();
        }
        public function getMemberData() {
            $user_id = $_GET['user_id'];

            $sql = "SELECT m.*, mp.plan_name 
                    FROM members m 
                    LEFT JOIN subscriptions s ON s.user_id = m.user_id 
                    LEFT JOIN membership_plans mp ON mp.plan_id = s.plan_id 
                    WHERE m.user_id = :user_id";

            $query = $this->connect()->prepare($sql);
            $query->bindParam(":user_id", $user_id);

            if($query->execute()) {
                echo json_encode([
                    'success' => true,
                    'data' => $query->fetch(),
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'Data' => NULL,
                ]);
            }
        }

        public function updateMemberProfile($user_id, $data) {
            // Update only personal info, excluding role/status for security
            $sql = "UPDATE members SET 
                    first_name = :first_name, 
                    last_name = :last_name, 
                    middle_name = :middle_name, 
                    email = :email, 
                    phone_no = :phone_no,
                    profile_picture = :profile_picture
                    WHERE user_id = :user_id";

            $query = $this->connect()->prepare($sql);
            $query->bindParam(":first_name", $data['first_name']);
            $query->bindParam(":last_name", $data['last_name']);
            $query->bindParam(":middle_name", $data['middle_name']);
            $query->bindParam(":email", $data['email']);
            $query->bindParam(":phone_no", $data['phone_no']);
            $query->bindParam(":profile_picture", $data['profile_picture']);
            $query->bindParam(":user_id", $user_id);

            return $query->execute();
        }

        public function updateUserAddress($user_id, $zip, $street, $city) {
            $db = $this->connect();
            try {
                $db->beginTransaction();

                // 1. Insert or Update the Address Table
                // Uses ON DUPLICATE KEY UPDATE to handle if the ZIP already exists
                $sqlAddr = "INSERT INTO member_address (zip, street_address, city) 
                            VALUES (:zip, :street, :city) 
                            ON DUPLICATE KEY UPDATE street_address = :street, city = :city";
                
                $stmtAddr = $db->prepare($sqlAddr);
                $stmtAddr->bindParam(':zip', $zip);
                $stmtAddr->bindParam(':street', $street);
                $stmtAddr->bindParam(':city', $city);
                $stmtAddr->execute();

                // 2. Update the Link Table to point to this Zip
                // Uses INSERT ... ON DUPLICATE KEY UPDATE in case the link doesn't exist yet
                $sqlLink = "INSERT INTO member_address_link (user_id, zip, address_type) 
                            VALUES (:user_id, :zip, 'Home') 
                            ON DUPLICATE KEY UPDATE zip = :zip";
                
                $stmtLink = $db->prepare($sqlLink);
                $stmtLink->bindParam(':user_id', $user_id);
                $stmtLink->bindParam(':zip', $zip);
                $stmtLink->execute();

                $db->commit();
                return true;
            } catch (Exception $e) {
                $db->rollBack();
                return false;
            }
        }

        public function updateMemberViaUserId($userData, $user_id) {
            $sql = "UPDATE members SET first_name=:first_name, last_name=:last_name, middle_name=:middle_name, email=:email, password=:password, role=:role , status=:status WHERE user_id = :user_id";

            $query = $this->connect()->prepare($sql);
            $query->bindParam(":first_name", $userData['first_name']);
            $query->bindParam(":last_name", $userData['last_name']);
            $query->bindParam(":middle_name", $userData['middle_name']);
            $query->bindParam(":email", $userData['email']);
            $query->bindParam(":password", $userData['password']);
            $query->bindParam(":role", $userData['role']);
            $query->bindParam(":status", $userData['status']);
            $query->bindParam(":user_id", $user_id);
            if($query->execute()) {
                return true;
            } else {
                return false;
            }
        }

        public function deleteMemberViaId($user_id) {
            $sql = "UPDATE members SET status='inactive' WHERE user_id = :user_id";
            
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":user_id", $user_id);

            if($query->execute()) {
                return true;
            } else {
                return false;
            }
        }

        public function countActiveMembers() {
            $sql = "SELECT COUNT(*) as active_member_count FROM members";

            $query = $this->connect()->prepare($sql);

            if($query->execute()) {
                return $query->fetch();
            } else {
                return 0;
            }
        }

        public function getTrainerId($user_id) {
            $sql = "SELECT trainer_id 
            FROM trainers 
            WHERE user_id = :user_id";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":user_id", $user_id);

            if($query->execute()){
                return $query->fetch();
            } else {
                return null;
            }
        }
        
        public function getMemberGrowthLast12Months() {
            $sql = "SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') as month,
                    DATE_FORMAT(created_at, '%b %Y') as month_label,
                    COUNT(*) as new_members
                    FROM members 
                    WHERE role = 'member'
                    AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                    GROUP BY DATE_FORMAT(created_at, '%Y-%m'), DATE_FORMAT(created_at, '%b %Y')
                    ORDER BY month ASC";
            
            $query = $this->connect()->prepare($sql);
            
            if($query->execute()) {
                return $query->fetchAll(PDO::FETCH_ASSOC);
            }
            return [];
        }

        public function getActiveInactiveCount() {
            $sql = "SELECT 
                    SUM(CASE WHEN status='active' THEN 1 ELSE 0 END) as active_count,
                    SUM(CASE WHEN status='inactive' THEN 1 ELSE 0 END) as inactive_count
                    FROM members 
                    WHERE role = 'member'";
            
            $query = $this->connect()->prepare($sql);
            
            if($query->execute()) {
                return $query->fetch();
            }
            return ['active_count' => 0, 'inactive_count' => 0];
        }

        public function getMembersByPlan() {
            $sql = "SELECT 
                    mp.plan_name,
                    COUNT(DISTINCT s.user_id) as member_count
                    FROM membership_plans mp
                    LEFT JOIN subscriptions s ON s.plan_id = mp.plan_id AND s.status = 'active'
                    WHERE mp.status = 'active'
                    GROUP BY mp.plan_id, mp.plan_name
                    ORDER BY member_count DESC";
            
            $query = $this->connect()->prepare($sql);
            
            if($query->execute()) {
                return $query->fetchAll(PDO::FETCH_ASSOC);
            }
            return [];
        }

        public function getRetentionRate() {
            $sql = "SELECT 
                    COUNT(CASE WHEN status='active' THEN 1 END) as active,
                    COUNT(*) as total
                    FROM members 
                    WHERE role = 'member'
                    AND created_at <= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
            
            $query = $this->connect()->prepare($sql);
            
            if($query->execute()) {
                $result = $query->fetch();
                $rate = $result['total'] > 0 ? ($result['active'] / $result['total']) * 100 : 0;
                return [
                    'active' => $result['active'],
                    'total' => $result['total'],
                    'rate' => round($rate, 2)
                ];
            }
            return ['active' => 0, 'total' => 0, 'rate' => 0];
        }
        public function getMemberGrowth($startDate, $endDate) {
            // Determine grouping based on date range
            $days = (strtotime($endDate) - strtotime($startDate)) / 86400;
            
            if ($days <= 31) {
                $dateFormat = '%Y-%m-%d';
                $labelFormat = '%b %d';
            } elseif ($days <= 90) {
                $dateFormat = '%Y-%u';
                $labelFormat = 'Week %u';
            } else {
                $dateFormat = '%Y-%m';
                $labelFormat = '%b %Y';
            }
            
            $sql = "SELECT 
                    DATE_FORMAT(created_at, :date_format) as period,
                    DATE_FORMAT(created_at, :label_format) as period_label,
                    COUNT(*) as new_members
                    FROM members 
                    WHERE role = 'member'
                    AND DATE(created_at) BETWEEN :start_date AND :end_date
                    GROUP BY DATE_FORMAT(created_at, :date_format), DATE_FORMAT(created_at, :label_format)
                    ORDER BY period ASC";
            
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":date_format", $dateFormat);
            $query->bindParam(":label_format", $labelFormat);
            $query->bindParam(":start_date", $startDate);
            $query->bindParam(":end_date", $endDate);
            
            if($query->execute()) {
                return $query->fetchAll(PDO::FETCH_ASSOC);
            }
            return [];
        }

        public function getMemberAddress($user_id) {
            $sql = "SELECT ma.street_address, ma.city, ma.zip 
                    FROM member_address_link mal 
                    JOIN member_address ma ON mal.zip = ma.zip 
                    WHERE mal.user_id = :user_id 
                    LIMIT 1";
                    
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":user_id", $user_id);
            
            if($query->execute()) {
                return $query->fetch();
            }
            return null;
        }
    
    public function deleteWalkinViaId($walkin_id) {
        $sql = "DELETE FROM walk_ins WHERE walkin_id = :walkin_id";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":walkin_id", $walkin_id);

        if($query->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function getPendingMembers() {
        $sql = "SELECT m.user_id, CONCAT(m.first_name, ' ', m.last_name) as name, m.email, m.created_at, m.valid_id_picture 
                FROM members m 
                WHERE m.status = 'pending_approval'
                ORDER BY m.created_at DESC";

        $query = $this->connect()->prepare($sql);

        if($query->execute()) {
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return [];
        }
    }

    public function approveMemberStatus($user_id) {
        $sql = "UPDATE members SET status = 'active' WHERE user_id = :user_id";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":user_id", $user_id);
        return $query->execute();
    }

    public function rejectMemberStatus($user_id) {
        $sql = "UPDATE members SET status = 'rejected' WHERE user_id = :user_id";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":user_id", $user_id);
        return $query->execute();
    }
}

?>