<?php 
require_once __DIR__ . "/../models/notification.php";
require_once __DIR__ . "/../config/Database.php";
class NotificationHelper {
    private static $notificationModel;
    
    private static function getModel() {
        if(self::$notificationModel === null) {
            self::$notificationModel = new Notification();
        }
        return self::$notificationModel;
    }
    private static function notify($userId, $title, $message, $type = 'info', $category = 'general', $link = null) {
        try {
            // Make sure Notification model exists
            if(!class_exists('Notification')) {
                error_log("Notification model not found");
                return false;
            }
            
            $notificationModel = new Notification();
            return $notificationModel->create($userId, $title, $message, $type, $category, $link);
            
        } catch(Exception $e) {
            error_log("Notification creation error: " . $e->getMessage());
            return false;
        }
    }
    // ===== MEMBERSHIP NOTIFICATIONS =====
    
    public static function membershipExpiring($userId, $daysLeft) {
        return self::getModel()->create(
            $userId,
            'Membership Expiring Soon',
            "Your membership will expire in $daysLeft days. Please renew to continue enjoying our services.",
            'warning',
            'membership',
            'index.php?controller=member&action=renewMembership'
        );
    }

    public static function welcome($userId, $firstName) {
        return self::notify(
            $userId,
            "Welcome to Gymazing, $firstName!",
            'Your account has been created successfully. Complete your profile to get started with your fitness journey.',
            'success',
            'general',
            'index.php?controller=member&action=profile'
        );
    }

    public static function profileUpdated($userId) {
        return self::notify(
            $userId,
            'Profile Updated',
            'Your profile has been updated successfully.',
            'success',
            'general'
        );
    }

    public static function passwordChanged($userId) {
        return self::notify(
            $userId,
            'Password Changed',
            'Your password has been changed successfully. If this wasn\'t you, please contact support immediately.',
            'warning',
            'general'
        );
    }

    public static function settingsUpdated($userId, $settingChanged = 'settings') {
        return self::notify(
            $userId,
            'Settings Updated',
            "Your $settingChanged have been updated successfully.",
            'success',
            'general'
        );
    }
    
    public static function membershipExpired($userId) {
        return self::getModel()->create(
            $userId,
            'Membership Expired',
            'Your membership has expired. Please renew to continue using the gym.',
            'error',
            'membership',
            'index.php?controller=member&action=renewMembership'
        );
    }
    
    public static function membershipRenewed($userId, $newExpiryDate) {
        return self::getModel()->create(
            $userId,
            'Membership Renewed',
            "Your membership has been successfully renewed until $newExpiryDate.",
            'success',
            'membership'
        );
    }
    
    // ===== PAYMENT NOTIFICATIONS =====
    
    public static function paymentReceived($userId, $amount) {
        return self::getModel()->create(
            $userId,
            'Payment Received',
            "We have received your payment of ₱$amount. Thank you!",
            'success',
            'payment',
            'index.php?controller=member&action=paymentHistory'
        );
    }
    
    public static function paymentDue($userId, $amount, $dueDate) {
        return self::getModel()->create(
            $userId,
            'Payment Due',
            "You have a pending payment of ₱$amount due on $dueDate.",
            'warning',
            'payment',
            'index.php?controller=member&action=makePayment'
        );
    }
    
    // ===== BOOKING NOTIFICATIONS =====
    
    public static function bookingConfirmed($userId, $sessionType, $date, $time) {
        return self::getModel()->create(
            $userId,
            'Booking Confirmed',
            "Your $sessionType session on $date at $time has been confirmed.",
            'success',
            'booking',
            'index.php?controller=member&action=myBookings'
        );
    }
    
    public static function bookingCancelled($userId, $sessionType, $date) {
        return self::getModel()->create(
            $userId,
            'Booking Cancelled',
            "Your $sessionType session on $date has been cancelled.",
            'warning',
            'booking',
            'index.php?controller=member&action=myBookings'
        );
    }
    
    public static function sessionReminder($userId, $sessionType, $time) {
        return self::getModel()->create(
            $userId,
            'Session Reminder',
            "Reminder: You have a $sessionType session at $time today.",
            'info',
            'schedule'
        );
    }
    
    // ===== TRAINER NOTIFICATIONS =====
    
    public static function newClientAssigned($trainerId, $clientName) {
        return self::getModel()->create(
            $trainerId,
            'New Client Assigned',
            "You have been assigned a new client: $clientName.",
            'info',
            'trainer',
            'index.php?controller=trainer&action=clients'
        );
    }
    
    public static function sessionScheduled($trainerId, $clientName, $date) {
        return self::getModel()->create(
            $trainerId,
            'New Session Scheduled',
            "A new session with $clientName has been scheduled for $date",
            'info',
            'schedule',
            'index.php?controller=trainer&action=schedule'
        );
    }
    
    // ===== ADMIN NOTIFICATIONS =====
    
    public static function newMemberRegistered($adminId, $memberName) {
        return self::getModel()->create(
            $adminId,
            'New Member Registered',
            "A new member has registered: $memberName.",
            'info',
            'general',
            'index.php?controller=admin&action=members'
        );
    }
    
    public static function paymentReceived_Admin($adminId, $memberName, $amount) {
        return self::getModel()->create(
            $adminId,
            'Payment Received',
            "$memberName has made a payment of ₱$amount.",
            'success',
            'payment',
            'index.php?controller=admin&action=payments'
        );
    }
    public static function trainerAccountCreated($userId, $specialization) {
    return self::notify(
        $userId,
        'Welcome to the Trainer Team!',
        "Your trainer account has been created. You're now specializing in $specialization. Access your trainer dashboard to get started.",
        'success',
        'trainer',
        'index.php?controller=trainer&action=dashboard'
    );
}

    public static function trainerPromoted($userId, $specialization) {
        try {
            return self::notify(
                $userId,
                'You\'ve Been Promoted to Trainer!',
                "Congratulations! You've been promoted to a trainer role with specialization in $specialization. Check your new dashboard to explore your trainer features.",
                'success',
                'trainer',
                'index.php?controller=trainer&action=dashboard'
            );
        } catch(Exception $e) {
            error_log("trainerPromoted error: " . $e->getMessage());
            return false;
        }
    }

    
    public static function trainerDeactivated($userId) {
        return self::notify(
            $userId,
            'Trainer Account Deactivated',
            'Your trainer account has been deactivated. Please contact administration if you have any questions.',
            'warning',
            'trainer'
        );
    }

    public static function trainerReactivated($userId) {
        return self::notify(
            $userId,
            'Trainer Account Reactivated',
            'Great news! Your trainer account has been reactivated. You can now access all trainer features.',
            'success',
            'trainer',
            'index.php?controller=trainer&action=dashboard'
        );
    }
    
    public static function notifyAllAdmins($title, $message, $link = null) {
        try {
            return self::notifyByRole('admin', $title, $message, $link);
        } catch(Exception $e) {
            error_log("notifyAllAdmins error: " . $e->getMessage());
            return false;
        }
    }

    public static function notifyByRole($role, $title, $message, $link = null) {
        try {
            require_once __DIR__ . '/../config/Database.php';
            
            $database = new Database();
            $conn = $database->connect();
            
            // Get all users with specified role
            $stmt = $conn->prepare("SELECT user_id FROM members WHERE role = ? AND is_active = 1");
            $stmt->execute([$role]);
            $users = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            if(empty($users)) {
                error_log("No users found with role: $role");
                return 0;
            }
            
            $notificationModel = new Notification();
            $successCount = 0;
            
            foreach($users as $userId) {
                try {
                    if($notificationModel->create($userId, $title, $message, 'info', 'general', $link)) {
                        $successCount++;
                    }
                } catch(Exception $e) {
                    error_log("Failed to create notification for user $userId: " . $e->getMessage());
                    continue;
                }
            }
            
            return $successCount;
            
        } catch(Exception $e) {
            error_log("notifyByRole error: " . $e->getMessage());
            return 0;
        }
    }
    
    // ===== GENERAL NOTIFICATIONS =====
    
    public static function systemAnnouncement($title, $message) {
        // Notify all active users
        $database = new Database();
        $db = $database->connect();
        $stmt = $db->query("SELECT user_id FROM users WHERE is_active = 1");
        $users = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        foreach($users as $userId) {
            self::getModel()->create($userId, $title, $message, 'info', 'general');
        }
        
        return true;
    } 
}
?>