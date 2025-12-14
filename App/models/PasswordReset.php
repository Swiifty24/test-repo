<?php
require_once __DIR__ . "/../config/Database.php";

class PasswordReset extends Database {
    
    /**
     * Create a new password reset token
     * @param int $userId
     * @param string $email
     * @return string|false Returns token on success, false on failure
     */
    public function createToken($userId, $email) {
        try {
            // Generate secure random token
            $token = bin2hex(random_bytes(32));
            
            // Token expires in 1 hour
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $sql = "INSERT INTO password_reset_tokens (user_id, email, token, expires_at) 
                    VALUES (:user_id, :email, :token, :expires_at)";
            
            $query = $this->connect()->prepare($sql);
            $query->bindParam(':user_id', $userId);
            $query->bindParam(':email', $email);
            $query->bindParam(':token', $token);
            $query->bindParam(':expires_at', $expiresAt);
            
            if ($query->execute()) {
                return $token;
            }
            return false;
        } catch (Exception $e) {
            error_log("Error creating reset token: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verify if a token is valid
     * @param string $token
     * @return array|false Returns user data if valid, false otherwise
     */
    public function verifyToken($token) {
        $sql = "SELECT prt.*, m.email, m.first_name 
                FROM password_reset_tokens prt
                JOIN members m ON prt.user_id = m.user_id
                WHERE prt.token = :token 
                AND prt.is_used = FALSE 
                AND prt.expires_at > NOW()";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(':token', $token);
        
        if ($query->execute()) {
            return $query->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }
    
    /**
     * Mark a token as used
     * @param string $token
     * @return bool
     */
    public function markTokenUsed($token) {
        $sql = "UPDATE password_reset_tokens 
                SET is_used = TRUE 
                WHERE token = :token";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(':token', $token);
        
        return $query->execute();
    }
    
    /**
     * Clean up expired tokens (should be run via cron)
     * @return int Number of deleted tokens
     */
    public function cleanExpiredTokens() {
        $sql = "DELETE FROM password_reset_tokens 
                WHERE expires_at < NOW() OR is_used = TRUE";
        
        $query = $this->connect()->prepare($sql);
        $query->execute();
        
        return $query->rowCount();
    }
    
    /**
     * Invalidate all tokens for a user (e.g., after successful password change)
     * @param int $userId
     * @return bool
     */
    public function invalidateUserTokens($userId) {
        $sql = "UPDATE password_reset_tokens 
                SET is_used = TRUE 
                WHERE user_id = :user_id AND is_used = FALSE";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(':user_id', $userId);
        
        return $query->execute();
    }
}
?>
