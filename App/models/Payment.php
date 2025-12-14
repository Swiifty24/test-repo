<?php 
    require_once __DIR__ . "/../config/Database.php";

    class Payment extends Database {

        private $last_transaction_id = "";

        public function openPayment($subData) {
            $sql = "INSERT INTO payments (subscription_id, amount, payment_date, status) VALUES (
            :subscription_id, :amount, :payment_date, :status)";

            $query = $this->connect()->prepare($sql);
            $query->bindParam(":subscription_id", $subData['subscription_id']);
            $query->bindParam(":amount", $subData['amount']);
            $query->bindParam(":payment_date", $subData['payment_date']);
            $query->bindParam(":status", $subData['status']);

            if($query->execute()){
                return true;
            } else {
                return false;
            }

        }

        public function totalEarned() {
            $sql = "SELECT SUM(amount) as total_earned FROM payments WHERE status='paid'";

            $query = $this->connect()->prepare($sql);

            if($query->execute()) {
                return $query->fetch();
            } else {
                return 0;
            }
        }

        public function getPaymentDetails($user_id) {
            $sql = "SELECT 
            s.subscription_id, mp.plan_name, s.start_date, s.end_date, 
            p.amount, p.payment_id, p.payment_date, p.status 
            FROM subscriptions s 
            JOIN membership_plans mp ON mp.plan_id = s.plan_id
            JOIN payments p ON p.subscription_id = s.subscription_id
            WHERE s.user_id = :user_id ORDER BY status DESC";

            $query = $this->connect()->prepare($sql);
            $query->bindParam(":user_id", $user_id);

            if($query->execute()) {
                return $query->fetchAll();
            } else {
                return null;
            }
        }

        public function completePayment($paymentDetails) {
            try {
                $sql = "INSERT INTO payment_transaction(
                            subscription_id, payment_id, payment_method,
                            transaction_type, payment_status, remarks
                        )
                        VALUES (
                            :subscription_id, :payment_id, :payment_method,
                            :transaction_type, :payment_status, :remarks
                        )";

                $query = $this->connect()->prepare($sql);
                $query->bindParam(":subscription_id", $paymentDetails['subscription_id']);
                $query->bindParam(":payment_id", $paymentDetails['payment_id']);
                $query->bindParam(":payment_method", $paymentDetails['payment_method']);
                $query->bindParam(":transaction_type", $paymentDetails['transaction_type']);
                $query->bindParam(":payment_status", $paymentDetails['payment_status']);
                $query->bindParam(":remarks", $paymentDetails['remarks']);

                if ($query->execute()) {
                    $transaction_id = $this->connect()->lastInsertId();

                    // Mark payment as paid
                    $updated = $this->markPaid($paymentDetails['payment_id']);

                    if ($updated) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } catch (PDOException $e) {
                return false;
            }
        }


        public function markPaid($payment_id) {
            $sql = "UPDATE payments SET status= 'paid' WHERE payment_id = :payment_id";

            $query = $this->connect()->prepare($sql);
            
            $query->bindParam(":payment_id", $payment_id);

            if($query->execute()) {
                return true;
            } else {
                return false;
            }
        }

        public function getPaymentId($subscription_id) {
            $sql = "SELECT payment_id, amount
                    FROM payments
                    WHERE subscription_id = :subscription_id";

            $query = $this->connect()->prepare($sql);
            
            $query->bindParam(":subscription_id", $subscription_id);

            if($query->execute()) {
                return $query->fetch();
            } else {
                return null;
            }
        }

        public function getMonthlyRevenue($year = null, $month = null) {
            $year = $year ?? date('Y');
            $month = $month ?? date('m');
            
            $sql = "SELECT SUM(amount) as monthly_revenue 
                    FROM payments 
                    WHERE status='paid' 
                    AND YEAR(payment_date) = :year 
                    AND MONTH(payment_date) = :month";
            
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":year", $year);
            $query->bindParam(":month", $month);
            
            if($query->execute()) {
                return $query->fetch();
            } else {
                return ['monthly_revenue' => 0];
            }
        }

        public function getLast12MonthsRevenue() {
            $sql = "SELECT 
                    DATE_FORMAT(payment_date, '%Y-%m') as month,
                    DATE_FORMAT(payment_date, '%b %Y') as month_label,
                    SUM(amount) as revenue
                    FROM payments 
                    WHERE status='paid'  
                    AND payment_date >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                    GROUP BY DATE_FORMAT(payment_date, '%Y-%m'), DATE_FORMAT(payment_date, '%b %Y')
                    ORDER BY month ASC";
            
            $query = $this->connect()->prepare($sql);
            
            if($query->execute()) {
                return $query->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return [];
            }
        }

        public function getRevenueByPlan() {
            $sql = "SELECT mp.plan_name, SUM(p.amount) as total_revenue, COUNT(*) as payment_count 
                    FROM payments p
                    JOIN subscriptions s ON s.subscription_id = p.subscription_id
                    JOIN membership_plans mp ON mp.plan_id = s.plan_id
                    WHERE p.status = 'paid'
                    GROUP BY mp.plan_id, mp.plan_name
                    ORDER BY total_revenue DESC";
            
            $query = $this->connect()->prepare($sql);
            
            if($query->execute()) {
                return $query->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return [];
            }
        }

        public function getPendingPayments() {
            $sql = "SELECT COUNT(*) as pending_count, COALESCE(SUM(amount), 0) as pending_amount 
                    FROM payments 
                    WHERE status='pending'";
            
            $query = $this->connect()->prepare($sql);
            
            if($query->execute()) {
                return $query->fetch();
            } else {
                return ['pending_count' => 0, 'pending_amount' => 0];
            }
        }

        public function getUserPendingPayments($userId) {
            $sql = "SELECT COUNT(s.subscription_id) FROM subscriptions s 
                JOIN payments p ON p.subscription_id = s.subscription_id
                WHERE s.user_id = :user_id AND p.status = 'pending'
            ";

            $query = $this->connect()->prepare($sql);
            $query->bindParam(":user_id", $userId);
            if($query->execute()) {
                return $query->fetch();
            } else {
                return 0;
            }
        }

        public function getPaymentStats() {
            $sql = "SELECT 
                    COUNT(*) as total_transactions,
                    SUM(CASE WHEN status='paid' THEN 1 ELSE 0 END) as paid_count,
                    SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END) as pending_count,
                    SUM(CASE WHEN status='failed' THEN 1 ELSE 0 END) as failed_count,
                    COALESCE(SUM(CASE WHEN status='paid' THEN amount ELSE 0 END), 0) as total_paid,
                    COALESCE(SUM(CASE WHEN status='pending' THEN amount ELSE 0 END), 0) as total_pending
                    FROM payments";
            
            $query = $this->connect()->prepare($sql);
            
            if($query->execute()) {
                return $query->fetch();
            } else {
                return null;
            }
        }

        public function getDailyRevenueLast30Days() {
            $sql = "SELECT 
                    DATE(payment_date) as date,
                    DATE_FORMAT(payment_date, '%b %d') as date_label,
                    COALESCE(SUM(amount), 0) as revenue
                    FROM payments 
                    WHERE status='paid' 
                    AND payment_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                    GROUP BY DATE(payment_date), DATE_FORMAT(payment_date, '%b %d')
                    ORDER BY date ASC";
            
            $query = $this->connect()->prepare($sql);
            
            if($query->execute()) {
                return $query->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return [];
            }
        }

        public function getPaymentMethodStats() {
            $sql = "SELECT 
                    pt.payment_method,
                    COUNT(*) as transaction_count,
                    SUM(p.amount) as total_amount
                    FROM payments p
                    JOIN payment_transaction pt ON pt.payment_id = p.payment_id
                    WHERE p.status = 'paid'
                    GROUP BY pt.payment_method
                    ORDER BY total_amount DESC";
            
            $query = $this->connect()->prepare($sql);
            
            if($query->execute()) {
                return $query->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return [];
            }
        }

        public function getRevenueByDateRange($start_date, $end_date) {
            $sql = "SELECT 
                    DATE(payment_date) as date,
                    DATE_FORMAT(payment_date, '%b %d') as date_label,
                    COALESCE(SUM(amount), 0) as revenue
                    FROM payments 
                    WHERE status='paid' 
                    AND DATE(payment_date) BETWEEN :start_date AND :end_date
                    GROUP BY DATE(payment_date), DATE_FORMAT(payment_date, '%b %d')
                    ORDER BY date ASC";
            
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":start_date", $start_date);
            $query->bindParam(":end_date", $end_date);
            
            if($query->execute()) {
                return $query->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return [];
            }
        }
        public function getPaymentBySubscriptionId($subscription_id) {
            $sql = "SELECT * FROM payments WHERE subscription_id = :subscription_id ORDER BY payment_date DESC LIMIT 1";
            
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":subscription_id", $subscription_id);
            
            if($query->execute()) {
                return $query->fetch(PDO::FETCH_ASSOC);
            } else {
                return null;
            }
        }
        public function getDailyRevenue($startDate, $endDate) {
            $sql = "SELECT 
                    DATE(payment_date) as date,
                    DATE_FORMAT(payment_date, '%b %d') as date_label,
                    COALESCE(SUM(amount), 0) as revenue
                    FROM payments 
                    WHERE status='paid' 
                    AND DATE(payment_date) BETWEEN :start_date AND :end_date
                    GROUP BY DATE(payment_date), DATE_FORMAT(payment_date, '%b %d')
                    ORDER BY date ASC";
            
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":start_date", $startDate);
            $query->bindParam(":end_date", $endDate);
            
            if($query->execute()) {
                return $query->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return [];
            }
        }
        public function getRevenueTrend($startDate, $endDate) {
            // Determine grouping based on date range
            $days = (strtotime($endDate) - strtotime($startDate)) / 86400;
            
            if ($days <= 31) {
                // Daily grouping for up to 31 days
                $dateFormat = '%Y-%m-%d';
                $labelFormat = '%b %d';
            } elseif ($days <= 90) {
                // Weekly grouping for up to 90 days
                $dateFormat = '%Y-%u';
                $labelFormat = 'Week %u';
            } else {
                // Monthly grouping for longer periods
                $dateFormat = '%Y-%m';
                $labelFormat = '%b %Y';
            }
            
            $sql = "SELECT 
                    DATE_FORMAT(payment_date, :date_format) as period,
                    DATE_FORMAT(payment_date, :label_format) as period_label,
                    SUM(amount) as revenue
                    FROM payments 
                    WHERE status='paid' 
                    AND DATE(payment_date) BETWEEN :start_date AND :end_date
                    GROUP BY DATE_FORMAT(payment_date, :date_format), DATE_FORMAT(payment_date, :label_format)
                    ORDER BY period ASC";
            
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":date_format", $dateFormat);
            $query->bindParam(":label_format", $labelFormat);
            $query->bindParam(":start_date", $startDate);
            $query->bindParam(":end_date", $endDate);
            
            if($query->execute()) {
                return $query->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return [];
            }
        }
        public function getPaymentStatsFiltered($startDate, $endDate) {
            $sql = "SELECT 
                    COUNT(*) as total_transactions,
                    SUM(CASE WHEN status='paid' THEN 1 ELSE 0 END) as paid_count,
                    SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END) as pending_count,
                    SUM(CASE WHEN status='failed' THEN 1 ELSE 0 END) as failed_count,
                    COALESCE(SUM(CASE WHEN status='paid' THEN amount ELSE 0 END), 0) as total_paid,
                    COALESCE(SUM(CASE WHEN status='pending' THEN amount ELSE 0 END), 0) as total_pending
                    FROM payments
                    WHERE DATE(payment_date) BETWEEN :start_date AND :end_date";
            
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":start_date", $startDate);
            $query->bindParam(":end_date", $endDate);
            
            if($query->execute()) {
                return $query->fetch();
            } else {
                return null;
            }
        }

        public function getPaymentById($payment_id) {
            $sql = "SELECT p.*, mp.plan_name, CONCAT(m.first_name, ' ', m.last_name) as member_name 
                    FROM payments p 
                    JOIN subscriptions s ON s.subscription_id = p.subscription_id
                    JOIN membership_plans mp ON mp.plan_id = s.plan_id
                    JOIN members m ON m.user_id = s.user_id
                    WHERE p.payment_id = :payment_id";
            
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":payment_id", $payment_id);
            
            if($query->execute()) {
                return $query->fetch(PDO::FETCH_ASSOC);
            } else {
                return null;
            }
        }

        public function updatePaymentStatus($payment_id, $status) {
            $sql = "UPDATE payments SET status = :status WHERE payment_id = :payment_id";
            
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":status", $status);
            $query->bindParam(":payment_id", $payment_id);
            
            return $query->execute();
        }

    }
    
?>