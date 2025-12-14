-- Password Reset Tokens Table
-- Add this to your database to enable forgot password functionality

CREATE TABLE `password_reset_tokens` (
  `token_id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `token` VARCHAR(64) UNIQUE NOT NULL,
  `expires_at` TIMESTAMP NOT NULL,
  `is_used` BOOLEAN DEFAULT FALSE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `members`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add index for faster token lookups
CREATE INDEX idx_token ON password_reset_tokens(token);
CREATE INDEX idx_email ON password_reset_tokens(email);
CREATE INDEX idx_expires ON password_reset_tokens(expires_at);
