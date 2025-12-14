<?php 
    require_once __DIR__ . "../../config/Database.php";
    class Plan extends Database {

        public $plan_id = "";
        public $plan_name = "";
        public $description = "";
        public $duration_months = "";
        public $price = "";

        public function getAllPlans() {
            $sql = "SELECT * FROM membership_plans ORDER BY price ASC";

            $query = $this->connect()->prepare($sql);

            if($query->execute()) {
                return $query->fetchAll();
            } else {
                return null;
            }
        }
        public function getAllActivePlans() {
            $sql = "SELECT * FROM membership_plans WHERE status = 'active' ORDER BY price ASC";
            $query = $this->connect()->prepare($sql);

            if($query->execute()) {
                return $query->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return null;
            }
        }
        public function getUserPlan($user_id) {
            $sql = "SELECT p.plan_name, p.price, s.end_date, s.status FROM membership_plans p
            join subscriptions s on s.plan_id = p.plan_id
            where s.user_id = :user_id AND s.status = 'active'";

            $query = $this->connect()->prepare($sql);
            $query->bindParam(":user_id", $user_id);

            
            if($query->execute()) {
                $rows = $query->fetch();
                if($rows) {
                    return $rows;
                } else {
                    return null;
                }
            } else {
                return null;
            }       
        }
        public function addNewPlan($plan) {
            $sql = "INSERT INTO `membership_plans`(`plan_name`, `description`, `duration_months`, `price`) VALUES (:plan_name, :description , :duration_months , :price)";

            $query = $this->connect()->prepare($sql);
            $query->bindParam(":plan_name", $plan['plan_name']);
            $query->bindParam(":description", $plan['description']);
            $query->bindParam(":duration_months", $plan['duration_months']);
            $query->bindParam(":price", $plan['price']);

            if($query->execute()) {
                return true;
            } else {
                return false;
            }
        }

        public function updatePlan($plan_id, $data) {
            $sql = "UPDATE membership_plans SET 
                    plan_name = :plan_name, 
                    description = :description, 
                    duration_months = :duration_months, 
                    price = :price, 
                    status = :status 
                    WHERE plan_id = :plan_id";

            $query = $this->connect()->prepare($sql);
            $query->bindParam(":plan_name", $data['plan_name']);
            $query->bindParam(":description", $data['description']);
            $query->bindParam(":duration_months", $data['duration_months']);
            $query->bindParam(":price", $data['price']);
            $query->bindParam(":status", $data['status']);
            $query->bindParam(":plan_id", $plan_id);

            return $query->execute();
        }

        public function getPlanById($plan_id) {
            $sql = "SELECT * FROM membership_plans WHERE plan_id = :plan_id";
            
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":plan_id", $plan_id);
            
            if($query->execute()) {
                return $query->fetch(PDO::FETCH_ASSOC);
            }
            return null;
        }
    public function deletePlanViaId($plan_id) {
            $sql = "UPDATE membership_plans SET status = 'removed' WHERE plan_id = :plan_id";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":plan_id", $plan_id);

            return $query->execute();
        }
    }

?>