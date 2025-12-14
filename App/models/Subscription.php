<?php
    require_once __DIR__ . "../../config/Database.php";

    class Subscription extends Database {

        public function subscripePlan($subData) {   
            
            $sql = "INSERT INTO subscriptions(user_id, plan_id, start_date, end_date) VALUES (:user_id, :plan_id,:start_date, :end_date)";
             
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":user_id", $subData['user_id']);           
            $query->bindParam(":plan_id", $subData['plan_id']);
            $query->bindParam(":start_date", $subData['start_date']);
            $query->bindParam(":end_date", $subData['end_date']);
            
            if($query->execute()) {
                return true;
            } else {
                return false;
            }
        }
        public function cancelPlan($subscription_id) {
            $sql = "UPDATE subscriptions SET status = 'cancelled' WHERE subscription_id = :subscription_id";

            $query = $this->connect()->prepare($sql);
            $query->bindParam(":subscription_id", $subscription_id);

            if($query->execute()) {
                return true;
            } else {
                return false;
            }
        }

        public function expirePlan($subscription_id) {
            $sql = "UPDATE subscriptions SET status = 'expired' WHERE subscription_id = :subscription_id";

            $query = $this->connect()->prepare($sql);
            $query->bindParam(":subscription_id", $subscription_id);

            if($query->execute()) {
                return true;
            } else {
                return false;
            }
        }

        public function checkUserCurrentPlan($user_id) {
            $sql = "SELECT subscription_id FROM subscriptions WHERE user_id = :user_id AND status = 'active'";

            $query = $this->connect()->prepare($sql);
            $query->bindParam(":user_id", $user_id);

            if($query->execute()) {
                return $query->fetch(PDO::FETCH_ASSOC);
            } else {
                return null;
            }

        }

        public function getUserPayments() {
            $sql = "SELECT 
            CONCAT(m.first_name, ' ', m.last_name) as name, pt.transaction_id, mp.plan_name, p.amount, p.payment_date, p.status, p.payment_id
            FROM members m
            LEFT JOIN subscriptions s ON s.user_id = m.user_id
            LEFT JOIN membership_plans mp ON mp.plan_id = s.plan_id
            LEFT JOIN payments p ON p.subscription_id = s.subscription_id
            LEFT JOIN payment_transaction pt ON pt.payment_id = p.payment_id
            WHERE m.role = 'member'
            ORDER BY p.payment_date DESC";
            // WHERE p.status = 'pending'

            $query = $this->connect()->prepare($sql);
            if($query->execute()) {
                return $query->fetchAll();
            } else {
                return null;
            }
        }

        public function countTotalPayments() {
            $sql = "SELECT COUNT(*) as total_number_of_payments FROM payments";
            $query = $this->connect()->prepare($sql);
            if($query->execute()) {
                return $query->fetch();
            } else {
                return null;
            }
        }

        public function getExpiringSubscriptions($days = 7) {
            $sql = "SELECT 
                    COUNT(*) as expiring_count
                    FROM subscriptions 
                    WHERE status = 'active' 
                    AND end_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL :days DAY)";
            
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":days", $days, PDO::PARAM_INT);
            
            if($query->execute()) {
                return $query->fetch();
            }
            return ['expiring_count' => 0];
        }

        public function getSubscriptionStatusBreakdown() {
            $sql = "SELECT 
                    status,
                    COUNT(*) as count
                    FROM subscriptions
                    GROUP BY status";
            
            $query = $this->connect()->prepare($sql);
            
            if($query->execute()) {
                return $query->fetchAll(PDO::FETCH_ASSOC);
            }
            return [];
        }
        public function getSubscriptionById($subscription_id) {
            $sql = "SELECT * FROM subscriptions WHERE subscription_id = :subscription_id";
            
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":subscription_id", $subscription_id);
            
            if($query->execute()) {
                return $query->fetch(PDO::FETCH_ASSOC);
            }
            return null;
        }
        
        /**
         * Request a membership freeze
         * @param int $subscriptionId
         * @param int $userId
         * @param string $freezeStart
         * @param string $freezeEnd
         * @param string $reason
         * @return array [success, message, freeze_id]
         */
        public function requestFreeze($subscriptionId, $userId, $freezeStart, $freezeEnd, $reason = '') {
            // Check if subscription is active
            $subscription = $this->getSubscriptionById($subscriptionId);
            
            if (!$subscription) {
                return ['success' => false, 'message' => 'Subscription not found'];
            }
            
            if ($subscription['status'] !== 'active') {
                return ['success' => false, 'message' => 'Only active subscriptions can be frozen'];
            }
            
            if ($subscription['is_frozen']) {
                return ['success' => false, 'message' => 'Subscription is already frozen'];
            }
            
            // Validate dates
            if (strtotime($freezeStart) >= strtotime($freezeEnd)) {
                return ['success' => false, 'message' => 'End date must be after start date'];
            }
            
            // Check max freeze duration (e.g., 90 days)
            $maxDays = 90;
            $days = (strtotime($freezeEnd) - strtotime($freezeStart)) / 86400;
            if ($days > $maxDays) {
                return ['success' => false, 'message' => "Freeze period cannot exceed {$maxDays} days"];
            }
            
            // Insert freeze request
            $sql = "INSERT INTO membership_freeze_history 
                    (subscription_id, user_id, freeze_start, freeze_end, reason, status) 
                    VALUES (:subscription_id, :user_id, :freeze_start, :freeze_end, :reason, 'pending')";
            
            $query = $this->connect()->prepare($sql);
            $query->bindParam(':subscription_id', $subscriptionId);
            $query->bindParam(':user_id', $userId);
            $query->bindParam(':freeze_start', $freezeStart);
            $query->bindParam(':freeze_end', $freezeEnd);
            $query->bindParam(':reason', $reason);
            
            if ($query->execute()) {
                $freezeId = $this->connect()->lastInsertId();
                return [
                    'success' => true, 
                    'message' => 'Freeze request submitted for admin approval',
                    'freeze_id' => $freezeId
                ];
            }
            
            return ['success' => false, 'message' => 'Failed to submit freeze request'];
        }
        
        /**
         * Approve a freeze request (Admin only)
         * @param int $freezeId
         * @param int $adminUserId
         * @return bool
         */
        public function approveFreeze($freezeId, $adminUserId) {
            // Get freeze request
            $sql = "SELECT * FROM membership_freeze_history WHERE freeze_id = :freeze_id";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(':freeze_id', $freezeId);
            $query->execute();
            $freeze = $query->fetch(PDO::FETCH_ASSOC);
            
            if (!$freeze) {
                return false;
            }
            
            // Update freeze request status
            $sql = "UPDATE membership_freeze_history 
                    SET status = 'approved', approved_by = :admin_id, approved_at = NOW() 
                    WHERE freeze_id = :freeze_id";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(':freeze_id', $freezeId);
            $query->bindParam(':admin_id', $adminUserId);
            $query->execute();
            
            // Apply freeze to subscription
            $sql = "UPDATE subscriptions 
                    SET is_frozen = TRUE, freeze_start_date = :start_date, 
                        freeze_end_date = :end_date, freeze_reason = :reason 
                    WHERE subscription_id = :subscription_id";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(':subscription_id', $freeze['subscription_id']);
            $query->bindParam(':start_date', $freeze['freeze_start']);
            $query->bindParam(':end_date', $freeze['freeze_end']);
            $query->bindParam(':reason', $freeze['reason']);
            
            return $query->execute();
        }
        
        /**
         * Reject a freeze request (Admin only)
         * @param int $freezeId
         * @param int $adminUserId
         * @param string $notes
         * @return bool
         */
        public function rejectFreeze($freezeId, $adminUserId, $notes = '') {
            $sql = "UPDATE membership_freeze_history 
                    SET status = 'rejected', approved_by = :admin_id, 
                        approved_at = NOW(), admin_notes = :notes 
                    WHERE freeze_id = :freeze_id";
            
            $query = $this->connect()->prepare($sql);
            $query->bindParam(':freeze_id', $freezeId);
            $query->bindParam(':admin_id', $adminUserId);
            $query->bindParam(':notes', $notes);
            
            return $query->execute();
        }
        
        /**
         * Unfreeze a subscription (manual or automatic)
         * @param int $subscriptionId
         * @return bool
         */
        public function unfreezeSubscription($subscriptionId) {
            // Mark current freeze as completed
            $sql = "UPDATE membership_freeze_history 
                    SET status = 'completed' 
                    WHERE subscription_id = :subscription_id 
                    AND status = 'approved' 
                    AND freeze_end <= CURDATE()";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(':subscription_id', $subscriptionId);
            $query->execute();
            
            // Remove freeze from subscription
            $sql = "UPDATE subscriptions 
                    SET is_frozen = FALSE, freeze_start_date = NULL, 
                        freeze_end_date = NULL, freeze_reason = NULL 
                    WHERE subscription_id = :subscription_id";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(':subscription_id', $subscriptionId);
            
            return $query->execute();
        }
        
        /**
         * Check if user can request a freeze
         * @param int $userId
         * @return array [can_request, message]
         */
        public function canRequestFreeze($userId) {
            // Check for active subscription
            $subscription = $this->checkUserCurrentPlan($userId);
            if (!$subscription) {
                return ['can_request' => false, 'message' => 'No active subscription found'];
            }
            
            // Check if already frozen
            $sql = "SELECT is_frozen FROM subscriptions WHERE subscription_id = :subscription_id";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(':subscription_id', $subscription['subscription_id']);
            $query->execute();
            $sub = $query->fetch(PDO::FETCH_ASSOC);
            
            if ($sub['is_frozen']) {
                return ['can_request' => false, 'message' => 'Subscription is already frozen'];
            }
            
            // Check for pending requests
            $sql = "SELECT COUNT(*) as pending FROM membership_freeze_history 
                    WHERE user_id = :user_id AND status = 'pending'";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(':user_id', $userId);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);
            
            if ($result['pending'] > 0) {
                return ['can_request' => false, 'message' => 'You have a pending freeze request'];
            }
            
            return ['can_request' => true, 'message' => 'You can request a freeze'];
        }
        
        /**
         * Get freeze history for a user
         * @param int $userId
         * @return array
         */
        public function getFreezeHistory($userId) {
            $sql = "SELECT mfh.*, CONCAT(m.first_name, ' ', m.last_name) as approved_by_name
                    FROM membership_freeze_history mfh
                    LEFT JOIN members m ON mfh.approved_by = m.user_id
                    WHERE mfh.user_id = :user_id
                    ORDER BY mfh.requested_at DESC";
            
            $query = $this->connect()->prepare($sql);
            $query->bindParam(':user_id', $userId);
            
            if ($query->execute()) {
                return $query->fetchAll(PDO::FETCH_ASSOC);
            }
            return [];
        }
        
        /**
         * Get all pending freeze requests (Admin)
         * @return array
         */
        public function getPendingFreezeRequests() {
            $sql = "SELECT mfh.*, CONCAT(m.first_name, ' ', m.last_name) as member_name, 
                           m.email, mp.plan_name
                    FROM membership_freeze_history mfh
                    JOIN members m ON mfh.user_id = m.user_id
                    JOIN subscriptions s ON mfh.subscription_id = s.subscription_id
                    JOIN membership_plans mp ON s.plan_id = mp.plan_id
                    WHERE mfh.status = 'pending'
                    ORDER BY mfh.requested_at ASC";
            
            $query = $this->connect()->prepare($sql);
            
            if ($query->execute()) {
                return $query->fetchAll(PDO::FETCH_ASSOC);
            }
            return [];
        }
        
        /**
         * Process automatic unfreeze for expired freeze periods (Cron job)
         * @return int Number of subscriptions unfrozen
         */
        public function processAutomaticUnfreeze() {
            $sql = "SELECT DISTINCT subscription_id 
                    FROM subscriptions 
                    WHERE is_frozen = TRUE 
                    AND freeze_end_date <= CURDATE()";
            
            $query = $this->connect()->prepare($sql);
            $query->execute();
            $subscriptions = $query->fetchAll(PDO::FETCH_COLUMN);
            
            $count = 0;
            foreach ($subscriptions as $subId) {
                if ($this->unfreezeSubscription($subId)) {
                    $count++;
                }
            }
            
            return $count;
        }
    }
?>